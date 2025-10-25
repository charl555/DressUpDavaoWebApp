<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use App\Models\Shops;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use BackedEnum;
use Exception;

class Create3dModels extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected static ?string $navigationLabel = 'Create 3D Models';
    protected static ?string $slug = 'create3d-models';
    protected string $view = 'filament.clusters.model-management.pages.create3d-models';
    protected static ?string $cluster = ModelManagementCluster::class;
    protected static ?int $navigationSort = 1;

    public $images = [];
    public ?string $serialize = null;
    public bool $isProcessing = false;
    public string $statusMessage = '';

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function mount(): void
    {
        $this->form->fill([
            'images' => $this->images,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $shop = Shops::where('user_id', Auth::id())->first();

        if (!$shop?->allow_3d_model_access) {
            return $schema
                ->schema([
                    Section::make('Access Denied')
                        ->description('Your account does not have access to 3D model features')
                        ->schema([
                            // Empty schema - just shows the access denied message
                        ]),
                ]);
        }

        return $schema
            ->schema([
                Section::make('Upload Images for 3D Model')
                    ->description('Upload 20-100 images to generate a 3D model')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Images')
                            ->helperText('Upload 20-100 high-quality images from all angles')
                            ->multiple()
                            ->image()
                            ->required()
                            ->minFiles(20)
                            ->maxFiles(100)
                            ->maxSize(10240)
                            ->disk('public')
                            ->directory('3d-model-images')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ]),
            ]);
    }

    public function submit()
    {
        $shop = Shops::where('user_id', Auth::id())->first();
        if (!$shop?->allow_3d_model_access) {
            $this->addError('general', 'Your account does not have access to 3D model features.');
            return;
        }

        $this->resetErrorBag();
        $this->serialize = null;
        $this->statusMessage = '';

        // Validate the form
        $this->validate([
            'images' => ['required', 'array', 'min:20', 'max:300'],
            'images.*' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $data = $this->form->getState();

        $this->isProcessing = true;
        $this->statusMessage = 'Uploading images...';

        $apiKey = config('services.kiri.key');

        if (empty($apiKey)) {
            $this->addError('general', 'API configuration error. Please contact support.');
            $this->isProcessing = false;
            return;
        }

        // Create job record
        try {
            $job = KiriEngineJobs::create([
                'user_id' => Auth::id(),
                'serialize_id' => 'temp_' . uniqid(),
                'status' => 'uploading',
                'kiri_options' => [],
                'is_downloaded' => false,
                'url_expiry' => Carbon::now()->addDays(7)->toDateString(),
            ]);
        } catch (Exception $e) {
            $this->addError('general', 'Failed to create job. Please try again.');
            $this->isProcessing = false;
            return;
        }

        // Prepare images for upload - USING STORED PATHS
        $multipart = [];
        foreach ($data['images'] as $storedImagePath) {
            try {
                // Get the full path to the stored image
                $fullPath = Storage::disk('public')->path($storedImagePath);

                if (!file_exists($fullPath)) {
                    throw new Exception('Stored file not found: ' . $storedImagePath);
                }

                $multipart[] = [
                    'name' => 'imagesFiles',
                    'contents' => fopen($fullPath, 'r'),
                    'filename' => basename($storedImagePath),
                ];
            } catch (Exception $e) {
                Log::error('File upload error: ' . $e->getMessage() . ' - Path: ' . $storedImagePath);
                $this->addError('images', 'Error preparing images for upload.');
                $this->isProcessing = false;
                $job->update(['status' => 'failed', 'notes' => 'Image file error: ' . $e->getMessage()]);
                return;
            }
        }

        // Add required API parameters with optimal defaults
        $multipart[] = ['name' => 'modelQuality', 'contents' => '0'];
        $multipart[] = ['name' => 'textureQuality', 'contents' => '0'];
        $multipart[] = ['name' => 'fileFormat', 'contents' => 'GLB'];
        $multipart[] = ['name' => 'isMask', 'contents' => '0'];
        $multipart[] = ['name' => 'textureSmoothing', 'contents' => '1'];
        $multipart[] = ['name' => 'notifyUrl', 'contents' => route('webhooks.kiri-model-ready', ['job_id' => $job->kiri_engine_job_id])];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(300)
                ->send('POST', 'https://api.kiriengine.app/api/v1/open/photo/image', [
                    'multipart' => $multipart,
                ]);

            if ($response->successful()) {
                $responseBody = $response->json();
                if (isset($responseBody['data']['serialize'])) {
                    $kiriSerializeId = $responseBody['data']['serialize'];
                    $job->update([
                        'serialize_id' => $kiriSerializeId,
                        'status' => 'processing',
                    ]);
                    $this->serialize = $kiriSerializeId;
                    $this->statusMessage = 'Upload successful! 3D model generation started. Check Download 3D Models page for progress.';

                    // Optional: Clean up uploaded images after successful API call
                    // $this->cleanupUploadedImages($data['images']);
                } else {
                    $job->update(['status' => 'failed', 'notes' => 'No serialize ID in response']);
                    $this->addError('general', 'Upload failed: No model ID received');
                }
            } else {
                $errorMsg = $response->json('message', 'Upload failed');
                $job->update(['status' => 'failed', 'notes' => 'API error: ' . $errorMsg]);
                $this->addError('general', 'Upload failed: ' . $errorMsg);
            }
        } catch (Exception $e) {
            $job->update(['status' => 'failed', 'notes' => 'Exception: ' . $e->getMessage()]);
            $this->addError('general', 'Upload error: ' . $e->getMessage());
        }

        $this->isProcessing = false;
        $this->images = [];
    }

    private function cleanupUploadedImages(array $imagePaths): void
    {
        try {
            foreach ($imagePaths as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            Log::info('Cleaned up uploaded images after successful API call');
        } catch (Exception $e) {
            Log::warning('Failed to clean up uploaded images: ' . $e->getMessage());
        }
    }
}
