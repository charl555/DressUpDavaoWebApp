<?php

namespace App\Models;

use App\Services\RentalBusinessRules;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Rentals extends Model
{
    use HasFactory;

    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';

    protected $fillable = [
        'product_id',
        'customer_id',
        'user_id',
        'pickup_date',
        'event_date',
        'return_date',
        'rental_status',
        'rental_price',
        'deposit_amount',
        'balance_due',
        'condition_notes',
        'actual_return_date',
        'penalty_amount',
        'is_returned',
        'overdue_notification_sent_at',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'event_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'is_returned' => 'boolean',
        'rental_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'overdue_notification_sent_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'rental_id', 'rental_id');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->rental_price - $this
            ->payments()
            ->where('payment_type', 'rental')
            ->sum('amount_paid'));
    }

    public function getStatusAttribute()
    {
        if (!$this->is_returned && now()->gt($this->return_date)) {
            return 'Overdue';
        }

        return $this->rental_status;
    }
}
