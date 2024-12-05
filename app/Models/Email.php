<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_email',
        'guid',
        'body',
        'subject',
        'contact_id',
        'organisation_id',
        'order_id',
        'offer_id',
        'wanted_id',
        'is_read',
        'name',
        'to_email',
        'surplu_id',
        'created_at',
        'updated_at',
        'to_recipients',
        'is_draft',
        'type_draft',
        'cc_email',
        'bcc_email',
        'attachments_draft',
        'body_sumary',
        'color_id',
        'is_remind',
        'remind_email_id',
        'remind_due_date',
        'task_id'
    ];

    /**
     * The contact information
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * The organisation information
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    /**
     * The labels information
     */
    public function labels()
    {
        return $this->belongsToMany(Labels::class, 'emails_labels');
    }

    /**
     * The labels information
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'emails_tasks');
    }

    /**
     * The labels information
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    /**
     * The labels information
     */
    public function directory()
    {
        return $this->belongsTo(Directorie::class, 'directorie_id');
    }

    /**
     * The labels information
     */
    public function surplu()
    {
        return $this->belongsTo(Surplus::class, 'surplu_id');
    }

    /**
     * The labels information
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * The labels information
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    /**
     * The labels information
     */
    public function wanted()
    {
        return $this->belongsTo(Wanted::class, 'wanted_id');
    }

    /**
     * The labels information
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * The offers related with the contact.
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'email_id');
    }

    /**
     * Get all of the offer tasks.
     */
    public function items()
    {
        return $this->morphMany(ItemDashboard::class, 'itemable');
    }
}
