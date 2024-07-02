<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\GetCurrentOrderResource;
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
use App\Http\Resources\GetOrderResource;
use App\Http\Resources\RiderOrderResource;
use App\Http\Resources\BusinessOrderResource;
use App\Services\RiderService;
use App\Services\BusinessService;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected CustomerService $customerService;
    protected UserService $userService;
    protected RiderService $riderService;
    protected BusinessService $businessService;

    public function __construct(
        OrderService $orderService, 
        CustomerService $customerService,
        UserService $userService,
        RiderService $riderService,
        BusinessService $businessService
    )
    {
        $this->orderService = $orderService;
        $this->customerService = $customerService;
        $this->userService = $userService;
        $this->riderService = $riderService;
        $this->businessService = $businessService;
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

    
    public function listRiderOrder(Request $request): JsonResponse
    {
        $rider = $this->riderService->getRider($request->user());
        $orderQuery = $this->orderService->getOrderQuery(["rider_id" => $rider->id]);
        return ApiResponse::responsePaginate($orderQuery, $request, GetOrderResource::class);
    }

    public function getRiderCompletedOrder(Request $request): JsonResponse
    {
        $orderQuery = $this->orderService->getCompletedOrder($request);
        return ApiResponse::responsePaginate($orderQuery, $request, GetCurrentOrderResource::class);
    }

    public function getRiderCurrentOrder(Request $request): JsonResponse
    {
        $rider = $this->riderService->getRider($request->user());
        $orderQuery = $this->orderService->getOrderQuery(['rider_id' => $rider->id])->whereIn('status', [
            "RIDER_ACCEPTED_ORDER",
            "RIDER_AT_PICK_UP",
            "RIDER_PICKED_UP_ORDER",
            "ORDER_ARRIVED",
        ]);
        return ApiResponse::responsePaginate($orderQuery, $request, GetCurrentOrderResource::class);
    }

    public function getRiderFailedOrder(Request $request): JsonResponse
    {
        $rider = $this->riderService->getRider($request->user());
        $orderQuery = $this->orderService->getOrderQuery(['rider_id' => $rider->id])->whereIn('status', [
            "ORDER_CANCELLED",
        ]);
        return ApiResponse::responsePaginate($orderQuery, $request, GetCurrentOrderResource::class);
    }

    public function getRiderNewOrder(Request $request): JsonResponse
    {
        $orderQuery = $this->orderService->getNewOrder($request->user());
        return ApiResponse::responsePaginate($orderQuery, $request, GetCurrentOrderResource::class);
    }

    public function getRiderOrder(Request $request, $order_id): JsonResponse
    {
        $rider = $this->riderService->getRider($request->user());
        $order = $this->orderService->getOrder($order_id, ['rider_id' => $rider->id]);
        return ApiResponse::responseSuccess(new RiderOrderResource($order), 'Order Information');
    }

    public function riderAcceptCustomerOrder(Request $request, $order_id): JsonResponse
    {
        $order = $this->orderService->riderAcceptCustomerOrder($request->user(), $order_id);
        return ApiResponse::responseSuccess(new RiderOrderResource($order), 'Order Information');
    }

    public function riderAtPickup(Request $request, $order_id): JsonResponse
    {
        $this->orderService->riderAtPickup($request->user(), $order_id);
        return ApiResponse::responseSuccess([], 'Order Updated');
    }

    public function riderPickupOrder(Request $request, $order_id): JsonResponse
    {
        try{
            $request->validate([
                'proof' => 'nullable',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $proof = $request->file('proof');
        $this->orderService->riderPickupOrder($order_id, $request->user(), $proof);
        return ApiResponse::responseSuccess([], 'Order Updated');
    }

    public function riderFailedPickup(Request $request, $order_id): JsonResponse
    {
        try{
            $request->validate([
                'reason' => 'required',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $reason = $request->reason;
        $this->orderService->riderFailedPickup($order_id, $request->user(), $reason);
        return ApiResponse::responseSuccess([], 'Order Updated');
    }

    public function riderAtDestination(Request $request, $order_id): JsonResponse
    {
        $this->orderService->riderAtDestination($order_id, $request->user());
        return ApiResponse::responseSuccess([], 'Order Updated');
    }

    public function riderMadeDelivery(Request $request, $order_id): JsonResponse
    {
        try{
            $request->validate([
                'proof' => 'nullable',

            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $proof = $request->file('proof');
        $this->orderService->riderMadeDelivery($order_id, $request->user(), $proof);
        return ApiResponse::responseSuccess([], 'Order Updated');
    }

    public function riderReceivePayment(Request $request, $order_id): JsonResponse
    {
        $this->orderService->riderReceivedPayment($order_id, $request->user());
        return ApiResponse::responseSuccess([], 'Order Updated');
    }

    public function listBusinessOrder(Request $request): JsonResponse
    {
        $business = $this->businessService->getBusiness($request->user());
        $orderQuery = $this->orderService->getOrderQuery(['business_id' => $business->id]);
        return ApiResponse::responsePaginate($orderQuery, $request, GetCustomerOrderResource::class);
    }

    public function getBusinessOrder(Request $request, $order_id): JsonResponse
    {
        $business = $this->businessService->getBusiness($request->user());
        $order = $this->orderService->getOrder($order_id, ['business_id' => $business->id]);
        return ApiResponse::responseSuccess(new BusinessOrderResource($order), 'Order Information');
    }

    public function initiateBusinessOrder(InitiateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->orderService->initiateOrder(Auth::user(), $data, false);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }

    public function placeBusinessOrder(Request $request, $order_id)
    {
        try{
            $request->validate([
                'note_to_driver' => 'nullable|string'
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        $response = $this->orderService->placeBusinessOrder(Auth::user(), $order_id, $request);
        return ApiResponse::responseSuccess($response, 'Order Information');
    }


}

