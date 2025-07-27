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
    public function toArray($request)
    {
        $passengers = $this->whenLoaded('passengers');

        $economy = $passengers->where('class', 'economy');
        $business = $passengers->where('class', 'business');

        $baseCost = json_decode($this->base_cost, true) ?? [];

        $revenue = $economy->sum("ticket_price") + $business->sum("ticket_price");


        return [
            "id" => $this->id,
            "estimated_arrival_date" => $this->estimated_arrival_date,
            "created_at" => $this->created_at,
            "total_passenger" => [
                "count" => $passengers->count(),
                "economy" => $economy->count(),
                "business" => $business->count(),
                "revenue" => round($revenue, 2),
            ],
            "base_cost" => $baseCost,
            'airplane' => $this->whenLoaded('airplane'),
            'departure_airport' => $this->whenLoaded('departureAirport'),
            'arrival_airport' => $this->whenLoaded('arrivalAirport'),
        ];
    }


}
