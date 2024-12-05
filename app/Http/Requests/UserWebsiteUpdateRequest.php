<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserWebsiteUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guest();
        //return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'                 => 'required|string|max:50',
            'last_name'                  => 'required|string|max:50',
            'email'                      => 'required|max:150|email',
            'title'                      => 'required|string|max:4',
            'position'                   => 'nullable|string|max:20',
            'interests'                  => 'required|array|max:7',
            'receive_surplus_wanted'     => 'required|boolean',
            'password'                   => 'required|string|min:8',
            'phone'                      => 'nullable|string|min:10',
            'mobile_phone'               => 'nullable|string|max:30',
            'country'                    => 'required|integer|exists:countries,id',
            'city'                       => 'nullable|string|max:30',
            'organisation'               => 'required|array',
            'website'                    => 'nullable|string|max:80',
            'facebook_page'              => 'nullable|string|max:120',
            'short_description'          => 'nullable|string',
            'public_zoos_relation'       => 'nullable|string|max:80',
            'animal_related_association' => 'nullable|string|max:80',
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
            'first_name.required'             => 'First name is required.',
            'last_name.required'              => 'Last name is required.',
            'email.required'                  => 'Email is required.',
            'title.required'                  => 'Title is required.',
            'interests.required'              => 'Interest sections are required.',
            'receive_surplus_wanted.required' => 'Newsletters option is required.',
            'password.required'               => 'Password is required.',
            'code_number.required'            => 'Code number is required.',
            'country.required'                => 'Country is required.',
            'organisation.required'           => 'Institution info is required.',
        ];
    }
}
