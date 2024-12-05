<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OfferCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'client' => 'required|exists:contacts,id',
            'offer_currency'        => 'required',
            'sale_price_type'       => 'required',
            'delivery_country_id'   => 'required',
            'delivery_airport_id'   => 'required',
            'cost_price_status'     => 'required',
            'institution_client_id' => 'required',
            'manager_id'            => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //'client.exists'  => 'Client must be a contact.',
            'offer_currency.required'        => 'Offer currency is required.',
            'sale_price_type.required'       => 'Offer sale price type is required.',
            'delivery_country_id.required'   => 'Offer delivery country is required.',
            'delivery_airport_id.required'   => 'Offer delivery airport-city is required.',
            'cost_price_status.required'     => 'Offer cost price status is required.',
            'institution_client_id.required' => 'Institution client sis required.',
            'manager_id.required'            => 'Manager is required.',
        ];
    }
}
