<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalUpdateRequest extends FormRequest
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
            'common_name'      => 'required|string',
            'scientific_name'  => 'required|string',
            'class_id'         => 'required',
            'order_id'         => 'required',
            'family_id'        => 'required',
            'genus_id'         => 'required',
            'code_number_temp' => 'digits:12',
            'code_number'      => 'required|digits:4|not_in:0',
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
            'common_name.required'     => 'Common name is required.',
            'scientific_name.required' => 'Scientific name is required.',
            'class_id.required'        => 'Class is required.',
            'order_id.required'        => 'Order is required.',
            'family_id.required'       => 'Family is required.',
            'genus_id.required'        => 'Genus is required.',
            'code_number_temp.digits'  => 'Code number is not completely, a classification is missing.',
            'code_number.required'     => 'Code number is required.',
            'code_number.digits'       => 'Code number is not completely, last digits must be four.',
            'code_number.not_in'       => 'Last 4 digits of the code number must be different to zero.',
        ];
    }
}
