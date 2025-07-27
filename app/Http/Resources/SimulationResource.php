<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimulationResource extends JsonResource
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
            'flight_id' => $this->flight_id,
            'statistics' => $this->statistics,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'flight' => $this->whenLoaded('flight')
        ];
    }
}
