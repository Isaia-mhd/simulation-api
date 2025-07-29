<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AirplaneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "real_name" => $this->real_name,
            "fuel_consumption_lh" => $this->fuel_consumption_lh,
            "capacity" => $this->capacity
        ];
    }
}
