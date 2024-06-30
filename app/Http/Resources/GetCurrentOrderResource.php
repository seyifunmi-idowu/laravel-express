<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\RiderRating;
use App\Services\OrderService;


class GetCurrentOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    // public function toArray($request)
    // {
    //     $orderService = app()->make(OrderService::class);
    //     $distance = $orderService->getKmInWord($this->distance);
    //     $duration = $orderService->getKmInWord($this->duration);

    //     return [
    //         'order_id' => $this->order_id,
    //         'status' => $this->status,
    //         'payment_method' => $this->payment_method,
    //         'payment_by' => $this->payment_by,
    //         'rider' => $this->getRider(),
    //         'rider_contact' => $this->getRiderContact(),
    //         'pickup' => $this->getPickup(),
    //         'delivery' => $this->getDelivery(),
    //         'total_amount' => $this->total_amount,
    //         'tip_amount' => $this->tip_amount,
    //         'note_to_driver' => $this->getNoteToDriver(),
    //         'distance' => $distance,
    //         'duration' => $duration,
    //         'timeline' => $this->orderTimeline,
    //         'created_at' => $this->getCreatedAt(),
    //         'rating' => $this->getRating(),
    //     ];
    // }

    public function toArray($request)
    {
        $orderService = app()->make(OrderService::class);
        $distance = $orderService->getKmInWord($this->distance);
        $duration = $orderService->getKmInWord($this->duration);


        return [
            'order_id' => $this->order_id,
            'status' => $this->status,
            'pickup' => $this->getPickup($this),
            'delivery' => $this->getDelivery($this),
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'payment_by' => $this->payment_by,
            'distance' => $distance,
            'duration' => $duration,
            'created_at' => $this->created_at,
            'contact' => $this->getContact($this),
            'note_to_rider' => $this->order_meta_data['note_to_driver'] ?? '',
            'order_by' => $this->getOrderBy($this),
        ];
    }

    protected function getContact($order)
    {
        $status = $order->status;
        if (in_array($status, ['PENDING', 'PROCESSING_ORDER', 'RIDER_ACCEPTED_ORDER', 'RIDER_AT_PICK_UP'])) {
            $destination = 'pickup';
            $contact = $order->pickup_number;
        } elseif (in_array($status, ['RIDER_PICKED_UP_ORDER', 'ORDER_ARRIVED'])) {
            $destination = 'delivery';
            $contact = $order->delivery_number;
        } else {
            $destination = null;
            $contact = '';
        }

        return ['contact' => $contact, 'destination' => $destination];
    }

    protected function getPickup($order)
    {
        return [
            'address' => $order->pickup_location,
            'longitude' => $order->pickup_location_longitude,
            'latitude' => $order->pickup_location_latitude,
            'short_address' => $order->pickup_name,
            'complete_address' => $order->pickup_location,
            'contact' => $order->pickup_number,
            'contact_name' => $order->pickup_contact_name,
            'time' => $order->getPickUpTime(),
            'name' => $order->pickup_name,
        ];
    }

    protected function getDelivery($order)
    {
        return [
            'address' => $order->delivery_location,
            'longitude' => $order->delivery_location_longitude,
            'latitude' => $order->delivery_location_latitude,
            'short_address' => $order->delivery_name,
            'complete_address' => $order->delivery_location,
            'contact' => $order->delivery_number,
            'contact_name' => $order->delivery_contact_name,
            'time' => $order->getDeliveryTime(),
            'name' => $order->delivery_name,
        ];
    }

    protected function getOrderBy($order)
    {
        $orderBy = $order->order_by;
        $orderByMapper = [
            'CUSTOMER' => $order->customer,
            'BUSINESS' => $order->business,
        ];

        return $orderByMapper[$orderBy]->display_name;
    }
}
