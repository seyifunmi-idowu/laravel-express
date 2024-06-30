<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Helpers\Validator as FieldValidators;

class LocationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'latitude' => 'required|string|max:50',
            'longitude' => 'required|string|max:50',
            'address_details' => 'nullable|string|max:200',
            'contact_phone_number' => 'nullable|string|max:50',
            'contact_name' => 'nullable|string|max:100',
            'save_address' => 'nullable|boolean'
        ];
    }
}
