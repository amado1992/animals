<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Classification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'common_name', 'common_name_slug', 'common_name_spanish', 'scientific_name', 'code', 'rank', 'belongs_to',
    ];

    /**
     * Set the slug value.
     *
     * @param  string  $value
     * @return void
     */
    public function setCommonNameSlugValue($value)
    {
        $this->attributes['common_name_slug'] = Str::slug($value, '_');
    }

    /**
     * The roles that belong to the user.
     */
    public function above()
    {
        return $this->belongsTo(self::class, 'belongs_to');
    }

    /**
     * The classifications that belong to this classification.
     */
    public function under()
    {
        return $this->hasMany(self::class, 'belongs_to');
    }

    /**
     * The classifications that belong to this classification.
     */
    public function family()
    {
        return $this->above();
    }

    /**
     * The classifications that belong to this classification.
     */
    public function order()
    {
        return $this->family->above();
    }

    /**
     * The classifications that belong to this classification.
     */
    public function class()
    {
        return $this->order->above();
    }

    /**
     * The animals that have this classification
     */
    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
}
