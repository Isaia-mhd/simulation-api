<?php

namespace App\Imports;

use App\Models\Lighting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class LightingImport implements ToCollection, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'ALL' => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        $currentAirplane = null;

        foreach ($rows as $index => $row) {

            if ((empty($row[0]) && empty($row[1])) ||
                $row[0] === 'appareil' ||
                $row[1] === 'airport' ||
                str_contains(strtolower($row[0] ?? ''), 'atterrissage')) {

                continue;
            }


            if (in_array($row[0], ['ATR72-500', 'ATR72-600'])) {
                $currentAirplane = $row[0];


                if (!empty($row[1])) {
                    $adema = !empty($row[5]) ? (float)$row[5] : 0.00;
                    $asecna = !empty($row[6]) ? (float)$row[6] : 0.00;
                    $total_lighting = $adema + $asecna;


                    Lighting::create([
                        'airplane_name' => $currentAirplane,
                        'airport_code' => $row[1],
                        'adema' => $adema,
                        'asecna' => $asecna,
                        'total_lighting' => $total_lighting,
                    ]);
                }
                continue;
            }


            if ($currentAirplane && !empty($row[1])) {
                $adema = !empty($row[5]) ? (float)$row[5] : 0.00;
                $asecna = !empty($row[6]) ? (float)$row[6] : 0.00;
                $total_lighting = $adema + $asecna;

                Lighting::create([
                    'airplane_name' => $currentAirplane,
                    'airport_code' => $row[1],
                    'adema' => $adema,
                    'asecna' => $asecna,
                    'total_lighting' => $total_lighting,
                ]);
            }
        }
    }
}
