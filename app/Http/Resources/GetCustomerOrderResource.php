<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetCustomerOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'order_id' => $this->order_id,
            'status' => $this->status,
            'pickup' => $this->getPickup(),
            'delivery' => $this->getDelivery(),
            'rider' => $this->getRider(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

    protected function getPickup()
    {
        return [
            'address' => $this->pickup_location,
            'name' => $this->pickup_name,
        ];
    }

    protected function getDelivery()
    {
        return [
            'address' => $this->delivery_location,
            'name' => $this->delivery_name,
        ];
    }

    protected function getRider()
    {
        if ($this->rider) {
            return [
                'id' => $this->rider->id,
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

    protected function getCreatedAt()
    {
        return $this->created_at->format('Y-F-d H:i:s');
    }

}
