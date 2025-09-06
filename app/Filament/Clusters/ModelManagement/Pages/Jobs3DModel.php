<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\KiriEngineJobs;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use BackedEnum;

class Jobs3DModel extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected string $view = 'filament.clusters.model-management.pages.jobs3-d-model';
    protected static ?string $navigationLabel = 'Download 3D Models';
    protected static ?string $title = 'Download 3D Models';
    protected static ?string $cluster = ModelManagementCluster::class;
    public static ?string $model = KiriEngineJobs::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(KiriEngineJobs::query())
            ->columns([
                TextColumn::make('kiri_engine_job_id')
                    ->label('Job ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('serialize_id')
                    ->label('KIRI Engine ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('KIRI Engine ID copied!'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'uploading', 'processing' => 'info',
                        'finished' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('url_expiry')
                    ->label('Url Expiry Date')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Action::make('fetchDownloadLink')
                    ->label('Fetch Download Link')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn($record) => !empty($record->serialize_id))  // Show if serialize_id exists
                    ->requiresConfirmation()  // Optional: add confirmation modal
                    ->action(function (KiriEngineJobs $record) {
                        try {
                            $apiKey = config('services.kiri.key');
                            $serializeId = $record->serialize_id;

                            $response = Http::withToken($apiKey)
                                ->timeout(60)
                                ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$serializeId}");

                            if ($response->successful() && isset($response['data']['modelUrl'])) {
                                $modelUrl = $response['data']['modelUrl'];

                                // Save to DB
                                $record->update([
                                    'model_url' => $modelUrl,
                                    'status' => 'finished',  // Mark as finished if link is fetched
                                ]);

                                // Redirect to download
                                return redirect()->away($modelUrl);
                            }

                            $this->notify('danger', 'Failed to fetch download link: ' . ($response['msg'] ?? 'Unknown error.'));
                        } catch (\Exception $e) {
                            Log::error('Fetch download link error: ' . $e->getMessage());
                            $this->notify('danger', 'Error fetching ZIP link.');
                        }
                    }),
            ])
            ->bulkActions([]);
    }
}
