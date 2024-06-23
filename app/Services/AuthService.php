<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomAPIException;

class AuthService
{

    public function __construct()
    {

    }
   
    public function generateOtp()
    {
        return rand(100000, 999999);
    }

    public function createOtp($email = null, $phoneNumber = null)
    {
        $otp = $this->generateOtp();
        $expirationTime = Carbon::now()->addHours(2); // Set expiration time to 2 hours from now

        return OtpVerification::create([
            'email' => $email,
            'phone_number' => $phoneNumber,
            'otp' => $otp,
            'expiration_time' => $expirationTime,
        ]);
    }

    public function verifyOtp($emailOrPhoneNumber, $otp)
    {
        $otpVerification = OtpVerification::where(function ($query) use ($emailOrPhoneNumber) {
            $query->where('email', $emailOrPhoneNumber)
                  ->orWhere('phone_number', $emailOrPhoneNumber);
        })->where('otp', $otp)
          ->where('expiration_time', '>', Carbon::now())
          ->first();

        if ($otpVerification) {
            // OTP is valid
            return true;
        }

        // OTP is invalid or expired
        return false;
    }

    public function initiatePhoneVerification($phoneNumber)
    {
        $user = User::where('phone_number', $phoneNumber)->first();
        if (!$user) {
            throw new CustomAPIException(
                "User with this phone number not found",
                400
            );
        }

        if ($user && $user->phone_verified) {
            throw new CustomAPIException(
                "User with this phone number has been verified",
                400
            );
        }

        $existingVerification = OTPVerification::where('phone_number', $phoneNumber)->first();

        if ($existingVerification && $existingVerification->trials >= config('constants.phone_verification_max_trials', 3)) {
            throw new CustomAPIException(
                "Oops you\'ve reached the maximum request for this operation. Retry in 24hrs time.",
                429
            );
        }

        $otp = $this->generateOtp();
        $expirationTime = Carbon::now()->addSeconds(config('constants.phone_verification_ttl', 300));

        DB::transaction(function () use ($phoneNumber, $otp, $expirationTime, $existingVerification) {
            if ($existingVerification) {
                $existingVerification->update([
                    'otp' => $otp,
                    'expiration_time' => $expirationTime,
                    'trials' => DB::raw('trials + 1'),
                ]);
            } else {
                OTPVerification::create([
                    'phone_number' => $phoneNumber,
                    'otp' => $otp,
                    'expiration_time' => $expirationTime,
                    'trials' => 1,
                ]);
            }
        });

        $message = "Your Fele Express OTP is {$otp}";
        NotificationService::sendSmsMessage($user, $message);

        return true;
    }

    public function initiateEmailVerification($email, $subject="Verify Email")
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new CustomAPIException(
                "User with this email not found",
                400
            );
        }
        if ($user && $user->email_verified) {
            throw new CustomAPIException(
                "User with this email has already been verified.",
                429
            );
        }

        $existingVerification = OTPVerification::where('email', $email)->first();

        if ($existingVerification && $existingVerification->trials >= config('constants.email_verification_max_trials', 5)) {
            throw new CustomAPIException(
                "Oops you've reached the maximum request for this operation. Retry in 24hrs time.",
                429
            );
        }

        $otp = $this->generateOtp();
        $expirationTime = Carbon::now()->addSeconds(config('constants.email_verification_ttl', 1800));

        DB::transaction(function () use ($email, $otp, $expirationTime, $existingVerification) {
            if ($existingVerification) {
                $existingVerification->update([
                    'otp' => $otp,
                    'expiration_time' => $expirationTime,
                    'trials' => DB::raw('trials + 1'),
                ]);
            } else {
                OTPVerification::create([
                    'email' => $email,
                    'otp' => $otp,
                    'expiration_time' => $expirationTime,
                    'trials' => 1,
                ]);
            }
        });

        $context = [
            "otp" => $otp,
            "name" => $user->first_name. " " . $user->last_name,
        ];
        NotificationService::sendEmailMessage(
            [$user->email], 
            $subject, 
            $context,
            "emails.otp_template"
        );

        return true;
    }

    public function verifyEmailVerification($email, $otp)
    {
        $verification = OTPVerification::where('email', $email)
            ->where('otp', $otp)
            ->where('expiration_time', '>', Carbon::now())
            ->first();

        if (!$verification) {
            throw new CustomAPIException(
                "Oops seems the otp has expired.",
                400
            );
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new CustomAPIException(
                "User with this email was not found",
                404
            );
        }

        $user->email_verified = true;
        $user->save();

        $verification->delete();

        return true;
    }

    public function verifyPhoneVerification($phoneNumber, $otp)
    {
        $verification = OTPVerification::where('phone_number', $phoneNumber)
            ->where('otp', $otp)
            ->where('expiration_time', '>', Carbon::now())
            ->first();
        if (!$verification) {
            throw new CustomAPIException(
                "Oops seems the OTP has expired or is invalid.",
                400
            );
        }

        $user = User::where('phone_number', $phoneNumber)->first();
        if (!$user) {
            throw new CustomAPIException(
                "User with this phone number was not found",
                404
            );
        }

        $user->phone_verified = true;
        $user->save();

        $verification->delete();

        return true;
    }




}
