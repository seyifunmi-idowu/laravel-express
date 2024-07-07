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
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class BusinessController extends Controller
{
    protected BusinessService $businessService;
    protected AuthService $authService;
    protected UserService $userService;
    protected WalletService $walletService;

    public function __construct(
        BusinessService $businessService, 
        AuthService $authService,
        UserService $userService,
        WalletService $walletService
    )
    {
        $this->businessService = $businessService;
        $this->authService = $authService;
        $this->userService = $userService;
        $this->walletService = $walletService;
    }


    public function showLoginForm()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == "BUSINESS") {
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
        $error = session('error') ?? null;
        return view('app.verify_email', compact('email', 'error'));
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $data = $request->only('email', 'code');
        $response = $this->businessService->verifyBusinessUserEmail($data);
        if (!$response){
            $email = $data['email'];
            return Redirect::route('business-verify-email', ['email' => $email]);
        }
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
        $this->businessService->registerBusinessUser($request->all());

        return Redirect::route('business-verify-email', ['email' => $request->email]);
    }

    public function resendOtp(Request $request)
    {
        $email = $request->query('email', '');
        $user = $this->userService->getUser("", $email);       

        if ($user == null) {
            return Redirect::back()->withErrors(['User not found']);
        }
        $this->authService->initiateEmailVerification($email);

        return Redirect::route('business-verify-email', ['email' => $email]);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return Redirect::route('business-login');
    }

    public function dashboard()
    {
        $user = Auth::guard('web')->user();
        $response = $this->businessService->getBusinessDashboardView($user);

        return view('app.dashboard', ['view' => 'Dashboard'] + $response);
    }

    public function order()
    {
        $user = Auth::guard('web')->user();
        $response = $this->businessService->getBusinessOrderView($user);

        return view('app.order', ['view' => 'Order'] + $response);
    }

    public function get_order(Request $request, $order_id)
    {
        $user = Auth::guard('web')->user();
        $response = $this->businessService->getBusinessGetOrderView($user, $order_id);

        return view('app.view_order', ['view' => 'Order'] + $response);
    }

    public function wallet(Request $request)
    {
        $user = Auth::guard('web')->user();
        $response = $this->businessService->getBusinessWalletView($user);
        $transactions = collect($response['transactions']);
        unset($response['transactions']);

        $page = $request->get('page', 1);
        $perPage = 10;
        $transactions = new LengthAwarePaginator(
            $transactions->forPage($page, $perPage),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('app.wallet', ['view' => 'Order', 'transactions' => $transactions] +  $response);
    }

    public function fundWallet(Request $request)
    {
        try{
            $request->validate([
                'amount' => 'required|int|min:1000',
            ]);
        } catch (ValidationException $e) {
            return Redirect::route('business-wallet');
        }

        $user = Auth::guard('web')->user();

        if ($request->isMethod('post')) {
            $callbackUrl = URL::route('business-verify-card-transaction');
            $responseUrl = $this->businessService->initiateBusinessTransaction(
                $user,
                $request->input('amount'),
                $callbackUrl
            );
            return Redirect::to($responseUrl);
        }

        return Redirect::route('business-wallet');
    }

    public function verifyBusinessCardTransaction(Request $request)
    {
        try{
            $this->walletService->verifyCardTransaction($request->query());
        } catch(Exception $e){
        
        }
        return Redirect::route('business-wallet');
    }

    public function deleteCard(Request $request, $card_id)
    {
        $user = Auth::guard('web')->user();
        $card = $this->walletService->getUserCards($user);

        if (!$card) {
            return Redirect::route('business-wallet');
        }
        $card->delete();
        return Redirect::route('business-wallet');
    }
    public function settings(Request $request)
    {
        $user = Auth::guard('web')->user();
        $response = $this->businessService->getBusinessSettingsView($user);    
        $error = session('error') ?? null;    

        return view('app.settings', ['view' => 'Settings', "error" => $error] + $response);
    }

    public function updateWebhook(Request $request)
    {
        $error = null;
        try{
            $request->validate([
                'webhook_url' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            $error = $message[0];
            return Redirect::route('business-settings')->with('error', $error);
        }

        $user = Auth::guard('web')->user();
        $business = $this->businessService->getBusiness($user);
        $business->webhook_url = $request->webhook_url;
        $business->save();
        return Redirect::route('business-settings');
    }

    public function regenerateSecretKey(Request $request)
    {
        $user = Auth::guard('web')->user();
        $this->businessService->generateBusinessSecretKey($user);
        return Redirect::route('business-settings');
    }

    public function docsIndex(Request $request)
    {
        return view('app.docs', ["base_url"=> "api.feleexpress.com"]);
    }

}
