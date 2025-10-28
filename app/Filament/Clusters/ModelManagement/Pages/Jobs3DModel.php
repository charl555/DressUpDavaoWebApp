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
            ->query(KiriEngineJobs::query()->latest())
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
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn($record) => $record->model_url)
                    ->action(function ($record, $livewire) {
                        try {
                            if ($record->model_url) {
                                $livewire->js('window.open("' . $record->model_url . '", "_blank")');
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to download: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('check_status')
                    ->label('Check Status')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn($record) => !$record->model_url && in_array($record->status, ['uploading', 'processing']))
                    ->action(function ($record, $livewire) {
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

            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$job->serialize_id}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['data']['modelUrl'])) {
                    $job->update([
                        'model_url' => $data['data']['modelUrl'],
                        'status' => 'finished',
                        'url_expiry' => now()->addDays(3),
                    ]);

                    Notification::make()
                        ->title('Model Ready!')
                        ->body('Your 3D model is now available for download.')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Still Processing')
                        ->body('Your model is still being processed. Please check back later.')
                        ->warning()
                        ->send();
                }
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Status Check Failed')
                ->body('Unable to check model status. Please try again.')
                ->danger()
                ->send();
        }
    }
}
