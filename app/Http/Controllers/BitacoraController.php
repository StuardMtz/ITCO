<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Bitacora;
use App\Historial;
use Carbon\Carbon;
use App\SolicitudEnvio;
use Yajra\DataTables\DataTables;

class BitacoraController extends Controller
{
  public function __construct()
    {
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
        $this->user = Auth::user();
        return $next($request);
      });
    }
//ver_mapa
//----------------------------------------------- Función para ver el historial de una entrega realizada ------------------------------------------------------
/*Funcion que muestra el historial de actividades realizadas por un usuario dentro de la aplicación*/
  public function historial($id)
  { 
    $bit = Bitacora::where('id_entrega',$id)->orderby('id','desc')->get();
    return view('bitacora.bitacora_entrega',compact('bit','id'));
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function vista_entregas_por_aldea($id)
  {
/*Muestra las entregas realizadas a una aldea seleccionada*/
    return view('bitacora.entregasAldeas',compact('id'));
  }

  public function todas_las_entregas_aldeas($id)
  {
    /*Carga todas las entregas realizadas a una aldea seleccionada*/
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel con el listado de entregas por aldeas';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    $entregas = DB::select('select c.nombre, a.nombre as aldea, count(i.id) as entregas,i.id_cliente,
    u.name, i.id_otros
    from  inventario_web_entregas as i,
    inventario_web_aldeas_otros as a,
    inventario_web_clientes as c,
    users as u,
    where a.id = i.id_otros
    and i.id_estado = 8
    and i.id_cliente = c.id
    and i.id_sucursal = u.id
    and i.id_otros = :id
    group by c.nombre, a.nombre, i.id_cliente, u.name, i.id_otros',['id'=>$id]);
    return DataTables::of($entregas)->addColumn('details_url', function($entregas){
      return url('detAlde/'. $entregas->id_cliente. '/'.$entregas->id_otros);
    })->make(true);
  }

  public function detalles_entregas_por_aldeas($id_cliente,$id_otros)
  {
/*Carga los detalles de las entregas realizadas a una aldea seleccionada*/
    $detalles = DB::select('select a.nombre as aldea, count(i.id) as entregas, i.fecha_carga,
    i.fecha_entregado, u.name, c.placa, i.id
    from  inventario_web_entregas as i,
    inventario_web_aldeas_otros as a,
    inventario_web_camiones as c,
    users as u,
    where a.id = i.id_otros
    and i.id_estado = 8
    and i.id_camion = c.id
    and i.id_usuario = u.id
    and i.id_otros = :id_otros
    and i.id_cliente = :id_cliente
    group by a.nombre, c.placa, i.fecha_carga, i.fecha_entregado, u.name, i.id',['id_otros'=>$id_otros,'id_cliente'=>$id_cliente]);
    return DataTables::of($detalles)->make(true);
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

  public function ver_entrega($id)
  {
    $envio = SolicitudEnvio::find($id);//Devuleve toda la información que contiene una entrega
    $total = Carbon::parse($envio->fecha_carga)->DiffInMinutes(Carbon::parse($envio->fecha_entregado));
    return view('solicitudes.ver_envio',compact('envio','total'));
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Vista de todos los clientes registrados en el sistema de entregas ---------------------------------------------------------------
  public function por_cliente()
  {
    return view('bitacora.clientes');//Devuelve a la vista que contiene todos los clientes almacenados
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Vista de las entregas realizadas a un mismo cliente -----------------------------------------------------------------------------
  public function entregas_cliente($id)
  { 
    $id       = $id;
    return view('bitacora.total_entregas',compact('id'));
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de camiones para entregas ---------------------------------------------------------------------------
  public function listado_camiones()
  {
    $camiones = DB::select('select * from inventario_web_camiones
    where id_sucursal != 0');
    return view('bitacora.vista_camiones',compact('camiones'));
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver las entregas realizadas por un camion --------------------------------------------------------------------------
  public function entregas_por_camion($id)
  {
    /*Muestra la vista con las entregas realizadas por el camión seleccionado*/
    $id = $id;
    return view('bitacora.rutas_camion',compact('id'));
  }
}
