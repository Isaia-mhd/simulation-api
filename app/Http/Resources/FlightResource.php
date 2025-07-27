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
            "name" => $this->name,
            "departure_date" => $this->departure_date,
            "estimated_arrival_date" => $this->estimated_arrival_date,
            "created_at" => $this->created_at,
            "total_passenger" => count($this->whenLoaded('passengers')),
            "base_cost" => json_decode($this->base_cost),
            'airplane' => $this->whenLoaded('airplane'),
            'departure_airport' => $this->whenLoaded('departureAirport'),
            'arrival_airport' => $this->whenLoaded('arrivalAirport'),
            "passengers" => $this->whenLoaded('passengers'),

        ];
    }
}
