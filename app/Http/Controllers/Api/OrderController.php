<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\CustomerService;
use App\Services\UserService;
use App\Services\MapService;
use App\Services\CustomerAddressService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InitiateOrderRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomAPIException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\GetCustomerOrderResource;
use App\Http\Resources\OrderHistoryResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\AddressResource;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected CustomerService $customerService;
    protected UserService $userService;

    public function __construct(
        OrderService $orderService, 
        CustomerService $customerService,
        UserService $userService
    )
    {
        $this->orderService = $orderService;
        $this->customerService = $customerService;
        $this->userService = $userService;
    }

    public function getOrderHistory(Request $request): JsonResponse
    {
        $customer = $this->customerService->getCustomer($request->user());
        $orderQuery = $this->orderService->getOrderQuery(["customer_id" => $customer->id])->whereNotIn('status', ['PROCESSING_ORDER']);
        return ApiResponse::responsePaginate($orderQuery, $request, OrderHistoryResource::class);
    }

    public function getOngoingOrder(Request $request): JsonResponse
    {
        $customer = $this->customerService->getCustomer($request->user());
        $orderQuery = $this->orderService->getOrderQuery(["customer_id" => $customer->id])
        ->whereNotIn('status', ["ORDER_COMPLETED", "ORDER_CANCELLED", "PROCESSING_ORDER"]);
        return ApiResponse::responsePaginate($orderQuery, $request, GetCustomerOrderResource::class);
    }

    public function getCustomerOrder(Request $request): JsonResponse
    {
        $customer = $this->customerService->getCustomer($request->user());
        $orderQuery = $this->orderService->getOrderQuery(["customer_id" => $customer->id])
        ->whereNotIn('status', ['PROCESSING_ORDER']);
        return ApiResponse::responsePaginate($orderQuery, $request, GetCustomerOrderResource::class);
    }


    public function getOrder(Request $request, $order_id): JsonResponse
    {
        $customer = $this->customerService->getCustomer($request->user());
        $order = $this->orderService->getOrder($order_id, ["customer_id" => $customer->id]);
        return ApiResponse::responseSuccess(new OrderResource($order));
    }

    public function initiateOrder(InitiateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->orderService->initiateOrder(Auth::user(), $data);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }

    public function placeOrder(Request $request, $order_id)
    {
        try{
            $request->validate([
                'note_to_driver' => 'nullable|string',
                'promo_code' => 'nullable|string|max:20',
                'payment_by' => 'required|in:SENDER,RECIPIENT',
                'payment_method' => 'required|in:WALLET,CASH',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $response = $this->orderService->placeOrder(Auth::user(), $order_id, $request);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }

    public function addRiderTip(Request $request, $order_id)
    {
        try{
            $request->validate([
                'tip_amount' => 'required|int',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $response = $this->orderService->addRiderTip(Auth::user(), $order_id, $request->tip_amount);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }

    public function rateRider(Request $request, $order_id)
    {
        try{
            $request->validate([
                'rating' => 'required|int',
                'remark' => 'required|string',
                'favorite_rider' => 'required|bool',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $response = $this->orderService->rateRider(Auth::user(), $order_id, $request);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }

    public function cancelOrder(Request $request, $order_id)
    {
        try{
            $request->validate([
                'reason' => 'required|string',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $response = $this->orderService->customerCancelOrder(Auth::user(), $order_id, $request->rider_id);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }

    

}

