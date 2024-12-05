<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferAirfreightPallet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers_airfreight_pallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'airfreight_id',
        'pallet_quantity',
        'departure_continent',
        'arrival_continent',
        'pallet_cost_value',
        'pallet_sale_value',
        'status',
    ];

    /**
     * The parent offer.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * The selected airfreight for the species.
     */
    public function airfreight()
    {
        return $this->belongsTo(Airfreight::class);
    }

    /**
     * Departure continent
     */
    public function from_continent()
    {
        return $this->belongsTo(Region::class, 'departure_continent');
    }

    /**
     * Arrival continent
     */
    public function to_continent()
    {
        return $this->belongsTo(Region::class, 'arrival_continent');
    }
}
