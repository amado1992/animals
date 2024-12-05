<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceCreateRequest extends FormRequest
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
            'invoice_contact_id' => 'required|numeric|exists:contacts,id',
            'invoice_date'       => 'required|date',
            'invoice_company'    => 'required',
            'bank_account_id'    => 'required',
            'invoice_currency'   => 'required',
            'invoice_amount'     => 'required|numeric',
            'paid_value'         => 'numeric',
            'paid_date'          => 'nullable|date',
            'invoice_type'       => 'required',
            'invoiceFile'        => 'file',
        ];
    }
}
