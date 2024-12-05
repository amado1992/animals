<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrateCreateRequest extends FormRequest
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
            'name'      => 'required|string',
            'type'      => 'required',
            'length'    => 'required|numeric|min:0|not_in:0',
            'wide'      => 'required|numeric|min:0|not_in:0',
            'height'    => 'required|numeric|min:0|not_in:0',
            'weight'    => 'required|numeric|min:0',
            'iata_code' => 'required|numeric',
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
            'name.required'      => 'Crate name is required.',
            'type.required'      => 'Crate type is required.',
            'length.numeric'     => 'Length dimension need to be a valid number.',
            'wide.numeric'       => 'Wide dimension need to be a valid number.',
            'height.numeric'     => 'Height dimension need to be a valid number.',
            'length.min'         => 'Length dimension must be greater than zero.',
            'wide.min'           => 'Wide dimension must be greater than zero.',
            'height.min'         => 'Height dimension must be greater than zero.',
            'length.not_in'      => 'Length dimension must be greater than zero.',
            'wide.not_in'        => 'Wide dimension must be greater than zero.',
            'height.not_in'      => 'Height dimension must be greater than zero.',
            'weight.required'    => 'Crate weight is required.',
            'weight.numeric'     => 'Crate weight must be a valid number.',
            'iata_code.required' => 'Crate iata code is required.',
            'iata_code.required' => 'Crate iata code must be a valid number.',
        ];
    }
}
