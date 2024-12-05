<?php

namespace App\Models;

use App\Enums\AgeGroup;
use App\Enums\LookingFor;
use App\Models\Origin;
use Illuminate\Database\Eloquent\Model;

class OurWanted extends Model
{
    //use Enumerable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'our_wanted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'animal_id',
        'origin',
        'age_group',
        'looking_for',
        'remarks',
        'intern_remarks',
        'created_at',
        'updated_at',
    ];

    /**
     * Define attributes that are enum.
     *
     * @var array
     */
    protected static $enums = [
        'age_group'   => \App\Enums\AgeGroup::class,
        'looking_for' => \App\Enums\LookingFor::class,
    ];

    /**
     * Get looking_for field.
     *
     * @return string
     */
    public function getLookingFieldAttribute()
    {
        return ($this->looking_for != null) ? LookingFor::getValue($this->looking_for) : '';
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
     * The animal that is wanted
     */
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * A standard wanted can be available for many area regions.
     */
    public function area_regions()
    {
        return $this->belongsToMany(AreaRegion::class, 'ourwanted_arearegion');
    }

    /**
     * A standard wanted can be on different lists.
     */
    public function ourwanted_lists()
    {
        return $this->belongsToMany(OurWantedList::class, 'ourwantedlists_ourwanted');
    }
}
