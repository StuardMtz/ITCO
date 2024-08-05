<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    //
    protected $table = "inventario_web_rutas";
    public $incrementing = false;
    public $timestamps = false;
    public function camion()
    {
      return $this->belongsTo('App\Camion','id_camion');
	  }
    public function estado()
    {
      return $this->belongsTo('App\Estado','id_estado');
    }
}
