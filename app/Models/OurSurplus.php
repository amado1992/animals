<?php

namespace App\Models;

use App\Enums\AgeGroup;
use App\Enums\AvailabilityGroup;
use App\Enums\Enumerable;
use App\Enums\Size;
use App\Models\Origin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OurSurplus extends Model
{
    use Enumerable;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'our_surplus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id',
        'animal_id',
        'quantityM',
        'quantityF',
        'quantityU',
        'availability',
        'region_id',
        'area_region_id',
        'origin',
        'age_group',
        'bornYear',
        'size',
        'remarks',
        'intern_remarks',
        'special_conditions',
        'cost_currency',
        'costPriceM',
        'costPriceF',
        'costPriceU',
        'costPriceP',
        'sale_currency',
        'salePriceM',
        'salePriceF',
        'salePriceU',
        'salePriceP',
        'is_public',
        'created_at',
        'updated_at',
        'catalog_pic'
    ];

    /**
     * Define attributes that are enum.
     *
     * @var array
     */
    protected static $enums = [
        'currencies' => \App\Enums\Currency::class,
    ];

    /**
     * Get the surplus male quantity.
     *
     * @return string
     */
    public function getMaleQuantityAttribute()
    {
        return ($this->quantityM < 0) ? 'x' : $this->quantityM;
    }

    /**
     * Get the surplus female quantity.
     *
     * @return string
     */
    public function getFemaleQuantityAttribute()
    {
        return ($this->quantityF < 0) ? 'x' : $this->quantityF;
    }

    /**
     * Get the surplus unknown quantity.
     *
     * @return string
     */
    public function getUnknownQuantityAttribute()
    {
        return ($this->quantityU < 0) ? 'x' : $this->quantityU;
    }

    /**
     * Get the surplus pair quantity.
     *
     * @return string
     */
    public function getPairQuantityAttribute()
    {
        return ($this->salePriceP > 0) ? (($this->quantityM >= 0) ? $this->quantityM : 'x') : 0;
    }

    /**
     * Get the label related to availability
     *
     * @return string
     */
    public function getAvailabilityFieldAttribute()
    {
        if (!$this->availability) {
            return '';
        }

        return AvailabilityGroup::getValue($this->availability);
    }

    /**
     * Get the label of the age group
     *
     * @return string
     */
    public function getAgeFieldAttribute()
    {
        if (!$this->age_group) {
            return '';
        }

        return AgeGroup::getValue($this->age_group);
    }

    /**
     * Get the label of the age group
     *
     * @return string
     */
    public function getOriginFieldAttribute()
    {
        $origin = Origin::where('short_cut', $this->origin)->first();

        return (!empty($origin)) ? $origin['name'] : '';
    }

    /**
     * Get the label of the size group
     *
     * @return string
     */
    public function getSizeFieldAttribute()
    {
        if (!$this->size) {
            return '';
        }

        return Size::getValue($this->size);
    }

    /**
     * Get surplus pictures.
     *
     * @return array
     */
    public function getOurSurplusPicturesCatalogAttribute(): array
    {
        return Storage::files('public/oursurplus_pictures/'.$this->id);
    }

    /**
     * Get the surplus remarks complete.
     *
     * @return string
     */
    public function getCompleteRemarksAttribute()
    {
        //Surplus details
        $surplusDetail = [];
        if (trim($this->origin) != '') {
            $origin = Origin::where('short_cut', $this->origin)->first();
            if (!empty($origin)) {
                array_push($surplusDetail, $origin['name']);
            }
        }
        if (trim($this->age_group) != '') {
            array_push($surplusDetail, AgeGroup::getValue($this->age_group));
        }
        if (trim($this->bornYear) != '') {
            array_push($surplusDetail, $this->bornYear);
        }
        if (trim($this->size) != '') {
            array_push($surplusDetail, Size::getValue($this->size));
        }
        if (trim($this->remarks) != '') {
            array_push($surplusDetail, $this->remarks);
        }

        return implode(', ', $surplusDetail);
    }

    /**
     * Get the surplus remarks complete.
     *
     * @return string
     */
    public function getCompleteRemarksShortAttribute()
    {
        //Surplus details
        $surplusDetail = [];
        if (trim($this->age_group) != '') {
            array_push($surplusDetail, AgeGroup::getValue($this->age_group));
        }
        if (trim($this->bornYear) != '') {
            array_push($surplusDetail, $this->bornYear);
        }
        if (trim($this->size) != '') {
            array_push($surplusDetail, Size::getValue($this->size));
        }
        if (trim($this->remarks) != '') {
            array_push($surplusDetail, $this->remarks);
        }

        return implode(', ', $surplusDetail);
    }

    /**
     * Get the surplus remarks that we want to show in the surplus-wanted list.
     *
     * @return string
     */
    public function getListRemarksAttribute()
    {
        //Surplus details
        $surplusDetail = [];
        if (trim($this->origin) != '') {
            $origin = Origin::where('short_cut', $this->origin)->first();
            if (!empty($origin)) {
                array_push($surplusDetail, $origin['name']);
            }
        }
        if (trim($this->size) != '') {
            array_push($surplusDetail, Size::getValue($this->size));
        }
        if (trim($this->remarks) != '') {
            array_push($surplusDetail, $this->remarks);
        }

        return implode(', ', $surplusDetail);
    }

    /**
     * Get the surplus location.
     *
     * @return string
     */
    public function getLocationAttribute()
    {
        return 'ex ' . (($this->region != null) ? $this->region->name : '');
    }

    /**
     * Get surplus pictures.
     *
     * @return array
     */
    public function getSurplusPicturesAttribute(): array
    {
        return Storage::files('public/oursurplus_docs/' . $this->id);
    }


     /**
     * Get the Animal Related Surplus.
     *
     * @return string
     */
    public function getAnimalRelatedSurplusAttribute()
    {
        return $this->animal->surpluses_not_order()
        ->select('*', 'surplus.created_at as created_at', 'surplus.created_at as updated_at', DB::raw('organisations.level IS NULL AS sortOrderNull'))
        ->join('organisations', 'organisations.id', '=', 'surplus.organisation_id')
        ->where('surplus_status', '<>', 'collection')
        ->where('animal_id', $this->animal_id)
        ->where('area_region_id', $this->area_region_id)
        ->where('origin', $this->origin)
        ->orderBy('sortOrderNull', 'ASC')
        ->orderBy('organisations.level', 'ASC')
        ->paginate(10);
    }

    /**
     * The method returns a list of surplus where the species are from the same continent.
     * @return mixed
     */
    public function getSpeciesSameContinentAttribute()
    {
        return $this->animal->surpluses_not_order()
        ->select('*', 'surplus.created_at as created_at', 'surplus.created_at as updated_at', DB::raw('organisations.level IS NULL AS sortOrderNull'))
        ->join('organisations', 'organisations.id', '=', 'surplus.organisation_id')
        ->where('area_region_id', $this->area_region_id)
        ->orderBy('sortOrderNull', 'ASC')
        ->orderBy('organisations.level', 'ASC')
        ->paginate(10);
    }

    /**
     * The animal that belong to the surplus
     */
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * The region of the surplus
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * A standard surplus can be available for many regions.
     */
    /*public function regions()
    {
        return $this->belongsToMany(Region::class);
    }*/

    /**
     * The region of the surplus
     */
    public function area_region()
    {
        return $this->belongsTo(AreaRegion::class);
    }

    /**
     * A standard surplus can be available for many regions.
     */
    public function area_regions()
    {
        return $this->belongsToMany(AreaRegion::class, 'oursurplus_arearegion');
    }

    /**
     * A standard surplus can be on different lists.
     */
    public function oursurplus_lists()
    {
        return $this->belongsToMany(OurSurplusList::class, 'oursurpluslists_oursurplus');
    }

    /**
     * The offer species records that are related with the standard surplus.
     */
    public function offer_species()
    {
        return $this->hasMany(OfferSpecies::class);
    }
}
