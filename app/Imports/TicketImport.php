<?php

namespace App\Imports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TicketImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 7;
    }

    public function model(array $row)
    {
        // Remove "(OW)" from the itinerary (first column)
        $itineraire = str_replace(' (OW)', '', $row[0]);

        return new Ticket([
            'itineraire' => $itineraire,
            'business' => round($row[1]), // Round business price (J)
            'economy' => round($row[2]), // Round economic price (Y)
        ]);
    }
}
