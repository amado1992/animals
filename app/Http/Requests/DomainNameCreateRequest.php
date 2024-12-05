<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomainNameCreateRequest extends FormRequest
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
            'domain_name' => [
                'required',
                'string',
                'unique:domain_name_links',
                function ($attribute, $value, $fail) {
                    $domain = explode('.', $value);
                    if (count($domain) < 2) {
                        $fail('The domain field is not valid');
                    }
                },
            ],
            'canonical_name' => 'required',
        ];
    }
}
