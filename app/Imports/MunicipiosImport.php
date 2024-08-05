<?php

namespace App\Imports;

use App\Municipio;
use Maatwebsite\Excel\Concerns\ToModel;

class MunicipiosImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Municipio([
            //
            'id_departamento' => $row[1],
            'codigo_postal'   => $row[2],
            'nombre'          => $row[3],
        ]);
    }
}
