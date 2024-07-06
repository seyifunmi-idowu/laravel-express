<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Validator as FieldValidators;

class BusinessRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'email' => ['required', 'email'],
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:15',
            ],
            'password' => ['required', 'string', function ($attribute, $value, $fail) {
                FieldValidators::validatePassword($attribute, $value, $fail);
            }],
            'verify_password' => 'required|string|same:password',
        ];
    }

    public function messages()
    {
        return [
            'verify_password.same' => 'Passwords do not match.',
        ];
    }
}
