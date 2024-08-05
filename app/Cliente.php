<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //
    protected $table="inventario_web_clientes";
    public $incrementing = false;
    public $timestamps = false;
    public function departamento()
    {
      return $this->belongsTo('App\Departamento','id_departamento');
	  }

    public function municipio()
    {
      return $this->belongsTo('App\Municipio','id_municipio');
   	}

    public function otros()
    {
      return $this->belongsTo('App\Otros','id_otros');
   	}
}
