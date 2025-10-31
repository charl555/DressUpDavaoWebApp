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
                    ->visible(fn($record) => $record->model_url)
                    ->action(function ($record, $livewire) {
                        try {
                            if ($record->model_url) {
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
