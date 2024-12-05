<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUploadInvoice extends FormRequest
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
            'invoice_from'          => 'required',
            'bank_account_id'       => 'required',
            'upload_invoice_amount' => 'required|numeric',
            'invoice_contact_id'    => 'required|numeric|exists:contacts,id',
            'file'                  => 'nullable|file',
        ];
    }
}
