<?php

namespace App\Imports;

use App\Models\Hebergement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HebergementImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Hebergement([
            'escale' => $row['escale'],
            'room_cost' => !empty($row['chambrehotel']) ? (int) $row['chambrehotel'] : null,
            'breakfast_cost' => !empty($row['petitdejeuner']) ? (int) $row['petitdejeuner'] : null,
            'lunch_cost' => !empty($row['dejeuner']) ? (int) $row['dejeuner'] : null,
            'dinner_cost' => !empty($row['diner']) ? (int) $row['diner'] : null,
            'go_transfer_cost' => !empty($row['tranfertaller']) ? (int) $row['tranfertaller'] : null,
            'back_transfer_cost' => !empty($row['transfertretour']) ? (int) $row['transfertretour'] : null,
        ]);
    }
}
