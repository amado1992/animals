<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultTextTask extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text',
    ];
}
