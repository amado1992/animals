<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'email', 'token', 'created_at', 'updated_at',
    ];

    /**
     * The organisation information
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
