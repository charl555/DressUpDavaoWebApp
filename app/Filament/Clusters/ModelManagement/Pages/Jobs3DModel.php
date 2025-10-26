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
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(true)
                    ->action(function ($record, $livewire) {
                        try {
                            $apiKey = config('services.kiri.key');

                            // Use existing URL if present
                            if ($record->model_url) {
                                $livewire->js('window.open("' . $record->model_url . '", "_blank")');
                                return;
                            }

                            $response = Http::withToken($apiKey)
                                ->timeout(30)
                                ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$record->serialize_id}");

                            Log::info('Kiri Engine Download API Response', [
                                'serialize_id' => $record->serialize_id,
                                'status' => $response->status(),
                                'response' => $response->json()
                            ]);

                            if ($response->successful()) {
                                $data = $response->json();

                                if (isset($data['data']['modelUrl'])) {
                                    $record->update([
                                        'model_url' => $data['data']['modelUrl'],
                                        'status' => 'finished',
                                        'url_expiry' => now()->addDays(3),
                                    ]);

                                    $livewire->js('window.open("' . $data['data']['modelUrl'] . '", "_blank")');
                                } else {
                                    $errorCode = $data['code'] ?? null;
                                    $errorMsg = $data['msg'] ?? 'Model is still processing';

                                    $message = match ($errorCode) {
                                        2006 => 'Model not found. Please check the job ID.',
                                        200 => '3D model is still being processed. Please try again in a few minutes.',
                                        default => $errorMsg,
                                    };

                                    Notification::make()
                                        ->title('Download Not Ready')
                                        ->body($message)
                                        ->warning()
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->title('Download Failed')
                                    ->body('Unable to connect to download service. Please try again.')
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Download failed for serialize_id: ' . $record->serialize_id, [
                                'error' => $e->getMessage()
                            ]);

                            Notification::make()
                                ->title('Error')
                                ->body('Failed to download: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No 3D models generated yet')
            ->emptyStateDescription('Upload images in the Create 3D Models page to get started');
    }
}
