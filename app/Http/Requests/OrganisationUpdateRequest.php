<?php

namespace App\Http\Requests;

use App\Models\DomainNameLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganisationUpdateRequest extends FormRequest
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
            'name'  => 'required|string|min:2',
            'email' => (Request::input('email') != null) ? [
                'string',
                'max:150',
                'email:rfc',
                Rule::unique('organisations')->ignore($this->organisation),
            ] : '',
            'canonical_name' => (Request::input('email') != null) ? [
                function ($attribute, $value, $fail) {
                    $email = Request::input('email');
                    $email = explode('@', $email);
                    if ($email !== null) {
                        $search = DomainNameLink::where('domain_name', $email[1])->first();
                        if (!empty($search) && empty($value)) {
                            $fail('The canonical name for that domain is required');
                        }
                        if (!empty($search) && !empty($value) && ($search['canonical_name'] != $value)) {
                            $fail('Please make sure the canonical name is correct. There is a fixed canonical name for this domain, and it is not entered correctly');
                        }
                    }
                },
            ] : '',
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
            'name.required' => 'Name is required for an organization',
            'email.unique'  => 'This email is already in, check if website link is missing',
        ];
    }
}
