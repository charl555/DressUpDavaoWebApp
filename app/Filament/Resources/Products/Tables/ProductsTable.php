<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product_images.thumbnail_image')
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        if ($record->product_images->isEmpty()) {
                            return null;
                        }

                        $firstImage = $record->product_images->first();

                        if (!$firstImage->thumbnail_image) {
                            return null;
                        }

                        return asset('uploads/' . $firstImage->thumbnail_image);
                    }),
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtype')
                    ->label('Style')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('events.event_name')
                    ->searchable()
                    ->label('Events')
                    ->wrap(),
                TextColumn::make('current_status')
                    ->label('Status')
                    ->getStateUsing(fn($record) => $record->current_status)
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Available' => 'success',
                        'Rented' => 'warning',
                        'Reserved' => 'info',
                        'Overdue' => 'danger',
                        'Pending Cleaning' => 'warning',
                        'In Cleaning' => 'warning',
                        'Steamed & Pressed' => 'warning',
                        'Quality Check' => 'warning',
                        'Needs Repair' => 'danger',
                        'In Alteration' => 'warning',
                        'Damaged – Not Rentable' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('colors'),
                TextColumn::make('size'),
                TextColumn::make('rental_price')
                    ->label('Rental Price')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Available' => 'Available',
                        'Pending Cleaning' => 'Pending Cleaning',
                        'In Cleaning' => 'In Cleaning',
                        'Steamed & Pressed' => 'Steamed & Pressed',
                        'Quality Check' => 'Quality Check',
                        'Needs Repair' => 'Needs Repair',
                        'In Alteration' => 'In Alteration',
                        'Damaged – Not Rentable' => 'Damaged – Not Rentable',
                    ])
                    ->label('Base Status')
                    ->placeholder('All Statuses'),
                SelectFilter::make('type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                SelectFilter::make('subtype')
                    ->label('Style')
                    ->placeholder('All Styles'),
                SelectFilter::make('size')
                    ->options([
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
                        'XLarge' => 'XLarge',
                        'XXLarge' => 'XXLarge',
                    ])
                    ->label('Size')
                    ->placeholder('All Sizes'),
                SelectFilter::make('visibility')
                    ->options([
                        'Yes' => 'Visible in Shop',
                        'No' => 'Hidden from Shop',
                    ])
                    ->label('Shop Visibility')
                    ->placeholder('All'),
                SelectFilter::make('maintenance_needed')
                    ->options([
                        'Yes' => 'Needs Maintenance',
                        'No' => 'No Maintenance Needed',
                    ])
                    ->label('Maintenance Status')
                    ->placeholder('All'),
                Filter::make('rental_price_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('price_from')
                            ->numeric()
                            ->placeholder('Min Price')
                            ->prefix('₱'),
                        \Filament\Forms\Components\TextInput::make('price_to')
                            ->numeric()
                            ->placeholder('Max Price')
                            ->prefix('₱'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn(Builder $query, $price): Builder => $query->where('rental_price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn(Builder $query, $price): Builder => $query->where('rental_price', '<=', $price),
                            );
                    })
                    ->label('Price Range'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('productStatus')
                        ->label('Set Status')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('gray')
                        ->tooltip('Set product availability or maintenance status. Rented/Reserved status is determined automatically based on active rentals and bookings.')
                        ->form([
                            Select::make('status')
                                ->label('Product Status')
                                ->options([
                                    'Available' => 'Available',
                                    'Pending Cleaning' => 'Pending Cleaning',
                                    'In Cleaning' => 'In Cleaning',
                                    'Steamed & Pressed' => 'Steamed & Pressed',
                                    'Quality Check' => 'Quality Check',
                                    'Needs Repair' => 'Needs Repair',
                                    'In Alteration' => 'In Alteration',
                                    'Damaged – Not Rentable' => 'Damaged – Not Rentable',
                                ])
                                ->required()
                                ->native(false)
                                ->helperText('Note: Rented/Reserved status is determined automatically based on active rentals and bookings.'),
                        ])
                        ->requiresConfirmation()
                        ->action(function (array $data, $record): void {
                            $record->update([
                                'status' => $data['status'],
                            ]);

                            Notification::make()
                                ->title('Status updated')
                                ->body("The product has been set to {$data['status']}.")
                                ->success()
                                ->send();
                        })
                        ->after(function ($record, $data, $livewire) {
                            $livewire->dispatch('refresh');
                        })
                        ->modalHeading('Update Status')
                        ->modalButton('Save Status')
                        ->modalWidth('xl'),
                    EditAction::make()
                        ->label('Edit Product Details')
                        ->color('gray'),
                    Action::make('editProductImages')
                        ->icon('heroicon-o-photo')
                        ->label('Edit Images')
                        ->color('gray')
                        ->url(function ($record) {
                            // Find the product images record for this product
                            $productImage = $record->product_images()->first();
                            if (!$productImage) {
                                return null;  // Will disable the action
                            }
                            return route('filament.admin.resources.product-images.edit', $productImage->product_image_id);
                        })
                        ->disabled(fn($record) => !$record->product_images()->exists())
                        ->tooltip(fn($record) => !$record->product_images()->exists()
                            ? 'No images found for this product. Create images first.'
                            : 'Edit product images'),
                    Action::make('editProductMeasurements')
                        ->icon('heroicon-o-clipboard')
                        ->label('Edit Measurements')
                        ->color('gray')
                        ->url(function ($record) {
                            // Find the product measurements record for this product
                            $productMeasurement = $record->product_measurements;
                            if (!$productMeasurement) {
                                return null;  // Will disable the action
                            }
                            return route('filament.admin.resources.product-measurements.edit', $productMeasurement->product_measurements_id);
                        })
                        ->disabled(fn($record) => !$record->product_measurements)
                        ->tooltip(fn($record) => !$record->product_measurements
                            ? 'No measurements found for this product. Create measurements first.'
                            : 'Edit product measurements'),
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
