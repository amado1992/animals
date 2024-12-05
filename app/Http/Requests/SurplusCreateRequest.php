<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurplusCreateRequest extends FormRequest
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
        $user = User::where('id', Auth::id())->first();

        $rules = [
            'organisation_id'   => 'required|numeric|exists:organisations,id',
            'contact_id'        => 'numeric|exists:contacts,id',
            'animal_id'         => 'required|numeric|exists:animals,id',
            'quantityM'         => 'numeric',
            'quantityF'         => 'numeric',
            'quantityU'         => 'numeric',
            'check_quantities1' => 'required_without_all:quantityM,quantityF,quantityU',
            'country_id'        => 'required',
            'area_region_id'    => 'required',
            'origin'            => 'required',
            'cost_currency'     => 'required',
            'costPriceM'        => 'numeric|min:0',
            'costPriceF'        => 'numeric|min:0',
            'costPriceU'        => 'numeric|min:0',
            'costPriceP'        => 'numeric|min:0',
            'sale_currency'     => ($user->hasPermission('surplus-suppliers.see-sale-prices') && !$user->hasRole('office')) ? 'required' : '',
            'salePriceM'        => 'numeric|min:0',
            'salePriceF'        => 'numeric|min:0',
            'salePriceU'        => 'numeric|min:0',
            'salePriceP'        => 'numeric|min:0',
        ];

        if (Request::input('quantityM') == 0 && Request::input('quantityF') == 0 && Request::input('quantityU') == 0) {
            $rules['check_quantities2'] = 'required';
        }

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
            'organisation_id.required'               => 'Institution is required.',
            'animal_id.required'                     => 'Animal is required.',
            'quantityM.numeric'                      => 'Male quantity need to be a valid number.',
            'quantityF.numeric'                      => 'Female quantity need to be a valid number.',
            'quantityU.numeric'                      => 'Unknown quantity need to be a valid number.',
            'check_quantities1.required_without_all' => 'Quantities cannot be empty.',
            'check_quantities2.required'             => 'Quantities cannot be empty.',
            'country_id'                             => 'Country field is required',
            'area_region_id'                         => 'Area field is required',
            'origin.required'                        => 'Origin field is required.',
            'cost_currency.required'                 => 'Cost currency is required.',
            'costPriceM.min'                         => 'Male cost price cannot be negative.',
            'costPriceF.min'                         => 'Female cost price cannot be negative.',
            'costPriceU.min'                         => 'Unknown cost price cannot be negative.',
            'costPriceP.min'                         => 'Pair cost price cannot be negative.',
            'costPriceM.numeric'                     => 'Male cost price need to be a valid number.',
            'costPriceF.numeric'                     => 'Fale cost price need to be a valid number.',
            'costPriceU.numeric'                     => 'Unknown cost price need to be a valid number.',
            'costPriceP.numeric'                     => 'Pair cost price need to be a valid number.',
            'sale_currency.required'                 => 'Sale currency is required.',
            'salePriceM.min'                         => 'Male sale price cannot be negative.',
            'salePriceF.min'                         => 'Female sale price cannot be negative.',
            'salePriceU.min'                         => 'Unknown sale price cannot be negative.',
            'salePriceP.min'                         => 'Pair sale price cannot be negative.',
            'salePriceM.numeric'                     => 'Male sale price need to be a valid number.',
            'salePriceF.numeric'                     => 'Fale sale price need to be a valid number.',
            'salePriceU.numeric'                     => 'Unknown sale price need to be a valid number.',
            'salePriceP.numeric'                     => 'Pair sale price need to be a valid number.',
        ];
    }
}
