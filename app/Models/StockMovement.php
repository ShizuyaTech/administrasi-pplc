<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'consumable_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
        'created_by',
    ];

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
