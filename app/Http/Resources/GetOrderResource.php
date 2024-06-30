<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\RiderRating;
use App\Services\OrderService;


class GetOrderResource extends JsonResource
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
            'created_at' => $this->getCreatedAt(),
            'assigned_by_customer' => $this->getAssignedyCustomer(),
        ];
    }

    private function getPickup()
    {
        return [
            'address' => $this->pickup_location,
            'name' => $this->pickup_name,
        ];
    }

    private function getDelivery()
    {
        return [
            'address' => $this->delivery_location,
            'name' => $this->delivery_name,
        ];
    }

    private function getCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
    }

    private function getAssignedyCustomer()
    {
        return$this->status === "PENDING_RIDER_CONFIRMATION";
    }

   
}
