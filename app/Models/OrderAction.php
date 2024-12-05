<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_action';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'action_id',
        'action_date',
        'action_remind_date',
        'action_received_date',
        'action_document',
        'remark',
        'toBeDoneBy',
        'status',
    ];

    /**
     * The related order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The related action.
     */
    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
