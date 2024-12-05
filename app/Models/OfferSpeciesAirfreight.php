<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSpeciesAirfreight extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers_species_airfreights';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_species_id',
        'airfreight_id',
        'cost_volKg',
        'sale_volKg',
        'status',
    ];

    /**
     * The selected airfreight for the species.
     */
    public function airfreight()
    {
        return $this->belongsTo(Airfreight::class);
    }

    /**
     * The parent species inside the offer.
     */
    public function offer_species()
    {
        return $this->belongsTo(OfferSpecies::class, 'offer_species_id');
    }
}
