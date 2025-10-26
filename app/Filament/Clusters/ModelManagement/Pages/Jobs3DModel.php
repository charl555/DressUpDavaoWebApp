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
                    ->label('Download Status')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state)
                            return 'Not Ready';
                        return 'Ready';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if (!$state)
                            return 'gray';
                        return 'success';
                    }),
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function ($record) {
                        // Only show if we have a model URL and status is finished
                        return $record->status === 'finished' && $record->model_url;
                    })
                    ->action(function ($record) {
                        return redirect()->away($record->model_url);
                    }),
                Action::make('checkAndDownload')
                    ->label('Check & Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function ($record) {
                        // Show for processing jobs or finished jobs without URL
                        return $record->status === 'processing' ||
                            ($record->status === 'finished' && !$record->model_url);
                    })
                    ->action(function ($record) {
                        try {
                            $apiKey = config('services.kiri.key');
                            $response = Http::withToken($apiKey)
                                ->timeout(30)
                                ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$record->serialize_id}");

                            if ($response->successful()) {
                                $data = $response->json();

                                if (isset($data['data']['modelUrl'])) {
                                    // Update record with new download URL
                                    $record->update([
                                        'model_url' => $data['data']['modelUrl'],
                                        'status' => 'finished',
                                        'url_expiry' => now()->addDays(3),
                                    ]);

                                    // Redirect to download
                                    return redirect()->away($data['data']['modelUrl']);
                                } else {
                                    Notification::make()
                                        ->title('Zip File Not Ready')
                                        ->body('The 3D model is still being processed. Please try again in a few minutes.')
                                        ->warning()
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->title('Download Failed')
                                    ->body('Unable to check download status. Please try again later.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to check download status: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No 3D models generated yet')
            ->emptyStateDescription('Upload images in the Create 3D Models page to get started');
    }
}
