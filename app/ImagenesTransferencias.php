<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImagenesTransferencias extends Model
{
    protected $table = "inventario_web_imagenes_transferencias";
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['imagen'];
}
