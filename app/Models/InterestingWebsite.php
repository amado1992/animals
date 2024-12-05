<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestingWebsite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'siteName',
        'siteUrl',
        'siteRemarks',
        'loginUsername',
        'loginPassword',
        'siteCategory',
        'only_for_john',
    ];

    /**
     * Array with enum categories
     */
    public static $enumCategories = [
        'website-templates'   => 'Website templates',
        'animals-pictures'    => 'Animals pictures',
        'task-managers'       => 'Task managers',
        'tools-informatic'    => 'Tools informatic',
        'website-development' => 'Website development',
        'regulations'         => 'Regulations',
        'others'              => 'Others',
    ];
}
