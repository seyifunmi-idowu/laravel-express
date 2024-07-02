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

class BusinessService
{
    protected UserService $userService;
    protected AuthService $authService;

    public function __construct(
        UserService $userService,
        AuthService $authService
    )
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function createBusiness($user, $data){
        $data['user_id'] = $user->id;
        $business = Business::create($data);
        return $business;
    }

    public function getBusiness($user)
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

    // public static function initiateTransaction($user, $amount, $callbackUrl)
    // {
    //     $transactionObj = TransactionService::getTransaction([
    //         'user_id' => $user->id,
    //         'amount' => $amount,
    //         'transaction_type' => 'CREDIT',
    //         'transaction_status' => 'PENDING',
    //         'pssp' => 'PAYSTACK',
    //     ])->first();

    //     if ($transactionObj) {
    //         $authorizationUrl = $transactionObj->pssp_meta_data['authorization_url'];
    //         return $authorizationUrl;
    //     }
    //     $paystackService = new PaystackService();
    //     $paystackResponse = $paystackService->initializePayment($user->email, $amount, $callbackUrl);
    //     $authorizationUrl = $paystackResponse['data']['authorization_url'];
    //     $reference = $paystackResponse['data']['reference'];

    //     $transactionObj = Transaction::create([
    //         'transaction_type' => 'CREDIT',
    //         'transaction_status' => 'PENDING',
    //         'amount' => $amount,
    //         'user_id' => $user->id,
    //         'reference' => $reference,
    //         'pssp' => 'PAYSTACK',
    //         'payment_category' => 'FUND_WALLET',
    //         'pssp_meta_data' => json_encode($paystackResponse['data']),
    //         'currency' => "₦",
    //     ]);

    //     return $authorizationUrl;
    // }

    public function getBusinessUserSecretKey($user)
    {
        $business = $this->getBusiness($user);
        if (is_null($business->e_secret_key)) {
            return null;
        }
        $encryptedAccessToken = $business->e_secret_key;
        return EncryptionClass::decryptData($encryptedAccessToken);
    }
   
    
    // public function completeSignup($request){
    //     $session_token = $request->session_token;
    //     $business_name = $request->business_name;
    //     $business_address = $request->business_address;
    //     $business_category = $request->business_category;
    //     $delivery_volume = $request->delivery_volume ?? "";

    //     $sessionToken = OTPVerification::where('otp', $session_token)->first();
    //     if (!$sessionToken) {
    //         throw new CustomAPIException('Invalid session token', 401);
    //     }
    //     $email = $sessionToken->email;
    //     $phone_number = $sessionToken->phone_number;
    //     $user = $this->userService->getUser($phone_number, $email);
        
    //     $customer = Customer::where("user_id", $user->id)->first();
    //     if ($customer->customer_type != "BUSINESS"){
    //         throw new CustomAPIException('User not a business customer.', 401);
    //     }
    //     $customer->business_name = $business_name;
    //     $customer->business_address = $business_address;
    //     $customer->business_category = $business_category;
    //     $customer->delivery_volume = $delivery_volume;
    //     $customer->save();

    //     $sessionToken->delete();
    //     return true;
    // }

    // public function login($request)
    // {
    //     $user = $this->userService->authenticateUser($request->get('email'), $request->get('password'));            
    //     $token = Auth::login($user);
    //     $customer = $this->getCustomer($user);
    //     return [
    //         "customer" => new RetrieveCustomerResource($customer),
    //         "token" => ["access" => $token],
    //     ];
    // }

    // public function getCustomerFavouriteRider($user)
    // {
    //     $customer = $this->getCustomer($user);
    //     return FavoriteRider::where('customer_id', $customer->id)->get();
    // }
    
    // public function completeBusinessCustomerSignup($request, $user){
    //     $business_name = $request->business_name;
    //     $business_address = $request->business_address;
    //     $business_category = $request->business_category;
    //     $delivery_volume = $request->delivery_volume ?? "";

    //     $customer = $this->getCustomer($user);
    //     if ($customer->customer_type != "BUSINESS"){
    //         throw new CustomAPIException('User not a business customer.', 401);
    //     }
    //     $customer->business_name = $business_name;
    //     $customer->business_address = $business_address;
    //     $customer->business_category = $business_category;
    //     $customer->delivery_volume = $delivery_volume;
    //     $customer->save();

    //     return true;
    // }

    // public function updateProfile($request, User $user)
    // {
    //     $data = $request->only(['first_name', 'last_name', 'email', 'phone_number']);

    //     $avatarFile = $request->file('avatar');
    //     if ($avatarFile) {
    //         $s3Uploader = new S3Uploader('/available-vehicles');
    //         if ($user->avatar_url){
    //             $s3Uploader->hardDeleteObject($user->avatar_url);
    //         }
    //         $keyname = 'avatars/{$user->id}/' . $avatarFile->getClientOriginalName();    
    //         $fileUrl = $s3Uploader->uploadFileObject($avatarFile, $keyname);
    //         $user->avatar_url = $fileUrl;
    //     }

    //     if (!empty($data['first_name'])) {
    //         $user->first_name = $data['first_name'];
    //     }
    //     if (!empty($data['last_name'])) {
    //         $user->last_name = $data['last_name'];
    //     }
    //     if (!empty($data['email'])) {
    //         $user->email = $data['email'];
    //         $user->email_verified_at = false;
    //     }
    //     if (!empty($data['phone_number'])) {
    //         $user->phone_number = $data['phone_number'];
    //         $user->phone_verified_at = false;
    //     }

    //     $user->save();
    //     return $user;
    // }


}
