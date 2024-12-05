<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'email'     => 'required|unique:users|email:rfc',
            'role'      => 'required|integer|exists:roles,id',
            'name'      => 'required|string',
            'last_name' => 'required|string',
            'password'  => 'required|string|min:6',
        ];
    }
}
