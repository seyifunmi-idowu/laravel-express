<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Services\OrderService;

class RiderOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $orderService = app()->make(OrderService::class);
        $distance = $orderService->getKmInWord($this->distance);
        $duration = $orderService->getKmInWord($this->duration);


        return [
            'order_id' => $this->order_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_by' => $this->payment_by,
            'pickup' => $this->getPickup(),
            'delivery' => $this->getDelivery(),
            // 'stopover' => $this->getStopover(),
            'total_amount' => $this->total_amount,
            'tip_amount' => $this->tip_amount,
            'note_to_driver' => $this->getNoteToDriver(),
            'distance' => $distance,
            'duration' => $duration,
            'created_at' => $this->getCreatedAt(),
            'contact' => $this->getContact(),
        ];
    }

    private function getPickup()
    {
        return [
            'longitude' => $this->pickup_location_longitude,
            'latitude' => $this->pickup_location_latitude,
            'address' => $this->pickup_location,
            'short_address' => $this->pickup_name,
            'complete_address' => $this->pickup_location,
            'contact_phone_number' => $this->pickup_number,
            'contact_name' => $this->pickup_contact_name,
            'time' => $this->getPickUpTime(),
            'name' => $this->pickup_name,
        ];
    }

    private function getDelivery()
    {
        return [
            'address' => $this->delivery_location,
            'longitude' => $this->delivery_location_longitude,
            'latitude' => $this->delivery_location_latitude,
            'short_address' => $this->delivery_name,
            'complete_address' => $this->delivery_location,
            'contact_phone_number' => $this->delivery_number,
            'contact_name' => $this->delivery_contact_name,
            'time' => $this->getDeliveryTime(),
            'name' => $this->delivery_name,
        ];
    }

    // private function getStopover()
    // {
    //     return array_map(function ($result) {
    //         return [
    //             'latitude' => $result['latitude'],
    //             'longitude' => $result['longitude'],
    //             'address' => $result['address'],
    //             'contact_name' => $result['contact_name'],
    //             'contact_phone_number' => $result['contact_number'],
    //         ];
    //     }, $this->order_stop_overs_meta_data);
    // }

    private function getCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
    }

    private function getNoteToDriver()
    {
        return $this->order_meta_data['note_to_driver'] ?? '';
    }

    private function getContact()
    {
        $status = $this->status;
        if (in_array($status, [
            'PENDING',
            'PROCESSING_ORDER',
            'RIDER_ACCEPTED_ORDER',
            'RIDER_AT_PICK_UP',
        ])) {
            $destination = 'pickup';
            $contact = $this->pickup_number;
        } elseif (in_array($status, ['RIDER_PICKED_UP_ORDER', 'ORDER_ARRIVED'])) {
            $destination = 'delivery';
            $contact = $this->delivery_number;
        } else {
            $destination = null;
            $contact = '';
        }

        return [
            'contact' => $contact,
            'destination' => $destination,
        ];
    }

}
