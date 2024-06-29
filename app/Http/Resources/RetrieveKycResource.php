<?php

namespace App\Http\Resources;
use App\Models\Order;
use App\Services\RiderService;
use App\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class RetrieveKycResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

     protected $riderService;

     public function __construct($resource)
     {
         parent::__construct($resource);
         $this->riderService = app(RiderService::class);
     }
 
     public function toArray($request)
     {
         return [
             'status' => $this->getRiderStatus(),
             'vehicle_photo' => $this->getVehiclePhoto(),
             'passport_photo' => $this->getPassportPhoto(),
             'government_id' => $this->getGovernmentId(),
             'guarantor_letter' => $this->getGuarantorLetter(),
             'address_verification' => $this->getAddressVerification(),
             'driver_license' => $this->getDriverLicense(),
             'insurance_certificate' => $this->getInsuranceCertificate(),
             'certificate_of_vehicle_registration' => $this->getCertificateOfVehicleRegistration(),
             'authorization_letter' => $this->getAuthorizationLetter(),
         ];
     }
 
     protected function getRiderStatus()
     {
         return $this->resource->getRiderStatus();
     }
 
     protected function getVehiclePhoto()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'vehicle_photo');
     }
 
     protected function getPassportPhoto()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'passport_photo');
     }
 
     protected function getGovernmentId()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'government_id');
     }
 
     protected function getGuarantorLetter()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'guarantor_letter');
     }
 
     protected function getAddressVerification()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'address_verification');
     }
 
     protected function getDriverLicense()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'driver_license');
     }
 
     protected function getInsuranceCertificate()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'insurance_certificate');
     }
 
     protected function getCertificateOfVehicleRegistration()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'certificate_of_vehicle_registration');
     }
 
     protected function getAuthorizationLetter()
     {
         return $this->riderService->getRiderDocumentStatus($this->resource, 'authorization_letter');
     }
  
}
