<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterestingWebsiteCreateRequest extends FormRequest
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
            'siteCategory' => 'required|string',
            'siteName'     => 'required|string',
            'siteUrl'      => 'required|string',
        ];
    }
}
