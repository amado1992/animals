<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurSurplusList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'our_surplus_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * A standard surplus list can have many our-surplus records.
     */
    public function our_surpluses()
    {
        return $this->belongsToMany(OurSurplus::class, 'oursurpluslists_oursurplus');
    }
}
