<?php

namespace App\Imports;

use App\ReporteSat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class SatImport implements ToModel, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ReporteSat([
            //
            'No' => $row[0],
            'GUID' => $row[1],
            'Sucursal' => $row[2],
            'NIT_Vendedor' => $row[3],
            'Vendedor' => $row[4],
            'NIT_Comprador' => $row[5],
            'Comprador' => $row[6],
            'Tipo' => $row[7],
            'Serie' => $row[8],
            'Folio' => $row[9],
            'Numero_Interno' => $row[10],
            'Fecha_Emision' => Carbon::createFromFormat('Y-m-d H:i:s',date('Y-d-m H:i:s',strtotime($row[11]))),
            'Fecha_de_Recibido_por_GFACE' => Carbon::createFromFormat('d-m-Y H:i:s',date('m-d-Y H:i:s',strtotime($row[12]))),
            //'Anulado' => date('Y-m-d H:i:s',strtotime($row[13])),
            'Fecha_de_anulado' => $row[14],
            'Moneda' => $row[15],
            'Factor_Conv' => $row[16],
            'Descuentos' => $row[17],
            'IVA' => $row[18],
            'IDP' => $row[19],
            'IDT' => $row[20],
            'TML' => $row[21],
            'ITP' => $row[22],
            'IBV' => $row[23],
            'TABACO' => $row[24],
            'SubTotal' => $row[25],
            'Impuestos' => $row[26],
            'Total' => $row[27],
        ]);
    }



    public function chunkSize(): int
    {
        return 3000;
    }


}
