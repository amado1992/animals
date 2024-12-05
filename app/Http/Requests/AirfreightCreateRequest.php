<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AirfreightCreateRequest extends FormRequest
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
            'source'              => 'required',
            'transport_agent'     => 'exists:contacts,id',
            'departure_continent' => 'required',
            'arrival_continent'   => 'required',
            'type'                => 'required',
            'currency'            => 'required',
            'volKg_weight_value'  => 'numeric|min:0',
            'lowerdeck_value'     => 'numeric|min:0',
            'maindeck_value'      => 'numeric|min:0',
            'volKg_weight_cost'   => 'numeric|min:0',
            'lowerdeck_cost'      => 'numeric|min:0',
            'maindeck_cost'       => 'numeric|min:0',
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
            'source.required'              => 'Airfreight source is required.',
            'transport_agent.exists'       => 'Transport agent must be a contact.',
            'departure_continent.required' => 'Departure continent is required.',
            'arrival_continent.required'   => 'Arrival continent is required.',
            'type.required'                => 'Airfreight type is required.',
            'currency.required'            => 'Airfreight currency is required.',
            'volKg_weight_value.numeric'   => 'Vol. kg sale price need to be a valid number.',
            'lowerdeck_value.numeric'      => 'Lowerdeck sale price need to be a valid number.',
            'maindeck_value.numeric'       => 'Maindeck sale price need to be a valid number.',
            'volKg_weight_cost.numeric'    => 'Vol. kg cost price need to be a valid number.',
            'lowerdeck_cost.numeric'       => 'Lowerdeck cost price need to be a valid number.',
            'maindeck_cost.numeric'        => 'Maindeck cost price need to be a valid number.',
            'volKg_weight_value.min'       => 'Vol. kg sale price must be greater than zero.',
            'lowerdeck_value.min'          => 'Lowerdeck sale price must be greater than zero.',
            'maindeck_value.min'           => 'Maindeck sale price must be greater than zero.',
            'volKg_weight_cost.min'        => 'Vol. kg cost price must be greater than zero.',
            'lowerdeck_cost.min'           => 'Lowerdeck cost price must be greater than zero.',
            'maindeck_cost.min'            => 'Maindeck cost price must be greater than zero.',
        ];
    }
}
