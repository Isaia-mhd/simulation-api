<?php

namespace App\Imports;

use App\Models\Airport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FuelPriceImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Airport([
            "name" => $row["aeroports"],
            "code" => $row["code"],
            "fuel_price" => $row["moyennet1"],
            "unit" => $row["unite"],
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
