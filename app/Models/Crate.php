<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id',
        'name',
        'iata_code',
        'type',
        'animal_quantity',
        'length',
        'wide',
        'height',
        'weight',
        'currency',
        'cost_price',
        'cost_price_changed',
        'sale_price',
        'sale_price_changed',
    ];

    /**
     * Get full dimensions.
     *
     * @return string
     */
    public function getFullDimensionsAttribute()
    {
        return "{$this->length} x {$this->wide} x {$this->height} cm";
    }

    /**
     * Animals that belong to a crate.
     */
    public function animals()
    {
        return $this->belongsToMany(Animal::class, 'animal_crate');
    }

    /**
     * Offers that use this crate
     */
    public function offers_species()
    {
        return $this->hasMany(OfferSpeciesCrate::class);
    }
}
