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
                ->query(KiriEngineJobs::query()->where('kiri_engine_job_id', 0))  // Empty query
                ->columns([])
                ->emptyStateHeading('Access Denied')
                ->emptyStateDescription('Your account currently does not have access to 3D model features. Please contact support to enable this functionality.')
                ->actions([]);  // Remove all actions
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
                TextColumn::make('url_expiry')
                    ->label('Link Expiry Date')
                    ->date('M j, Y g:i A')
                    ->color(fn($state) => $this->isLinkExpired($state) ? 'danger' : 'success')
                    ->tooltip(function ($state) {
                        if ($this->isLinkExpired($state)) {
                            return 'Download link has expired. Please click "Get Download Link" to regenerate.';
                        }
                        return null;
                    }),
                TextColumn::make('model_url')
                    ->label('Download Status')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state)
                            return 'Not Available';

                        if ($this->isLinkExpired($record->url_expiry) || $this->isLinkActuallyExpired($record)) {
                            return 'Expired';
                        }

                        return 'Ready';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if (!$state)
                            return 'gray';

                        if ($this->isLinkExpired($record->url_expiry) || $this->isLinkActuallyExpired($record)) {
                            return 'danger';
                        }

                        return 'success';
                    }),
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function ($record) {
                        // Only show Download button when:
                        // - Status is finished
                        // - Model URL exists
                        // - Link is NOT expired
                        return $record->status === 'finished' &&
                            $record->model_url &&
                            !$this->isLinkExpired($record->url_expiry) &&
                            !$this->isLinkActuallyExpired($record);
                    })
                    ->action(function ($record) {
                        return redirect()->away($record->model_url);
                    }),
                Action::make('fetchDownloadLink')
                    ->label('Get Download Link')
                    ->icon('heroicon-o-link')
                    ->visible(function ($record) {
                        // Show Get Download Link button when:
                        // - Still processing
                        // - No model URL yet
                        // - Link is expired (based on either check)
                        return $record->status === 'processing' ||
                            ($record->status === 'finished' && !$record->model_url) ||
                            ($record->status === 'finished' && $this->isLinkExpired($record->url_expiry)) ||
                            ($record->status === 'finished' && $this->isLinkActuallyExpired($record));
                    })
                    ->action(function ($record) {
                        try {
                            $apiKey = config('services.kiri.key');
                            $response = Http::withToken($apiKey)
                                ->timeout(60)
                                ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$record->serialize_id}");

                            if ($response->successful() && isset($response['data']['modelUrl'])) {
                                // Calculate new expiry date (3 days from now)
                                $newExpiry = now()->addDays(3);

                                $record->update([
                                    'model_url' => $response['data']['modelUrl'],
                                    'url_expiry' => $newExpiry,
                                    'status' => 'finished',
                                    'is_downloaded' => true,
                                ]);

                                Notification::make()
                                    ->title('Download Link Generated')
                                    ->body('Download link generated successfully! Click the Download button to get your file.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Download Not Ready')
                                    ->body('Download not ready yet. Please try again later.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Error fetching download link: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No 3D models generated yet')
            ->emptyStateDescription('Upload images in the Create 3D Models page to get started');
    }

    /**
     * Check if a download link has expired based on database expiry date
     */
    private function isLinkExpired(?string $expiryDate): bool
    {
        if (!$expiryDate) {
            return true;
        }

        try {
            $expiryTime = \Carbon\Carbon::parse($expiryDate);
            return now()->greaterThan($expiryTime);
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Check if link is actually expired by testing the URL or checking creation time
     * This handles cases where the database expiry date is incorrect
     */
    private function isLinkActuallyExpired(KiriEngineJobs $record): bool
    {
        // If the record was created more than 3 days ago, consider it expired
        // KiriEngine links typically expire after 3 days regardless of the stored expiry date
        if ($record->created_at && $record->created_at->diffInDays(now()) > 3) {
            return true;
        }

        return false;
    }
}
