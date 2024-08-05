<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    protected $table = "cotizaciones";
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
}
