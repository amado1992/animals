<?php

namespace App\Models;

use App\Enums\Enumerable;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //use Enumerable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'invoice_type',
        'payment_type',
        'bank_account_id',
        'bank_account_number',
        'invoice_contact_id',
        'invoice_currency',
        'invoice_percent',
        'invoice_amount',
        'paid_value',
        'banking_cost',
        'paid_date',
        'invoice_date',
        'invoice_from',
        'invoice_file',
        'remark',
        'belong_to_order',
    ];

    /**
     * Define attributes that are enum.
     *
     * @var array
     */
    /*protected static $enums = [
        'invoice_currency' => \App\Enums\Currency::class,
        'invoice_type' => \App\Enums\InvoiceType::class,
        'payment_type' => \App\Enums\InvoicePaymentType::class
    ];*/

    /**
     * Get full offer number.
     *
     * @return string
     */
    public function getFullNumberAttribute()
    {
        return ($this->bank_account_number) ?
            date('Y', strtotime($this->invoice_date)) . '-' . str_pad((string) $this->bank_account_number, 3, '0', STR_PAD_LEFT) :
            '';
    }

    /**
     * The order related with the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The contact related with the invoice.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'invoice_contact_id');
    }

    /**
     * Return the Institution
     */
    public function getInstitutionAttribute()
    {
        $c = Contact::where('id', '=', $this->invoice_contact_id)->first();

        return Organisation::where('id', '=', $c->organisation_id)->first();
    }

    /**
     * Get the offer that is connected to the order of the invoice
     */
    public function getAnimalsAttribute()
    {
        $order   = Order::where('id', '=', $this->order_id)->first();
        $offer   = Offer::where('id', '=', $order->offer_id)->first();
        $animals = [];
        foreach ($offer->offer_species as $offer_species) {
            $surplus   = OurSurplus::where('id', '=', $offer_species->oursurplus_id)->first();
            $animals[] = Animal::where('id', '=', $surplus->animal_id)->first();
        }

        return $animals;
    }

    /**
     * The bank account related with the invoice.
     */
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
