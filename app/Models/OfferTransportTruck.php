<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferTransportTruck extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers_transport_truck';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'from_country',
        'to_country',
        'total_km',
        'cost_rate_per_km',
        'sale_rate_per_km',
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
     * Origin country
     */
    public function origin_country()
    {
        return $this->belongsTo(Country::class, 'from_country');
    }

    /**
     * Delivery country
     */
    public function delivery_country()
    {
        return $this->belongsTo(Country::class, 'to_country');
    }
}
