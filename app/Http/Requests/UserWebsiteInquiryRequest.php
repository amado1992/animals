<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserWebsiteInquiryRequest extends FormRequest
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
            'userId'                      => 'required|integer|exists:users,id',
            'surplusses'                  => 'required|array|min:1',
            'surplusses.*.surplus_id'     => 'required|integer|exists:our_surplus,id',
            'surplusses.*.quantityM'      => 'required|integer',
            'surplusses.*.quantityF'      => 'required|integer',
            'surplusses.*.quantityU'      => 'required|integer',
            'surplusses.*.client_remarks' => 'nullable|string',
        ];
    }
}
