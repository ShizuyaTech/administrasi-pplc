<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    protected $fillable = [
        'section_id',
        'name',
        'unit',
        'current_stock',
        'minimum_stock',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock()
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
