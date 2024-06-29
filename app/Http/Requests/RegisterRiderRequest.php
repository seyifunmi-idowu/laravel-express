<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Helpers\Validator as FieldValidators;

class RegisterRiderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:user,email',
            'phone_number' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'unique:user,phone_number'
            ],
            'password' => ['required', 'string', function ($attribute, $value, $fail) {
                FieldValidators::validatePassword($attribute, $value, $fail);
            }],
            'verify_password' => 'required|string',
            'address' => 'nullable|string',
            'receive_email_promotions' => 'boolean',
            'one_signal_id' => 'nullable|string',
            'city' => 'nullable|string',
            'referral_code' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'fullname.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'phone_number.required' => 'Phone number is required',
            'password.required' => 'Password is required',
            'city.required' => 'City type is required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fullname = $this->input('fullname');
            if ($fullname) {
                $fullname_split = explode(' ', $fullname);
                if (count($fullname_split) < 2) {
                    $validator->errors()->add('fullname', 'Full name must contain first name and last name.');
                }
            }
            $password = $this->input('password');
            $verify_password = $this->input('verify_password');
            if ($password != $verify_password){
                $validator->errors()->add('verify_password', 'Passwords do not match.');
            }
        });
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
