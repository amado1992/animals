<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserWebsiteContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'department' => 'required',
            'message'    => 'required|string|min:5',
        ];
    }
}
