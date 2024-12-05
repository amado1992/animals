<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WantedList extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wanted_lists';

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
    public function wanteds()
    {
        return $this->belongsToMany(Wanted::class, 'wantedlists_wanted');
    }
}
