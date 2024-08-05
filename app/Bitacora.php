<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    //
    protected $table="inventario_web_bitacora_entrega";
    protected $fillable = ['foto'];
    public $incrementing = false;
    public $timestamps = false;

    public function estado()
    {
      return $this->belongsTo('App\Estado','id_estado');
    }
}


