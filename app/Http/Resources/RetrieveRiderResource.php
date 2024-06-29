<?php

namespace App\Http\Resources;
use App\Models\Order;
use App\Models\RiderRating;

use Illuminate\Http\Resources\Json\JsonResource;

class RetrieveRiderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserProfileResource($this->user),
            'status' => $this->getStatus(),
            'on_duty' => $this->on_duty,
            'vehicle' => $this->getVehicle(),
            'vehicle_make' => $this->vehicle_make,
            'vehicle_model' => $this->vehicle_model,
            'vehicle_plate_number' => $this->vehicle_plate_number,
            'vehicle_color' => $this->vehicle_color,
            'rider_info' => $this->rider_info,
            'city' => $this->city,
            'avatar_url' => $this->avatar_url,
            'vehicle_photos' => $this->getVehiclePhotos(),
            'total_orders' => $this->getTotalOrders(),
            'total_earns' => $this->getTotalEarns(),
            'review_count' => $this->getReviewCount(),
        ];
    }

    private function getStatus()
    {
        return $this->getRiderStatus();
    }

    private function getVehiclePhotos()
    {
        return $this->vehiclePhotos();
    }

    private function getVehicle()
    {
        return $this->vehicle ? $this->vehicle->name : null;
    }

    private function getTotalOrders()
    {
        return Order::where('rider_id', $this->id)
            ->whereNotIn('status', ['PROCESSING_ORDER', 'PENDING', 'ORDER_CANCELLED'])
            ->count();
    }

    private function getTotalEarns()
    {
        $orders = Order::where('rider_id', $this->id)
            ->where('status', 'ORDER_COMPLETED')
            ->get();

        $total_amount_sum = $orders->sum('total_amount') ?? 0.0;
        $fele_amount_sum = $orders->sum('fele_amount') ?? 0.0;
        $total_earns = floatval($total_amount_sum) - floatval($fele_amount_sum);

        return floatval(number_format($total_earns, 2, '.', ''));
    }

    private function getReviewCount()
    {
        return RiderRating::where('rider_id', $this->id)->count();
    }


}
