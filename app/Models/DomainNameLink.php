<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainNameLink extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_name',
        'canonical_name',
    ];
}
