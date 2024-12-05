<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SearchMailing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'search_mailings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'searchable_id',
        'searchable_type',
        'animal_id',
        'date_sent_out',
        'next_reminder_at',
        'times_reminded',
        'remarks',
    ];

    /**
     * Get should be reminded attribute
     *
     * @return bool
     */
    public function getShouldBeRemindedAttribute(): bool
    {
        return $this->next_reminder_at && Carbon::today()->gte($this->next_reminder_at);
    }

    /**
     * Get the owning searchable model.
     */
    public function searchable()
    {
        return $this->morphTo();
    }

    /**
     * The animal that belong to the search mailing.
     */
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
