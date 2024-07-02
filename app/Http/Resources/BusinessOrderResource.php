<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\RiderRating;
use App\Services\OrderService;


class BusinessOrderResource extends JsonResource
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
            'rider' => $this->getRider(),
            'pickup' => $this->getPickup(),
            'delivery' => $this->getDelivery(),
            'total_amount' => $this->total_amount,
            'tip_amount' => $this->tip_amount,
            'note_to_driver' => $this->getNoteToDriver(),
            'distance' => $distance,
            'duration' => $duration,
            'timeline' => $this->orderTimeline,
            'created_at' => $this->getCreatedAt(),
        ];
    }

    private function getPickup()
    {
        return [
            'latitude' => $this->pickup_location_latitude,
            'longitude' => $this->pickup_location_longitude,
            'address' => $this->pickup_location,
            'contact_name' => $this->pickup_contact_name,
            'contact_phone_number' => $this->pickup_number,
            'name' => $this->pickup_name,
        ];
    }

    private function getDelivery()
    {
        return [
            'latitude' => $this->delivery_location_latitude,
            'longitude' => $this->delivery_location_longitude,
            'address' => $this->delivery_location,
            'contact_name' => $this->delivery_contact_name,
            'contact_phone_number' => $this->delivery_number,
            'name' => $this->delivery_name,
        ];
    }

    private function getCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
    }

    private function getNoteToDriver()
    {
        return $this->order_meta_data['note_to_driver'] ?? '';
    }

    private function getRider()
    {
        if ($this->rider) {
            return [
                'name' => $this->rider->display_name,
                'contact' => $this->rider->user->phone_number,
                'avatar_url' => $this->rider->avatar_url,
                'rating' => $this->rider->rating,
                'vehicle' => $this->rider->vehicle->name,
                'vehicle_type' => $this->rider->vehicle_type,
                'vehicle_make' => $this->rider->vehicle_make,
                'vehicle_model' => $this->rider->vehicle_model,
                'vehicle_plate_number' => $this->rider->vehicle_plate_number,
                'vehicle_color' => $this->rider->vehicle_color,
            ];
        }
        return null;
    }
}
