<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
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
            "departure_date" => $this->departure_date,
            "estimated_arrival_date" => $this->estimated_arrival_date,
            "passengers" => $this->passengers,
            "created_at" => $this->created_at
        ];
    }
}
