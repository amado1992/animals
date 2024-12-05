<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id', 'name', 'city', 'country_id', 'lat', 'long', 'icao_code', 'is_default',
    ];

    /**
     * The country that belong to the airport.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
