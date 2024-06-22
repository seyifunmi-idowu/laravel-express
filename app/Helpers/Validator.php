<?php
namespace App\Helpers;

use Illuminate\Validation\ValidationException;
use App\Exceptions\CustomAPIException;
use Illuminate\Support\Carbon;

class Validator
{
    const PASSWORD_REGEX_RULE = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,32}$/';

    public static function validatePassword($attribute, $value, $fail)
    {
        if (!preg_match(self::PASSWORD_REGEX_RULE, $value)) {
            return $fail('Password should be at least 8-32 characters and should contain upper, lower case letters, numbers, and special characters');
        }
    }

    public static function isStartDateLessThanOrEqualsEndDate($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($endDate->gte($startDate)) {
            return true;
        }

        $message = "The end date should be greater than or equal to the start date.";
        throw new CustomAPIException($message, 400);
    }

}
