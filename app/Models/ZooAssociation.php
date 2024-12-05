<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZooAssociation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'area',
        'country_id',
        'website',
        'name',
        'remark',
        'status',
        'started_date',
        'checked_date',
        'user_id',
    ];

    /**
     * The country that the zoo association belong to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The admin user who checked the zoo association.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
