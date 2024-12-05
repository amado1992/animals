<?php

namespace App\Models;

use App\Enums\ShipmentTerms;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Offer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'manager_id',
        'creator',
        'offer_number',
        'offer_currency',
        'client_id',
        'institution_id',
        'supplier_id',
        'airfreight_agent_id',
        'delivery_country_id',
        'delivery_airport_id',
        'offer_status',
        'status_level',
        'remarks',
        'sale_price_type',
        'cost_price_status',
        'total_profit',
        'airfreight_type',
        'quantity_x',
        'next_reminder_at',
        'times_reminded',
        'extra_fee',
        'offer_send_out',
        'offer_send_out',
        'new_offer_inquiry',
    ];

    /**
     * Get full offer number.
     *
     * @return string
     */
    public function getFullNumberAttribute()
    {
        return $this->created_at->format('Y') . '-' . $this->offer_number;
    }

    /**
     * Get offer type.
     *
     * @return string
     */
    public function getOfferTypeAttribute()
    {
        return ($this->sale_price_type != null) ? ShipmentTerms::getValue($this->sale_price_type) : '';
    }

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
     * Get airfreight folder files.
     *
     * @return array
     */
    public function getAirfreightDocsAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number . '/airfreight');
    }

    /**
     * Get crates folder files.
     *
     * @return array
     */
    public function getCratesDocsAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number . '/crates');
    }

    /**
     * Get cites documents folder files.
     *
     * @return array
     */
    public function getCitesDocsAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number . '/cites_docs');
    }

    /**
     * Get veterinary documents folder files.
     *
     * @return array
     */
    public function getVeterinaryDocsAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number . '/veterinary_docs');
    }

    /**
     * Get documents folder files.
     *
     * @return array
     */
    public function getDocumentsDocsAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number . '/documents');
    }

    /**
     * Get offers of suppliers folder files.
     *
     * @return array
     */
    public function getSuppliersOffersAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number . '/suppliers_offers');
    }

    /**
     * Get others folder files.
     *
     * @return array
     */
    public function getOthersDocsAttribute(): array
    {
        return Storage::files('public/offers_docs/' . $this->full_number);
    }

    /**
     * Get all offer files.
     *
     * @return array
     */
    public function getAllDocsAttribute(): array
    {
        return Storage::allFiles('public/offers_docs/' . $this->full_number);
    }

    /**
     * Scope a query to only include offers with a created_at year in the currect year
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCurrentYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->format('Y'));
    }

    /**
     * The species of the offer.
     */
    public function offer_species()
    {
        return $this->hasMany(OfferSpecies::class);
    }

    /**
     * The species of the offer ordered by class and order common_name.
     *
     * @return array
     */
    public function getSpeciesOrderedAttribute(): array
    {
        $speciesList = $this->offer_species
            ->groupBy([
                'oursurplus.animal.classification.class.common_name',
                'oursurplus.animal.classification.order.common_name',
            ]);

        $array_object_results = [];
        $speciesList          = $speciesList->sortKeys();

        foreach ($speciesList as $groupByClass) {
            $groupByClass = $groupByClass->sortKeys();

            foreach ($groupByClass as $groupByOrder) {
                $groupByOrder = $groupByOrder->sortBy('oursurplus.animal.common_name');

                foreach ($groupByOrder as $species) {
                    array_push($array_object_results, $species);
                }
            }
        }

        return $array_object_results;
    }

    /**
     * Transport truck selected for the offer.
     */
    public function transport_truck()
    {
        return $this->hasOne(OfferTransportTruck::class);
    }

    /**
     * Offer airfreight pallet.
     */
    public function airfreight_pallet()
    {
        return $this->hasOne(OfferAirfreightPallet::class);
    }

    /**
     * The additional costs of the offer.
     */
    public function additional_costs()
    {
        return $this->hasMany(OfferAdditionalCost::class);
    }

    /**
     * The offer's manager.
     */
    public function manager()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The client of the offer.
     */
    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    /**
     * The organisation of the offer.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'institution_id');
    }

    /**
     * The supplier of the offer.
     */
    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    /**
     * Contact airfreight agent related with the offer.
     */
    public function airfreight_agent()
    {
        return $this->belongsTo(Contact::class, 'airfreight_agent_id');
    }

    /**
     * The delivery country related with the offer.
     */
    public function delivery_country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The delivery airport related with the offer.
     */
    public function delivery_airport()
    {
        return $this->belongsTo(Airport::class);
    }

    /**
     * Order related with the offer.
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    /**
     * Get all of the offer tasks.
     */
    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable')->orderBy('due_date');
    }

    /**
     * Get all search mailings related with the offer.
     */
    public function search_mailings()
    {
        return $this->morphMany(SearchMailing::class, 'searchable')->orderByDesc('created_at');
    }

    /**
     * Get offer's tasks of today.
     *
     * @return object
     */
    public function getOfferTodayTasksAttribute(): object
    {
        return $this->tasks()
            ->whereNull('finished_at')
            ->whereDate('due_date', '<=', Carbon::now()->format('Y-m-d'))
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get offer's tasks greater than today.
     *
     * @return object
     */
    public function getOfferOtherTasksAttribute(): object
    {
        return $this->tasks()
            ->where(function ($query) {
                $query->whereDate('due_date', '>', Carbon::now()->format('Y-m-d'))
                    ->orWhereNull('due_date');
            })
            ->whereNull('finished_at')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Offer actions.
     */
    public function offer_actions()
    {
        return $this->hasMany(OfferAction::class);
    }
}
