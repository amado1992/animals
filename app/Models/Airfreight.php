<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airfreight extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source',
        'type',
        'departure_continent',
        'arrival_continent',
        'currency',
        'volKg_weight_value',
        'volKg_weight_cost',
        'lowerdeck_value',
        'lowerdeck_cost',
        'maindeck_value',
        'maindeck_cost',
        'offered_date',
        'transport_agent',
        'remarks',
        'inserted_by',
        'standard_flight'
    ];

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

    /**
     * Transport agent who offered the freight quotation.
     */
    public function agent()
    {
        return $this->belongsTo(Contact::class, 'transport_agent');
    }

    /**
     * User who inserted the airfreight.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The related offer's airfreight.
     */
    public function offer_airfreight()
    {
        return $this->hasOne(OfferSpeciesAirfreight::class);
    }

    /**
     * The related offer's airfreight pallet.
     */
    public function offer_airfreight_pallet()
    {
        return $this->hasOne(OfferAirfreightPallet::class);
    }
}
