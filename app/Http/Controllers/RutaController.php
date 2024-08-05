<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Ruta;
use App\User;
use App\Estado;
use App\Camion;
use Carbon\Carbon;
use App\SolicitudEnvio;
use Yajra\DataTables\DataTables;

class RutaController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->user = Auth::user();
      return $next($request);
    });
  }

  //vista_agregar_camion

//----------------------------------------------- Funcion para asignar rutas a los camiones -------------------------------------------------------------------
  public function asignar_ruta_camiones()
  {
    
    return view('rutas.vista_camiones',compact('camiones','rutas'));
  }
//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//guardar_camion


  //-------------------------------------------- Vista de entregas en espera ----------------------------------------------------------------------------------
  public function inicio()
  {
    //Muestra las solicitudes y entregas que están pendientes de agregar a una ruta
    $solicitudes = SolicitudEnvio::where('id_sucursal',Auth::id())->where('id_estado',1)->orwhere('id_usuario',Auth::id())
    ->where('id_entregar','!=','')->where('id_estado',1)->paginate(15);
    return view('rutas.inicio',compact('solicitudes'));
  }
    
  public function datos_inicio()
  {
    /*Carga los datos de las entregas que se encuentran pendientes de asignar a una ruta*/
    $solicitudes = DB::table('inventario_web_entregas')
    ->join('inventario_web_clientes','inventario_web_entregas.id_cliente','=','inventario_web_clientes.id')
    ->join('inventario_web_aldeas_otros','inventario_web_entregas.id_otros','=','inventario_web_aldeas_otros.id')
    ->select(['inventario_web_entregas.id as ide','inventario_web_entregas.comprobante','inventario_web_entregas.created_at'
    ,'inventario_web_clientes.nombre','inventario_web_aldeas_otros.nombre as aldea'])
    ->where('inventario_web_entregas.id_sucursal','=',Auth::id())->where('inventario_web_entregas.id_estado',1)
    /*->orwhere('inventario_web_entregas.id_usuario',Auth::id())
    ->where('inventario_web_entregas.id_entregar','!=','')->where('inventario_web_entregas.id_estado',1)*/;
    return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes 
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //-------------------------------------------- Vista de Entregas en Ruta ------------------------------------------------------------------------------------
  public function solicitudes_en_ruta()
  {
    //Muestra las solicitudes y entregas que se encuentran en ruta, pero aún no están entregadas
    $solicitudes = SolicitudEnvio::where('id_sucursal',Auth::id())->where('id_estado','>=',2)->where('id_estado','<=',7)
    ->orwhere('id_usuario',Auth::id())->where('id_entregar','!=','')->where('id_estado','>=',2)->where('id_estado','<=',7)->paginate(15);
    return view('rutas.en_ruta',compact('solicitudes'));
  }
    
  public function d_solicitudes_en_ruta()
  {
    /*Carga los datos de las entregas que se encuentran en ruta*/
    $solicitudes = DB::table('inventario_web_entregas')
    ->join('inventario_web_clientes','inventario_web_entregas.id_cliente','=','inventario_web_clientes.id')
    ->join('inventario_web_aldeas_otros','inventario_web_entregas.id_otros','=','inventario_web_aldeas_otros.id')
    ->join('inventario_web_estados','inventario_web_entregas.id_estado','=','inventario_web_estados.id')
    ->join('inventario_web_camiones','inventario_web_entregas.id_camion','=','inventario_web_camiones.id')
    ->select(['inventario_web_entregas.id as ide','inventario_web_entregas.comprobante','inventario_web_entregas.created_at'
    ,'inventario_web_clientes.nombre','inventario_web_aldeas_otros.nombre as aldea','inventario_web_estados.nombre as estado',
    'inventario_web_camiones.placa as placa'])
    ->where('inventario_web_entregas.id_sucursal','=',Auth::id())->whereBetween('inventario_web_entregas.id_estado',[2,7]);
    return DataTables::of($solicitudes)->make(true);
    //Muestra las solicitudes y entregas que se encuentran en ruta, pero aún no están entregadas
    /*$solicitudes = SolicitudEnvio::where('id_sucursal',Auth::id())->where('id_estado','>=',2)->where('id_estado','<=',7)
    ->orwhere('id_usuario',Auth::id())->where('id_entregar','!=','')->where('id_estado','>=',2)->where('id_estado','<=',7)->paginate(15);*/
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------  Vista de Entregas en Ruta -------------------------------------------------------------------------
  public function solicitudes_finalizadas()
  {
    //Muestra las solicitudes y entregas que están completadas
    $solicitudes = SolicitudEnvio::where('id_sucursal',Auth::id())->where('id_estado',8)->where('id_sucursal',Auth::id())
    ->where('id_estado',8)->orderby('id','desc')->paginate(15);
    return view('rutas.fin',compact('solicitudes'));
  }
    
  public function d_solicitudes_finalizadas()
  {
    /*Carga los datos de las entregas que han sido entregadas y marcadas como finalizadas por parte del piloto*/
    $solicitudes = DB::table('inventario_web_entregas')
    ->join('inventario_web_clientes','inventario_web_entregas.id_cliente','=','inventario_web_clientes.id')
    ->join('inventario_web_aldeas_otros','inventario_web_entregas.id_otros','=','inventario_web_aldeas_otros.id')
    ->join('inventario_web_estados','inventario_web_entregas.id_estado','=','inventario_web_estados.id')
    ->join('inventario_web_camiones','inventario_web_entregas.id_camion','=','inventario_web_camiones.id')
    ->select(['inventario_web_entregas.id as ide','inventario_web_entregas.comprobante','inventario_web_entregas.created_at'
    ,'inventario_web_clientes.nombre','inventario_web_aldeas_otros.nombre as aldea','inventario_web_estados.nombre as estado',
    'inventario_web_camiones.placa as placa'])
    ->where('inventario_web_entregas.id_sucursal','=',Auth::id())->where('inventario_web_entregas.id_estado',8);
    return DataTables::of($solicitudes)->make(true);
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------ Vista de rutas en espera ¡Actualmente sin uso ------------------------------------------------------
  /*public function rutas_sin_finalizar()
  {
    //Las rutas contienen las solicitudes y entrgas que debe realizar un piloto, estan marcadas con 1 cuando no han sido finalizadas
    $rutas = Ruta::where('id_usuario',Auth::id())->where('id_estado',1)->paginate(15);
    return view('rutas.sin_finalizar',compact('rutas'));
  }*/
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------ Vista de rutas finalizadas -------------------------------------------------------------------------
  /*Actualmente sin uso
  public function finalizadas()
  {
    //Las rutas se deben finalizar luego de finalizar las entregas y solicitudes que contiene la misma
    $rutas = Ruta::where('id_usuario',Auth::id())->where('id_estado',8)->get();
    $id = Auth::id();
    return view('rutas.finalizadas',compact('rutas','id'));
  }*/
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  

  //------------------------------------------------------ Vista para crear una nueva ruta --------------------------------------------------------------------
  public function vista_crear_ruta($id)
  {
    //Muestra la vista para agregar las entregas a una nueva ruta
    $rut = Ruta::find($id);//Obtiene el ID de la ruta creada, para poder agregar o eliminar una entrega o solicitud
    $soli_agregadas = SolicitudEnvio::where('id_ruta',$id)->get();//Muestra las entregas y solicitudes que están agregadas a la ruta
    $solicitudes = SolicitudEnvio::where('id_sucursal',Auth::id())->where('id_estado',1)->orderby('id_otros')->get();//
    $ruta = $id;
    return view('rutas.vista_crear_ruta',compact('solicitudes','soli_agregadas','ruta','rut'));
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  

  

  

  //------------------------------------------------------ Funcion para agregar entrega a una ruta ------------------------------------------------------------
  public function agregar_envio_a_ruta($id,$ruta,Request $request)
  {
    //Permite agregar una entrega o solicitud a una ruta creada
    $fecha = new Carbon();
    $rut = Ruta::find($ruta);
    if($solicitud = SolicitudEnvio::where('id',$id)->where('id_estado',1)->first())/*Si la ruta sigue en espera, permite agregar una nueva entrega
    o solicitud a la misma */
    {
      $editar = SolicitudEnvio::where('id',$id)->update(['id_ruta'=>$ruta,'id_estado'=>2,'id_camion'=>$rut->id_camion,'fecha_asignacion'=>$fecha]);
      return back()->with('success','Agregado con exito');//Se actualiza la información de la entrega que se ha agregado
    }
    else
    {
      return back()->with('error','No es posible agregar envío');
    }
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------ Funcion para eliminar entrega de ruta --------------------------------------------------------------
  public function eliminar_envio_de_ruta($id,$ruta)
  {
    if($solicitud = SolicitudEnvio::where('id',$id)->where('id_estado',2)->first())/*Se permite eliminar una solicitud o entrega si aún siguen
    marcada como asignada, de lo contrario no se permite modificar*/
    {
      $editar = SolicitudEnvio::where('id',$id)->update(['id_ruta'=>NULL,'id_estado'=>1,'id_camion'=>NULL]);
      return back()->with('success','Envío eliminado con exito');
    }
    else
    {
      return back()->with('error','No es posible eliminar un envío entregado');
    }
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------ Muestra la información de la entrega en un modal ---------------------------------------------------
  public function ver_modal($id)
  {
    //Carga la vista de una entrega o solicitud dentro de un modal
    $envio = SolicitudEnvio::find($id);
    return view('rutas.ver_envio',compact('envio'));
    //return DataTables::of($data)->make(true);
    //return response()->json($data);
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------ Muestra la informacion de una entrega --------------------------------------------------------------
  public function ver($id)
  {
    $envio = SolicitudEnvio::find($id);//Muestra la informacion de una entrega o solicitud
    $total = Carbon::parse($envio->fecha_carga)->DiffInMinutes(Carbon::parse($envio->fecha_entregado));
    return view('rutas.ver',compact('envio','total'));
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  

  

  //------------------------------------------------------ Muestra el resultado de las busquedas por camion ---------------------------------------------------
  public function busqueda_por_camion($id, Request $request)
  {
    $id = $id;
    $entregas = SolicitudEnvio::where('id_camion',$id)->whereBetween('created_at',array($request->fecha_inicial,$request->fecha_final))
    ->get();//Permite hacer busquedas por fecha de las entregas que realizo un camión
    return view('rutas.busqueda_por_camion',compact('entregas','id'));
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //------------------------------------------------------ Busqueda de Rutas en general Actualmente sin uso ---------------------------------------------------
  public function busqueda($id, Request $request)
  {
    $id = $id;
    $rutas = Ruta::where('id_usuario',$id)->where('id_estado',8)->whereBetween('created_at',array($request->fecha_inicial,$request->fecha_final))
    ->get();//Permite realizar busquedas por fechas en las entregas y solicitudes marcadas como finalizadas
    return view('rutas.busqueda',compact('rutas','id'));
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  

  
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  

  //------------------------------------------------------ Agregar Fletero, actualemente s encuentras sin uso -------------------------------------------------
    public function agregar_fletero()
    {
      //-------------------------Ni idea :v -----------------------------------------------------------------------------------
      if($camiones = Camion::where('id_sucursal',Auth::id())->where('id_piloto','==',''))
      {
        return redirect()->route('v_camiones')->with('error','No puede tener más de un fletero');
      }
      else
      {
        $n_camion               = new Camion();
        $n_camion->marca        = 'Fletero';
        $n_camion->placa        = 'Fletero';
        $n_camion->tonelaje     = 0;
        $n_camion->id_estado    = 1;
        $n_camion->tipo_camion  = 0;
        $n_camion->espacio      = 0;
        $n_camion->id_sucursal  = Auth::id();
        $n_camion->save();
        return redirect()->route('v_camiones')->with('success','Agregado correctamente');
      }
    }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  
    
//-------------------------------------------------------- Funcion para finalizar una entrega por parte del encargado de la sucursal --------------------------
  public function cancelar_solicitud($id,Request $request)
  {
    //Muestra el formulario para modificar una entrega
    $entrega = SolicitudEnvio::find($id);
    $estados = DB::table('inventario_web_estados')->where('id','>',0)->where('id','<',10)->get();
    return view('rutas.finalizar_entrega',compact('entrega','estados'));
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}
