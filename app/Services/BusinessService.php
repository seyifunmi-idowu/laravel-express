<?php

namespace App\Services;

use App\Models\Business;
use App\Models\OTPVerification;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\EncryptionClass;
use App\Exceptions\CustomAPIException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RetrieveCustomerResource;
use App\Models\User;
use App\Models\Transaction;
use App\Helpers\S3Uploader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Card;
use App\Models\BankAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class BusinessService
{
    protected UserService $userService;
    protected AuthService $authService;
    protected PaystackService $paystackService;

    public function __construct(
        UserService $userService,
        AuthService $authService,
        PaystackService $paystackService
    )
    {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->paystackService = $paystackService;
    }

    public function createBusiness($user, $data){
        $data['user_id'] = $user->id;
        $business = Business::create($data);
        return $business;
    }

    public function getBusiness(User $user)
    {
        $business = Business::where('user_id', $user->id);
        if (!$business) {
            throw new CustomAPIException('Business not found', 404);
        }
        return $business->first();
    }
   
    public function updateWebhook($user, $request)
    {
        $business = $this->getBusiness($user);
        $business->webhook_url = $request->webhook_url;
        $business->save();
    }   

    public function getBusinessUserSecretKey($user)
    {
        $business = $this->getBusiness($user);
        if (is_null($business->e_secret_key)) {
            return null;
        }
        $encryptedAccessToken = $business->e_secret_key;
        return EncryptionClass::decryptData($encryptedAccessToken);
    }

    public function generateBusinessSecretKey($user)
    {    
        $business = $this->getBusiness($user);
        // Auth::logout($this->getBusinessUserSecretKey($user));
        JWTAuth::invalidate($this->getBusinessUserSecretKey($user));
        $token = Auth::login($user);
        $encryptedToken = EncryptionClass::encryptData($token);
        $business->e_secret_key = $encryptedToken;
        $business->save();
        return true;
    }
   
    public function registerBusinessUser(array $data)
    {
        $data = collect($data);
        $email = $data->get('email');
        $phone_number = $data->get('phone_number');
        $business_name = $data->get('business_name');
        $password = $data->pull('password');

        DB::transaction(function () use ($email, $phone_number, $business_name, $password) {
            $instance_user = $this->userService->createUser([
                'email' => $email,
                'first_name' => $business_name,
                'phone_number' => $phone_number,
                'user_type' => 'BUSINESS',
                'password' => $password,
                'referral_code' => null,
            ]);

            $this->createBusiness($instance_user, [
                'business_name' => $business_name,
            ]);

            $this->authService->initiateEmailVerification($email);
        });

        return [];
    }

    public function loginBusinessUser(array $data)
    {
        $email = $data['email'];
        $password = $data['password'];
        try{
            $user = $this->userService->authenticateUser($email, $password);       
        } catch(CustomAPIException $e){
            Session::flash('error', $e->getMessage());
            return false;
        }
        if ($user->user_type != "BUSINESS"){
            Auth::logout();
            return false;
        }
        $credentials = ['email' => $email, 'password' => $password];
        Auth::guard('web')->attempt($credentials);
        // Auth::login($user);

        $user->last_login = now();
        $user->save();
        return true;
    }

    public static function verifyBusinessUserEmail($request, array $data)
    {
        $user = UserService::getUserInstance(['email' => $data['email']]);
        if (!$user) {
            Session::flash('error', 'User not found.');
            return;
        }

        try {
            AuthService::validateEmailVerification($data['email'], $data['code']);
            Auth::login($user);
            $user->last_login = now();
            $user->save();
        } catch (CustomAPIException $e) {
            Session::flash('error', $e->getMessage());
        } catch (CustomFieldValidationException $e) {
            Session::flash('error', $e->getErrors()['code'][0]);
        }
    }

    public function getBusinessDashboardView($user)
    {
        $today = Carbon::today();
        $business = $this->getBusiness($user);

        // Fetch orders related to the business user
        $orders = Order::where('business_id', $business->id)->orderBy('created_at', 'desc')->with('rider')->take(10)->get();
        $totalOrders = Order::where('business_id', $business->id)->orderBy('created_at', 'desc')->count();
        $todayOrders = Order::where('business_id', $business->id)->whereDate('created_at', $today)->count();        
        $walletBalance = $user->wallet->balance;
        
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        $data = [
            'orders' => $orders,
            'total_orders' => $totalOrders,
            'today_orders' => $todayOrders,
            'wallet_balance' => $walletBalance,
            'transactions' => $transactions,
        ];
        
        return $data;
    }

    public function getBusinessOrderView($user)
    {
        $business = $this->getBusiness($user);
        $orders = Order::where('business_id', $business->id)->orderBy('created_at', 'desc')->with('rider')->take(10)->get();
        
        $data = [
            'orders' => $orders,
        ];
        
        return $data;
    }

    public function getBusinessGetOrderView($user, $order_id)
    {
        $business = $this->getBusiness($user);
        $order = Order::where(['id'=> $order_id, 'business_id'=> $business->id])->first();
        $distanceInKm = $order->distance / 1000;
        $formattedDistance = number_format($distanceInKm, 1);

        $timeInMinutes = $order->duration / 60;
        $formattedTime = number_format($timeInMinutes, 0);

        $data = [
            'order' => $order,
            "distance"=>  "{$formattedDistance} km",
            "duration" => "{$formattedTime} mins",
        ];
        
        return $data;
    }

    public function getKmInWord(int $distanceInMeters): string
    {
        $distanceInKm = $distanceInMeters / 1000;
        $formattedDistance = number_format($distanceInKm, 1);
        return "{$formattedDistance} km";
    }

    public function getTimeInWord(int $timeInSeconds): string
    {
        $timeInMinutes = $timeInSeconds / 60;
        $formattedTime = number_format($timeInMinutes, 0);
        return "{$formattedTime} mins";
    }

    public function getBusinessWalletView($user)
    {
        $walletBalance = $user->wallet->balance;        
        $bankAccounts = BankAccount::where('user_id', $user->id)->get();
        $cards = Card::where('user_id', $user->id)->get();
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = [
            'cards' => $cards,
            'bankAccounts' => $bankAccounts,
            'wallet_balance' => $walletBalance,
            'transactions' => $transactions,
        ];
        
        return $data;
    }

    public function initiateBusinessTransaction($user, $amount = 100, $callbackUrl)
    {
        $transaction = Transaction::where([
            'user_id' => $user->id,
            'amount' => $amount,
            'transaction_type' => 'CREDIT',
            'transaction_status' => 'PENDING',
            'pssp' => 'PAYSTACK',
        ])->first();

        if ($transaction) {
            $authorizationUrl = $transaction->pssp_meta_data['authorization_url'];
            return $authorizationUrl;
        }

        $paystackResponse = $this->paystackService->initializePayment($user->email, $amount, "NGN", $callbackUrl);
        $authorizationUrl = $paystackResponse['data']['authorization_url'];
        $reference = $paystackResponse['data']['reference'];

        $transactionData = [
            'transaction_type' => 'CREDIT',
            'transaction_status' => 'PENDING',
            'amount' => $amount,
            'user_id' => $user->id,
            'reference' => $reference,
            'pssp' => 'PAYSTACK',
            'payment_category' => 'FUND_WALLET',
            'pssp_meta_data' => $paystackResponse['data'],
            'currency' => "₦",
        ];

        Transaction::create($transactionData);

        return $authorizationUrl;
    }

    public function getBusinessSettingsView($user)
    {
        $business = $this->getBusiness($user);
        
        $data = [
            'secret_key' => $this->getBusinessUserSecretKey($user),
            'current_webhook_url' => $business->webhook_url,
        ];
        
        return $data;
    }


}
