<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use Carbon\Carbon;
use App\Inventario;
use App\Semana;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('inicio');
    }
    
    public function no_inicio()
    {
       if(Auth::user()->roles==1)
        {
            
        }
        elseif(Auth::user()->roles==3)
        {
            $fech = new Carbon();
            $fecha = $fech->format('Y-m-d');
            $fechaf = $fech->subHours(90);
            $usuario = Auth::user()->id;
            $datos = DB::table('users')->where('id',Auth::id())->first();
            $inventarios = DB::select('call inventario_web_encabezado_sucursal(?)',array($usuario));
            return view('sucursales.inicio',compact('inventarios','fecha','fechaf','datos'));
        }
        elseif(Auth::user()->roles==4)
        {
            $semanas = Semana::orderby('semana','asc')->get();
            $fechas = new Carbon();
            $fecha = Carbon::parse($fechas)->format('Y-m-d');
            return view('panel.inicio',compact('semanas','fecha'));
        }
        elseif(Auth::user()->roles==5)
        {
          return redirect()->route('p_inicio');
        }
        elseif(Auth::user()->roles==6)
        {
            return redirect()->route('vista_asesores');
        }
        elseif(Auth::user()->roles == 16)
        {
            return view('suptransferencia.inicio');
        }
        elseif(Auth::user()->roles == 17)
        {
            return view('bodega.inicio');
        }
        elseif(Auth::user()->roles == 18)
        {
            return redirect()->route('Vtran');
        }
        else
        {
            return back();
        }
    }

    public function grafica_chafa()
    {
        $datos = DB::select('select inventarios_diarios.cod_unidad,  ROUND(sum( punteo)/ count(distinct inventarios.cod_producto),2) as punteo
        from inventarios_diarios
        join inventarios on inventarios_diarios.cod_producto = inventarios.cod_producto
        where inventarios_diarios.cod_unidad = 1
        and inventarios_diarios.cod_bodega = 1
        and fecha > :fecha
        and inventarios.Ubicacion_X = 1 
        group by inventarios_diarios.cod_unidad',['fecha'=>'2020-06-19']);
		foreach($datos as $d)
		{
			$existenciaA =  (floatval($d->punteo));

		}
		
        $datosB = DB::select('select inventarios_diarios.cod_unidad,  ROUND(sum( punteo)/ count(distinct inventarios.cod_producto),2) as punteo
        from inventarios_diarios
        join inventarios on inventarios_diarios.cod_producto = inventarios.cod_producto
        where inventarios_diarios.cod_unidad = 1
        and inventarios_diarios.cod_bodega = 1
        and fecha > :fecha
        and inventarios.Ubicacion_X = 2
        group by inventarios_diarios.cod_unidad',['fecha'=>'2020-07-19']);
		foreach($datosB as $dB)
		{
			$existenciaB =  (floatval($dB->punteo));

		}
		//return response()->json($yourFirstChart);
		return view('reportes.grafica_chafa', compact('existenciaA','existenciaB'));
    }
}
