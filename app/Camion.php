<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Camion extends Model
{
    //
    protected $table = "inventario_web_camiones";
    public $incrementing = false;
    public $timestamps = false;
    public function estado()
    {
      return $this->belongsTo('App\Estado','id_estado');
	  }
}
