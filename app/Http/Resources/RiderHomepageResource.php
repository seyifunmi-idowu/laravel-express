<?php

namespace App\Http\Resources;
use App\Models\Order;
use App\Models\RiderRating;
use App\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class RiderHomepageResource extends JsonResource
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
             'on_duty' => $this->on_duty,
             'total_deliveries' => $this->getTotalDeliveries(),
             'ongoing_deliveries' => $this->getOngoingDeliveries(),
             'delivery_request' => $this->getDeliveryRequest(),
             'today_earns' => $this->getTodayEarns(),
             'this_week_earns' => $this->getThisWeekEarns(),
             'rider_activity' => $this->getRiderActivity(),
         ];
     }
 
     private function getTotalDeliveries()
     {
         return Order::where('rider_id', $this->id)
             ->whereIn('status', ['ORDER_DELIVERED', 'ORDER_COMPLETED'])
             ->count();
     }
 
     private function getOngoingDeliveries()
     {
        return Order::where('rider_id', $this->id)
            ->whereIn('status', [
                 'RIDER_ACCEPTED_ORDER',
                 'RIDER_AT_PICK_UP',
                 'RIDER_PICKED_UP_ORDER',
                 'ORDER_ARRIVED',
             ])
             ->count();
     }
 
     private function getDeliveryRequest()
     {
        return Order::where('rider_id', $this->id)
            ->where('status', 'PENDING_RIDER_CONFIRMATION')
             ->count();
     }
 
     private function getTodayEarns()
     {
         $today = Carbon::now()->format('Y-m-d');
         return Order::where('rider_id', $this->id)
             ->whereDate('created_at', $today)
             ->sum('total_amount') ?? 0.0;
     }
 
     private function getThisWeekEarns()
     {
         $today = Carbon::now();
         $startOfWeek = $today->startOfWeek()->format('Y-m-d');
         $endOfWeek = $today->endOfWeek()->format('Y-m-d');
 
         return Order::where('rider_id', $this->id)
             ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
             ->sum('total_amount') ?? 0.0;
     }
 
     private function getRiderActivity()
     {
         $transactions = Transaction::where('user_id', $this->user_id)
             ->orderBy('created_at', 'desc')
             ->take(10)
             ->get();
 
         return $transactions;
     }
 
    }
