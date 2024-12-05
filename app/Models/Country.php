<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id', 'name', 'country_code', 'phone_code', 'region_id', 'language', 'created_at',
    ];

    /**
     * The region that the country belong to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function airports()
    {
        return $this->hasMany(Airport::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }
}
