<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "airplane_id" => "required|exists:airplanes,id",
            "departure_airport_id" => "required|exists:airports,id",
            "arrival_airport_id" => "required|exists:airports,id",
            "departure_date" => "required|date",
            "passengers" => "required|integer",
            "status" => "required|string",
        ];
    }
}
