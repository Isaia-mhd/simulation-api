<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePassengerRequest extends FormRequest
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
            "flight_id" => "required|exists:flights,id",
            "name" => "required|string",
            "email" => "required|string",
            "phone" => "required|string",
            "passport_number" => "required|string",
            "gender" => "required|string",
            "nationality" => "required|string",
            "date_of_birth" => "required|date",
            "class" => "required|string"
        ];
    }
}
