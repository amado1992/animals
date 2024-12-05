<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferAction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_action';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
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
     * The related offer.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * The related action.
     */
    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
