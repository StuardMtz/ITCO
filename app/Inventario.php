<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    //
    protected $table = "inventario_web_encabezado";
	protected $fillable = ["porcentaje"];
    public $incrementing = false;
    public $timestamps = false;
}
