<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
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
            'code' => 'required|string|max:6',
            'email' => ['nullable', 'email'],
            'phone_number' => [
                'nullable',
                'string',
                'min:10',
                'max:15'
            ],
        ];
    }

    public function validate(array $data)
    {
        // FormValidator::validateEmailOrPhoneNumber($data);
        return $data;
    }
}
