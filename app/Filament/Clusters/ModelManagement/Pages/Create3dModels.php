<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use App\Models\Shops;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use BackedEnum;
use Exception;

class Create3dModels extends Page implements HasForms
{
    use InteractsWithForms;

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
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        $shop = Shops::where('user_id', Auth::id())->first();

        if (!$shop?->allow_3d_model_access) {
            return [
                Section::make('Access Denied')
                    ->description('Your account does not have access to 3D model features')
                    ->schema([]),
            ];
        }

        return [
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
                        ->maxSize(10240)  // 10MB per file
                        ->disk('public')
                        ->directory('3d-model-images')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                ]),
        ];
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
            'images' => ['required', 'array', 'min:20', 'max:100'],
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

        // Create job record first
        try {
            $job = KiriEngineJobs::create([
                'user_id' => Auth::id(),
                'serialize_id' => 'temp_' . uniqid(),
                'status' => 'uploading',
                'kiri_options' => [],
                'is_downloaded' => false,
                'url_expiry' => Carbon::now()->addDays(7),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create KiriEngine job: ' . $e->getMessage());
            $this->addError('general', 'Failed to create job. Please try again.');
            $this->isProcessing = false;
            return;
        }

        // Prepare images for direct API upload
        $multipart = [];
        $uploadedFiles = [];

        try {
            // Process each uploaded image
            foreach ($data['images'] as $storedImagePath) {
                // Get the full path to the stored image
                $fullPath = Storage::disk('public')->path($storedImagePath);

                if (!file_exists($fullPath)) {
                    // Try alternative path
                    $fullPath = public_path("storage/{$storedImagePath}");
                    if (!file_exists($fullPath)) {
                        throw new Exception("Stored file not found: {$storedImagePath}");
                    }
                }

                // Check file size (max 10MB)
                $fileSize = filesize($fullPath);
                if ($fileSize > 10 * 1024 * 1024) {
                    throw new Exception('File too large: ' . basename($storedImagePath));
                }

                // Add to multipart array for API upload
                $multipart[] = [
                    'name' => 'imagesFiles',
                    'contents' => fopen($fullPath, 'r'),
                    'filename' => basename($storedImagePath),
                ];

                $uploadedFiles[] = $storedImagePath;
            }

            // Add required API parameters according to documentation
            $multipart[] = ['name' => 'modelQuality', 'contents' => '0'];  // High quality
            $multipart[] = ['name' => 'textureQuality', 'contents' => '0'];  // 4K texture
            $multipart[] = ['name' => 'fileFormat', 'contents' => 'GLB'];  // GLB format
            $multipart[] = ['name' => 'isMask', 'contents' => '0'];  // Auto masking off
            $multipart[] = ['name' => 'textureSmoothing', 'contents' => '1'];  // Texture smoothing on

            Log::info('Sending request to Kiri Engine API', [
                'job_id' => $job->kiri_engine_job_id,
                'image_count' => count($uploadedFiles),
                'api_endpoint' => 'https://api.kiriengine.app/api/v1/open/photo/image'
            ]);

            // Send request to Kiri Engine API
            $response = Http::withToken($apiKey)
                ->timeout(300)  // 5 minutes timeout
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->send('POST', 'https://api.kiriengine.app/api/v1/open/photo/image', [
                    'multipart' => $multipart,
                ]);

            Log::info('Kiri Engine API response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseBody = $response->json();

                if (isset($responseBody['data']['serialize'])) {
                    $kiriSerializeId = $responseBody['data']['serialize'];

                    // Update job with serialize ID and set status to processing
                    $job->update([
                        'serialize_id' => $kiriSerializeId,
                        'status' => 'processing',
                        'notes' => 'Successfully submitted to Kiri Engine',
                    ]);

                    $this->serialize = $kiriSerializeId;
                    $this->statusMessage = "Upload successful! 3D model generation started. Serial ID: {$kiriSerializeId}";

                    Log::info('Kiri Engine job created successfully', [
                        'job_id' => $job->kiri_engine_job_id,
                        'serialize_id' => $kiriSerializeId
                    ]);
                } else {
                    $errorMsg = $responseBody['msg'] ?? 'No serialize ID in response';
                    $job->update([
                        'status' => 'failed',
                        'error_message' => $errorMsg,
                        'notes' => 'API response error'
                    ]);
                    $this->addError('general', "Upload failed: {$errorMsg}");
                    Log::error('Kiri Engine API response missing serialize ID', $responseBody);
                }
            } else {
                $errorBody = $response->json();
                $errorMsg = $errorBody['msg'] ?? "Upload failed with status: {$response->status()}";
                $job->update([
                    'status' => 'failed',
                    'error_message' => $errorMsg,
                    'notes' => 'API request failed'
                ]);
                $this->addError('general', "Upload failed: {$errorMsg}");
                Log::error('Kiri Engine API error', [
                    'status' => $response->status(),
                    'response' => $errorBody
                ]);
            }

            // Clean up uploaded files
            foreach ($uploadedFiles as $filePath) {
                try {
                    Storage::disk('public')->delete($filePath);
                } catch (\Exception $e) {
                    Log::warning("Failed to delete uploaded file: {$filePath}", ['error' => $e->getMessage()]);
                }
            }
        } catch (Exception $e) {
            $job->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'notes' => 'Upload processing failed'
            ]);
            $this->addError('general', "Upload failed: {$e->getMessage()}");
            Log::error('3D model upload failed', [
                'job_id' => $job->kiri_engine_job_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->isProcessing = false;
        $this->form->fill(['images' => []]);  // Reset the form
    }
}
