<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OurSurplusCreateRequest extends FormRequest
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
        $rules = [
            'animal_id'     => 'required|numeric|exists:animals,id',
            'availability'  => 'required',
            'region'        => 'required',
            'area'          => 'required',
            'origin'        => 'required',
            'cost_currency' => 'required',
            'costPriceM'    => 'numeric|min:0',
            'costPriceF'    => 'numeric|min:0',
            'costPriceU'    => 'numeric|min:0',
            'costPriceP'    => 'numeric|min:0',
            'sale_currency' => 'required',
            'salePriceM'    => 'numeric|min:0',
            'salePriceF'    => 'numeric|min:0',
            'salePriceU'    => 'numeric|min:0',
            'salePriceP'    => 'numeric|min:0',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'animal_id.required'    => 'Animal is required.',
            'availability.required' => 'Availability is required.',
            'region'                => 'Region field is required',
            'area'                  => 'Area field is required',
            'costPriceM.min'        => 'Male sale price cannot be negative.',
            'costPriceF.min'        => 'Female sale price cannot be negative.',
            'costPriceU.min'        => 'Unknown sale price cannot be negative.',
            'costPriceP.min'        => 'Pair sale price cannot be negative.',
            'costPriceM.numeric'    => 'Male sale price need to be a valid number.',
            'costPriceF.numeric'    => 'Fale sale price need to be a valid number.',
            'costPriceU.numeric'    => 'Unknown sale price need to be a valid number.',
            'costPriceP.numeric'    => 'Pair sale price need to be a valid number.',
            'salePriceM.min'        => 'Male sale price cannot be negative.',
            'salePriceF.min'        => 'Female sale price cannot be negative.',
            'salePriceU.min'        => 'Unknown sale price cannot be negative.',
            'salePriceP.min'        => 'Pair sale price cannot be negative.',
            'salePriceM.numeric'    => 'Male sale price need to be a valid number.',
            'salePriceF.numeric'    => 'Fale sale price need to be a valid number.',
            'salePriceU.numeric'    => 'Unknown sale price need to be a valid number.',
            'salePriceP.numeric'    => 'Pair sale price need to be a valid number.',
        ];
    }
}
