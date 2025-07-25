<?php

namespace App\Imports;

use App\Models\Landing;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class LandingImport implements ToCollection, WithMultipleSheets
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
                Log::info('Ligne ' . ($index + 1) . ' ignorée (en-tête ou vide)');
                continue;
            }

            if (in_array($row[0], ['ATR72-500', 'ATR72-600'])) {
                $currentAirplane = $row[0];


                if (!empty($row[1])) {
                    $adema = !empty($row[2]) ? (float)$row[2] : 0.00;
                    $asecna = !empty($row[3]) ? (float)$row[3] : 0.00;
                    $ravinala = !empty($row[4]) ? (float)$row[4] : 0.00;
                    $total_landing = $adema + $asecna + $ravinala;

                    Landing::create([
                        'airplane_name' => $currentAirplane,
                        'airport_code' => $row[1],
                        'adema' => $adema,
                        'asecna' => $asecna,
                        'ravinala' => $ravinala,
                        'total_landing' => $total_landing,
                    ]);
                }
                continue;
            }


            if ($currentAirplane && !empty($row[1])) {
                $adema = !empty($row[2]) ? (float)$row[2] : 0.00;
                $asecna = !empty($row[3]) ? (float)$row[3] : 0.00;
                $ravinala = !empty($row[4]) ? (float)$row[4] : 0.00;
                $total_landing = $adema + $asecna + $ravinala;

                Landing::create([
                    'airplane_name' => $currentAirplane,
                    'airport_code' => $row[1],
                    'adema' => $adema,
                    'asecna' => $asecna,
                    'ravinala' => $ravinala,
                    'total_landing' => $total_landing,
                ]);
            }
        }
    }
}
