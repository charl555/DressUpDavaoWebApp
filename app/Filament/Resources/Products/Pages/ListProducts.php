<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add New Product'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Gowns' => Tab::make()
                ->label('Gowns')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('type', 'Gown')
                ),
            'Suits' => Tab::make()
                ->label('Suits')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('type', 'Suit')
                ),
        ];
    }
}
