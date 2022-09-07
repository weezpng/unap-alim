<?php

namespace App\Imports;

use App\ementatable;
use Maatwebsite\Excel\Concerns\ToModel;

class EmentaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ementatable([
          'name'     => $row[0],
          'email'    => $row[1], 
          'password' => Hash::make($row[2]),
        ]);
    }
}
