<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavouriteRiderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rider' => [
                'id' => $this->rider->id,
                'display_name' => $this->rider->display_name,
                'avatar_url' => $this->rider->avatar_url,
                'assigned_orders' => $this->rider->getActiveOrder()->count(),
            ],
        ];
    }
}
