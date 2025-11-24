<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use App\Models\Products;
use App\Models\Shops;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
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
    public ?int $productId = null;
    public ?Products $product = null;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function mount(?int $product_id = null): void
    {
        $this->productId = $product_id;

        if ($this->productId) {
            $this->product = Products::find($this->productId);
        }

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

        $schema = [
            Section::make('Upload Images for 3D Model')
                ->description($this->getUploadDescription())
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
        ];

        // Add hidden field to preserve product_id if it exists
        if ($this->productId) {
            $schema[] = \Filament\Forms\Components\Hidden::make('product_id')
                ->default($this->productId);
        }

        return $schema;
    }

    private function getUploadDescription(): string
    {
        if ($this->product) {
            return "Upload 20-100 images to generate a 3D model for: {$this->product->name}";
        }
        return 'Upload 20-100 images to generate a 3D model';
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

        // Get product_id from form data or from the page property
        $productId = $data['product_id'] ?? $this->productId;

        // Create job record with product_id if available
        try {
            $job = KiriEngineJobs::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,  // Store product association
                'serialize_id' => 'temp_' . uniqid(),
                'status' => 'uploading',
                'kiri_options' => [],
                'is_downloaded' => false,
                'url_expiry' => Carbon::now()->addDays(7),
            ]);

            Log::info('KiriEngine job created', [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $productId,
                'user_id' => Auth::id()
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
                $fullPath = Storage::disk('public')->path($storedImagePath);

                if (!file_exists($fullPath)) {
                    $fullPath = public_path("storage/{$storedImagePath}");
                    if (!file_exists($fullPath)) {
                        throw new Exception("Stored file not found: {$storedImagePath}");
                    }
                }

                $fileSize = filesize($fullPath);
                if ($fileSize > 10 * 1024 * 1024) {
                    throw new Exception('File too large: ' . basename($storedImagePath));
                }

                $multipart[] = [
                    'name' => 'imagesFiles',
                    'contents' => fopen($fullPath, 'r'),
                    'filename' => basename($storedImagePath),
                ];

                $uploadedFiles[] = $storedImagePath;
            }

            // Add required API parameters
            $multipart[] = ['name' => 'modelQuality', 'contents' => '3'];
            $multipart[] = ['name' => 'textureQuality', 'contents' => '3'];
            $multipart[] = ['name' => 'fileFormat', 'contents' => 'GLB'];
            $multipart[] = ['name' => 'isMask', 'contents' => '1'];
            $multipart[] = ['name' => 'textureSmoothing', 'contents' => '1'];

            Log::info('Sending request to Kiri Engine API', [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $productId,
                'image_count' => count($uploadedFiles),
            ]);

            // Send request to Kiri Engine API
            $response = Http::withToken($apiKey)
                ->timeout(300)
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

                    // Update job with serialize ID
                    $job->update([
                        'serialize_id' => $kiriSerializeId,
                        'status' => 'processing',
                        'notes' => 'Successfully submitted to Kiri Engine',
                    ]);

                    $this->serialize = $kiriSerializeId;

                    // Get product name for message if product exists
                    $productName = null;
                    if ($productId) {
                        $product = Products::find($productId);
                        $productName = $product?->name;
                    }

                    if ($productName) {
                        $this->statusMessage = "Upload successful! 3D model generation started for '{$productName}'. The model will be automatically attached when ready.";
                    } else {
                        $this->statusMessage = "Upload successful! 3D model generation started. Serial ID: {$kiriSerializeId}";
                    }

                    Log::info('Kiri Engine job created successfully', [
                        'job_id' => $job->kiri_engine_job_id,
                        'product_id' => $productId,
                        'serialize_id' => $kiriSerializeId
                    ]);

                    // Show success notification
                    if ($productName) {
                        Notification::make()
                            ->title('3D Model Generation Started')
                            ->body("3D model generation started for '{$productName}'. It will be automatically attached when ready.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('3D Model Generation Started')
                            ->body('3D model generation started. Check the Jobs page for status.')
                            ->success()
                            ->send();
                    }
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
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
        }

        $this->isProcessing = false;
        $this->form->fill(['images' => []]);
    }
}
