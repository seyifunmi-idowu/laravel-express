<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InitiateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pickup.latitude' => 'required|max:50',
            'pickup.longitude' => 'required|max:50',
            'pickup.address_details' => 'nullable|string|max:200',
            'pickup.contact_phone_number' => 'nullable|max:50',
            'pickup.contact_name' => 'nullable|string|max:100',
            'pickup.save_address' => 'nullable|boolean',
            'delivery.latitude' => 'required|max:50',
            'delivery.longitude' => 'required|max:50',
            'delivery.address_details' => 'nullable|string|max:200',
            'delivery.contact_phone_number' => 'nullable|max:50',
            'delivery.contact_name' => 'nullable|string|max:100',
            'delivery.save_address' => 'nullable|boolean',
            'vehicle_id' => 'required|string|max:100',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();
        $message  = collect($errors)->unique()->first();

        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'errors' => $errors,
                'message' => $message[0],
            ], 400)
        );
    }
}
