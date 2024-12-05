<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OurWantedUpdateRequest extends FormRequest
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
            'animal_id'   => 'required|numeric|exists:animals,id',
            'origin'      => 'required',
            'looking_for' => 'required',
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
            'animal_id.required'   => 'Animal is required.',
            'origin.required'      => 'Origin field is required.',
            'looking_for.required' => 'Looking for field is required.',
        ];
    }
}
