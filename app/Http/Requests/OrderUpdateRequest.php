<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OrderUpdateRequest extends FormRequest
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
            'client' => (Request::input('hidden_client_id') == null && Request::input('client_id') == null && Request::input('contact_client_id') == null) ? 'required' : '',
            //'client_id' => 'required|exists:contacts,id',
            //'supplier_id' => 'required|exists:contacts,id|not_in:0',
            'order_status'        => 'required',
            'manager_id'          => 'required|exists:users,id',
            'delivery_country_id' => 'required',
            'delivery_airport_id' => 'required',
            'cost_currency'       => 'required',
            'cost_price_type'     => 'required',
            'sale_currency'       => 'required',
            'sale_price_type'     => 'required',
            'company'             => 'required',
            'bank_account_id'     => 'required',
            'cost_price_status'   => 'required',
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
            'client.required' => 'Client is required.',
            //'client_id.exists'  => 'Client must be a contact.',
            //'supplier_id.required'  => 'Supplier is required.',
            //'supplier_id.not_in'  => 'Supplier need to be selected.',
            //'supplier_id.exists'  => 'Supplier must be a contact.',
            'order_status.required'        => 'Order status is required.',
            'manager_id.required'          => 'Order manager is required.',
            'delivery_country_id.required' => 'Order delivery country is required.',
            'delivery_airport_id.required' => 'Order delivery airport-city is required.',
            'cost_currency.required'       => 'Order cost currency is required.',
            'cost_price_type.required'     => 'Order cost price type is required.',
            'sale_currency.required'       => 'Order sale currency is required.',
            'sale_price_type.required'     => 'Order sale price type is required.',
            'company.required'             => 'Order company is required.',
            'bank_account_id.required'     => 'Order bank account is required.',
            'cost_price_status.required'   => 'Offer cost price status is required.',
        ];
    }
}
