<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDashboard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'itemable_id',
        'itemable_type',
        'dashboard_id',
        'new',
    ];

    /**
     * Get the owning itemable model.
     */
    public function itemable()
    {
        return $this->morphTo();
    }

    /**
     * The user who created task.
     */
    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class, 'dashboard_id');
    }
}
