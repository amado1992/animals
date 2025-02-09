<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email:rfc',
                Rule::unique('users')->ignore($this->user),
            ],
            'role'      => 'required|string|exists:roles,id',
            'name'      => 'required|string',
            'last_name' => 'required|string',
            'password'  => 'required|string|min:6',
        ];
    }
}
