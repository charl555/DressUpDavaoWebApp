<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use App\Models\Shops;
use App\Services\ModelDownloadService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use BackedEnum;

class Jobs3DModel extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static ?string $slug = 'download-3d-models';
    protected string $view = 'filament.clusters.model-management.pages.jobs3-d-model';
    protected static ?string $navigationLabel = 'Download 3D Models';
    protected static ?string $title = 'Download 3D Models';
    protected static ?int $navigationSort = 2;
    protected static ?string $cluster = ModelManagementCluster::class;

    public bool $isPolling = false;
    public int $polledCount = 0;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function mount(): void
    {
        // Start automatic polling when the page loads
        $this->startAutomaticPolling();
    }

    /**
     * Start automatic polling for ready models and job status
     */
    private function startAutomaticPolling(): void
    {
        if ($this->isPolling) {
            return;
        }

        $this->isPolling = true;

        $this->js(<<<JS
                (function() {
                    let pollCount = 0;
                    const maxPolls = 30; 
                    const pollInterval = 5000; 
                    
                    function executePolling() {
                        pollCount++;
                        
                     
                        if (pollCount >= maxPolls) {
                            console.log('Auto-polling stopped after ' + maxPolls + ' attempts');
                            return;
                        }
                        
                      
                        \$wire.call('pollForReadyModels').then((downloadResult) => {
                            console.log('Download polling attempt ' + pollCount + ': ' + downloadResult);
                        });
                        
                        \$wire.call('pollForJobStatusUpdates').then((statusResult) => {
                            console.log('Status polling attempt ' + pollCount + ': ' + statusResult);
                         
                            if (statusResult === 'no_active_jobs' && pollCount > 5) {
                                console.log('Auto-polling completed - no active jobs to monitor');
                            }
                        });
                    }
                    
                    
                    executePolling();
                    
                    
                    const poll = setInterval(() => {
                        if (pollCount >= maxPolls) {
                            clearInterval(poll);
                            return;
                        }
                        executePolling();
                    }, pollInterval);
                    
                })();
            JS);
    }

    /**
     * Polling method for ready models (download and store)
     */
    public function pollForReadyModels(): string
    {
        try {
            $readyJobs = KiriEngineJobs::where('user_id', Auth::id())
                ->where('status', 'finished')
                ->whereNotNull('model_url')
                ->whereDoesntHave('stored3dModel')
                ->get();

            if ($readyJobs->isEmpty()) {
                return 'no_jobs';
            }

            $processedCount = 0;

            foreach ($readyJobs as $job) {
                if ($this->downloadAndStoreModel($job)) {
                    $processedCount++;
                    $this->polledCount++;

                    // Auto-attach to product if job has product_id
                    $this->autoAttachToProduct($job);
                }
            }

            Log::info('Auto-polling processed jobs', [
                'processed' => $processedCount,
                'total_ready' => $readyJobs->count(),
                'user_id' => Auth::id()
            ]);

            return "processed_{$processedCount}_jobs";
        } catch (\Exception $e) {
            Log::error('Auto-polling failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return 'error';
        }
    }

    /**
     * Polling method for job status updates
     */
    public function pollForJobStatusUpdates(): string
    {
        try {
            // Get jobs that are still processing (uploading or processing status)
            $activeJobs = KiriEngineJobs::where('user_id', Auth::id())
                ->whereIn('status', ['uploading', 'processing'])
                ->whereNull('model_url')
                ->get();

            if ($activeJobs->isEmpty()) {
                return 'no_active_jobs';
            }

            $updatedCount = 0;
            $finishedCount = 0;

            foreach ($activeJobs as $job) {
                $previousStatus = $job->status;

                // Check the current status via API
                $this->checkJobStatus($job);

                // Refresh the job to get updated status
                $job->refresh();

                if ($previousStatus !== $job->status) {
                    $updatedCount++;

                    // If job just finished, notify user
                    if ($job->status === 'finished' && $job->model_url) {
                        $finishedCount++;

                        Notification::make()
                            ->title('3D Model Ready!')
                            ->body("Job {$job->serialize_id} has completed and is ready for download.")
                            ->success()
                            ->send();

                        Log::info('Job completed via auto-polling', [
                            'job_id' => $job->kiri_engine_job_id,
                            'serialize_id' => $job->serialize_id
                        ]);
                    }
                }
            }

            // Refresh table if any status changed
            if ($updatedCount > 0) {
                $this->dispatch('refreshTable');
            }

            Log::info('Auto-status polling completed', [
                'active_jobs' => $activeJobs->count(),
                'updated' => $updatedCount,
                'finished' => $finishedCount,
                'user_id' => Auth::id()
            ]);

            return "updated_{$updatedCount}_jobs_finished_{$finishedCount}";
        } catch (\Exception $e) {
            Log::error('Auto-status polling failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return 'error';
        }
    }

    /**
     * Auto-attach stored 3D model to product
     */
    private function autoAttachToProduct(KiriEngineJobs $job): void
    {
        try {
            // Check if job has a product association
            if (!$job->product_id) {
                return;
            }

            // Get the stored model
            $storedModel = $job->stored3dModel;
            if (!$storedModel) {
                Log::warning('No stored model found for auto-attach', [
                    'job_id' => $job->kiri_engine_job_id,
                    'product_id' => $job->product_id
                ]);
                return;
            }

            // Get the product
            $product = $job->product;
            if (!$product) {
                Log::warning('Product not found for auto-attach', [
                    'job_id' => $job->kiri_engine_job_id,
                    'product_id' => $job->product_id
                ]);
                return;
            }

            // Use the exact same model_path from stored3d_models
            $modelPath = $storedModel->model_path;

            // Check if a 3D model already exists for this product
            $existingModel = $product->product_3d_models;

            if ($existingModel) {
                // Update existing model_path with the exact same path
                $existingModel->update(['model_path' => $modelPath]);
                $message = "3D model automatically updated for '{$product->name}'!";
            } else {
                // Create a new 3D model record with the exact same path
                \App\Models\Product3dModels::create([
                    'product_id' => $product->product_id,
                    'model_path' => $modelPath,
                ]);
                $message = "3D model automatically attached to '{$product->name}'!";
            }

            // Send notification
            Notification::make()
                ->title('3D Model Auto-Attached')
                ->body($message)
                ->success()
                ->send();

            Log::info('3D model auto-attached to product', [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $job->product_id,
                'product_name' => $product->name,
                'model_path' => $modelPath
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to auto-attach 3D model to product', [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $job->product_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Download and store model (extracted for reuse)
     */
    private function downloadAndStoreModel($record): bool
    {
        try {
            $downloadService = new ModelDownloadService();
            $storedModel = $downloadService->downloadAndStoreModel($record);

            if ($storedModel) {
                Notification::make()
                    ->title('3D Model Stored Successfully')
                    ->body('The 3D model has been automatically downloaded and stored locally.')
                    ->success()
                    ->send();

                // Refresh the table
                $this->dispatch('refreshTable');
                return true;
            } else {
                Log::warning('Auto-download failed for job', [
                    'job_id' => $record->kiri_engine_job_id,
                    'serialize_id' => $record->serialize_id
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Auto-store locally failed', [
                'job_id' => $record->kiri_engine_job_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function table(Table $table): Table
    {
        $shop = Shops::where('user_id', Auth::id())->first();

        if (!$shop?->allow_3d_model_access) {
            return $table
                ->query(KiriEngineJobs::query()->where('kiri_engine_job_id', 0))
                ->columns([])
                ->emptyStateHeading('Access Denied')
                ->emptyStateDescription('Your account currently does not have access to 3D model features. Please contact support to enable this functionality.')
                ->actions([]);
        }

        return $table
            ->query(KiriEngineJobs::query()->where('user_id', Auth::id())->latest())
            ->columns([
                TextColumn::make('serialize_id')
                    ->label('Job ID')
                    ->formatStateUsing(function ($record) {
                        $state = $record->serialize_id;

                        if (strlen($state) >= 36 && str_contains($state, '-')) {
                            $parts = explode('-', $state);
                            return substr($parts[0], 0, 8);  // Just show the first segment
                        }

                        return substr($state, 0, 8);  // Show first 8 characters
                    })
                    ->copyable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'uploading', 'processing' => 'warning',
                        'finished' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('model_url')
                    ->label('Download Ready')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Yes' : 'No';
                    })
                    ->badge()
                    ->color(function ($state) {
                        return $state ? 'success' : 'gray';
                    }),
                TextColumn::make('stored3dModel.model_name')
                    ->label('Stored Locally')
                    ->formatStateUsing(function ($record) {
                        return $record->stored3dModel ? 'Yes' : 'No';
                    })
                    ->badge()
                    ->color(function ($record) {
                        return $record->stored3dModel ? 'success' : 'gray';
                    }),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
                TextColumn::make('error_message')
                    ->label('Error Details')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->error_message)
                    ->visible(fn($record) => !empty($record->error_message))
                    ->color('danger'),
            ])
            ->actions([
                Action::make('download_and_store')
                    ->label('Store Locally')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->visible(fn($record) => $this->canDownloadOrRegenerate($record) && !$record->stored3dModel)
                    ->action(function ($record) {
                        try {
                            $downloadService = new ModelDownloadService();
                            $storedModel = $downloadService->downloadAndStoreModel($record);

                            if ($storedModel) {
                                Notification::make()
                                    ->title('3D Model Stored Successfully')
                                    ->body('The 3D model has been downloaded and stored locally.')
                                    ->success()
                                    ->send();

                                // Filament v4 correct refresh
                                $this->dispatch('filament-refresh');
                            } else {
                                Notification::make()
                                    ->title('Storage Failed')
                                    ->body('Failed to download and store the 3D model. Please try again.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Store locally failed', [
                                'job_id' => $record->kiri_engine_job_id,
                                'error' => $e->getMessage()
                            ]);

                            Notification::make()
                                ->title('Error')
                                ->body("Failed to store model: {$e->getMessage()}")
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('view_stored')
                    ->label('View 3D Model')
                    ->icon('heroicon-o-eye')
                    ->visible(fn($record) => $record->stored3dModel)
                    ->url(fn($record) => route('view-3d-model', ['id' => $record->stored3dModel->stored_3d_model_id])),
                // Action::make('download_zip')
                //     ->label('Download ZIP')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->visible(fn($record) => $this->canDownloadOrRegenerate($record))
                //     ->action(function ($record, $livewire) {
                //         try {
                //             // Check if URL is expired or about to expire (within 5 minutes)
                //             $isExpired = $record->url_expiry && $record->url_expiry->isPast();
                //             $isExpiringSoon = $record->url_expiry && $record->url_expiry->diffInMinutes(now()) < 5;
                //             $isOldRecord = $record->updated_at->diffInDays(now()) > 3;
                //             $downloadUrl = null;
                //             if ($isExpired || $isExpiringSoon || !$record->model_url || $isOldRecord) {
                //                 // For old records, check if regeneration is possible
                //                 if ($isOldRecord) {
                //                     $canRegenerate = $this->checkIfRegenerationPossible($record);
                //                     if (!$canRegenerate) {
                //                         Notification::make()
                //                             ->title('Download Expired')
                //                             ->body('This 3D model file has been deleted from the server (files are only retained for 3 days). Please generate a new model.')
                //                             ->warning()
                //                             ->send();
                //                         return;
                //                     }
                //                 }
                //                 // Regenerate the download URL
                //                 $downloadUrl = $this->regenerateDownloadUrl($record);
                //                 if ($downloadUrl) {
                //                     Notification::make()
                //                         ->title('Download Generated')
                //                         ->body('New download link created and will expire in 1 hour.')
                //                         ->success()
                //                         ->send();
                //                 } else {
                //                     Notification::make()
                //                         ->title('Download Unavailable')
                //                         ->body('The 3D model file is no longer available. Files are only stored for 3 days. Please generate a new model.')
                //                         ->warning()
                //                         ->send();
                //                     return;
                //                 }
                //             } else {
                //                 // Use existing URL
                //                 $downloadUrl = $record->model_url;
                //             }
                //             // Actually trigger the download
                //             if ($downloadUrl) {
                //                 $livewire->js("window.open('{$downloadUrl}', '_blank')");
                //             }
                //         } catch (\Exception $e) {
                //             Notification::make()
                //                 ->title('Error')
                //                 ->body("Failed to download: {$e->getMessage()}")
                //                 ->danger()
                //                 ->send();
                //         }
                //     }),
                Action::make('check_status')
                    ->label('Check Status')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn($record) => !$record->model_url && in_array($record->status, ['uploading', 'processing']))
                    ->action(function ($record) {
                        $this->checkJobStatus($record);
                    }),
            ])
            ->emptyStateHeading('No 3D models generated yet')
            ->emptyStateDescription('Upload images in the Create 3D Models page to get started');
    }

    private function refreshTable()
    {
        $this->dispatch('refreshTable');
    }

    private function canDownloadOrRegenerate($record): bool
    {
        if ($record->status !== 'finished') {
            return false;
        }

        return $record->model_url ||
            (!empty($record->serialize_id) && $record->updated_at->diffInDays(now()) <= 3);
    }

    private function checkIfRegenerationPossible(KiriEngineJobs $job): bool
    {
        try {
            $apiKey = config('services.kiri.key');

            $statusResponse = Http::withToken($apiKey)
                ->timeout(10)
                ->get("https://api.kiriengine.app/api/v1/open/model/getStatus?serialize={$job->serialize_id}");

            if ($statusResponse->successful()) {
                $statusData = $statusResponse->json();
                $status = $statusData['data']['status'] ?? null;

                return $status === 2;
            }

            return false;
        } catch (\Exception $e) {
            Log::warning('Failed to check regeneration possibility', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function regenerateDownloadUrl(KiriEngineJobs $job): ?string
    {
        try {
            $apiKey = config('services.kiri.key');

            $downloadResponse = Http::withToken($apiKey)
                ->timeout(15)
                ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$job->serialize_id}");

            if ($downloadResponse->successful()) {
                $downloadData = $downloadResponse->json();

                if (isset($downloadData['data']['modelUrl'])) {
                    $newUrl = $downloadData['data']['modelUrl'];

                    $job->update([
                        'model_url' => $newUrl,
                        'url_expiry' => now()->addHour(),
                    ]);

                    Log::info('Download URL regenerated', [
                        'job_id' => $job->id,
                        'serialize_id' => $job->serialize_id,
                    ]);

                    return $newUrl;
                } else {
                    $errorMsg = $downloadData['message'] ?? 'Unknown error';
                    Log::warning('API returned no modelUrl', [
                        'job_id' => $job->id,
                        'api_response' => $downloadData
                    ]);
                }
            } else {
                Log::warning('API request failed for URL regeneration', [
                    'job_id' => $job->id,
                    'status_code' => $downloadResponse->status(),
                    'response' => $downloadResponse->body()
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to regenerate download URL', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Manual status check (fallback if webhook fails)
     */
    private function checkJobStatus(KiriEngineJobs $job)
    {
        try {
            $apiKey = config('services.kiri.key');

            // First, check the status
            $statusResponse = Http::withToken($apiKey)
                ->timeout(15)
                ->get("https://api.kiriengine.app/api/v1/open/model/getStatus?serialize={$job->serialize_id}");

            Log::info('Kiri Engine status check', [
                'job_id' => $job->kiri_engine_job_id,
                'serialize_id' => $job->serialize_id,
                'status_response' => $statusResponse->body()
            ]);

            if ($statusResponse->successful()) {
                $statusData = $statusResponse->json();
                $status = $statusData['data']['status'] ?? null;

                // Update status based on API response
                // Status: -1=Uploading, 0=Processing, 1=Failed, 2=Successful, 3=Queuing, 4=Expired
                $newStatus = match ($status) {
                    -1 => 'uploading',
                    0, 3 => 'processing',
                    1 => 'failed',
                    2 => 'finished',
                    4 => 'expired',
                    default => $job->status
                };

                $job->update(['status' => $newStatus]);

                if ($status === 2) {
                    // Model is ready, get download URL
                    $downloadResponse = Http::withToken($apiKey)
                        ->timeout(15)
                        ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$job->serialize_id}");

                    if ($downloadResponse->successful()) {
                        $downloadData = $downloadResponse->json();

                        if (isset($downloadData['data']['modelUrl'])) {
                            $job->update([
                                'model_url' => $downloadData['data']['modelUrl'],
                                'status' => 'finished',
                                'url_expiry' => now()->addHour(),  // URLs expire in 60 minutes
                            ]);

                            Notification::make()
                                ->title('Model Ready!')
                                ->body('Your 3D model is now available for download.')
                                ->success()
                                ->send();
                        }
                    }
                } elseif ($status === 1) {
                    $job->update([
                        'error_message' => 'Model generation failed',
                        'notes' => 'Processing failed on Kiri Engine'
                    ]);

                    Notification::make()
                        ->title('Generation Failed')
                        ->body('Your 3D model generation failed. Please try uploading again.')
                        ->danger()
                        ->send();
                } else {
                    $statusText = match ($status) {
                        -1 => 'uploading',
                        0 => 'processing',
                        3 => 'queued',
                        4 => 'expired',
                        default => 'unknown'
                    };

                    Notification::make()
                        ->title('Status Updated')
                        ->body("Your model is currently: {$statusText}")
                        ->info()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Status Check Failed')
                    ->body('Unable to check model status. Please try again.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Log::error('Status check failed', [
                'job_id' => $job->kiri_engine_job_id,
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Status Check Failed')
                ->body('Unable to check model status. Please try again.')
                ->danger()
                ->send();
        }
    }
}
