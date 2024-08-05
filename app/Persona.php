<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = "inventario_web_personas_gastos";
    public $incrementing = false;
    public $timestamps = false;
}
