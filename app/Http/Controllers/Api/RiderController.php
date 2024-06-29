<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\VehicleInformationResource;
use App\Http\Controllers\Controller;
use App\Services\RiderService;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\MapService;
use App\Services\CustomerAddressService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterRiderRequest;
use App\Http\Requests\DocumentUploadRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomAPIException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RetrieveRiderResource;
use App\Http\Resources\RiderHomepageResource;
use App\Http\Resources\RetrieveKycResource;


class RiderController extends Controller
{
    protected RiderService $riderService;
    protected AuthService $authService;
    protected UserService $userService;

    public function __construct(
        RiderService $riderService, 
        AuthService $authService,
        UserService $userService
    )
    {
        $this->riderService = $riderService;
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function availableCities()
    {
        $available = ["available_cities" => ["MAKURDI", "GBOKO", "OTUKPO"]];
        return ApiResponse::responseSuccess($available, 'Available cities');
    }

    public function registerRider(RegisterRiderRequest $request): JsonResponse
    {
        $response = $this->riderService->registerRider($request);
        return ApiResponse::responseSuccess($response, 'Rider sign up successful');
    }

    public function verifyOtp(Request $request)
    {
        try{
            $request->validate([
                'code' => 'required|string|max:6',
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
        
        $response = $this->riderService->login($request);
        return ApiResponse::responseSuccess($response, '');
    }

    public function riderInfo(Request $request)
    {
        $rider = $this->riderService->getRider($request->user());
        return ApiResponse::responseSuccess(new RetrieveRiderResource($rider), 'Rider info');
    }

    public function getRiderHome(Request $request)
    {
        $rider = $this->riderService->getRider($request->user());
        return ApiResponse::responseSuccess(new RiderHomepageResource($rider), 'Rider home');
    }

    public function getRiderPerformance(Request $request)
    {   
        $response = $this->riderService->getRiderPerformance($request, $request->user());
        return ApiResponse::responseSuccess($response, 'Rider performance');
    }

    public function updateRiderVehicle(Request $request)
    {
        try{
            $request->validate([
                'vehicle_make' => 'nullable|string',
                'vehicle_model' => 'nullable|string',
                'vehicle_plate_number' => 'nullable|string',
                'vehicle_color' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $rider = $this->riderService->updateRiderVehicle($request, $request->user());
        return ApiResponse::responseSuccess(new VehicleInformationResource($rider), 'Vehicle information updated');
    }
    
    public function getRiderVehicle(Request $request)
    {
        $rider = $this->riderService->getRider($request->user());
        return ApiResponse::responseSuccess(new VehicleInformationResource($rider), 'Vehicle information');
    }

    public function setRiderDuty(Request $request)
    {
        try{
            $request->validate([
                'on_duty' => 'required|bool',    
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $rider = $this->riderService->setRiderDuty($request->user(), $request->on_duty);
        return ApiResponse::responseSuccess([], 'rider duty status set');
    }

    public function getKycInfo(Request $request)
    {
        $rider = $this->riderService->getRider($request->user());
        return ApiResponse::responseSuccess(new RetrieveKycResource($rider), 'Kyc Info');
    }

    public function submitKyc(Request $request)
    {
        try{
            $request->validate([
                'vehicle_id' => 'required|string',
                'vehicle_plate_number' => 'nullable|string|max:20',
                'vehicle_color' => 'nullable|string|max:20',
                'vehicle_make' => 'nullable|string|max:50',
                'vehicle_model' => 'nullable|string|max:50',
                'vehicle_photo' => 'nullable',
                'passport_photo' => 'nullable',
                'government_id' => 'nullable',
                'guarantor_letter' => 'nullable',
                'address_verification' => 'nullable',
                'driver_license' => 'nullable',
                'insurance_certificate' => 'nullable',
                'certificate_of_vehicle_registration' => 'nullable',
                'authorization_letter' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $rider = $this->riderService->submitKyc($request->user(), $request);
        return ApiResponse::responseSuccess(new RetrieveKycResource($rider), 'Kyc submitted');
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'document_type' => 'required|string|in:vehicle_photo,passport_photo,government_id,guarantor_letter,address_verification,driver_license,insurance_certificate,certificate_of_vehicle_registration,authorization_letter',
                // 'documents' => 'required',
                'documents' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        // print_r($request);

        $documentType = $request->document_type;
        $documents = $request->file('documents');

        $rider = $this->riderService->getRider($request->user());

        $this->riderService->addRiderDocument($rider, $documentType, $documents);
        return ApiResponse::responseSuccess([], 'Documents uploaded successfully');
    }

}

