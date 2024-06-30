<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\RiderRating;
use App\Services\OrderService;


class OrderResource extends JsonResource
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
            'rider' => $this->getRider(),
            'rider_contact' => $this->getRiderContact(),
            'pickup' => $this->getPickup(),
            'delivery' => $this->getDelivery(),
            'total_amount' => $this->total_amount,
            'tip_amount' => $this->tip_amount,
            'note_to_driver' => $this->getNoteToDriver(),
            'distance' => $distance,
            'duration' => $duration,
            'timeline' => $this->orderTimeline,
            'created_at' => $this->getCreatedAt(),
            'rating' => $this->getRating(),
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

    private function getStopover()
    {
        return array_map(function ($stopover) {
            return [
                'latitude' => $stopover['latitude'],
                'longitude' => $stopover['longitude'],
                'address' => $stopover['address'],
                'contact_name' => $stopover['contact_name'],
                'contact_phone_number' => $stopover['contact_number'],
            ];
        }, $this->order_stop_overs_meta_data ?? []);
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

    private function getRiderContact()
    {
        return $this->rider ? $this->rider->user->phone_number : null;
    }

    private function getRating()
    {
        $rating = RiderRating::where('rider_id', $this->rider->id)
            ->where('customer_id', $this->customer->id)
            ->first();

        if ($rating) {
            return [
                'rating' => $rating->rating,
                'created_at' => $rating->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return ['rating' => null, 'created_at' => null];
    }
}
