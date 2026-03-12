<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function businessTrips()
    {
        return $this->hasMany(BusinessTrip::class);
    }

    public function consumables()
    {
        return $this->hasMany(Consumable::class);
    }
}
