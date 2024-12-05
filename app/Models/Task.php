<?php

namespace App\Models;

use App\Enums\TaskActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'action',
        'due_date',
        'user_id',
        'taskable_id',
        'taskable_type',
        'finished_at',
        'created_by',
        'status',
        'send_forapproval',
        'send_complete',
        'send_incomplete',
        'comment',
        'never',
        'contact_id',
    ];

    /**
     * Get action field.
     *
     * @return string
     */
    public function getActionFieldAttribute()
    {
        return ($this->action != null) ? TaskActions::getValue($this->action) : '';
    }

    /**
     * The user that belong to the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who created task.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the owning taskable model.
     */
    public function taskable()
    {
        return $this->morphTo();
    }

    /**
     * The orders related with the contact.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * The emails information
     */
    public function emails()
    {
        return $this->belongsToMany(Email::class, 'emails_tasks');
    }
}
