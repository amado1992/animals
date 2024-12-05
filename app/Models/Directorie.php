<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directorie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'name', 'icon', 'created_at', 'updated_at',
    ];

    /**
     * The contact information
     */
    public function directorie()
    {
        return $this->belongsTo(self::class, 'directorie_id');
    }
}
