<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mailing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'date_created',
        'date_sent_out',
        'language',
        'institution_level',
        'institution_types',
        'part_of_world',
        'exclude_continents',
        'exclude_countries',
        'remarks',
        'mailing_template',
    ];

    /**
     * Get file url.
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return ($this->mailing_template != null && Storage::exists('public/mailings/' . $this->mailing_template)) ? Storage::url('mailings/' . $this->mailing_template) : '#';
    }
}
