<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Jobs\Process3DModelUpload;
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

        // Dispatch background job for processing
        try {
            // Store image paths for background processing
            $imagePaths = $data['images'];

            // Validate images exist before dispatching job
            foreach ($imagePaths as $storedImagePath) {
                $fullPath = Storage::disk('public')->path($storedImagePath);
                if (!file_exists($fullPath)) {
                    $fullPath = public_path('storage/' . $storedImagePath);
                    if (!file_exists($fullPath)) {
                        throw new Exception("Stored file not found: {$storedImagePath}");
                    }
                }

                // Check file size
                $fileSize = filesize($fullPath);
                if ($fileSize > 10 * 1024 * 1024) {  // 10MB
                    throw new Exception('File too large: ' . basename($storedImagePath));
                }
            }

            // Dispatch the background job
            Process3DModelUpload::dispatch($job->kiri_engine_job_id, $imagePaths, $apiKey);

            $this->statusMessage = 'Images uploaded successfully! Processing has started in the background. Check the Download 3D Models page for progress.';

            Log::info('3D Model upload job dispatched', [
                'job_id' => $job->kiri_engine_job_id,
                'image_count' => count($imagePaths)
            ]);
        } catch (Exception $e) {
            $job->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'notes' => 'Failed to dispatch background job'
            ]);
            $this->addError('general', "Upload failed: {$e->getMessage()}");
            Log::error('Failed to dispatch 3D model upload job', [
                'job_id' => $job->kiri_engine_job_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->isProcessing = false;
        $this->form->fill(['images' => []]);  // Reset the form
    }
}
