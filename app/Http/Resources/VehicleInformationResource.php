<?php

namespace App\Http\Resources;
use App\Models\Order;
use App\Models\RiderRating;
use App\Services\RiderService;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class VehicleInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

     public function toArray($request)
     {
        $riderKYCService = app()->make(RiderService::class);
        $driverLicense = $riderKYCService->getRiderDocumentStatus($this->resource, 'driver_license');
        $insuranceCertificate = $riderKYCService->getRiderDocumentStatus($this->resource, 'insurance_certificate');

         return [
            'vehicle' => $this->vehicle ? $this->vehicle->name : null,
            'vehicle_make' => $this->vehicle_make,
            'vehicle_model' => $this->vehicle_model,
            'vehicle_plate_number' => $this->vehicle_plate_number,
            'vehicle_color' => $this->vehicle_color,
            'driver_license' => $driverLicense,
            'insurance_certificate' => $insuranceCertificate,
         ];
     }     
 
}
