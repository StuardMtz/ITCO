<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudEnvio extends Model
{
    //
    protected $table="inventario_web_entregas";
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

    public function cliente()
    {
      return $this->belongsTo('App\Cliente','id_cliente');
   	}

    public function sucursal()
    {
      return $this->belongsTo('App\Sucursal','id_sucursal');
   	}

    public function estado()
    {
      return $this->belongsTo('App\Estado','id_estado');
    }

    public function usuario()
    {
      return $this->belongsTo('App\User','id_usuario');
    }
    public function sucur()
    {
      return $this->belongsTo('App\User','id_entregar');
    }

    public function camion()
    {
      return $this->belongsTo('App\Camion','id_camion');
	  }
}
