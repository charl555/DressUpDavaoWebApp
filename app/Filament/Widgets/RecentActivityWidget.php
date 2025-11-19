<?php

namespace App\Filament\Widgets;

use App\Models\KiriEngineJobs;
use App\Models\Products;
use App\Models\Rentals;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    protected function getTableHeading(): string
    {
        return 'Recent Rentals';
    }

    protected function getTableDescription(): string
    {
        return 'Latest rental activities and bookings';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rented_by')
                    ->label('Rented By')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->name;
                        } elseif ($record->customer) {
                            return $record->customer->first_name . ' ' . $record->customer->last_name;
                        }
                        return 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('customer', function ($q) use ($search) {
                            $q
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('rental_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'On Going' => 'warning',
                        'Returned' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('pickup_date')
                    ->label('Pickup Date')
                    ->date('M j, Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25]);
    }

    protected function getTableQuery(): Builder
    {
        $userId = Auth::id();

        // For simplicity, let's just show recent rentals for now
        // This avoids the complex union query that was causing issues
        return Rentals::whereHas('product', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['product', 'customer'])
            ->latest()
            ->take(15);
    }
}
