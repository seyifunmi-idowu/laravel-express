<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Services\BusinessService;
use App\Services\UserService;
use App\Services\AuthService;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\BusinessRegistrationRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    protected BusinessService $businessService;
    protected AuthService $authService;
    protected UserService $userService;

    public function __construct(
        BusinessService $businessService, 
        AuthService $authService,
        UserService $userService
    )
    {
        $this->businessService = $businessService;
        $this->authService = $authService;
        $this->userService = $userService;
    }


    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->user_type == "BUSINESS") {
            return Redirect::route('business-dashboard');
        }

        return view('app.login');
    }

    public function login(LoginFormRequest $request)
    {
        $response = $this->businessService->loginBusinessUser(
            $request->only('email', 'password')
        );

        if ($response) {
            return Redirect::route('business-dashboard');
        }

        return Redirect::back()->withErrors(['Invalid credentials']);
    }

    public function showVerifyEmailForm(Request $request)
    {
        $email = $request->query('email', '');

        return view('app.verify_email', compact('email'));
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        // BusinessAuthService::verifyBusinessUserEmail($request->only('email', 'otp'));

        return Redirect::route('business-dashboard');
    }

    public function showRegistrationForm()
    {
        if (Auth::check() && Auth::user()->user_type == "BUSINESS") {
            return Redirect::route('business-dashboard');
        }

        return view('app.signup');
    }

    public function register(BusinessRegistrationRequest $request)
    {
        // BusinessAuthService::registerBusinessUser($request->all());

        return Redirect::route('business-verify-email', ['email' => $request->email]);
    }

    public function resendOtp(Request $request)
    {
        $email = $request->query('email', '');
        // $user = UserService::getUserInstance($email);

        // if ($user == null) {
        //     return Redirect::back()->withErrors(['User not found']);
        // }

        // AuthService::initiateEmailVerification($email, $user->display_name);

        return Redirect::route('business-verify-email', ['email' => $email]);
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::route('business-login');
    }

    public function dashboard()
    {
        $user = $this->userService->getUser("", 'chowdeck@gmail.com');       
        $response = $this->businessService->getBusinessDashboardView($user);

        return view('app.dashboard', ['view' => 'Dashboard'] + $response);
    }

    public function order()
    {
        $user = $this->userService->getUser("", 'chowdeck@gmail.com');       
        $response = $this->businessService->getBusinessOrderView($user);

        return view('app.order', ['view' => 'Order'] + $response);
    }

    public function get_order(Request $request, $order_id)
    {
        $user = $this->userService->getUser("", 'chowdeck@gmail.com');       
        $response = $this->businessService->getBusinessGetOrderView($user, $order_id);

        return view('app.view_order', ['view' => 'Order'] + $response);
    }

}
