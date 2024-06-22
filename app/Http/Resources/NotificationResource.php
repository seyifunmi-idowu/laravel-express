<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->getFormattedCreatedAtAttribute(),
            'title' => $this->title,
            'message' => $this->message,
            'opened' => $this->opened,
        ];
    }

    private function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }
}
