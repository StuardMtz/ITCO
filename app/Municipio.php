<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    //
    protected $table="inventario_web_municipios";
    public $incrementing = false;
    protected $fillable = ['id_departamento','codigo_postal','nombre'];
    public $timestamps = false;
}
