<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $table = "inventario_web_liquidaciones";
    public $incrementing = false;
    public $timestamps = false;
}
