<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferAdditionalCost extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers_additional_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'name',
        'quantity',
        'currency',
        'costPrice',
        'salePrice',
        'is_test',
    ];

    /**
     * The parent offer.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
