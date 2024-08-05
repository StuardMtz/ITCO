<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Historial;
use App\User;

class HistorialController extends Controller
{
    //listado_usuarios
    public function __construct()
    {
        $this->middleware('auth');
    }  
}
