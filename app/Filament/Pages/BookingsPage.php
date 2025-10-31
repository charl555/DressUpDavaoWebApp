<?php

namespace App\Filament\Pages;

use App\Models\Bookings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class BookingsPage extends Page implements HasTable, hasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected string $view = 'filament.pages.bookings-page';

    protected static ?string $navigationLabel = 'Bookings & Reservations';

    protected static string|UnitEnum|null $navigationGroup = 'Rental Management';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function getTableQuery(): Builder
    {
        return Bookings::query()
            ->whereHas('product', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['user', 'product.product_images']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product.firstProductImage')
                    ->label('Product Image')
                    ->disk('public')
                    ->getStateUsing(function ($record) {
                        // Get the first product image's thumbnail
                        return $record->product->product_images->first()?->thumbnail_image;
                    })
                    ->defaultImageUrl(function ($record) {
                        // Fallback to first product image if thumbnail doesn't exist
                        return $record->product->product_images->first()?->image_path;
                    }),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('booking_date')
                    ->label('Booking Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'On Going' => 'info',
                        'Confirmed' => 'success',
                        'Cancelled' => 'gray',
                        'Completed' => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('exclude_cancelled')
                    ->label('Exclude Cancelled Bookings')
                    ->default()
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Cancelled'))
                    ->toggle()
                    ->indicateUsing(function () {
                        return 'Excluding cancelled bookings';
                    }),
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'On Going' => 'On Going',
                        'Confirmed' => 'Confirmed',
                        'Cancelled' => 'Cancelled',
                        'Completed' => 'Completed',
                    ])
                    ->label('Booking Status')
                    ->placeholder('All Statuses'),
                Filter::make('booking_date')
                    ->form([
                        DatePicker::make('booking_from')
                            ->label('Booking From')
                            ->placeholder('Select start date'),
                        DatePicker::make('booking_until')
                            ->label('Booking Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['booking_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['booking_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    })
                    ->label('Booking Date Range'),
                Filter::make('exclude_cancelled')
                    ->label('Exclude Cancelled Bookings')
                    ->default()
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Cancelled'))
                    ->toggle()
                    ->indicateUsing(function () {
                        return 'Excluding cancelled bookings';
                    }),
                Filter::make('exclude_cancelled')
                    ->label('Exclude Cancelled Bookings')
                    ->default()
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Cancelled'))
                    ->toggle()
                    ->indicateUsing(function () {
                        return 'Excluding cancelled bookings';
                    }),
                Filter::make('exclude_completed')
                    ->label('Exclude Completed Bookings')
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Completed'))
                    ->toggle(),
                Filter::make('upcoming_bookings')
                    ->query(fn(Builder $query): Builder => $query->where('booking_date', '>=', now()))
                    ->toggle()
                    ->label('Upcoming Bookings Only'),
                Filter::make('past_bookings')
                    ->query(fn(Builder $query): Builder => $query->where('booking_date', '<', now()))
                    ->toggle()
                    ->label('Past Bookings Only'),
                Filter::make('today_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereDate('booking_date', now()))
                    ->toggle()
                    ->label("Today's Bookings Only"),
                Filter::make('tomorrow_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereDate('booking_date', now()->addDay()))
                    ->toggle()
                    ->label("Tomorrow's Bookings Only"),
                Filter::make('yesterday_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereDate('booking_date', now()->subDay()))
                    ->toggle()
                    ->label("Yesterday's Bookings Only"),
                Filter::make('this_week_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle()
                    ->label("This Week's Bookings Only"),
            ])
            ->actions([
                ActionGroup::make([
                    // 👁 View Action — visible only when record is Completed or Cancelled
                    ViewAction::make('viewBooking')
                        ->visible(fn($record) => in_array($record->status, ['Completed', 'Cancelled']))
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Booking Details')
                        ->modalDescription('Review the details of this booking.')
                        ->form(function (Bookings $record) {
                            return [
                                Section::make('Booking Information')
                                    ->schema([
                                        TextInput::make('user.name')
                                            ->label('Customer')
                                            ->disabled(),
                                        TextInput::make('product.name')
                                            ->label('Product')
                                            ->disabled(),
                                        TextInput::make('status')
                                            ->label('Status')
                                            ->disabled(),
                                        DatePicker::make('booking_date')
                                            ->label('Booking Date')
                                            ->disabled(),
                                    ])
                                    ->columns(2),
                            ];
                        }),
                    // ✏️ Edit Action — hidden when Completed or Cancelled
                    EditAction::make('editBooking')
                        ->visible(fn($record) => !in_array($record->status, ['Completed', 'Cancelled']))
                        ->label('Edit Booking')
                        ->icon('heroicon-o-pencil-square')
                        ->modalHeading('Edit Booking')
                        ->modalDescription('Modify the booking date or details below.')
                        ->form(function (Bookings $record) {
                            return [
                                Section::make('Booking Information')
                                    ->schema([
                                        TextInput::make('user.name')
                                            ->label('Customer')
                                            ->disabled(),
                                        TextInput::make('product.name')
                                            ->label('Product')
                                            ->disabled(),
                                        TextInput::make('status')
                                            ->label('Status')
                                            ->disabled(),
                                        DatePicker::make('booking_date')
                                            ->label('Booking Date')
                                            ->required()
                                            ->minDate(now()->addDay())
                                            ->native(false)
                                            ->displayFormat('F j, Y'),
                                    ])
                                    ->columns(2),
                            ];
                        }),
                    // ✅ Complete Booking — only visible if today == booking_date and not completed/cancelled
                    Action::make('completeBooking')
                        ->hidden(fn($record) => in_array($record->status, ['Completed', 'Cancelled']))
                        ->visible(fn($record) =>
                            $record->booking_date->isSameDay(now()) ||
                            $record->booking_date->isPast())
                        ->label('Complete Booking')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Complete Booking')
                        ->modalDescription('Are you sure you want to complete this booking?')
                        ->action(function ($record) {
                            $record->update(['status' => 'Completed']);

                            if ($record->product) {
                                $record->product->update(['status' => 'Available']);
                            }

                            // Send Filament notification
                            Notification::make()
                                ->success()
                                ->title('Booking Completed')
                                ->body('The booking has been successfully marked as completed.')
                                ->send();
                        }),
                    // ⚠️ Cancel Booking — hidden when Completed or Cancelled
                    Action::make('cancelBooking')
                        ->hidden(fn($record) => in_array($record->status, ['Completed', 'Cancelled']))
                        ->label('Cancel Booking')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Booking')
                        ->modalDescription('Are you sure you want to cancel this booking? This action cannot be undone.')
                        ->action(function ($record) {
                            $record->update(['status' => 'Cancelled']);

                            if ($record->product) {
                                $record->product->update(['status' => 'Available']);
                            }
                            Notification::make()
                                ->success()
                                ->title('Booking Cancelled')
                                ->body('The booking has been successfully cancelled.')
                                ->send();
                        }),
                    Action::make('createRental')
                        ->visible(fn($record) => $record->status === 'Completed')
                        ->label('Create Rental')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->url(fn($record) => route('filament.admin.resources.rentals.create', ['booking_id' => $record->booking_id])),
                ])
                    ->label('Manage')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this booking'),
            ]);
    }
}
