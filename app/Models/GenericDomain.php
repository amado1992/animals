<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenericDomain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain',
    ];
}
