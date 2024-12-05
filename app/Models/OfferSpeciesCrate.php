<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSpeciesCrate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers_species_crates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_species_id',
        'crate_id',
        'quantity_males',
        'quantity_females',
        'quantity_unsexed',
        'quantity_pairs',
        'length',
        'wide',
        'height',
        'cost_price',
        'sale_price',
        'status',
    ];

    /**
     * The selected crate for the species.
     */
    public function crate()
    {
        return $this->belongsTo(Crate::class);
    }

    /**
     * The parent species inside the offer.
     */
    public function offer_species()
    {
        return $this->belongsTo(OfferSpecies::class, 'offer_species_id');
    }
}
