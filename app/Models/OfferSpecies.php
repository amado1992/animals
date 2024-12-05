<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSpecies extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offers_species';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'oursurplus_id',
        'offerQuantityM',
        'offerQuantityF',
        'offerQuantityU',
        'offerQuantityP',
        'offerCostPriceM',
        'offerCostPriceF',
        'offerCostPriceU',
        'offerCostPriceP',
        'offerSalePriceM',
        'offerSalePriceF',
        'offerSalePriceU',
        'offerSalePriceP',
        'client_remarks',
        'origin',
        'region_id',
        'status',
    ];

    /**
     * Get species crates.
     *
     * @return array
     */
    public function getSpeciesCratesAttribute()
    {
        if ($this->oursurplus->animal->iata_code) {
            $crates = Crate::where('iata_code', $this->oursurplus->animal->iata_code)->orderBy('name')->get();
        } else {
            $crates = $this->oursurplus->animal->crates;
        }

        return $crates;
    }

    /**
     * The region of the surplus
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Calculate species total sales price.
     *
     * @return float
     */
    public function getTotalSalesPriceAttribute()
    {
        $total_sales_price = $this->offerQuantityM  * $this->offerSalePriceM;
        $total_sales_price += $this->offerQuantityF * $this->offerSalePriceF;
        $total_sales_price += $this->offerQuantityU * $this->offerSalePriceU;

        if ($this->offerQuantityM > 0 && $this->offerQuantityM == $this->offerQuantityF && $this->offerSalePriceP > 0) {
            $total_sales_price += $this->offerQuantityM * $this->offerSalePriceP;
        }

        return $total_sales_price;
    }

    /**
     * The parent offer.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * The our-surplus related with the species of the offer.
     */
    public function oursurplus()
    {
        return $this->belongsTo(OurSurplus::class, 'oursurplus_id')->withTrashed();
    }

    /**
     * Get the crate related with the species of the offer.
     */
    public function species_crate()
    {
        return $this->hasOne(OfferSpeciesCrate::class);
    }

    /**
     * Get the airfreigth related with the species of the offer.
     */
    public function species_airfreights()
    {
        return $this->hasMany(OfferSpeciesAirfreight::class);
    }
}
