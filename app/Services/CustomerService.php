<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\OTPVerification;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\StaticFunction;
use App\Exceptions\CustomAPIException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RetrieveCustomerResource;
use App\Models\User;
use App\Models\FavoriteRider;
use App\Helpers\S3Uploader;

class CustomerService
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

    public function createCustomer($user, $data){
        $data['user_id'] = $user->id;
        $customer = Customer::create($data);
        return $customer;
    }

    public function getCustomer($user)
    {
        $customer = Customer::where('user_id', $user->id);
        if (!$customer) {
            throw new CustomAPIException('Customer not found', 404);
        }
        return $customer->first();
    }
   
    public function registerCustomer($request)
    {
        $email = $request->email;
        $phone_number = $request->phone_number;
        $fullname = $request->fullname;
        $password = $request->password;
        $receive_email_promotions = $request->receive_email_promotions;
        $customer_type = $request->customer_type;
        $business_name = $request->business_name;
        $business_address = $request->business_address;
        $business_category = $request->business_category;
        $delivery_volume = $request->delivery_volume;
        $one_signal_id = $request->one_signal_id;
        $referral_code = $request->referral_code;
        $first_name = explode(" ", $fullname)[0];
        $last_name = explode(" ", $fullname)[1];
        
        $user = $this->userService->createUser([
            'email' => $email,
            'phone_number' => $phone_number,
            'user_type' => "CUSTOMER",
            'first_name' => $first_name,
            'last_name' => $last_name,
            'password' => $password,
            'receive_email_promotions' => $receive_email_promotions,
            'one_signal_id' => $one_signal_id,
            'referral_code' => $referral_code,
        ]);
        // $user->assignRole('customer');
        $this->createCustomer(
            $user, 
            [
                'customer_type' => $customer_type,
                'business_name' => $business_name,
                'business_address' => $business_address,
                'business_category' => $business_category,
                'delivery_volume' => $delivery_volume
            ]
        );
        
        $this->authService->initiatePhoneVerification($phone_number);

        if ($customer_type == "BUSINESS"){
            $session_token = "SESSION-".StaticFunction::generateCode(25);
            OTPVerification::create([
                'phone_number' => $phone_number,
                'email' => $email,
                'otp' => $session_token,
            ]);
            return ["session_token" => $session_token];
        }
        return array();
    }
    
    public function completeSignup($request){
        $session_token = $request->session_token;
        $business_name = $request->business_name;
        $business_address = $request->business_address;
        $business_category = $request->business_category;
        $delivery_volume = $request->delivery_volume ?? "";

        $sessionToken = OTPVerification::where('otp', $session_token)->first();
        if (!$sessionToken) {
            throw new CustomAPIException('Invalid session token', 401);
        }
        $email = $sessionToken->email;
        $phone_number = $sessionToken->phone_number;
        $user = $this->userService->getUser($phone_number, $email);
        
        $customer = Customer::where("user_id", $user->id)->first();
        if ($customer->customer_type != "BUSINESS"){
            throw new CustomAPIException('User not a business customer.', 401);
        }
        $customer->business_name = $business_name;
        $customer->business_address = $business_address;
        $customer->business_category = $business_category;
        $customer->delivery_volume = $delivery_volume;
        $customer->save();

        $sessionToken->delete();
        return true;
    }

    public function login($request)
    {
        $user = $this->userService->authenticateUser($request->get('email'), $request->get('password'));            
        $token = Auth::login($user);
        $customer = $this->getCustomer($user);
        return [
            "customer" => new RetrieveCustomerResource($customer),
            "token" => ["access" => $token],
        ];
    }

    public function getCustomerFavouriteRider($user)
    {
        $customer = $this->getCustomer($user);
        return FavoriteRider::where('customer_id', $customer->id)->get();
    }
    
    public function completeBusinessCustomerSignup($request, $user){
        $business_name = $request->business_name;
        $business_address = $request->business_address;
        $business_category = $request->business_category;
        $delivery_volume = $request->delivery_volume ?? "";

        $customer = $this->getCustomer($user);
        if ($customer->customer_type != "BUSINESS"){
            throw new CustomAPIException('User not a business customer.', 401);
        }
        $customer->business_name = $business_name;
        $customer->business_address = $business_address;
        $customer->business_category = $business_category;
        $customer->delivery_volume = $delivery_volume;
        $customer->save();

        return true;
    }

    public function updateProfile($request, User $user)
    {
        $data = $request->only(['first_name', 'last_name', 'email', 'phone_number']);

        $avatarFile = $request->file('avatar');
        if ($avatarFile) {
            $s3Uploader = new S3Uploader('/available-vehicles');
            if ($user->avatar_url){
                $s3Uploader->hardDeleteObject($user->avatar_url);
            }
            $keyname = 'avatars/{$user->id}/' . $avatarFile->getClientOriginalName();    
            $fileUrl = $s3Uploader->uploadFileObject($avatarFile, $keyname);
            $user->avatar_url = $fileUrl;
        }

        if (!empty($data['first_name'])) {
            $user->first_name = $data['first_name'];
        }
        if (!empty($data['last_name'])) {
            $user->last_name = $data['last_name'];
        }
        if (!empty($data['email'])) {
            $user->email = $data['email'];
            $user->email_verified_at = false;
        }
        if (!empty($data['phone_number'])) {
            $user->phone_number = $data['phone_number'];
            $user->phone_verified_at = false;
        }

        $user->save();
        return $user;
    }


}
