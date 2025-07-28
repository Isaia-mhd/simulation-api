<?php

namespace App\Imports;

use App\Models\Convoyeur;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ConvoyeurImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $room_cost = $this->cleanNumeric($row['chambrehotel']);
        $meal_cost = $this->cleanNumeric($row['repas']);
        $round_trip_transfer_cost = $this->cleanNumeric($row['transfertallerretour']);
        $daily_transport_cost = $this->cleanNumeric($row['fraitransportjournalier']);

        return new Convoyeur([
            'escale' => trim($row['escale']),
            'room_cost' => $room_cost,
            'meal_cost' => $meal_cost,
            'round_trip_transfer_cost' => $round_trip_transfer_cost,
            'daily_transport_cost' => $daily_transport_cost,
        ]);
    }

    public function rules(): array
    {
        return [
            'escale' => 'required|string|max:10|unique:convoyeurs,escale',
            'chambrehotel' => 'nullable|numeric|min:0',
            'repas' => 'nullable|numeric|min:0',
            'transfertallerretour' => 'nullable|numeric|min:0',
            'fraitransportjournalier' => 'nullable|numeric|min:0',
        ];
    }

    private function cleanNumeric($value)
    {

        if (is_null($value) || $value === '') {
            return null;
        }
        $cleaned = preg_replace('/[^0-9]/', '', trim($value));
        return is_numeric($cleaned) ? (int)$cleaned : null;
    }
}

?>
