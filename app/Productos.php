<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    //
    protected $table = "inventario_web_detalle";
    protected $fillable = ['usuario'];
    public $incrementing = false;
    public $timestamps = false;
    public function nombre()
    {
        return $this->belongsTo('App\User','usuario');
	}
}