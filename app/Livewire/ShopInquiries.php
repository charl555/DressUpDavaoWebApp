<?php

namespace App\Livewire;

use App\Models\Products;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ShopInquiries extends Component implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'shop-inquiries';

    protected string $view = 'livewire.shop-inquiries';

    protected static ?string $title = 'Shop Inquiries';

    public function getTableQuery(): Builder
    {
        return Products::query()->where('user_id', auth()->id());
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render()
    {
        return view('livewire.shop-inquiries');
    }
}
