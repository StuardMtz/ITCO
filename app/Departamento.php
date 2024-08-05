<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    //
    protected $table="inventario_web_departamentos";
    public $incrementing = false;
    public $timestamps = false;
}
