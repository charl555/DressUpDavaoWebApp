<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\ProductImages\ProductImagesResource;
use App\Filament\Resources\ProductMeasurements\ProductMeasurementsResource;
use App\Models\ProductImages;
use App\Models\ProductMeasurements;
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
use Filament\Tables\Enums\RecordActionsPosition;
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
                    ->disk('public'),
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtype')
                    ->label('Style')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('occasions.occasion_name')
                    ->searchable()
                    ->label('Events')
                    ->wrap(),
                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Available' => 'success',
                        'Rented' => 'warning',
                        'Reserved' => 'info',
                        'Maintenance' => 'danger',
                        'Pending Cleaning' => 'warning',
                        'In Cleaning' => 'warning',
                        'Steamed & Pressed' => 'warning',
                        'Quality Check' => 'warning',
                        'Needs Repair' => 'danger',
                        'In Alteration' => 'warning',
                        'Damaged – Not Rentable' => 'danger',
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
                        'Rented' => 'Rented',
                        'Reserved' => 'Reserved',
                        'Maintenance' => 'Maintenance',
                    ])
                    ->label('Status')
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
                        ->disabled(fn($record) => $record->status === 'Rented')  // 🔒 disable if rented
                        ->tooltip(fn($record) => $record->status === 'Rented'
                            ? 'This product is currently rented and cannot be modified.'
                            : 'Set or update the current product status.')
                        ->form([
                            Select::make('status')
                                ->label('Product Status')
                                ->options([
                                    'Available' => 'Available',
                                    'Reserved' => 'Reserved',
                                    'Pending Cleaning' => 'Pending Cleaning',
                                    'In Cleaning' => 'In Cleaning',
                                    'Steamed & Pressed' => 'Steamed & Pressed',
                                    'Quality Check' => 'Quality Check',
                                    'Needs Repair' => 'Needs Repair',
                                    'In Alteration' => 'In Alteration',
                                    'Damaged – Not Rentable' => 'Damaged – Not Rentable',
                                ])
                                ->required()
                                ->native(false),
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
                        ->url(fn($record) => route('filament.admin.resources.product-images.edit', $record)),
                    Action::make('editProductMeasurements')
                        ->icon('heroicon-o-clipboard')
                        ->label('Edit Measurements')
                        ->color('gray')
                        ->url(fn($record) => route('filament.admin.resources.product-measurements.edit', $record)),
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
