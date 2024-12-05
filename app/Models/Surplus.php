<?php

namespace App\Models;

use App\Enums\AgeGroup;
use App\Enums\Enumerable;
use App\Enums\Size;
use App\Models\Origin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Surplus extends Model
{
    use Enumerable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surplus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id',
        'organisation_id',
        'contact_id',
        'animal_id',
        'quantityM',
        'quantityF',
        'quantityU',
        'country_id',
        'area_region_id',
        'surplus_status',
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
        'to_members',
        'to_members_date',
        'warning_indication',
        'inserted_by',
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
     * Get origin field.
     *
     * @return string
     */
    public function getOriginFieldAttribute()
    {
        $origin = Origin::where('short_cut', $this->origin)->first();

        return (!empty($origin)) ? $origin['name'] : '';
    }

    /**
     * Get age field.
     *
     * @return string
     */
    public function getAgeFieldAttribute()
    {
        return ($this->age_group != null) ? AgeGroup::getValue($this->age_group) : '';
    }

    /**
     * Get size field.
     *
     * @return string
     */
    public function getSizeFieldAttribute()
    {
        return ($this->size != null) ? Size::getValue($this->size) : '';
    }

    /**
     * Get the surplus location.
     *
     * @return string
     */
    public function getLocationAttribute()
    {
        return 'ex ' . (($this->country != null) ? $this->country->region->name : '');
    }

    /**
     * Get surplus pictures.
     *
     * @return array
     */
    public function getSurplusPicturesAttribute(): array
    {
        return Storage::files('public/surpluses_docs/' . $this->id);
    }

    /**
     * Get surplus pictures.
     *
     * @return array
     */
    public function getSurplusPicturesCatalogAttribute(): array
    {
        return Storage::files('public/surpluses_pictures/'.$this->id);
    }

    /**
     * The animal that belong to the surplus
     */
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * The surplus institution
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * The supplier owner of the surplus
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * The region of the surplus
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The area of the surplus
     */
    public function area_region()
    {
        return $this->belongsTo(AreaRegion::class);
    }

    /**
     * The admin user who inserted the record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    /**
     * A standard surplus can be on different lists.
     */
    public function surplus_lists()
    {
        return $this->belongsToMany(SurplusList::class, 'surpluslists_surplus');
    }
}
