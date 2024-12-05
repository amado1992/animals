<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_id', 'path', 'name', 'guid',
    ];

    /**
     * The organisation information
     */
    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }

    /**
     * Get all of the offer tasks.
     */
    public function items()
    {
        return $this->morphMany(ItemDashboard::class, 'itemable');
    }
}
