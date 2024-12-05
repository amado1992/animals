<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestSection extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * A section can belongs to many organisations.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organisation::class, 'organisation_interestsections');
    }

    /**
     * A section can belongs to many contacts.
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
