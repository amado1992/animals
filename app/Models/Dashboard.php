<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'main',
        'parent_id',
        'type_style',
        'order',
        'url',
        'row_color',
        'filter_data',
        'show_only',
    ];

    public function parent()
    {
        return $this->belongsTo("App\Models\Dashboard");
    }

    public function dashboards()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function items(){
        return $this->hasMany(ItemDashboard::class, 'dashboard_id')->orderBy('created_at', 'DESC');
    }
}
