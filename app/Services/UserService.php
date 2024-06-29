<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Exceptions\CustomAPIException;
use App\Models\User;
use App\Models\ReferralUser;
use App\Helpers\PasswordManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserService
{
    protected $walletService;
    protected $notificationService;
    protected $authService;

    public function __construct(
        WalletService $walletService,
        NotificationService $notificationService,
        AuthService $authService
    )
    {
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;
        $this->authService = $authService;
    }

    public function getUser ($phone_number="", $email="")
    {
        if ($email){
            $user = User::where('email', $email);
        } else {
            $user = User::where('phone_number', $phone_number);
        }
        return $user->first();
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $referralCode = $data['referral_code'] ?? '';
            $oneSignalId = $data['one_signal_id'] ?? null;
            $referralCode = empty($referralCode) ? null : $referralCode;
            $referredBy = null;

            if ($referralCode) {
                $referredBy = User::where('referral_code', $referralCode)->first();
                if (!$referredBy) {
                    throw new CustomAPIException(
                        "Invalid referral code. Edit referral code or sign up without referral code",
                        409
                    );
                }
            }

            $data['password'] = Hash::make($data['password']);
            $data['referral_code'] = $this->generateReferralCode();
            $data['new_pass'] = true;
            
            $user = User::create($data);

            $this->walletService->createUserWallet($user);

            if ($oneSignalId) {
                $this->notificationService->addUserOneSignal($user, $oneSignalId);
            }

            if ($referredBy) {
                ReferralUser::create([
                    'referred_by_id' => $referredBy->id,
                    'referred_user_id' => $user->id,
                    'referral_code' => $referralCode,
                ]);
            }

            return $user;
        });
    }

    private function generateReferralCode(int $length = 8): string
    {
        do {
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $referralCode = '';
            for ($i = 0; $i < $length; $i++) {
                $referralCode .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (User::where('referral_code', $referralCode)->exists());

        return $referralCode;
    }
   
    public function authenticateUser(string $email, string $password)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new CustomAPIException('Invalid credentials', 401);
        }
        if (!$user->new_pass){
            if (!$user || !PasswordManager::verifyPassword($password, $user->password)) {
                throw new CustomAPIException('Invalid credentials', 401);
            }
            $user->new_pass = true;
            $user->password = Hash::make($password);
            $user->save();
        }
        $credentials = ['email' => $email, 'password' => $password];
        if (!Auth::attempt($credentials)) {
            throw new CustomAPIException('Invalid credentials', 401);
        }
        $user = Auth::user();
        return $user;
    }

    public function changePhoneNumer($old_phone_number, $phone_number)
    {
        $user = $this->getUser($old_phone_number, "");
        if (!$user){
            throw new CustomAPIException('User not found.', 404);
        }
        $anotherUser = $this->getUser($phone_number, "");
        if ($anotherUser){
            throw new CustomAPIException('New phone number already taken.', 400);
        }

        $user->phone_number = $phone_number;
        $user->phone_verified = false;
        $user->save();
        $this->authService->initiatePhoneVerification($phone_number);
        return $user;
    }

    public function changeEmail($old_email, $email)
    {
        $user = $this->getUser("", $old_email);
        if (!$user){
            throw new CustomAPIException('User not found.', 404);
        }
        $anotherUser = $this->getUser("", $email);
        if ($anotherUser){
            throw new CustomAPIException('New email already taken.', 400);
        }
        $user->email = $email;
        $user->email_verified = false;
        $user->save();
        $this->authService->initiateEmailVerification($email);
        return $user;
    }

    public function changePassword($request)
    {
        $old_password = $request->old_password;
        $password = $request->password;
        $user = $request->user();
        $credentials = ['email' => $user->email, 'password' => $old_password];
        if (!Auth::attempt($credentials)) {
            throw new CustomAPIException('old password is not correct', 401);
        }
        $user->password = Hash::make($password);
        $user->save();
        return true;
    }

    public function customizeReferralCode($user, $referral_code)
    {
        $referredCodeUser = User::where('referral_code', $referral_code)->first();
        if ($referredCodeUser) {
            throw new CustomAPIException("Referral code taken", 409);
        }
        $user->referral_code = $referral_code;
        $user->save();
        return $user;
    }

    public function verifyPasswordReset($request)
    {
        $email= $request->email;
        $otp= $request->otp;
        $password= $request->password;

        $this->authService->verifyEmailVerification($email, $otp);

        $user = $this->getUser("", $email);
        $user->password = Hash::make($password);
        $user->save();

        return true;
    }
}
