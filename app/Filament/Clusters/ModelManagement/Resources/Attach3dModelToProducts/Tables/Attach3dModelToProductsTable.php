<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Tables;

use App\Models\KiriEngineJobs;
use App\Models\Product3dModels;
use App\Models\Products;
use App\Models\Shops;
use App\Models\Stored3dModels;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class Attach3dModelToProductsTable
{
    public static function configure(Table $table): Table
    {
        $shop = Shops::where('user_id', Auth::id())->first();
        $hasAccess = $shop?->allow_3d_model_access ?? false;

        if (!$hasAccess) {
            return $table
                ->query(Products::query()->where('product_id', 0))
                ->columns([])
                ->filters([])
                ->actions([])
                ->bulkActions([])
                ->headerActions([])
                ->paginated(false)
                ->emptyStateHeading('Access Denied')
                ->emptyStateDescription('Your account currently does not have access to 3D model features. Please contact support to enable this functionality.')
                ->emptyStateIcon('heroicon-o-lock-closed');
        }

        return $table
            ->columns([
                ImageColumn::make('product_images.thumbnail_image')
                    ->label('Image')
                    ->disk('public'),
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtype')
                    ->label('Subtype')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('product_3d_models.model_path')
                    ->label('Attached 3D Model')
                    ->icon(fn($state) => filled($state) ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state) => filled($state) ? 'success' : 'danger')
                    ->size('lg'),
            ])
            ->defaultSort('has_3d_model', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $query->withCount([
                    'product_3d_models as has_3d_model' => function ($query) {
                        $query->whereNotNull('model_path');
                    }
                ]);
            })
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                SelectFilter::make('subtype')
                    ->options([
                        'Ball Gown' => 'Ball Gown',
                        'A-Line' => 'A-Line',
                        'Mermaid' => 'Mermaid',
                        'Cocktail' => 'Cocktail',
                        'Business Suit' => 'Business Suit',
                        'Formal Suit' => 'Formal Suit',
                    ])
                    ->label('Product Subtype')
                    ->placeholder('All Subtypes'),
                Filter::make('has_3d_model')
                    ->query(fn(Builder $query): Builder => $query->has('product_3d_models'))
                    ->toggle()
                    ->label('Has 3D Model'),
                Filter::make('missing_3d_model')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('product_3d_models'))
                    ->toggle()
                    ->label('Missing 3D Model'),
            ])
            ->actions([
                ActionGroup::make([
                    // UPDATED: Create 3D Model Action - Now opens modal instead of redirecting
                    Action::make('create3DModel')
                        ->label('Create 3D Model')
                        ->icon('heroicon-o-photo')
                        ->color('primary')
                        ->visible(fn(Products $record) => !$record->product_3d_models)
                        ->modalHeading(fn(Products $record) => 'Create 3D Model for ' . $record->name)
                        ->modalDescription(fn(Products $record) => 'Upload 20-100 high-quality images from all angles. The 3D model will be automatically generated and attached to this product when ready.')
                        ->form(function (Products $record): array {
                            return [
                                Fieldset::make('Product Details')
                                    ->components([
                                        TextInput::make('name')
                                            ->default($record->name)
                                            ->disabled(),
                                        TextInput::make('type')
                                            ->default($record->type)
                                            ->disabled(),
                                        TextInput::make('subtype')
                                            ->default($record->subtype)
                                            ->disabled(),
                                    ])
                                    ->columns(3),
                                Hidden::make('product_id')
                                    ->default($record->product_id),
                                FileUpload::make('images')
                                    ->label('Upload Images')
                                    ->helperText('Upload 20-100 high-quality images from all angles. Typical completion times: 40 images (~1-1.5 min), 100 images (~2-4 min). For optimal speed, use 40-80 images at 500kb-1mb each.')
                                    ->multiple()
                                    ->maxParallelUploads(20)
                                    ->image()
                                    ->required()
                                    ->minFiles(20)
                                    ->maxFiles(100)
                                    ->maxSize(10240)
                                    ->disk('public')
                                    ->directory('3d-model-images')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->columnSpanFull(),
                            ];
                        })
                        ->action(function (array $data, Products $record): void {
                            self::handle3DModelCreation($data, $record);
                        })
                        ->modalSubmitActionLabel('Start 3D Model Generation')
                        ->modalCancelActionLabel('Cancel'),
                    // Action::make('View3DModel')
                    //     ->label('View 3D Model')
                    //     ->icon('heroicon-o-eye')
                    //     ->visible(fn(Products $record) => $record->product_3d_models !== null)
                    //     ->url(fn(Products $record) => route('view-3d-model', ['id' => $record->product_id])),
                    Action::make('attach3DModel')
                        ->visible(fn(Products $record) => !$record->product_3d_models)
                        ->label('Attach Existing 3D Model')
                        ->icon('heroicon-o-cube')
                        ->modalHeading(fn(Products $record) => 'Attach 3D Model to ' . $record->name)
                        ->form(function (Products $record): array {
                            $userModels = Stored3dModels::where('user_id', Auth::id())
                                ->get()
                                ->mapWithKeys(function ($model) {
                                    return [$model->stored_3d_model_id => $model->model_name . ' (' . $model->original_filename . ')'];
                                })
                                ->toArray();

                            return [
                                Fieldset::make('Product Details')
                                    ->components([
                                        TextInput::make('name')
                                            ->default($record->name)
                                            ->disabled(),
                                        TextInput::make('type')
                                            ->default($record->type)
                                            ->disabled(),
                                        TextInput::make('subtype')
                                            ->default($record->subtype)
                                            ->disabled(),
                                    ])
                                    ->columns(3),
                                Select::make('stored_model_id')
                                    ->label('Select 3D Model')
                                    ->options($userModels)
                                    ->required()
                                    ->searchable()
                                    ->helperText('Select a 3D model from your stored models collection.')
                                    ->placeholder('Choose a 3D model...'),
                            ];
                        })
                        ->action(function (array $data, Products $record): void {
                            try {
                                $storedModelId = $data['stored_model_id'];
                                $storedModel = Stored3dModels::find($storedModelId);

                                if (!$storedModel) {
                                    throw new \Exception('Selected 3D model not found.');
                                }

                                $modelPath = $storedModel->model_path;
                                $existingModel = $record->product_3d_models;

                                if ($existingModel) {
                                    $existingModel->update(['model_path' => $modelPath]);
                                    $message = '3D model updated successfully!';
                                } else {
                                    Product3dModels::create([
                                        'product_id' => $record->product_id,
                                        'model_path' => $modelPath,
                                    ]);
                                    $message = '3D model attached successfully!';
                                }

                                Notification::make()
                                    ->title($message)
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Failed to attach 3D model')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                                \Log::error('Failed to attach 3D model: ' . $e->getMessage());
                            }
                        })
                        ->modalSubmitActionLabel('Attach Model')
                        ->modalCancelActionLabel('Cancel'),
                    Action::make('remove3DModel')
                        ->visible(fn(Products $record) => $record->product_3d_models)
                        ->label('Remove 3D Model')
                        ->icon('heroicon-o-trash')
                        ->color(color: 'danger')
                        ->requiresConfirmation()
                        ->modalHeading(fn(Products $record) => 'Remove 3D Model from ' . $record->name)
                        ->modalDescription('Are you sure you want to remove the 3D model from this product? This will only remove the association, not the actual model file.')
                        ->action(function (Products $record): void {
                            try {
                                $existingModel = $record->product_3d_models;

                                if ($existingModel) {
                                    $existingModel->delete();
                                    Notification::make()
                                        ->title('3D model removed successfully!')
                                        ->success()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Failed to remove 3D model')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                                \Log::error('Failed to remove 3D model: ' . $e->getMessage());
                            }
                        }),
                ])
                    ->label('Manage')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this Product'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Handle 3D model creation from modal form
     */
    private static function handle3DModelCreation(array $data, Products $record): void
    {
        try {
            $productId = $data['product_id'];
            $images = $data['images'];

            $apiKey = config('services.kiri.key');

            if (empty($apiKey)) {
                throw new \Exception('API configuration error. Please contact support.');
            }

            // Create job record with product_id
            $job = KiriEngineJobs::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'serialize_id' => 'temp_' . uniqid(),
                'status' => 'uploading',
                'kiri_options' => [],
                'is_downloaded' => false,
                'url_expiry' => Carbon::now()->addDays(7),
            ]);

            Log::info('KiriEngine job created from modal', [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'image_count' => count($images)
            ]);

            // Prepare images for direct API upload
            $multipart = [];
            $uploadedFiles = [];

            // Process each uploaded image
            foreach ($images as $storedImagePath) {
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

            Log::info('Sending request to Kiri Engine API from modal', [
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

            Log::info('Kiri Engine API response from modal', [
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

                    Log::info('Kiri Engine job created successfully from modal', [
                        'job_id' => $job->kiri_engine_job_id,
                        'product_id' => $productId,
                        'serialize_id' => $kiriSerializeId
                    ]);

                    // Show success notification
                    Notification::make()
                        ->title('3D Model Generation Started')
                        ->body("3D model generation started for '{$record->name}'. It will be automatically attached when ready. You can check the progress in the 'Download 3D Models' page.")
                        ->success()
                        ->send();
                } else {
                    $errorMsg = $responseBody['msg'] ?? 'No serialize ID in response';
                    $job->update([
                        'status' => 'failed',
                        'error_message' => $errorMsg,
                        'notes' => 'API response error'
                    ]);
                    throw new \Exception("Upload failed: {$errorMsg}");
                }
            } else {
                $errorBody = $response->json();
                $errorMsg = $errorBody['msg'] ?? "Upload failed with status: {$response->status()}";
                $job->update([
                    'status' => 'failed',
                    'error_message' => $errorMsg,
                    'notes' => 'API request failed'
                ]);
                throw new \Exception("Upload failed: {$errorMsg}");
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
            Log::error('3D model creation failed from modal', [
                'product_id' => $record->product_id,
                'product_name' => $record->name,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('3D Model Creation Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
