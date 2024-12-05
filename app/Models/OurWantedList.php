<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurWantedList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'our_wanted_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * A standard wanted list can have many our-wanted records.
     */
    public function our_wanteds()
    {
        return $this->belongsToMany(OurWanted::class, 'ourwantedlists_ourwanted');
    }
}
