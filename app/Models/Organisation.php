<?php

namespace App\Models;

use App\Enums\OrganisationInfoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'specialty',
        'relation_type',
        'name',
        'synonyms',
        'domain_name',
        'organisation_type',
        'email',
        'phone',
        'fax',
        'website',
        'facebook_page',
        'address',
        'zipcode',
        'city',
        'country_id',
        'vat_number',
        //'language',
        'level',
        'info_status',
        'remarks',
        'internal_remarks',
        'short_description',
        'public_zoos_relation',
        'animal_related_association',
        'canonical_name',
        'mailing_category',
        'new_organisation',
    ];

    /**
     * Get where the organization is used.
     *
     * @return string
     */
    public function getActiveStateAttribute()
    {
        $return = collect();

        if (!$return->has('S') && $this->surpluses->count() > 0) {
            $return->put('S', 'S');
        }

        if (!$return->has('W') && $this->wanteds->count() > 0) {
            $return->put('W', 'W');
        }

        foreach ($this->contacts as $contact) {
            if (!$return->has('Off') && $contact->offers->count() > 0) {
                $return->put('Off', 'Off');
            }

            if (!$return->has('Ord') && ($contact->orders_contact_client->count() > 0 || $contact->orders_contact_supplier->count() > 0 || $contact->orders_contact_origin->count() > 0 || $contact->orders_contact_destination->count() > 0)) {
                $return->put('Ord', 'Ord');
            }

            if (!$return->has('Inv') && $contact->invoices->count() > 0) {
                $return->put('Inv', 'Inv');
            }
        }

        return $return->implode(', ');
    }

    /**
     * Get string where the organization is used.
     *
     * @return string
     */
    public function getActiveStateStrAttribute()
    {
        $return = collect();

        if (!$return->has('Surplus') && $this->surpluses->count() > 0) {
            $return->put('Surplus', 'Surplus');
        }

        if (!$return->has('Wanted') && $this->wanteds->count() > 0) {
            $return->put('Wanted', 'Wanted');
        }

        foreach ($this->contacts as $contact) {
            if (!$return->has('Offer') && $contact->offers->count() > 0) {
                $return->put('Offer', 'Offer');
            }

            if (!$return->has('Order') && ($contact->orders_contact_client->count() > 0 || $contact->orders_contact_supplier->count() > 0 || $contact->orders_contact_origin->count() > 0 || $contact->orders_contact_destination->count() > 0)) {
                $return->put('Order', 'Order');
            }

            if (!$return->has('Invoices') && $contact->invoices->count() > 0) {
                $return->put('Invoices', 'Invoices');
            }
        }

        return $return->implode(', ');
    }

    /**
     * Get institution info status.
     *
     * @return string
     */
    public function getInfoAttribute()
    {
        return ($this->info_status != null) ? OrganisationInfoStatus::getValue($this->info_status) : '';
    }

    /**
     * Get institution info.
     *
     * @return string
     */
    public function getInstitutionDetailsAttribute()
    {
        $institutionDetails = "{$this->name}" . '<br>';
        if ($this->country) {
            if ($this->city) {
                $institutionDetails .= "{$this->city}" . ', ';
            }
            $institutionDetails .= "{$this->country->name}" . '<br>';
        }
        if ($this->address) {
            $institutionDetails .= '<strong>Address: </strong>' . "{$this->address}" . '<br>';
        }

        return $institutionDetails;
    }

    /**
     * The contacts that belong to the organization.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'invoice_organisation_id');
    }

    /**
     * The organisation type that belongs to the organisation.
     */
    public function type()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type');
    }

    /**
     * The country that belongs to the organisation
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * An organisation can be interest in many sections.
     */
    public function interest()
    {
        return $this->belongsToMany(InterestSection::class, 'organisation_interestsections');
    }

    /**
     * @return HasMany
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'institution_id');
    }

    /**
     * An organisation can belong to many associations.
     */
    public function associations()
    {
        return $this->belongsToMany(Association::class);
    }

    /**
     * The surplus records that are related with a institution.
     */
    public function surpluses()
    {
        return $this->hasMany(Surplus::class);
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'institution_id');
    }

    /**
     * The wanted records that are related with a institution.
     */
    public function wanteds()
    {
        return $this->hasMany(Wanted::class);
    }

    /**
     * Related email records
     *
     * @return HasMany
     */
    public function emails()
    {
        return $this->hasMany(Email::class);
    }
}
