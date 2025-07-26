<?php

namespace App\Imports;

use App\Models\SunriseSunsetTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SunriseSunsetImport implements ToCollection, WithStartRow, WithHeadingRow
{
    private $airportColKeys = [];

    public function __construct()
    {
    }

    public function startRow(): int
    {
        return 3;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $firstDataRow = $rows->first();
        if ($firstDataRow) {
            Log::info('Keys available in the first data row: ', array_keys($firstDataRow->toArray()));
        } else {

            return;
        }

        $this->identifyAirportColumns($firstDataRow);

        if (empty($this->airportColKeys)) {
            return;
        }

        $recordsToInsert = [];
        $currentMonth = null;
        $monthData = [];

        foreach ($rows as $rowIndex => $row) {
            $rowArray = $row->toArray();

            $monthFromRow = isset($rowArray['mois']) ? (int) $rowArray['mois'] : null;
            $jourFromRow = isset($rowArray['jour']) ? (int) $rowArray['jour'] : null;

            if ($monthFromRow !== 0 && $monthFromRow !== null) {
                if (!empty($monthData) && $currentMonth !== null) {
                    $this->processMonthData($currentMonth, $monthData, $recordsToInsert, $this->airportColKeys);
                }
                $currentMonth = $monthFromRow;
                $monthData = [$rowArray];

            } elseif ($jourFromRow === 15 && $currentMonth !== null) {
                $monthData[] = $rowArray;
            } else {

                continue;
            }

            if (count($monthData) === 2) {
                $this->processMonthData($currentMonth, $monthData, $recordsToInsert, $this->airportColKeys);
                $monthData = [];
                $currentMonth = null;
            }
        }

        if (!empty($monthData)) {
            Log::error("End of file reached with incomplete month data for month " . ($currentMonth ?? 'N/A') . ". Expected 2 rows, got " . count($monthData) . ". No records added for this partial month.");
        }


        if (count($recordsToInsert) > 0) {

            try {
                SunriseSunsetTime::insert($recordsToInsert);
            } catch (\Exception $e) {
                Log::error('Database insertion failed: ' . $e->getMessage());
            }
        } else {
            Log::warning('No records to insert. Database will remain empty.');
        }
    }

    private function identifyAirportColumns(Collection $firstDataRow): void
    {
        $this->airportColKeys = [];
        $headerMap = $firstDataRow->keys()->toArray();
        $rowArray = $firstDataRow->toArray();

        $lastAirportNameKey = null;
        for ($i = 0; $i < count($headerMap); $i++) {
            $currentHeaderKey = $headerMap[$i];

            if ($currentHeaderKey === 'mois' || $currentHeaderKey === 'jour') {
                continue;
            }


            if (is_string($currentHeaderKey) && $currentHeaderKey !== '' && !is_numeric($currentHeaderKey)) {
                $lastAirportNameKey = strtolower($currentHeaderKey);
                $this->airportColKeys[$lastAirportNameKey] = [
                    'ls_key' => $currentHeaderKey,
                    'cs_key' => null,
                ];
            }

            else if ($lastAirportNameKey !== null && $this->airportColKeys[$lastAirportNameKey]['cs_key'] === null) {
                $this->airportColKeys[$lastAirportNameKey]['cs_key'] = $currentHeaderKey;

                $lastAirportNameKey = null;
            }
        }

        foreach ($this->airportColKeys as $airportCode => $keys) {
            if ($keys['cs_key'] === null) {
                unset($this->airportColKeys[$airportCode]);
            }
        }
    }


    private function processMonthData(int $month, array $monthData, array &$recordsToInsert, array $airportColKeys): void
    {

        if (count($monthData) !== 2) {
            Log::error("Incomplete month data for month {$month}. Expected 2 rows, got " . count($monthData) . ". Skipping month.");
            return;
        }

        $row1 = $monthData[0];
        $row2 = $monthData[1];

        $maxDay = Carbon::create(null, $month, 1)->daysInMonth;

        for ($day = 1; $day <= 14; $day++) {
            if ($day > $maxDay) {
                break;
            }
            $this->addDailyRecords($recordsToInsert, $month, $day, $row1, $airportColKeys);
        }

        for ($day = 15; $day <= $maxDay; $day++) {
            $this->addDailyRecords($recordsToInsert, $month, $day, $row2, $airportColKeys);
        }
    }

    private function addDailyRecords(array &$recordsToInsert, int $month, int $day, array $dataRow, array $airportColKeys): void
    {
        foreach ($airportColKeys as $airportCode => $cols) {
            $sunriseKey = $cols['ls_key'];
            $sunsetKey = $cols['cs_key'];

            if (!array_key_exists($sunriseKey, $dataRow) || !array_key_exists($sunsetKey, $dataRow)) {
                continue;
            }

            $sunriseDecimal = is_numeric($dataRow[$sunriseKey]) ? (float) $dataRow[$sunriseKey] : null;
            $sunsetDecimal = is_numeric($dataRow[$sunsetKey]) ? (float) $dataRow[$sunsetKey] : null;


            $sunriseTimeValue = $dataRow[$sunriseKey];
            $sunsetTimeValue = $dataRow[$sunsetKey];

            if (str_contains($sunriseTimeValue, ':') && str_contains($sunsetTimeValue, ':')) {
                $sunriseTime = Carbon::parse($sunriseTimeValue)->format('H:i');
                $sunsetTime = Carbon::parse($sunsetTimeValue)->format('H:i');
            } else {

                if ($sunriseDecimal === null || $sunsetDecimal === null) {
                    continue;
                }
                try {
                    $sunriseTime = Carbon::createFromTimestamp(round($sunriseDecimal * 86400))->format('H:i');
                    $sunsetTime = Carbon::createFromTimestamp(round($sunsetDecimal * 86400))->format('H:i');
                } catch (\Exception $e) {
                    continue;
                }
            }

            $sunriseTime = trim($sunriseTime);
            $sunsetTime = trim($sunsetTime);

            if (!empty($sunriseTime) && !empty($sunsetTime)) {
                $record = [
                    'month' => $month,
                    'day' => $day,
                    'airport_code' => $airportCode,
                    'sunrise_time' => $sunriseTime,
                    'sunset_time' => $sunsetTime,
                ];
                $recordsToInsert[] = $record;
            } else {
                Log::debug("Skipping record for Month {$month}, Day {$day}, Airport {$airportCode} due to empty time values after conversion. Sunrise: '{$sunriseTime}', Sunset: '{$sunsetTime}'");
            }
        }
    }
}
