<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cites extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cites';

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
}
