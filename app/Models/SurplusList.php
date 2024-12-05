<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurplusList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surplus_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * A surplus list can have many surplus records.
     */
    public function surpluses()
    {
        return $this->belongsToMany(Surplus::class, 'surpluslists_surplus');
    }
}
