<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_trip_id',
        'card_id',
        'initial_balance',
        'usage_amount',
        'final_balance',
        'usage_notes',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'usage_amount' => 'decimal:2',
        'final_balance' => 'decimal:2',
    ];

    /**
     * Get the business trip
     */
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    /**
     * Get the card
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Calculate and set final balance
     */
    public function calculateFinalBalance()
    {
        $this->final_balance = $this->initial_balance - $this->usage_amount;
        return $this->final_balance;
    }

    /**
     * Get formatted amounts
     */
    public function getFormattedInitialBalanceAttribute()
    {
        return 'Rp ' . number_format($this->initial_balance, 0, ',', '.');
    }

    public function getFormattedUsageAmountAttribute()
    {
        return 'Rp ' . number_format($this->usage_amount, 0, ',', '.');
    }

    public function getFormattedFinalBalanceAttribute()
    {
        return 'Rp ' . number_format($this->final_balance, 0, ',', '.');
    }

    /**
     * Boot method to auto-calculate final balance
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cardUsage) {
            // Auto calculate final balance before saving
            $cardUsage->calculateFinalBalance();
        });

        static::created(function ($cardUsage) {
            // Update card's current balance after usage is recorded
            $cardUsage->card->updateBalance($cardUsage->usage_amount);
        });
    }
}
