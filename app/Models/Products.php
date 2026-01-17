<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'subtype',
        'description',
        'inclusions',
        'status',
        'colors',
        'fabric',
        'size',
        'rental_price',
        'rental_count',
        'maintenance_needed',
        'visibility',
    ];

    /**
     * Maintenance statuses that make a product unavailable for rental
     */
    public const MAINTENANCE_STATUSES = [
        'Pending Cleaning',
        'In Cleaning',
        'Steamed & Pressed',
        'Quality Check',
        'Needs Repair',
        'In Alteration',
        'Damaged – Not Rentable',
    ];

    /**
     * Check if the product has a date conflict with existing rentals or bookings
     *
     * @param string|Carbon $startDate The start date of the requested period
     * @param string|Carbon $endDate The end date of the requested period
     * @param int|null $excludeRentalId Rental ID to exclude from conflict check (for editing)
     * @param int|null $excludeBookingId Booking ID to exclude from conflict check (for editing)
     * @return bool True if there is a conflict, false if dates are available
     */
    public function hasDateConflict($startDate, $endDate, $excludeRentalId = null, $excludeBookingId = null): bool
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->startOfDay();

        // Check for rental conflicts
        // A conflict exists if the requested period overlaps with any active rental period
        $rentalConflict = $this
            ->rentals()
            ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
            ->when($excludeRentalId, fn($query) => $query->where('rental_id', '!=', $excludeRentalId))
            ->where(function ($query) use ($startDate, $endDate) {
                // Overlap condition: start1 <= end2 AND end1 >= start1
                $query
                    ->where('pickup_date', '<=', $endDate)
                    ->where('return_date', '>=', $startDate);
            })
            ->exists();

        if ($rentalConflict) {
            return true;
        }

        // Check for booking conflicts
        // A booking conflict exists if any active booking date falls within the requested period
        $bookingConflict = $this
            ->bookings()
            ->whereIn('status', ['Pending', 'On Going', 'Confirmed'])
            ->when($excludeBookingId, fn($query) => $query->where('booking_id', '!=', $excludeBookingId))
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->exists();

        return $bookingConflict;
    }

    /**
     * Check if the product is available for rental/booking on specific dates
     *
     * @param string|Carbon $startDate The start date of the requested period
     * @param string|Carbon $endDate The end date of the requested period
     * @param int|null $excludeRentalId Rental ID to exclude from conflict check
     * @param int|null $excludeBookingId Booking ID to exclude from conflict check
     * @return bool True if available, false if not
     */
    public function isAvailableForDates($startDate, $endDate, $excludeRentalId = null, $excludeBookingId = null): bool
    {
        // First check if product is in a maintenance status
        if (in_array($this->status, self::MAINTENANCE_STATUSES)) {
            return false;
        }

        // Then check for date conflicts
        return !$this->hasDateConflict($startDate, $endDate, $excludeRentalId, $excludeBookingId);
    }

    /**
     * Check if a specific single date is available
     *
     * @param string|Carbon $date The date to check
     * @param int|null $excludeRentalId Rental ID to exclude from conflict check
     * @param int|null $excludeBookingId Booking ID to exclude from conflict check
     * @return bool True if available, false if not
     */
    public function isDateAvailable($date, $excludeRentalId = null, $excludeBookingId = null): bool
    {
        return $this->isAvailableForDates($date, $date, $excludeRentalId, $excludeBookingId);
    }

    /**
     * Get the current dynamic status based on today's date and active rentals/bookings
     * This is used for display purposes to show what the product's current state is
     *
     * @return string The current status
     */
    public function getCurrentStatusAttribute(): string
    {
        // If product is in maintenance, return the maintenance status
        if (in_array($this->status, self::MAINTENANCE_STATUSES)) {
            return $this->status;
        }

        $today = Carbon::today();

        // Check if currently rented (today falls within an active rental period)
        $activeRental = $this
            ->rentals()
            ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
            ->where('pickup_date', '<=', $today)
            ->where('return_date', '>=', $today)
            ->first();

        if ($activeRental) {
            // Check if overdue
            if ($today->gt($activeRental->return_date) && !$activeRental->is_returned) {
                return 'Overdue';
            }
            return 'Rented';
        }

        // Check if reserved for today (has an active booking for today)
        $activeBooking = $this
            ->bookings()
            ->whereIn('status', ['Pending', 'On Going', 'Confirmed'])
            ->whereDate('booking_date', $today)
            ->exists();

        if ($activeBooking) {
            return 'Reserved';
        }

        // Check if there are any future rentals or bookings
        $hasFutureRentals = $this
            ->rentals()
            ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
            ->where('pickup_date', '>', $today)
            ->exists();

        $hasFutureBookings = $this
            ->bookings()
            ->whereIn('status', ['Pending', 'On Going', 'Confirmed'])
            ->where('booking_date', '>', $today)
            ->exists();

        if ($hasFutureRentals || $hasFutureBookings) {
            return 'Available';  // Available now but has future commitments
        }

        return 'Available';
    }

    /**
     * Check if product can be rented (not in maintenance status)
     *
     * @return bool
     */
    public function isRentable(): bool
    {
        return !in_array($this->status, self::MAINTENANCE_STATUSES);
    }

    /**
     * Get all unavailable date ranges for this product
     * Useful for calendar display
     *
     * @return array Array of date ranges with their status
     */
    public function getUnavailableDateRanges(): array
    {
        $unavailableDates = [];

        // Get rental periods
        $activeRentals = $this
            ->rentals()
            ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
            ->get();

        foreach ($activeRentals as $rental) {
            $start = Carbon::parse($rental->pickup_date);
            $end = Carbon::parse($rental->return_date);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $unavailableDates[$date->format('Y-m-d')] = 'rented';
            }
        }

        // Get booking dates
        $activeBookings = $this
            ->bookings()
            ->whereIn('status', ['Pending', 'On Going', 'Confirmed'])
            ->get();

        foreach ($activeBookings as $booking) {
            $bookingDate = Carbon::parse($booking->booking_date);
            $unavailableDates[$bookingDate->format('Y-m-d')] = 'reserved';
        }

        return $unavailableDates;
    }

    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'product_id', 'product_id')->orderBy('booking_date', 'desc');
    }

    public function kiriEngineJobs()
    {
        return $this->hasMany(KiriEngineJobs::class, 'product_id', 'product_id');
    }

    public function occasions()
    {
        return $this->hasMany(Occasions::class, 'product_id', 'product_id');
    }

    public function events()
    {
        return $this->hasMany(ProductEvents::class, 'product_id', 'product_id');
    }

    public function product_measurements()
    {
        return $this->hasOne(ProductMeasurements::class, 'product_id', 'product_id');
    }

    public function product_images()
    {
        return $this->hasMany(ProductImages::class, 'product_id', 'product_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rentals::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product_3d_models()
    {
        return $this->hasOne(Product3dModels::class, 'product_id', 'product_id');
    }

    // Change this to belongsToMany for many-to-many relationship
    public function favoritedBy()
    {
        return $this
            ->belongsToMany(User::class, 'favorites', 'product_id', 'user_id')
            ->withTimestamps()
            ->using(Favorites::class);
    }

    public function getIsFavoritedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        // Use direct query to avoid ambiguous column issues
        return \App\Models\Favorites::where('user_id', auth()->id())
            ->where('product_id', $this->product_id)
            ->exists();
    }

    public function getFavoritesCountAttribute(): int
    {
        // Use direct query to avoid ambiguous column issues
        return \App\Models\Favorites::where('product_id', $this->product_id)->count();
    }
}
