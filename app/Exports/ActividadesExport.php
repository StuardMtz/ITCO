<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class ActividadesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'Nombre',
            'Descripción',
            'Prospecto',
            'Fecha',
            'Hora',
        ];
    }
    public function collection()
    {
       
            $detalles = DB::select("select ROW_NUMBER() OVER (order by drv.Fecha asc) as orden, u.name, 
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id, 
            drv.verificado
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p 
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto 
            and u.codigo_vendedor = 12
            and u.roles = 1
            and u.sucursal != 11
            order by drv.Fecha desc, drv.Hora desc");
            return $detalles;
    }
}
