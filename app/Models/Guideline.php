<?php

namespace App\Models;

use App\Enums\ProtocolSections;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Guideline extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'remark',
        'category',
        'section',
        'related_filename',
    ];

    /**
     * Get file url.
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        if ($this->section === 'guidelines') {
            return ($this->related_filename != null && Storage::exists('public/guidelines_docs/' . $this->related_filename)) ? Storage::url('guidelines_docs/' . $this->related_filename) : '#';
        } else {
            return ($this->related_filename != null && Storage::exists('public/guidelines_docs/' . $this->section . '/' . $this->related_filename)) ? Storage::url('guidelines_docs/' . $this->section . '/' . $this->related_filename) : '#';
        }
    }

    /**
     * Get section field.
     *
     * @return string
     */
    public function getSectionFieldAttribute()
    {
        return ($this->section != null) ? ProtocolSections::getValue($this->section) : '';
    }
}
