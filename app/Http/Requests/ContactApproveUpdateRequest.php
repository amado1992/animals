<?php

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactApproveUpdateRequest extends FormRequest
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
        $contact = Contact::where('id', Request::input('contact_id'))->first();

        return [
            'first_name'    => 'required|string|max:50',
            'last_name'     => 'required|string|max:50',
            'contact_email' => [
                'required',
                'string',
                'max:150',
                'email:rfc',
                Rule::unique('contacts', 'email')->ignore($contact),
            ],
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
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
        ];
    }
}
