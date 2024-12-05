<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'name', 'type', 'size',
    ];

    /**
     * Get all of the offer tasks.
     */
    public function items()
    {
        return $this->morphMany(ItemDashboard::class, 'itemable');
    }
}
