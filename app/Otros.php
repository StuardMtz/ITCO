<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otros extends Model
{
    //
    protected $table="inventario_web_aldeas_otros";
    public $incrementing = false;
    protected $fillable = ['id_municipio','nombre'];
    public $timestamps = false;
}
