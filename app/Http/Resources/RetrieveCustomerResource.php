<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RetrieveCustomerResource extends JsonResource
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
            'customer_type' => $this->customer_type,
            'business_profile_updated' => $this->isBusinessProfileUpdated(),
        ];
    }

    private function isBusinessProfileUpdated()
    {
        return $this->customer_type === 'BUSINESS' && !is_null($this->business_name);
    }
}
