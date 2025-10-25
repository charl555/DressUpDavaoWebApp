<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Tables;

use App\Models\Product3dModels;
use App\Models\Products;
use App\Models\Shops;
use App\Rules\ThreeDModelFileRule;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
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

class Attach3dModelToProductsTable
{
    public static function configure(Table $table): Table
    {
        $shop = Shops::where('user_id', Auth::id())->first();
        $hasAccess = $shop?->allow_3d_model_access ?? false;

        if (!$hasAccess) {
            return $table
                ->query(Products::query()->where('product_id', 0))  // Empty query
                ->columns([])  // No columns
                ->filters([])  // No filters
                ->actions([])  // No actions
                ->bulkActions([])  // No bulk actions
                ->headerActions([])  // No header actions
                ->paginated(false)  // Disable pagination
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
                    Action::make('View3DModel')
                        ->label('View 3D Model')
                        ->icon('heroicon-o-eye')
                        ->visible(fn(Products $record) => $record->product_3d_models !== null)
                        ->url(fn(Products $record) => route('view-3d-model', ['id' => $record->product_id])),
                    Action::make('attach3DModel')
                        ->label('Attach 3D Model')
                        ->icon('heroicon-o-cube')
                        ->modalHeading(fn(Products $record) => 'Attach 3D Model to ' . $record->name)
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
                                FileUpload::make('model_file')
                                    ->label('3D Model File')
                                    ->helperText(function () {
                                        $maxSizeMB = round(config('3d-models.max_file_size', 104857600) / 1024 / 1024);
                                        $extensions = implode(' or ', array_map(fn($ext) => ".$ext", config('3d-models.allowed_extensions', ['glb', 'gltf'])));
                                        return "Upload a 3D model file ({$extensions}). GLB format is recommended for web use. Maximum file size: {$maxSizeMB}MB.";
                                    })
                                    ->disk(config('3d-models.storage.disk', 'public'))
                                    ->visibility(config('3d-models.storage.visibility', 'public'))
                                    ->directory(config('3d-models.storage.directory', 'product-models'))
                                    ->visibility('public')
                                    ->required()
                                    ->appendFiles()
                                    ->maxSize(config('3d-models.max_file_size_kb', 102400))
                                    ->rules([
                                        'required',
                                        'file',
                                        new ThreeDModelFileRule(),
                                    ]),
                            ];
                        })
                        ->action(function (array $data, Products $record): void {
                            try {
                                $modelPath = $data['model_file'];

                                // Check if a 3D model already exists for this product
                                $existingModel = $record->product_3d_models;

                                if ($existingModel) {
                                    // Update existing model_path
                                    $existingModel->update(['model_path' => $modelPath]);
                                    $message = '3D model updated successfully!';
                                } else {
                                    // Create a new 3D model record
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
}
