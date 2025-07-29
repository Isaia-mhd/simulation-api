<?php

namespace App\Imports;

use App\Models\Timeflight;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class TimeflightImport implements ToCollection, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Feuil1' => $this, // Importer uniquement la feuille "Feuil1"
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
//            $rowIndex = $index + 1;

            if ((empty($row[0]) && empty($row[1]) && empty($row[2])) ||
                ($row[0] ?? '') === 'ITINERAIRE' ||
                ($row[1] ?? '') === 'TEMPS DE VOL' ||
                ($row[2] ?? '') === 'NAME') {
                continue;
            }

            if (!empty($row[0])) {

                $timeRaw = trim($row[1] ?? '');
                $minutes = $this->convertToMinutes($timeRaw);

                Timeflight::create([
                    'itineraire' => $row[0],
                    'timeflight' => gmdate('H:i:s', $minutes * 60),
                    'flight_name' => $row[2],
                ]);
            }
        }
    }

    private function convertToMinutes(string $time): int
    {
        $time = strtoupper(trim($time));
        if (empty($time)) {
            return 0;
        }


        if (preg_match('/^(\d+)H(\d+)?$/', $time, $matches)) {
            $hours = (int)($matches[1] ?? 0);
            $minutes = isset($matches[2]) ? (int)$matches[2] : 0;
            return $hours * 60 + $minutes;
        } elseif (preg_match('/^(\d+)MN$/', $time, $matches)) {

            return (int)($matches[1] ?? 0);
        }
        return 0;
    }
}
