<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use BackedEnum;
use Exception;

class Create3dModels extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Create 3D Models';

    protected string $view = 'filament.clusters.model-management.pages.create3d-models';

    protected static ?string $cluster = ModelManagementCluster::class;

    public $images = [];

    public int $modelQuality = 1;

    public int $textureQuality = 1;

    public string $fileFormat = 'GLB';

    public bool $isMask = false;

    public bool $textureSmoothing = true;

    public ?string $serialize = null;

    public ?string $modelUrl = null;

    public bool $isProcessing = false;

    public string $statusMessage = '';

    public ?int $currentJobId = null;

    public bool $isDownloaded = false;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function mount(): void
    {
        $this->form->fill([
            'modelQuality' => $this->modelQuality,
            'textureQuality' => $this->textureQuality,
            'fileFormat' => $this->fileFormat,
            'isMask' => $this->isMask,
            'textureSmoothing' => $this->textureSmoothing,
        ]);

        $this->reset(['serialize', 'modelUrl', 'isProcessing', 'statusMessage', 'currentJobId', 'isDownloaded']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Upload Images & Generate 3D Model')
                    ->description('Upload at least 20 images but no more than 300 images for consistent 3D models. Ensure images are well-lit and capture the object from various angles.')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Select Images for 3D Model')
                            ->multiple()
                            ->image()
                            ->required()
                            ->reactive()
                            ->minFiles(20)
                            ->maxFiles(300)
                            ->maxSize(10240),
                        Select::make('modelQuality')
                            ->label('Model Quality')
                            ->options([1 => 'High', 0 => 'Low'])
                            ->default($this->modelQuality)
                            ->required()
                            ->reactive(),
                        Select::make('textureQuality')
                            ->label('Texture Quality')
                            ->options([1 => 'High', 0 => 'Low'])
                            ->default($this->textureQuality)
                            ->required()
                            ->reactive(),
                        Select::make('fileFormat')
                            ->label('Output File Format')
                            ->options(['GLB' => 'GLB (Recommended for web)', 'OBJ' => 'OBJ'])
                            ->default($this->fileFormat)
                            ->required()
                            ->reactive(),
                        Toggle::make('isMask')
                            ->label('Apply Masking (If images have transparent backgrounds)')
                            ->default($this->isMask)
                            ->reactive(),
                        Toggle::make('textureSmoothing')
                            ->label('Apply Texture Smoothing')
                            ->default($this->textureSmoothing)
                            ->reactive(),
                    ]),
            ]);
    }

    public function submit()
    {
        $this->resetErrorBag();

        $this->modelUrl = null;
        $this->serialize = null;
        $this->statusMessage = '';
        $this->isDownloaded = false;
        $this->currentJobId = null;  // Clear previous job ID

        $data = $this->form->getState();

        if (empty($data['images'])) {
            $this->addError('images', 'Please upload at least 20 images.');
            return;
        }

        $this->isProcessing = true;
        $this->statusMessage = 'Uploading images and starting 3D model generation... This may take a moment. Check your job history for updates.';

        $apiKey = config('services.kiri.key');

        if (empty($apiKey)) {
            Log::error('Kiri Engine API Key is empty. Check .env and config/services.php');
            $this->addError('general', 'Kiri Engine API Key is not configured. Please contact support.');
            $this->isProcessing = false;
            return;
        }

        try {
            $expiryDate = Carbon::now()->addDays(3)->toDateString();

            $job = KiriEngineJobs::create([
                'user_id' => Auth::id(),
                'serialize_id' => 'temp_serialize_' . uniqid(),  // Temporary ID, will be updated by Kiri
                'status' => 'uploading',
                'kiri_options' => $data,
                'is_downloaded' => false,
                'url_expiry' => $expiryDate,
            ]);
            $this->currentJobId = $job->kiri_engine_job_id;  // Store the ID of the newly created job
            $this->serialize = $job->serialize_id;  // Display the temporary ID
        } catch (Exception $e) {
            Log::error('Failed to create Kiri Engine job record: ' . $e->getMessage());
            $this->addError('general', 'Failed to create job record. Please try again.');
            $this->isProcessing = false;
            return;
        }

        $multipart = [];
        foreach ($data['images'] as $imagePath) {
            try {
                // Ensure file paths are correctly resolved for Livewire temporary uploads
                $file = storage_path('app/livewire-tmp/' . basename($imagePath));
                if (!file_exists($file)) {
                    // Fallback for paths not in livewire-tmp if necessary, though it should be
                    $file = storage_path('app/' . $imagePath);
                    if (!file_exists($file)) {
                        $file = public_path('storage/' . $imagePath);  // Last resort
                        if (!file_exists($file)) {
                            throw new Exception('Temporary file not found at: ' . $imagePath);
                        }
                    }
                }
                $multipart[] = [
                    'name' => 'imagesFiles',
                    'contents' => fopen($file, 'r'),
                    'filename' => basename($file),
                ];
            } catch (Exception $e) {
                Log::error('Failed to open temporary file for Kiri Engine upload: ' . $e->getMessage());
                $this->addError('images', 'Error preparing some images for upload. Please try again.');
                $this->isProcessing = false;
                $job->update(['status' => 'failed', 'notes' => 'Image file error: ' . $e->getMessage()]);
                return;
            }
        }

        $multipart[] = ['name' => 'modelQuality', 'contents' => (string) $data['modelQuality']];
        $multipart[] = ['name' => 'textureQuality', 'contents' => (string) $data['textureQuality']];
        $multipart[] = ['name' => 'fileFormat', 'contents' => $data['fileFormat']];
        $multipart[] = ['name' => 'isMask', 'contents' => $data['isMask'] ? '1' : '0'];
        $multipart[] = ['name' => 'textureSmoothing', 'contents' => $data['textureSmoothing'] ? '1' : '0'];

        $multipart[] = ['name' => 'notifyUrl', 'contents' => route('webhooks.kiri-model-ready', ['job_id' => $job->kiri_engine_job_id])];

        try {
            $response = Http::withToken($apiKey)
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(300)
                ->send('POST', 'https://api.kiriengine.app/api/v1/open/photo/image', [
                    'multipart' => $multipart,
                ]);

            if ($response->successful()) {
                $responseBody = $response->json();
                if (isset($responseBody['data']['serialize'])) {
                    $kiriSerializeId = $responseBody['data']['serialize'];
                    $job->update([
                        'serialize_id' => $kiriSerializeId,  // Update with actual serialize ID from Kiri
                        'status' => 'processing',
                    ]);
                    $this->serialize = $kiriSerializeId;  // Update Livewire property for immediate display
                    $this->statusMessage = 'Images uploaded successfully! 3D model generation has started (Serial ID: ' . $this->serialize . '). Check your job history for progress.';
                } else {
                    $job->update(['status' => 'failed', 'notes' => 'Kiri API response missing serialize ID: ' . json_encode($responseBody)]);
                    $this->addError('general', 'Kiri Engine API response missing serialize ID. ' . ($responseBody['message'] ?? 'Unknown error.'));
                    $this->statusMessage = 'Upload successful, but could not get model ID. Check your job history for details.';
                }
            } else {
                $errorMsg = $response->json('message', 'Unknown error from Kiri Engine API.');
                $job->update(['status' => 'failed', 'notes' => 'Upload to Kiri Engine failed: ' . $response->body()]);
                $this->addError('general', 'Upload to Kiri Engine failed: ' . $errorMsg);
                $this->statusMessage = 'Upload to Kiri Engine failed: ' . $errorMsg . '. Check your job history for details.';
            }
        } catch (Exception $e) {
            $job->update(['status' => 'failed', 'notes' => 'API call exception: ' . $e->getMessage()]);
            Log::error('Kiri Engine API Call Error: ' . $e->getMessage());
            $this->addError('general', 'An error occurred during API call: ' . $e->getMessage());
            $this->statusMessage = 'An unexpected error occurred during upload. Please check your job history.';
        } finally {
            $this->isProcessing = false;
            $this->images = [];
        }
    }
}
