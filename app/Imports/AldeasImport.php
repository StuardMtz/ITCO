<?php

namespace App\Imports;

use App\Otros;
use Maatwebsite\Excel\Concerns\ToModel;

class AldeasImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Otros([
            //
            'id_municipio'  => $row[1],
            'nombre'        => $row[2],
        ]);
    }
}
