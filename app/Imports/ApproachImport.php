<?php

namespace App\Imports;

use App\Models\Approach;
use Maatwebsite\Excel\Concerns\ToModel;

class ApproachImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Approach([
            //
        ]);
    }
}
