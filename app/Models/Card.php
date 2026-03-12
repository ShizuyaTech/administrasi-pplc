<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'card_type',
        'current_balance',
        'status',
        'section_id',
        'notes',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    /**
     * Get the section that owns the card
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the card usages
     */
    public function cardUsages()
    {
        return $this->hasMany(CardUsage::class);
    }

    /**
     * Scope for active cards
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for cards with sufficient balance
     */
    public function scopeWithBalance($query, $minBalance = 0)
    {
        return $query->where('current_balance', '>=', $minBalance);
    }

    /**
     * Get formatted card type
     */
    public function getCardTypeNameAttribute()
    {
        return match($this->card_type) {
            'flazz' => 'Flazz (BCA)',
            'brizzi' => 'Brizzi (BRI)',
            'e-toll' => 'E-Toll (Mandiri)',
            'other' => 'Lainnya',
            default => $this->card_type,
        };
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }

    /**
     * Check if card has sufficient balance
     */
    public function hasSufficientBalance($amount)
    {
        return $this->current_balance >= $amount;
    }

    /**
     * Update balance after usage
     */
    public function updateBalance($usageAmount)
    {
        $this->current_balance -= $usageAmount;
        
        // Set to inactive if balance is too low
        if ($this->current_balance < 50000) { // Threshold 50k
            $this->status = 'inactive';
        }
        
        $this->save();
    }

    /**
     * Top up card balance
     */
    public function topUp($amount)
    {
        $this->current_balance += $amount;
        $this->status = 'active'; // Reactivate if topped up
        $this->save();
    }
}
