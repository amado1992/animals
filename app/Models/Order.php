<?php

namespace App\Models;

use App\Enums\ShipmentTerms;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'manager_id',
        'order_number',
        'client_id',
        'supplier_id',
        'contact_origin_id',
        'contact_final_destination_id',
        'airfreight_agent_id',
        'delivery_country_id',
        'delivery_airport_id',
        'cost_currency',
        'sale_currency',
        'company',
        'bank_account_id',
        'order_status',
        'order_remarks',
        'cost_price_type',
        'sale_price_type',
        'cost_price_status',
        'realized_date',
        'created_at',
    ];

    /**
     * Get full order number.
     *
     * @return string
     */
    public function getFullNumberAttribute()
    {
        return $this->created_at->format('Y') . '-' . $this->order_number;
    }

    /**
     * Get sales prices type.
     *
     * @return string
     */
    public function getSaleTypeAttribute()
    {
        return ($this->sale_price_type != null) ? ShipmentTerms::getValue($this->sale_price_type) : '';
    }

    /**
     * Get cost prices type.
     *
     * @return string
     */
    public function getCostTypeAttribute()
    {
        return ($this->cost_price_type != null) ? ShipmentTerms::getValue($this->cost_price_type) : '';
    }

    /**
     * Order within europe.
     *
     * @return bool
     */
    public function getWithinEuropeAttribute()
    {
        return ($this->client->country && $this->client->country->region->name == 'Europe (Eur. union + Swit + UK)' && $this->supplier->country && $this->supplier->country->region->name == 'Europe (Eur. union + Swit + UK)') ? true : false;
    }

    /**
     * Order within netherlands.
     *
     * @return bool
     */
    public function getWithinNetherlandsAttribute()
    {
        return ($this->client->country && $this->client->country->name == 'The Netherlands – EU') ? true : false;
    }

    /**
     * Get outgoing invoices files.
     *
     * @return array
     */
    public function getOutgoingInvoicesAttribute(): array
    {
        return Storage::files('public/orders_docs/' . $this->full_number . '/outgoing_invoices');
    }

    /**
     * Get incoming invoices files.
     *
     * @return array
     */
    public function getIncomingInvoicesAttribute(): array
    {
        return Storage::files('public/orders_docs/' . $this->full_number . '/incoming_invoices');
    }

    /**
     * Get order general files.
     *
     * @return array
     */
    public function getOrderDocsAttribute(): array
    {
        return Storage::files('public/orders_docs/' . $this->full_number);
    }

    /**
     * Get all order files.
     *
     * @return array
     */
    public function getAllDocsAttribute(): array
    {
        return Storage::allFiles('public/orders_docs/' . $this->full_number);
    }

    /**
     * The order's offer.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * The order's manager.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * The client of the order.
     */
    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    /**
     * The supplier of the order.
     */
    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    /**
     * Contact final destination related with the order.
     */
    public function contact_final_destination()
    {
        return $this->belongsTo(Contact::class, 'contact_final_destination_id');
    }

    /**
     * Contact origin related with the order.
     */
    public function contact_origin()
    {
        return $this->belongsTo(Contact::class, 'contact_origin_id');
    }

    /**
     * Contact airfreight agent related with the order.
     */
    public function airfreight_agent()
    {
        return $this->belongsTo(Contact::class, 'airfreight_agent_id');
    }

    /**
     * The delivery country related with the order.
     */
    public function delivery_country()
    {
        return $this->belongsTo(Country::class, 'delivery_country_id');
    }

    /**
     * The delivery airport related with the order.
     */
    public function delivery_airport()
    {
        return $this->belongsTo(Airport::class, 'delivery_airport_id');
    }

    /**
     * Order bank account.
     */
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /**
     * Order invoices.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all of the order tasks.
     */
    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable')->orderBy('due_date');
    }

    /**
     * Get order's tasks of today.
     *
     * @return object
     */
    public function getOrderTodayTasksAttribute(): object
    {
        return $this->tasks()
            ->whereNull('finished_at')
            ->whereDate('due_date', '<=', Carbon::now()->format('Y-m-d'))
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get order's tasks greater than today.
     *
     * @return object
     */
    public function getOrderOtherTasksAttribute(): object
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
     * Order actions.
     */
    public function order_actions()
    {
        return $this->hasMany(OrderAction::class);
    }
}
