<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReporteSat extends Model
{
    //
    protected $dates = ['Fecha_Emision','Fecha_de_Recibido_por_GFACE'];
    protected $table = 'inventario_reporte_sat';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['No','GUID','Sucursal','NIT_Vendedor','Vendedor','NIT_Comprador','Comprador','Tipo','Serie','Folio','Numero_Interno',
    'Fecha_Emision','Fecha_de_Recibido_por_GFACE','Anulado','Fecha_de_anulado','Moneda','Factor_Conv','Descuentos','IVA','IDP','IDT','TML','ITP','IBV',
    'TABACO','SubTotal','Impuestos','Total','created_at','updated_at'];
}
