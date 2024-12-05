<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StdText extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'category',
        'name',
        'remarks',
        'english_text',
        'spanish_text',
    ];

    /**
     * Array with enum categories
     */
    public static $enumCategories = [
        'airfreight'           => 'Airfreight',
        'animals-stock'        => 'Animals stock',
        'arrival-details'      => 'Arrival details',
        'booking'              => 'Booking',
        'contacts'             => 'Contacts',
        'crates'               => 'Crates',
        'email'                => 'Email',
        'general'              => 'General',
        'order-docs'           => 'Order docs',
        'payment'              => 'Payment',
        'permits-certificates' => 'Permits certificates',
        'website'              => 'Website',
    ];
}
