<?php

namespace App\Services;

use App\Models\Rider;
use App\Models\OrderTimeline;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\StaticFunction;
use App\Helpers\FeleWebhook;
use App\Exceptions\CustomAPIException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RetrieveRiderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\FavoriteRider;
use App\Models\User;
use App\Models\RiderRating;
use App\Models\RiderCommission;
use App\Models\Transaction;
use App\Helpers\S3Uploader;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected UserService $userService;
    protected CustomerService $customerService;
    protected RiderService $riderService;
    protected NotificationService $notificationService;
    protected WalletService $walletService;
    protected PaystackService $paystackService;
    protected BusinessService $businessService;

    public function __construct(
        UserService $userService,
        CustomerService $customerService,
        RiderService $riderService,
        NotificationService $notificationService,
        WalletService $walletService,
        PaystackService $paystackService,
        BusinessService $businessService
    )
    {
        $this->userService = $userService;
        $this->customerService = $customerService;
        $this->riderService = $riderService;
        $this->notificationService = $notificationService;
        $this->walletService = $walletService;
        $this->paystackService = $paystackService;
        $this->businessService = $businessService;
    }

    public function getOrder($orderId, $conditions = [], $raise404 = true)
    {
        $order = Order::where('order_id', $orderId)->where($conditions)->first();
        if (is_null($order) && $raise404) {
            throw new CustomAPIException("Order not found.", 404);
        }
        return $order;
    }

    public function getOrderQuery($conditions = [])
    {
        return Order::where($conditions);
    }

    public function getOrderTimeline($order)
    {
        return OrderTimeline::where('order_id', $order->id)->orderBy('created_at', 'desc')->get();
    }

    public function getNewOrder($user)
    {
        $rider = $this->riderService-> getRider($user);
        return Order::where(function ($query) use ($rider) {
            $query->where('rider_id', $rider->id)->where('status', 'PENDING_RIDER_CONFIRMATION')
                  ->orWhereNull('rider_id')->where('status', 'PENDING')->where('vehicle_id', $rider->vehicle_id);
        });
    }

    public function getCompletedOrder($request)
    {
        $createdAt = $request->query('created_at');
        $timeframe = $request->query('timeframe');

        $rider = $this->riderService-> getRider($request->user());
        $orderQuery = Order::where('rider_id', $rider->id)->where('status', 'ORDER_COMPLETED');

        if ($createdAt) {
            $startDate = Carbon::parse($createdAt);
            $endDate = $startDate->copy()->addDay();
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($timeframe) {
            $timeframe = strtolower($timeframe);
            if ($timeframe == 'today') {
                $orderQuery->whereDate('updated_at', Carbon::today());
            } elseif ($timeframe == 'yesterday') {
                $orderQuery->whereDate('updated_at', Carbon::yesterday());
            }
        }

        return $orderQuery;
    }

    public function getCurrentOrderQuery($user)
    {
        return Order::where('rider_id', $user->id)
            ->whereIn('status', [
                'RIDER_ACCEPTED_ORDER',
                'RIDER_AT_PICK_UP',
                'RIDER_PICKED_UP_ORDER',
                'ORDER_ARRIVED',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFailedOrder($user)
    {
        return Order::where('rider_id', $user->id)
            ->whereIn('status', ['ORDER_CANCELLED'])
            ->get();
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


    public function createOrder($orderId, $customer, $data)
    {
        $pickup = $data['pickup'];
        $delivery = $data['delivery'];
        $totalDuration = $data['total_duration'] ?? null;
        $totalDistance = $data['total_distance'] ?? null;
        $stopOvers = $data['stop_overs'] ?? [];
        $vehicleId = $data['vehicle_id'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;
        $paymentBy = $data['payment_by'] ?? null;
        $timeline = $data['timeline'];
        $firstTimelineEntry = $timeline[0];
        $toData = $firstTimelineEntry['to'] ?? [];
        $fromData = $firstTimelineEntry['from'] ?? [];
        $pickupLongitude = $fromData['longitude'] ?? null;
        $pickupLatitude = $fromData['latitude'] ?? null;
        $pickupName = $fromData['name'] ?? null;
        $deliveryLongitude = $toData['longitude'] ?? null;
        $deliveryLatitude = $toData['latitude'] ?? null;
        $deliveryName = $toData['name'] ?? null;

        if ($pickup['save_address'] ?? false) {
            $this->saveAddress($customer, $pickup);
        }
        if ($delivery['save_address'] ?? false) {
            $this->saveAddress($customer, $delivery);
        }
        foreach ($stopOvers as $stopOver) {
            if ($stopOver['save_address'] ?? false) {
                $this->saveAddress($customer, $stopOver);
            }
        }

        $orderMetaData = [
            'note_to_driver' => $data['note_to_driver'] ?? null,
            'promo_code' => $data['promo_code'] ?? null,
            'timeline' => $timeline,
            'pickup' => $pickup,
            'delivery' => $delivery,
        ];

        return Order::create([
            'customer_id' => $customer->id,
            'vehicle_id' => VehicleService::getVehicle($vehicleId)->id,
            'order_id' => $orderId,
            'status' => 'PROCESSING_ORDER',
            'pickup_number' => $pickup['contact_phone_number'] ?? null,
            'pickup_contact_name' => $pickup['contact_name'] ?? null,
            'pickup_location' => $pickup['address'],
            'pickup_name' => $pickup['name'] ?? $pickupName,
            'pickup_location_longitude' => $pickup['longitude'] ?? $pickupLongitude,
            'pickup_location_latitude' => $pickup['latitude'] ?? $pickupLatitude,
            'delivery_number' => $delivery['contact_phone_number'] ?? null,
            'delivery_contact_name' => $delivery['contact_name'] ?? null,
            'delivery_location' => $delivery['address'],
            'delivery_name' => $delivery['name'] ?? $deliveryName,
            'delivery_location_longitude' => $delivery['longitude'] ?? $deliveryLongitude,
            'delivery_location_latitude' => $delivery['latitude'] ?? $deliveryLatitude,
            'order_stop_overs_meta_data' => json_encode($stopOvers),
            'total_amount' => $data['total_price'],
            'tip_amount' => $data['tip_amount'] ?? null,
            'distance' => $totalDistance,
            'duration' => $totalDuration,
            'order_meta_data' => json_encode($orderMetaData),
            'payment_method' => $paymentMethod,
            'payment_by' => $paymentBy,
            'order_by' => 'CUSTOMER',
            'paid' => false,
            'paid_fele' => false,
            'fele_amount' => 0
        ]);
    }

    public function saveAddress($customer, $data)
    {
        Address::create([
            'customer_id' => $customer->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'formatted_address' => $data['address'],
            'meta_data' => json_encode($data),
        ]);
    }

    public function initiateOrder($user, array $data, bool $isCustomerOrder = true)
    {
        $pickup = $data['pickup'];
        $delivery = $data['delivery'];
        $stopOvers = $data['stop_overs'] ?? [];
        $vehicleId = $data['vehicle_id'];
        $pickupLatitude = $pickup['latitude'];
        $pickupLongitude = $pickup['longitude'];
        $deliveryLatitude = $delivery['latitude'];
        $deliveryLongitude = $delivery['longitude'];
        $totalPrice = 0;
        $totalDistance = 0;
        $totalDuration = 0;
        $timeline = [];
        $index = 1;

        $pickupAddressInfo = MapService::searchAddress("{$pickupLatitude},{$pickupLongitude}");
        if (count($pickupAddressInfo) < 1) {
            throw new CustomAPIException("Unable to locate pickup address", 404);
        }
        $pickup['address'] = $pickupAddressInfo[0]['formatted_address'];
        $pickup['name'] = $pickup['address_details'] ?? $pickupAddressInfo[0]['name'];

        $deliveryAddressInfo = MapService::searchAddress("{$deliveryLatitude},{$deliveryLongitude}");
        if (count($deliveryAddressInfo) < 1) {
            throw new CustomAPIException("Unable to locate delivery address", 404);
        }
        $delivery['address'] = $deliveryAddressInfo[0]['formatted_address'];
        $delivery['name'] = $delivery['address_details'] ?? $deliveryAddressInfo[0]['name'];

        $distanceAndDuration = MapService::getDistanceBetweenLocations("{$pickupLatitude},{$pickupLongitude}", "{$deliveryLatitude},{$deliveryLongitude}");
        if ($distanceAndDuration['distance'] === null) {
            throw new CustomAPIException("Unable to process, please check that the address or (longitude and latitude) are correct", 400);
        }
        $totalDistance += $distanceAndDuration['distance'];
        $totalDuration += $distanceAndDuration['duration'];

        $totalPrice += $this->calculateDistancePrice($distanceAndDuration['distance'], $distanceAndDuration['duration'], $vehicleId);
        $timeline[] = [
            'index' => $index,
            'from' => [
                'latitude' => $pickup['latitude'],
                'longitude' => $pickup['longitude'],
                'name' => $pickup['name'],
            ],
            'to' => [
                'latitude' => $delivery['latitude'],
                'longitude' => $delivery['longitude'],
                'name' => $delivery['name'],
            ],
            'vehicle_id' => $vehicleId,
            'price' => $totalPrice,
            'total_price' => $totalPrice,
            'distance' => $distanceAndDuration['distance'],
            'duration' => $distanceAndDuration['duration'],
        ];

        $orderId = strtolower(StaticFunction::generateCode(10));
        $data = [
            'user_id' => $user->id,
            'order_id' => $orderId,
            'pickup' => $pickup,
            'delivery' => $delivery,
            'stop_overs' => $stopOvers,
            'total_price' => round($totalPrice, 2),
            'vehicle_id' => $vehicleId,
            'total_distance' => $totalDistance,
            'total_duration' => $totalDuration,
            'timeline' => $timeline,
        ];

        if ($isCustomerOrder) {
            $customer = $this->customerService->getCustomer($user);
            $this->createOrder($orderId, $customer, $data);
        } else {
            $business = $this->businessService->getBusiness($user);
            $this->createBusinessOrder($orderId, $business, $data);
        }

        unset($data['timeline'], $data['vehicle_id'], $data['user_id']);
        unset($data['pickup']['save_address'], $data['delivery']['save_address']);

        return $data;
    }

    public function calculateDistancePrice(int $distance, int $duration, $vehicleId): float
    {
        $vehicle = VehicleService::getVehicle($vehicleId);
        $baseFare = $vehicle->base_fare;
        $pricePerKm0_5 = $vehicle->km_5_below_fare;
        $pricePerKm5Above = $vehicle->km_5_above_fare;
        $pricePerMinute = $vehicle->price_per_minute;

        $distanceKm = $distance / 1000;

        if ($distanceKm <= 5) {
            $priceDistance = $baseFare + $distanceKm * $pricePerKm0_5;
        } else {
            $priceDistance = $baseFare + 5 * $pricePerKm0_5 + ($distanceKm - 5) * $pricePerKm5Above;
        }

        $priceDuration = $duration / 60 * $pricePerMinute;

        return $priceDistance + $priceDuration;
    }

    public function placeOrder($user, $orderId, $request)
    {
        $customer = $this->customerService->getCustomer($user);
        $order = $this->getOrder($orderId, ['customer_id' => $customer->id]);

        if ($request->payment_method === 'WALLET') {
            $userWallet = $user->wallet;
            if ($userWallet->balance < $order->total_amount) {
                throw new CustomAPIException(
                    "You don't have sufficient balance in your wallet to place the order. Kindly fund your wallet.",
                    400
                );
            }
        }
        $order->status = 'PENDING';
        $order->payment_method = $request->payment_method;
        $order->payment_by = $request->payment_by;
        $metaData = json_decode($order->order_meta_data, true);
        if (isset($request->note_to_driver)) {
            $metaData['note_to_driver'] = $request->note_to_driver;
        }
        if (isset($request->promo_code)) {
            $metaData['promo_code'] = $request->promo_code;
        }
        $order->order_meta_data = json_encode($metaData);
        $this->notifyRidersAroundLocation($order);
        return true;
    }

    public function notifyRidersAroundLocation(Order $order)
    {
        $riders = Rider::where('on_duty', true)
            ->where('vehicle_id', $order->vehicle_id)
            ->get();

        $onDutyUsers = User::whereIn('id', $riders->pluck('user_id'))->get();

        $title = "New order request #{$order->order_id}";
        $message = "New customer order. Pick up: {$order->pickup_name}.";
        $this->notificationService->sendCollectivePushNotification($onDutyUsers, $title, $message);
    }

    public function addRiderTip($user, $orderId, $tipAmount)
    {
        $customer = $this->customerService->getCustomer($user);
        $order = $this->getOrder($orderId, ['customer_id' => $customer->id]);
        if (is_null($order->tip_amount)) {
            $order->tip_amount = $tipAmount;
        } else {
            $order->tip_amount += $tipAmount;
        }
        $order->save();
        return true;
    }

    public function addOrderTimelineEntry(Order $order, $orderStatus, $proofUrl = null, $reason = null, array $metaData = [])
    {
        return OrderTimeline::create([
            'order_id' => $order->id,
            'status' => $orderStatus,
            'proof_url' => $proofUrl,
            'reason' => $reason,
            'meta_data' => json_encode($metaData),
        ]);
    }

    public function assignRiderToOrder($user, $orderId, $riderId)
    {
        $customer = $this->customerService->getCustomer($user);
        $order = $this->getOrder($orderId, ['customer_id' => $customer->id]);
        if ($order->status != 'PENDING') {
            throw new CustomAPIException("Order is not pending", 404);
        }
        if (!is_null($order->rider_id)) {
            throw new CustomAPIException("Rider already assigned to ride", 404);
        }

        $favouriteRider = FavoriteRider::where('rider_id', $riderId)->where('customer_id', $customer->id)->first();
        if (is_null($favouriteRider)) {
            throw new CustomAPIException("Rider not a part of your favourite riders.", 404);
        }
        if (!$favouriteRider->rider->on_duty) {
            throw new CustomAPIException("Rider is not on duty.", 400);
        }

        $this->addOrderTimelineEntry($order, 'CUSTOMER_ASSIGN_RIDER');
        $this->addOrderTimelineEntry($order, 'PENDING_RIDER_CONFIRMATION');

        $order->rider_id = $favouriteRider->rider_id;
        $order->status = 'PENDING_RIDER_CONFIRMATION';
        $order->save();

        $title = "New order request #{$order->order_id}";
        $message = "A customer assigned you to an order. See order to accept or decline.";
        $this->notificationService->sendPushNotification($favouriteRider->rider->user, $title, $message);
    }

    public function rateRider($user, $orderId, $reequest)
    {
        $customer = $this->customerService->getCustomer($user);
        $order = $this->getOrder($orderId, ['customer_id' => $customer->id]);
        $favoriteRider = $reequest->favorite_rider ?? false;
        $rating = $reequest->rating;
        $remark = $reequest->remark ?? null;

        $riderRating = RiderRating::where('rider_id', $order->rider_id)->where('customer_id', $order->customer_id)->first();
        if ($riderRating) {
            $riderRating->remark = $remark;
            $riderRating->rating = $rating;
            $riderRating->save();
        } else {
            RiderRating::create([
                'rider_id' => $order->rider_id,
                'customer_id' => $order->customer_id,
                'remark' => $remark,
                'rating' => $rating,
            ]);
        }

        if ($favoriteRider) {
            FavoriteRider::firstOrCreate([
                'rider_id' => $order->rider_id,
                'customer_id' => $order->customer_id,
            ]);
        }

        return true;
    }

    public function customerCancelOrder($user, $orderId, $reason)
    {
        $customer = $this->customerService->getCustomer($user);
        $order = $this->getOrder($orderId, ['customer_id' => $customer->id]);

        if (in_array($order->status, [
            'RIDER_PICKED_UP_ORDER',
            'ORDER_ARRIVED',
            'ORDER_DELIVERED',
            'ORDER_COMPLETED',
        ])) {
            throw new CustomAPIException("Cannot cancel an ongoing order.", 400);
        }

        $this->addOrderTimelineEntry($order, 'ORDER_CANCELLED', null, $reason, [
            'cancelled_by' => 'customer',
        ]);

        $order->status = 'ORDER_CANCELLED';
        $order->save();

        return true;
    }

    public function riderAcceptCustomerOrder(User $user, $orderId)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId);

        $passRider = false;
        if (is_null($order->rider) && $order->status == 'PENDING') {
            $passRider = true;
        }

        if ($order->rider && $order->rider->user_id == $user->id && $order->status == 'PENDING_RIDER_CONFIRMATION') {
            $passRider = true;
        }

        if (!$passRider) {
            throw new CustomAPIException('Cannot accept order.', 400);
        }
        $rider = $this->riderService-> getRider($user);
        $this->addOrderTimelineEntry($order, 'RIDER_ACCEPTED_ORDER');
        $order->rider()->associate($rider);
        $order->status = 'RIDER_ACCEPTED_ORDER';
        $order->save();

        $title = "Rider accept order #{$orderId}";
        if ($order->isCustomerOrder()) {
            $message = "Your order has been accepted by {$rider->display_name}. Vehicle type: {$rider->vehicle_type} \n Plate number: {$rider->vehicle_plate_number}";
            $this->notificationService->sendPushNotification($order->customer->user, $title, $message);

        } else {
            FeleWebhook::sendOrderToWebhook($order);
        }

        return $order;
    }

    public function riderAtPickup($user, $orderId)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId, ['rider_id' => $rider->id]);
        $this->addOrderTimelineEntry($order, 'RIDER_AT_PICK_UP');
        $order->status = 'RIDER_AT_PICK_UP';
        $order->save();

        if ($order->isCustomerOrder()) {
            $title = "Rider arrived at pickup: #{$orderId}";
            $message = "Your rider {$order->rider->display_name}, is at pickup location: {$order->pickup_name}";
            $this->notificationService->sendPushNotification($order->customer->user, $title, $message);
        } else {
            FeleWebhook::sendOrderToWebhook($order);
        }
    }

    public function riderPickupOrder($orderId, User $user, $file)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId, ['rider_id' => $rider->id]);

        $fileName = $file->getClientOriginalName();
        $s3Uploader = new S3Uploader("/order/{$orderId}");
        $fileUrl = $s3Uploader->uploadFileObject($file, $fileName);

        $this->addOrderTimelineEntry($order, 'RIDER_PICKED_UP_ORDER', $fileUrl);
        $order->status = 'RIDER_PICKED_UP_ORDER';
        $order->save();

        if ($order->isCustomerOrder()) {
            $title = "Rider on the way to deliver: #{$orderId}";
            $message = "Your goods are on the way to drop off";
            $this->notificationService->sendPushNotification($order->customer->user, $title, $message);
        } else {
            FeleWebhook::sendOrderToWebhook($order);
        }
    }

    public function riderFailedPickup($orderId, User $user, $reason)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId, ['rider_id' => $rider->id]);

        $this->addOrderTimelineEntry($order, 'FAILED_PICKUP', null, $reason);
        $this->addOrderTimelineEntry($order, 'ORDER_CANCELLED');
        $order->status = 'ORDER_CANCELLED';
        $order->save();

        if ($order->isCustomerOrder()) {
            $title = "Rider failed to pick order: #{$orderId}";
            $message = "Your rider {$order->rider->display_name}, failed to pick up because: {$reason}";
            $this->notificationService->sendPushNotification($order->customer->user, $title, $message);
        } else {
            FeleWebhook::sendOrderToWebhook($order);
        }
    }

    public function riderAtDestination($orderId, User $user)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId, ['rider_id' => $rider->id]);
        $this->addOrderTimelineEntry($order, 'ORDER_ARRIVED');
        $order->status = 'ORDER_ARRIVED';
        $order->save();

        if ($order->isCustomerOrder()) {
            $title = "Rider at drop off: #{$orderId}";
            $message = "Your rider {$order->rider->display_name}, is at drop off point: {$order->delivery_name}";
            $this->notificationService->sendPushNotification($order->customer->user, $title, $message);
        } else {
            FeleWebhook::sendOrderToWebhook($order);
        }
    }

    public function riderMadeDelivery($orderId, User $user, $file)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId, ['rider_id' => $rider->id]);

        $fileName = $file->getClientOriginalName();
        $s3Uploader = new S3Uploader("/order/{$orderId}");
        $fileUrl = $s3Uploader->uploadFileObject($file, $fileName);

        $this->addOrderTimelineEntry($order, 'ORDER_DELIVERED', $fileUrl);
        $order->status = 'ORDER_DELIVERED';
        $order->save();

        if (!$order->isCustomerOrder()) {
            FeleWebhook::sendOrderToWebhook($order);
        }
        if ($order->payment_method === "WALLET"){
            if ($order->isCustomerOrder()) {
                $this->debitCustomer($order);
            } else {
                $this->debitBusiness($order);
            }
        }
    }

    public function riderReceivedPayment($orderId, User $user)
    {
        $rider = $this->riderService-> getRider($user);
        $order = $this->getOrder($orderId, ['rider_id' => $rider->id]);
        $riderCommission = RiderCommission::where('rider_id', $rider->id)->latest()->first();
        
        if ($riderCommission) {
            $charge = 100 - intval($riderCommission->commission->commission);
        } else {
            $charge = config('constants.FELE_CHARGE');
        }

        $this->addOrderTimelineEntry($order, 'ORDER_COMPLETED');
        
        $order->paid = true;
        $order->status = 'ORDER_COMPLETED';
        $order->fele_amount = $order->total_amount * ($charge / 100);
        $order->paid_fele = true;
        $order->save();

        $riderUser = $order->rider->user;
        $riderUserWallet = $riderUser->wallet;
        $reference = StaticFunction::generateCode(10);

        $transactionData = [
            'transaction_type' => 'CREDIT',
            'transaction_status' => 'SUCCESS',
            'amount' => $order->total_amount,
            'user_id' => $user->id,
            'reference' => $reference,
            'payment_category' => 'CUSTOMER_PAY_RIDER',
            'pssp_meta_data' => [],
            'currency' => "₦",
            'wallet_id' => $riderUserWallet->id,
            'pssp' => 'IN_HOUSE'
        ];
        Transaction::create($transactionData);

        $riderUserWallet->withdraw($order->fele_amount, true);
        
        $title = "Order Completed: #{$orderId}";
        $message = "Order completed, don't forget to rate rider.";
        $this->notificationService->sendPushNotification($order->customer->user, $title, $message);
        return $order;
    }

    public function debitCustomer($order)
    {
        $madePayment = false;

        DB::transaction(function() use ($order, &$madePayment) {
            $amount = $order->total_amount;
            $customerUser = $order->customer->user;
            $customerUserWallet = $customerUser->wallet;
            $transactionObj = null;

            if ($customerUserWallet->balance > $amount) {
                // debit wallet and mark as completed
                $reference = StaticFunction::generateCode(10);
                $customerUserWallet->withdraw($amount);
                $transactionObj = Transaction::create([
                    'transaction_type' => 'DEBIT',
                    'transaction_status' => 'SUCCESS',
                    'amount' => $amount,
                    'user_id' => $customerUser->id,
                    'reference' => $reference,
                    'pssp' => 'IN_HOUSE',
                    'payment_category' => 'CUSTOMER_PAY_RIDER',
                    'currency' => "₦",
                ]);
                $madePayment = true;
            } else {
                // debit card
                $userCard = $this->walletService->getUserCards($customerUser)->first();
                if ($userCard) {
                    $response = $this->paystackService->chargeCard($customerUser->email, $amount, $userCard->card_auth);
                    if ($response['status'] && $response['data']['status'] === 'success') {
                        $reference = $response['data']['reference'];
                        $transactionObj = Transaction::create([
                            'transaction_type' => 'DEBIT',
                            'transaction_status' => 'SUCCESS',
                            'amount' => $amount,
                            'user_id' => $customerUser->id,
                            'reference' => $reference,
                            'pssp' => 'PAYSTACK',
                            'payment_category' => 'CUSTOMER_PAY_RIDER',
                            'currency' => "₦",
                        ]);
                        $madePayment = true;
                    }
                }
            }

            if ($madePayment) {
                $this->creditRider($order, $transactionObj);
            }
        });

        return $madePayment;
    }

    public function debitBusiness($order)
    {
        $madePayment = false;

        DB::transaction(function() use ($order, &$madePayment) {
            $amount = $order->total_amount;
            $businessUser = $order->business->user;
            $businessUserWallet = $businessUser->wallet;
            $transactionObj = null;

            if ($businessUserWallet->balance > $amount) {
                // debit wallet and mark as completed
                $reference = StaticFunction::generateCode(10);
                $businessUserWallet->withdraw($amount);
                $transactionObj = Transaction::create([
                    'transaction_type' => 'DEBIT',
                    'transaction_status' => 'SUCCESS',
                    'amount' => $amount,
                    'user_id' => $businessUser->id,
                    'reference' => $reference,
                    'pssp' => 'IN_HOUSE',
                    'payment_category' => 'CUSTOMER_PAY_RIDER',
                    'currency' => "₦",
                ]);

                $madePayment = true;
            } else {
                // debit card
                $userCard = $this->walletService->getUserCards($businessUser)->first();

                if ($userCard) {
                    $response = $this->paystackService->chargeCard($businessUser->email, $amount, $userCard->card_auth);
                    if ($response['status'] && $response['data']['status'] === 'success') {
                        $reference = $response['data']['reference'];
                        $transactionObj = Transaction::create([
                            'transaction_type' => 'DEBIT',
                            'transaction_status' => 'SUCCESS',
                            'amount' => $amount,
                            'user_id' => $businessUser->id,
                            'reference' => $reference,
                            'pssp' => 'PAYSTACK',
                            'payment_category' => 'CUSTOMER_PAY_RIDER',
                            'currency' => "₦",
                        ]);
                        $madePayment = true;
                    }
                }
            }

            if ($madePayment) {
                $this->creditRider($order, $transactionObj);
            }
        });

        return $madePayment;
    }

    public function creditRider($order, $transactionObj)
    {
        $amount = $transactionObj->amount;
        $riderCommission = RiderCommission::where('rider_id', $order->rider->id)->latest()->first();
        $charge = $riderCommission ? 100 - intval($riderCommission->commission->commission) : config('constants.FELE_CHARGE');

        $this->addOrderTimelineEntry($order, 'ORDER_COMPLETED');
        $order->paid = true;
        $order->status = 'ORDER_COMPLETED';
        $order->fele_amount = $amount * ($charge / 100);
        $order->paid_fele = true;
        $order->save();

        $riderUser = $order->rider->user;
        $riderUserWallet = $riderUser->wallet;
        Transaction::create([
            'transaction_type' => 'CREDIT',
            'transaction_status' => 'SUCCESS',
            'amount' => $amount,
            'user_id' => $riderUser->id,
            'reference' => $transactionObj->reference,
            'pssp' => $transactionObj->pssp,
            'payment_category' => 'CUSTOMER_PAY_RIDER',
            'wallet_id' => $riderUserWallet->id,
            'currency' => "₦",
        ]);
        $riderUserWallet->deposit($order->total_amount - $order->fele_amount);
        // TODO: check if rider has outstanding and deduct it
    }


    public function createBusinessOrder($orderId, $business, $data)
    {
        $pickup = $data['pickup'];
        $delivery = $data['delivery'];
        $totalDuration = $data['total_duration'] ?? null;
        $totalDistance = $data['total_distance'] ?? null;
        $stopOvers = $data['stop_overs'] ?? [];
        $vehicleId = $data['vehicle_id'] ?? null;
        $timeline = $data['timeline'];
        $firstTimelineEntry = $timeline[0];
        $toData = $firstTimelineEntry['to'] ?? [];
        $fromData = $firstTimelineEntry['from'] ?? [];
        $pickupLongitude = $fromData['longitude'] ?? null;
        $pickupLatitude = $fromData['latitude'] ?? null;
        $pickupName = $fromData['name'] ?? null;
        $deliveryLongitude = $toData['longitude'] ?? null;
        $deliveryLatitude = $toData['latitude'] ?? null;
        $deliveryName = $toData['name'] ?? null;

        if ($pickup['save_address'] ?? false) {
            $this->saveAddress($business, $pickup);
        }
        if ($delivery['save_address'] ?? false) {
            $this->saveAddress($business, $delivery);
        }

        $orderMetaData = [
            'note_to_driver' => $data['note_to_driver'] ?? null,
            'promo_code' => $data['promo_code'] ?? null,
            'timeline' => $timeline,
            'pickup' => $pickup,
            'delivery' => $delivery,
        ];

        return Order::create([
            'business_id' => $business->id,
            'vehicle_id' => VehicleService::getVehicle($vehicleId)->id,
            'order_id' => $orderId,
            'status' => 'PROCESSING_ORDER',
            'pickup_number' => $pickup['contact_phone_number'] ?? null,
            'pickup_contact_name' => $pickup['contact_name'] ?? null,
            'pickup_location' => $pickup['address'],
            'pickup_name' => $pickup['name'] ?? $pickupName,
            'pickup_location_longitude' => $pickup['longitude'] ?? $pickupLongitude,
            'pickup_location_latitude' => $pickup['latitude'] ?? $pickupLatitude,
            'delivery_number' => $delivery['contact_phone_number'] ?? null,
            'delivery_contact_name' => $delivery['contact_name'] ?? null,
            'delivery_location' => $delivery['address'],
            'delivery_name' => $delivery['name'] ?? $deliveryName,
            'delivery_location_longitude' => $delivery['longitude'] ?? $deliveryLongitude,
            'delivery_location_latitude' => $delivery['latitude'] ?? $deliveryLatitude,
            'order_stop_overs_meta_data' => json_encode($stopOvers),
            'total_amount' => $data['total_price'],
            'tip_amount' => $data['tip_amount'] ?? null,
            'distance' => $totalDistance,
            'duration' => $totalDuration,
            'order_meta_data' => json_encode($orderMetaData),
            'payment_method' => "WALLET",
            'payment_by' => null,
            'order_by' => 'BUSINESS',
            'paid' => false,
            'paid_fele' => false,
            'fele_amount' => 0
        ]);
    }

    public function placeBusinessOrder($user, $orderId, $request)
    {
        $business = $this->businessService->getBusiness($user);
        $order = $this->getOrder($orderId, ['business_id' => $business->id]);
        $userWallet = $user->wallet;
        if ($userWallet->balance < $order->total_amount) {
            throw new CustomAPIException(
                "You don't have sufficient balance in your wallet to place the order. Kindly fund your wallet.",
                400
            );
        }
        $order->status = 'PENDING';
        $metaData = json_decode($order->order_meta_data, true);
        if (isset($request->note_to_driver)) {
            $metaData['note_to_driver'] = $request->note_to_driver;
        }
        $order->order_meta_data = json_encode($metaData);
        $order->save();
        $this->notifyRidersAroundLocation($order);
        return $order;
    }

}
