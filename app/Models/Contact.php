<?php

namespace App\Models;

use App\Enums\ContactApprovedStatus;
use App\Enums\ContactMailingCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contact extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id',
        'specialty',
        'relation_type',
        'first_name',
        'last_name',
        'title',
        'position',
        'email',
        'domain_name',
        'country_id',
        'city',
        'mobile_phone',
        'organisation_id',
        'owner_contact_id',
        'member_approved_status',
        'source',
        'mailing_category',
        'user_id',
        'inserted_by',
        'new_contact',
    ];

    /**
     * Scope a query to only include contacts that needs approval.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsApproved($query)
    {
        return $query->where('source', 'website')
                     ->where('member_approved_status', 'active');
    }

    /**
     * Scope a query to only include contacts that needs approval.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedsApproval($query)
    {
        return $query->where('source', 'website')
                     ->where(function ($query) {
                         $query->whereNull('member_approved_status')
                               ->orWhere(function ($query) {
                                   $query->where('member_approved_status', '<>', 'active')
                                         ->where('member_approved_status', '<>', 'no_active')
                                         ->where('member_approved_status', '<>', 'cancel');
                               });
                     });
    }

    /**
     * Scope a query to only include contacts that are not in the proccess to be approval.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetContacts($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('member_approved_status')
                  ->orWhere('member_approved_status', 'active');
        });
    }

    /**
     * Get the contact's full name including the title.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the contact's full name including the title.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->title} {$this->first_name} {$this->last_name}";
    }

    /**
     * Get the contact title and lastname.
     *
     * @return string
     */
    public function getLetterNameAttribute()
    {
        if ($this->first_name && Str::length("{$this->first_name}") > 2) {
            $letterName = "{$this->first_name}";
        } elseif ($this->last_name) {
            $letterName = ($this->title) ? "{$this->title} {$this->last_name}" : "{$this->last_name}";
        } else {
            $letterName = 'Mr./Mrs.';
        }

        return trim($letterName);
    }

    /**
     * Get approved status field.
     *
     * @return string
     */
    public function getApprovedStatusAttribute()
    {
        return ($this->member_approved_status != null) ? ContactApprovedStatus::getValue($this->member_approved_status) : 'Not indicated yet';
    }

    /**
     * Get mailing category field.
     *
     * @return string
     */
    public function getMailingAttribute()
    {
        return ($this->mailing_category != null) ? ContactMailingCategory::getValue($this->mailing_category) : 'Not indicated yet';
    }

    /**
     * @return string|null
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->mobile_phone;
    }

    /**
     * Get where the contact is used.
     *
     * @return string
     */
    public function getActiveStateAttribute()
    {
        $return = collect();

        if (!$return->has('Off') && $this->offers->count() > 0) {
            $return->put('Off', 'Off');
        }

        if (!$return->has('Ord') && ($this->orders_contact_client->count() > 0 || $this->orders_contact_supplier->count() > 0 || $this->orders_contact_origin->count() > 0 || $this->orders_contact_destination->count() > 0)) {
            $return->put('Ord', 'Ord');
        }

        if (!$return->has('Inv') && $this->invoices->count() > 0) {
            $return->put('Inv', 'Inv');
        }

        return $return->implode(', ');
    }

    /**
     * Get string where the contact is used.
     *
     * @return string
     */
    public function getActiveStateStrAttribute()
    {
        $return = collect();

        if (!$return->has('Offer') && $this->offers->count() > 0) {
            $return->put('Offer', 'Offer');
        }

        if (!$return->has('Order') && ($this->orders_contact_client->count() > 0 || $this->orders_contact_supplier->count() > 0 || $this->orders_contact_origin->count() > 0 || $this->orders_contact_destination->count() > 0)) {
            $return->put('Order', 'Order');
        }

        if (!$return->has('Invoices') && $this->invoices->count() > 0) {
            $return->put('Invoices', 'Invoices');
        }

        return $return->implode(', ');
    }

    /**
     * Get the contact country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The organizations of a contact. A contact can work only for one organization.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * A contact can be interest in many sections.
     */
    public function interest_sections()
    {
        return $this->belongsToMany(InterestSection::class);
    }

    /**
     * The offers related with the contact.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'client_id');
    }

    /**
     * The orders related with the contact.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * The orders related with the client contact.
     */
    public function orders_contact_client()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * The orders related with the supplier contact.
     */
    public function orders_contact_supplier()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }

    /**
     * The orders related with the origin contact.
     */
    public function orders_contact_origin()
    {
        return $this->hasMany(Order::class, 'contact_origin_id');
    }

    /**
     * The orders related with the destination contact.
     */
    public function orders_contact_destination()
    {
        return $this->hasMany(Order::class, 'contact_final_destination_id');
    }

    /**
     * The invoices related with the contact.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'invoice_contact_id');
    }

    /**
     * The surplus records that are related with a contact.
     */
    public function surpluses()
    {
        return $this->hasMany(Surplus::class);
    }

    /**
     * The wanted records that are related with a contact.
     */
    public function wanteds()
    {
        return $this->hasMany(Wanted::class, 'client_id');
    }

    /**
     * Get the user related with the contact.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin user who inserted the record.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    /**
     * The emails records that are related with a contact.
     */
    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * The emails records that are related with a contact.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(self::class, 'owner_contact_id', 'id');
    }

    /**
     * @return string|null
     */
    public function getAssociationLabelAttribute(): ?string
    {
        if (isset($this->organisation->associations)) {
            return $this->organisation->associations->first()->label ?? null;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getAssociationLevelAttribute(): ?string
    {
        if (isset($this->organisation->level)) {
            return $this->organisation->level ?? null;
        }

        return null;
    }
}
