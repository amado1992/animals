<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Labels extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'name', 'color', 'filter_email',
    ];

    /**
     * The labels information
     */
    public function emails()
    {
        return $this->belongsToMany(Email::class, 'emails_labels');
    }
}
