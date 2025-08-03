<?php

namespace App\Services;

class CombinationService
{
    public function combine($simulations): array
    {
        $rounds = [];

        // S'assurer que c'est un tableau indexé
        $simulations = array_values($simulations);
        foreach ($simulations as $simulation)
        {
            for ($i = 0; $i < count($simulation); $i += 2) {
                $go = $simulation[$i];
                $back = $simulation[$i + 1] ?? null;

                if ($back !== null) {
                    $rounds[] = [
                        'go' => $go,
                        'back' => $back,
                    ];
                }
            }
        }
        return $rounds;
    }
}
