<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the contact's full name including the title.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->currency}";
    }
}
