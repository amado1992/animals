<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountUpdateRequest extends FormRequest
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
            'name'                => 'required|string',
            'iban'                => 'required|string',
            'company_name'        => 'required',
            'currency'            => 'required',
            'company_address'     => 'required|string',
            'beneficiary_name'    => 'required|string',
            'beneficiary_address' => 'required|string',
            'beneficiary_account' => 'required|string',
            'beneficiary_swift'   => 'required|string',
        ];
    }
}
