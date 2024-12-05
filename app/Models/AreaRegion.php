<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaRegion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'short_cut',
    ];

    /**
     * The regions that belong to the area.
     */
    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    /**
     * The standard surplus that are available per each region.
     */
    public function oursurplus()
    {
        return $this->belongsToMany(OurSurplus::class);
    }

    /**
     * The standard wanted that are available per each region.
     */
    public function ourwanted()
    {
        return $this->belongsToMany(OurWanted::class, 'ourwanted_arearegion');
    }
}
