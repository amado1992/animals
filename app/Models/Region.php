<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'area_region_id', 'name', 'short_cut',
    ];

    /**
     * The area that the continent belong to.
     */
    public function area_region()
    {
        return $this->belongsTo(AreaRegion::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    /**
     * The standard surplus that are available per each region.
     */
    /*public function oursurplus()
    {
        return $this->belongsToMany(OurSurplus::class);
    }*/

    /**
     * The standard wanted that are available per each region.
     */
    /*public function ourwanted()
    {
        return $this->belongsToMany(OurWanted::class);
    }*/
}
