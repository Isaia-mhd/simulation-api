<?php

namespace App\Imports;

use App\Models\Approach;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class ApproachImport implements ToCollection, WithMultipleSheets
{
    /**
     * Spécifier la feuille à importer
     */
    public function sheets(): array
    {
        return [
            'ALL' => $this, // Importer uniquement la feuille "ALL"
        ];
    }

    /**
     * Traiter la collection de lignes
     */
    public function collection(Collection $rows)
    {
        $currentAirplane = null;

        foreach ($rows as $index => $row) {
            // Journaliser chaque ligne pour débogage
            Log::info('Ligne ' . ($index + 1) . ': ', $row->toArray());

            // Ignorer les lignes d'en-tête ou complètement vides
            if ((empty($row[0]) && empty($row[1])) ||
                $row[0] === 'appareil' ||
                $row[1] === 'airport' ||
                str_contains(strtolower($row[0] ?? ''), 'atterrissage')) {
                Log::info('Ligne ' . ($index + 1) . ' ignorée (en-tête ou vide)');
                continue;
            }

            // Détecter le nom de l'appareil et traiter les données si présentes
            if (in_array($row[0], ['ATR72-500', 'ATR72-600'])) {
                $currentAirplane = $row[0];

                // Vérifier si la ligne contient un code d'aéroport valide
                if (!empty($row[1])) {
                    $adema = !empty($row[8]) ? (float)$row[8] : 0.00;
                    $total_approach = $adema;

                    Log::info('Importation APPROCHE ligne ' . ($index + 1) . ' pour ' . $currentAirplane . ', airport: ' . $row[1]);

                    Approach::create([
                        'airplane_name' => $currentAirplane,
                        'airport_code' => $row[1],
                        'adema' => $adema,
                        'total_approach' => $total_approach,
                    ]);
                }
                continue;
            }

            // Traiter les lignes suivantes avec un code d'aéroport valide
            if ($currentAirplane && !empty($row[1])) {
                $adema = !empty($row[8]) ? (float)$row[8] : 0.00;
                $total_approach = $adema;

                Log::info('Importation APPROCHE ligne ' . ($index + 1) . ' pour ' . $currentAirplane . ', airport: ' . $row[1]);

                Approach::create([
                    'airplane_name' => $currentAirplane,
                    'airport_code' => $row[1],
                    'adema' => $adema,
                    'total_approach' => $total_approach,
                ]);
            }
        }
    }
}
