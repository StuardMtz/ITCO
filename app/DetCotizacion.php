<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetCotizacion extends Model
{
    protected $table = "detalle_Cotizaciones";
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
}
