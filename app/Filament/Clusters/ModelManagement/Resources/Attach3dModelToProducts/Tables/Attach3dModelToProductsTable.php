<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Tables;

use App\Models\Product3dModels;
use App\Models\Products;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class Attach3dModelToProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('ProductImages.image_path')
                    ->label('Image')
                    ->height(150)
                    ->width(120)
                    ->limit(1),
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
                TextColumn::make('Product3dModels.model_path')
                    ->label('Attached 3D Model')
                    ->formatStateUsing(function (?string $state): string {
                        return $state ? 'Yes' : 'No';
                    })
                    ->badge()
                    ->color(function (?string $state): string {
                        return $state ? 'success' : 'danger';
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
                                ->label('Upload 3D Model File (.glb, .gltf, .obj)')
                                ->acceptedFileTypes(['model/gltf-binary', 'model/gltf+json', 'model/obj'])
                                ->disk('public')
                                ->directory('product-models')
                                ->required()
                                ->appendFiles()
                                ->preserveFileNames()
                                ->maxSize(512000),
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
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
