<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurplusCollectionUpdateRequest extends FormRequest
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
        $user = User::where('id', Auth::id())->first();

        $rules = [
            'organisation_id' => 'required|numeric|exists:organisations,id',
            'contact_id'      => 'numeric|exists:contacts,id',
            'animal_id'       => 'required|numeric|exists:animals,id',
            'origin'          => 'required',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'organisation_id.required' => 'Institution is required.',
            'animal_id.required'       => 'Animal is required.',
            'origin.required'          => 'Origin field is required.',
        ];
    }
}
