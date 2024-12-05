<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalCost extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additional_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'usdCostPrice', 'usdSalePrice', 'eurCostPrice', 'eurSalePrice', 'is_test',
    ];
}
