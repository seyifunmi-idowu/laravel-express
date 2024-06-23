<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\AuthService;
use App\Http\Resources\UserProfileResource;
use App\Exceptions\CustomAPIException;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserService $userService;
    protected AuthService $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function getUserInfo(Request $request)
    {
        return ApiResponse::responseSuccess(new UserProfileResource($request->user()), 'User info');
    }
    
    public function logoutUser(Request $request)
    {
        Auth::logout();
        return ApiResponse::responseSuccess([], 'success');
    }

    public function deleteUser(Request $request)
    {
        $user = Auth::user();
        $user->delete();
        return ApiResponse::responseSuccess([], 'User deleted successfully');
    }

    public function changePassword(Request $request)
    {
        try{
            $request->validate([
                'old_password' => 'required',
                'password' => 'required',
                'verify_password' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        if ($request->password != $request->verify_password){
            throw new CustomAPIException("Passwords do not match");
        };
        
        $this->userService->changePassword($request);
        return ApiResponse::responseSuccess([], 'Password changed successful');
    }

    public function customizeReferralCode(Request $request)
    {
        try{
            $request->validate([
                'referral_code' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        if ($request->password != $request->verify_password){
            throw new CustomAPIException("Passwords do not match");
        };
        
        $user = $this->userService->customizeReferralCode($request->user(), $request->referral_code);
        return ApiResponse::responseSuccess(new UserProfileResource($user), 'Password changed successful');
    }

    public function initiatePasswordReset(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $this->authService->initiateEmailVerification($request->email, "Password reset");
        return ApiResponse::responseSuccess([], 'OTP sent');
    }

    public function verifyPasswordReset(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required',
                'otp' => 'required',
                'password' => 'required',
                'verify_password' => 'required',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        if ($request->password != $request->verify_password){
            throw new CustomAPIException("Passwords do not match");
        };
        $this->userService->verifyPasswordReset($request);
        return ApiResponse::responseSuccess([], 'Password reset successful');
    }

}
