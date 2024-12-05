<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color', 'filter_email', 'title',
    ];

    /**
     * The orders related with the contact.
     */
    public function emails()
    {
        return $this->hasMany(Email::class, 'color_id');
    }
}
