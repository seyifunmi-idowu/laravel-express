<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_verified' => $this->email_verified,
            'phone_number' => $this->phone_number,
            'phone_verified' => $this->phone_verified,
            'avatar_url' => $this->avatar_url,
            'street_address' => $this->street_address,
            'city' => $this->city,
            'last_login' => $this->last_login,
            'referral_code' => $this->referral_code,
            'is_rider' => $this->isRider(),
            'is_customer' => $this->isCustomer(),
            'display_name' => $this->display_name,
            'wallet_balance' => $this->getUserWalletBalance(),
        ];
    }

    private function isRider()
    {
        return $this->user_type === 'RIDER';
    }

    private function isCustomer()
    {
        return $this->user_type === 'CUSTOMER';
    }

    private function getUserWalletBalance()
    {
        return $this->wallet->balance;
    }

}
