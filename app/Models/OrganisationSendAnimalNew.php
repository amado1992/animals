<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationSendAnimalNew extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_from',
        'email_subject',
        'email_body',
    ];
}
