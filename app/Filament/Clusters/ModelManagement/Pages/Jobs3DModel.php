<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use App\Models\Shops;
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

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
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
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn($record) => $this->canDownloadOrRegenerate($record))
                    ->action(function ($record, $livewire) {
                        try {
                            // Check if URL is expired or about to expire (within 5 minutes)
                            $isExpired = $record->url_expiry && $record->url_expiry->isPast();
                            $isExpiringSoon = $record->url_expiry && $record->url_expiry->diffInMinutes(now()) < 5;
                            $isOldRecord = $record->updated_at->diffInDays(now()) > 3;  // Record older than 3 days

                            if ($isExpired || $isExpiringSoon || !$record->model_url || $isOldRecord) {
                                // For old records, check if regeneration is possible
                                if ($isOldRecord) {
                                    $canRegenerate = $this->checkIfRegenerationPossible($record);

                                    if (!$canRegenerate) {
                                        Notification::make()
                                            ->title('Download Expired')
                                            ->body('This 3D model file has been deleted from the server (files are only retained for 3 days). Please generate a new model.')
                                            ->warning()
                                            ->send();
                                        return;
                                    }
                                }

                                // Regenerate the download URL
                                $newUrl = $this->regenerateDownloadUrl($record);

                                if ($newUrl) {
                                    $livewire->js("window.open('{$newUrl}', '_blank')");

                                    Notification::make()
                                        ->title('Download Generated')
                                        ->body('New download link created and will expire in 1 hour.')
                                        ->success()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Download Unavailable')
                                        ->body('The 3D model file is no longer available. Files are only stored for 3 days. Please generate a new model.')
                                        ->warning()
                                        ->send();
                                }
                            } else {
                                // Use existing URL
                                $livewire->js("window.open('{$record->model_url}', '_blank')");
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body("Failed to download: {$e->getMessage()}")
                                ->danger()
                                ->send();
                        }
                    }),
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
     * Check if URL can be regenerated for a record
     */
    private function canRegenerateUrl($record): bool
    {
        return $record->status === 'finished' && !empty($record->serialize_id);
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
