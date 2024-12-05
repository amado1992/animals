<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ContactCreateRequest extends FormRequest
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
            'first_name'                => 'max:50',
            'last_name'                 => 'max:50',
            'contact_email'             => (Request::input('contact_email') != null) ? 'string|max:150|unique:contacts,email|email:rfc' : '',
            'select_institution_option' => (Request::input('select_institution_option') == null) ? 'required' : '',
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
            'select_institution_option.required' => 'An institution option must be selected.',
        ];
    }
}
