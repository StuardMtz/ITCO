<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    //
    protected $table = "inventario_web_bitacora";
	
    public $incrementing = false;
    public $timestamps = false;
    public function nombre()
    {
      return $this->belongsTo('App\User','id_usuario');
   	}
}
