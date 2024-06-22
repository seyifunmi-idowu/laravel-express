<?php
namespace App\Helpers;

use Illuminate\Validation\ValidationException;

class Validator
{
    const PASSWORD_REGEX_RULE = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,32}$/';

    public static function validatePassword($attribute, $value, $fail)
    {
        if (!preg_match(self::PASSWORD_REGEX_RULE, $value)) {
            return $fail('Password should be at least 8-32 characters and should contain upper, lower case letters, numbers, and special characters');
        }
    }
}
