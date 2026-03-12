<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTrip extends Model
{
    protected $fillable = [
        'letter_number',
        'section_id',
        'employee_name',
        'destination',
        'departure_date',
        'return_date',
        'purpose',
        'transport',
        'estimated_cost',
        'status',
        'attachment',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cardUsages()
    {
        return $this->hasMany(CardUsage::class);
    }
}
