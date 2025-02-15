<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\MapService;
use App\Services\CustomerAddressService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterCustomerRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomAPIException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RetrieveCustomerResource;
use App\Http\Resources\FavouriteRiderResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\AddressResource;

class CustomerController extends Controller
{
    protected CustomerService $customerService;
    protected AuthService $authService;
    protected UserService $userService;

    public function __construct(
        CustomerService $customerService, 
        AuthService $authService,
        UserService $userService
    )
    {
        $this->customerService = $customerService;
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function registerCustomer(RegisterCustomerRequest $request): JsonResponse
    {
        $response = $this->customerService->registerCustomer($request);
        return ApiResponse::responseSuccess($response, 'Customer sign up successful');
    }

    public function verifyOtp(Request $request)
    {
        try{
            $request->validate([
                'code' => 'required|max:6',
                'email' => 'sometimes|required_without:phone_number|email',
                'phone_number' => 'sometimes|required_without:email|string|min:10|max:15',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        if ($request->phone_number && $request->email){
            throw new CustomAPIException("Only one of email or phone number can be submitted at a time");
        };

        if ($request->phone_number){
            $this->authService->verifyPhoneVerification($request->phone_number, $request->code);
        } else{
            $this->authService->verifyEmailVerification($request->email, $request->code);
        }
        
        return ApiResponse::responseSuccess([], 'Verification successful');       

    }

    public function resendOtp(Request $request)
    {
        try{
            $request->validate([
                'email' => 'sometimes|required_without:phone_number|email',
                'phone_number' => 'sometimes|required_without:email|string|min:10|max:15',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        if ($request->phone_number && $request->email){
            throw new CustomAPIException("Only one of email or phone number can be submitted at a time");
        };
        if ($request->phone_number){
            $this->authService->initiatePhoneVerification($request->phone_number);
        } else{
            $this->authService->initiateEmailVerification($request->email);
        }
        
        return ApiResponse::responseSuccess([], 'Otp sent');
    }

    public function completeSignup(Request $request)
    {
        try{
            $request->validate([
                'session_token' => 'required',
                'business_name' => 'required',
                'business_address' => 'required',
                'business_category' => 'required',
                'delivery_volume' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }

        $this->customerService->completeSignup($request);
        return ApiResponse::responseSuccess([], 'Customer sign up successful');
    }

    public function changePhoneNumer(Request $request)
    {
        try{
            $request->validate([
                'old_phone_number' => 'required',
                'new_phone_number' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $this->userService->changePhoneNumer($request->old_phone_number, $request->new_phone_number);
        return ApiResponse::responseSuccess([], 'Otp sent');
    }

    public function changeEmail(Request $request)
    {
        try{
            $request->validate([
                'old_email' => 'required',
                'new_email' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $this->userService->changeEmail($request->old_email, $request->new_email);
        return ApiResponse::responseSuccess([], 'Otp sent');
    }

    public function login(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $response = $this->customerService->login($request);
        return ApiResponse::responseSuccess($response, '');
    }

    public function customerInfo(Request $request)
    {
        $customer = $this->customerService->getCustomer($request->user());
        return ApiResponse::responseSuccess(new RetrieveCustomerResource($customer), '');
    }

    public function getCustomerFavouriteRider(Request $request)
    {
        $FavouriteRiders = $this->customerService->getCustomerFavouriteRider($request->user());
        return ApiResponse::responseSuccess(FavouriteRiderResource::collection($FavouriteRiders), 'Customer favourite rider');
    }

    public function completeBusinessCustomerSignup(Request $request)
    {
        try{
            $request->validate([
                'business_name' => 'required',
                'business_address' => 'required',
                'business_category' => 'required',
                'delivery_volume' => 'required|int',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $this->customerService->completeBusinessCustomerSignup($request, $request->user());
        return ApiResponse::responseSuccess([], 'Customer sign up successful');
    }

    public function updateProfile(Request $request)
    {
        try{
            $request->validate([
                'first_name' => 'nullable|string',
                'last_name' => 'nullable|string',
                'avatar' => 'nullable|file',
                'email' => 'nullable|email|unique:user,email,' . $request->user()->id,
                'phone_number' => 'nullable|string|min:10|max:15|unique:user,phone_number,' . $request->user()->id,
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $user = $this->customerService->updateProfile($request, $request->user());
        return ApiResponse::responseSuccess(new UserProfileResource($user), 'Customer updated successful');
    }
    
    public function addressInfo(Request $request)
    {
        try{
            $request->validate([
                'address' => 'nullable|string',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $address = $request->address ?? null;
        $latitude = $request->latitude ?? null;
        $longitude = $request->longitude ?? null;

        if (!(
            ($address && !$latitude && !$longitude) ||
            ($latitude && $longitude && !$address)
        )) {
            throw new CustomAPIException("Provide either 'address' or both 'latitude' and 'longitude', but not all three.");
        }
        
        if (!empty($address)) {
            $response = MapService::searchAddress($address);
        } else {
            $response = MapService::searchAddress("$latitude,$longitude");
        }
        return ApiResponse::responseSuccess($response, 'Address information');
    }

    public function createAddress(Request $request)
    {
        try{
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'direction' => 'nullable|string',
                'landmark' => 'nullable|string',
                'label' => 'nullable|string',    
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $response = CustomerAddressService::createCustomerAddress($request->user(), $request);
        return ApiResponse::responseSuccess(new AddressResource($response), 'Address created successfully');
    }

    public function getAddress(Request $request)
    {
        $response = CustomerAddressService::getCustomerAddress($request->user());
        return ApiResponse::responseSuccess(AddressResource::collection($response), 'Customer saved addresses');
    }

    public function destroyAddress(Request $request, $id)
    {
        CustomerAddressService::deleteCustomerAddress($request->user(), $id);
        return ApiResponse::responseSuccess([], 'Address deleted successfully');
    }

    public function updateAddress(Request $request, $id)
    {
        try{
            $request->validate([
                'direction' => 'nullable|string',
                'landmark' => 'nullable|string',
                'label' => 'nullable|string',    
                'formatted_address' => 'nullable|string',    
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors, $message[0]);
        }
        $response = CustomerAddressService::updateCustomerAddress($request->user(), $id, $request);
        return ApiResponse::responseSuccess(new AddressResource($response), 'Address updated successfully');
    }


}

