<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Historial;
use Carbon\Carbon;
use Auth;
use App\User;
use App\Cliente;
use App\SolicitudEnvio;

class AsesoresController extends Controller
{
  //total_entregas
  //Controlador para las actividades de los asesores 
  public function __construct()
    {
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
        $this->user = Auth::user();
        return $next($request);
      });
    }
 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

 //------------------------------------------------------------------------------------------------------------------------------------------------------------ 
 /*Funciones para la vista inicial de los usuarios con rol de asesores*/   
  public function inicio()
  {
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel con el listado de sucursales';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    return view('asesores.inicio');
  }
    
  public function datos_inicio()
  {
    $sucursales = DB::table('users')
    ->select(['name','sucursal','bodega','id'])
    ->where('roles',3);//El rol 3 es para los asesores
    return DataTables::of($sucursales)->make(true);
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------------------------------------------------------------- 
/*Funciones para mostrar la existencia de productos dentro de una sucursal*/
  public function existencia_sucursal($sucursal,$bodega,$todo)
  {
    $sucu     = $sucursal; 
    $bod      = $bodega;
    $ayer     = Carbon::yesterday();
    $hoy      = Carbon::now();
    $sucursal = DB::table('unidades')->where('cod_unidad',$sucu)->where('empresa',1)->first();
    $bodega   = DB::table('bodegas')->where('empresa',1)->where('cod_unidad',$sucu)->where('cod_bodega',$bod)->first();
    $historial              = new Historial();
    $historial->id_usuario  = Auth::user()->id;
    $historial->actividad   = 'Consulto la existencia de la sucursal'.' '.$sucursal->nombre.' '.'de la bodega'.' '.$bodega->nombre.' '.'a las'.' '.$hoy;
    $historial->id_sucursal = $sucu;
    $historial->created_at  = Carbon::now();
    $historial->updated_at  = Carbon::now();
    $historial->save();
    if($todo == 1)
    {
      /*Muestra el total de productos dentro de la sucursal*/
      return view('asesores.sucursal_existencia',compact('sucu','bod','sucursal','bodega'));
    }
    elseif($todo == '2')
    {
      /*Muestra la existencia de los productos que se encuentran debajo del mínimo requerido*/
      return view('asesores.sucursal_existencia_minimo',compact('sucu','bod','sucursal','bodega'));
    }
    else
    {
      /*Muestra la existencia de los productos que se encuentran entre el punto de reorden y el mínimo*/
      return view('asesores.sucursal_existencia_reorden',compact('sucu','bod','sucursal','bodega'));
    }
  }

  public function datos_existencia($sucu,$bod)
  {
    /*Funcion que carga los datos de las existencias de todos los productos dentro de una sucursal*/
    $datos = DB::select('select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
        productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
        unidades.nombre as su_nombre,bodegas.nombre as bo_nombre, inventarios.minimo as min,inventarios.maximo as max,
        inventario_web_categorias.nombre as cod_tipo_prod, inventarios.piso_sugerido as reorden,
         (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, unidades.cod_unidad, bodegas.cod_bodega,
         ((existencia/max)*100) porcentaje 
        from inventarios
        join unidades on unidades.cod_unidad = inventarios.cod_unidad and unidades.empresa = 1
        join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad and bodegas.empresa = 1
        join productos_inve on productos_inve.cod_producto = inventarios.cod_producto and productos_inve.empresa = 1
        join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
        where inventarios.cod_unidad = :suc
        and inventarios.cod_bodega = :bod
        and inventarios.minimo <> 0
        and descontinuado = :des
        and inventario_web_categorias.empresa = 1
        and productos_inve.cod_tipo_prod <> :servicios
        group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia,
        unidades.cod_unidad, bodegas.cod_bodega
        order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucu,'bod'=>$bod]);
    return DataTables::of($datos)->make(true);
  }

  public function datos_existencia_minimos($sucursal,$bodega)
  {
    /*Funcion que carga los datos de las existencias de los productos que estan abajo del mínimo dentro de una sucursal*/
    $datos = DB::select('select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
        productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
        unidades.nombre as su_nombre,bodegas.nombre as bo_nombre, inventarios.minimo as min,inventarios.maximo as max,
        inventario_web_categorias.nombre as cod_tipo_prod, inventarios.piso_sugerido as reorden,
        (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, unidades.cod_unidad, bodegas.cod_bodega,
        ((existencia/max)*100) porcentaje 
        from inventarios
        join unidades on unidades.cod_unidad = inventarios.cod_unidad and unidades.empresa = 1
        join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad and bodegas.empresa = 1
        join productos_inve on productos_inve.cod_producto = inventarios.cod_producto and productos_inve.empresa = 1
        join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
        where inventarios.cod_unidad = :suc
        and inventarios.cod_bodega = :bod
        and inventarios.minimo <> 0
        and descontinuado = :des
        and existencia <= min
        and inventario_web_categorias.empresa = 1
        and productos_inve.cod_tipo_prod <> :servicios
        group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia,
        unidades.cod_unidad, bodegas.cod_bodega
        order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega]);
    return DataTables::of($datos)->make(true);
  }

  public function datos_existencia_reorden($sucursal,$bodega)
  {
    /*Funcion que carga los datos de las existencias de los productos que estan entre el reorden y el mínimo dentro de una sucursal*/
    $datos = DB::select('select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
        productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
        unidades.nombre as su_nombre,bodegas.nombre as bo_nombre, inventarios.minimo as min,inventarios.maximo as max,
        inventario_web_categorias.nombre as cod_tipo_prod, inventarios.piso_sugerido as reorden,
        (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, unidades.cod_unidad, bodegas.cod_bodega,
        ((existencia/max)*100) porcentaje
        from inventarios
        join unidades on unidades.cod_unidad = inventarios.cod_unidad and unidades.empresa = 1
        join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad and bodegas.empresa = 1
        join productos_inve on productos_inve.cod_producto = inventarios.cod_producto and productos_inve.empresa = 1
        join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
        where inventarios.cod_unidad = :suc
        and inventarios.cod_bodega = :bod
        and inventarios.minimo <> 0
        and descontinuado = :des
        and inventario_web_categorias.empresa = 1
        and existencia between min and reorden
        and productos_inve.cod_tipo_prod <> :servicios
        group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia,
        unidades.cod_unidad, bodegas.cod_bodega
        order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega]);
    return DataTables::of($datos)->make(true);
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------  
  public function vista_rutas($sucursal)
  {
    /*Muestra el listado de entregas pendientes de los camiones, según la sucursal que se ha seleccionado*/
    $sucursal= $sucursal;
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel con el listado de entregas pendientes';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    return view('asesores.rutas',compact('sucursal'));
  }
 
  public function listado_rutas($sucursal)
  {
    /*Carga los datos de las entregas pendientes de entregar por sucursal seleccionada*/
    $rutas = DB::table('inventario_web_entregas')
    ->join('inventario_web_clientes','inventario_web_entregas.id_cliente','=','inventario_web_clientes.id')
    ->join('inventario_web_estados','inventario_web_entregas.id_estado','=','inventario_web_estados.id')
    ->leftjoin('inventario_web_camiones','inventario_web_entregas.id_camion','=','inventario_web_camiones.id')
    ->select(['inventario_web_clientes.nombre as nombre','inventario_web_entregas.comprobante','inventario_web_entregas.created_at as fecha_solicitud',
    'inventario_web_estados.nombre as estado','inventario_web_camiones.marca','inventario_web_camiones.placa',
    'inventario_web_entregas.id as ide'])
    ->where('inventario_web_entregas.id_sucursal',$sucursal)->whereIn('inventario_web_entregas.id_estado',[1,2,3,4,5,6,7]);
    return DataTables::of($rutas)->make(true);
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function vista_clientes($su)
  {
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel con el listado de clientes';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    return view('asesores.clientes',compact('su'));//Devuelve a la vista que contiene todos los clientes almacenados
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function nueva_solicitud($id)
  {
    //Se llama solicitud cuando un asesor o sucursal solicita la entrega de producto a una sucursal
    $sucursales     = User::where('roles',3)->get();/*Devuelve las sucursales a las que se puede solicitar una entrega para ser
    entregada a un cliente. No aplica a entregas entre sucursales */
    $cliente        = Cliente::find($id);//Devuelve la información registrada de un cliente
    $departamentos  = DB::table('inventario_web_departamentos')->where('id','!=',$cliente->id_departamento)
    ->get();
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel para generar una nueva solicitud de entrega a una sucursal';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para esta acción');
    }
    else
    {
      return view('asesores.nueva_entrega',compact('sucursales','cliente','departamentos'));
    }
  }

  public function guardar_solicitud(Request $request)
  {
    /*Funcion que permite guardar la información de una nueva solicitud de entrega realizada por una sucursal*/
    $s_envio                    = new SolicitudEnvio();
    $s_envio->id_cliente        = $request->cliente;
    $s_envio->comprobante       = $request->comprobante;
    $s_envio->id_sucursal       = $request->sucursal;
    $s_envio->fecha_entrega     = $request->fecha_entrega;
    $s_envio->hora              = $request->hora;
    $s_envio->id_usuario        = Auth::id();
    $s_envio->id_estado         = 1;
    $s_envio->id_departamento   = $request->departamento;
    $s_envio->id_municipio      = $request->municipio;
    $s_envio->id_otros          = $request->otros;
    $s_envio->direccion         = $request->direccion;
    $s_envio->detalle_entrega   = $request->detalle_entrega;
    $s_envio->detalle_direccion = $request->detalle_direccion;
    $s_envio->created_at        = Carbon::now();
    $s_envio->updated_at        = Carbon::now();
    $s_envio->save(); 
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para esta acción');
    }
    else
    {
      return redirect()->route('vista_clientes',['id'=>$request->sucursal])->with('success','Nueva solicitud de entrega creada.');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function vista_editar_cliente($id,$suc)
  {
    /*Funcion para mostrar la vista del formulario que permite editar la información de un cliente*/
    $cliente        = Cliente::find($id);
    $departamentos  = DB::table('inventario_web_departamentos')->where('id','!=',$cliente->id_departamento)
    ->get();
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para realizar esta acción');
    }
    else
    {
      return view('asesores.editar_cliente',compact('cliente','departamentos','suc'));
    }
  }
   
  public function editar_cliente($id,Request $request)
  {
    //Funcion que permite almacenar los cambios realizados a información de un cliente*/
    $cliente                  = Cliente::FindOrFail($id);
    $cliente->nit             = $request->nit;
    $cliente->nombre          = $request->cliente;
    $cliente->correo          = $request->correo;
    $cliente->telefono        = $request->telefono;
    $cliente->id_departamento = $request->departamento;
    $cliente->id_municipio    = $request->municipio;
    $cliente->id_otros        = $request->otros;
    $cliente->direccion       = $request->direccion;
    $cliente->created_at      = Carbon::now();
    $cliente->updated_at      = Carbon::now();
    if($cliente->save())
    {
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Edito la información del cliente'. ' '.$request->cliente;
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
    }
    return redirect()->route('home');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function vista_entregas($id,$suc) 
  {
    /*Muestra la vista con el total de entregas realizadas a un cliente en especifico*/
    $id = $id;
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel con el listado de entregas realizadas a un cliente';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    return view('asesores.total_entregas',compact('id','suc'));
  }
    
  
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function nuevo_cliente()
  {
    /*Funcion que permite ver el formulario para agregar un nuevo cliente al sistema*/
    $departamentos = DB::table('inventario_web_departamentos')->get();
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('error','Usted no tiene permisos para esta acción');
    }
    else
    {
      return view('asesores.nuevo_cliente',compact('departamentos'));
    }
  }

  public function guardar_cliente(Request $request)
  {
    /*Funcion que permite guardar los datos del nuevo cliente al sistema*/
    if($request->nit == '')//Si el usuario no posee NIT, se almacena la información, no valida que el cliente exista.
    {
      $cliente                  = new Cliente();
      $cliente->nit             = $request->nit;
      $cliente->nombre          = $request->cliente;
      $cliente->correo          = $request->correo;
      $cliente->telefono        = $request->telefono;
      $cliente->id_departamento = $request->departamento;
      $cliente->id_municipio    = $request->municipio;
      $cliente->id_otros        = $request->otros;
      $cliente->direccion       = $request->direccion;
      $cliente->id_tipo         = 1;
      $cliente->created_at      = Carbon::now();
      $cliente->updated_at      = Carbon::now();
      if($cliente->save())
      {
        $historial              = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a '.$request->cliente.' '.'como nuevo cliente al sistema';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
      }
      $id_c                     = $cliente->created_at;
      $id = Cliente::where('created_at',$id_c)->first();
      return redirect()->route('nueva_solicitud',['id'=>$id]);
    }
    elseif($request->nit != '')//Si el cliente cuenta con número de NIT, se valida si el número ya existe
    {
      if($no_repetir = Cliente::where('nit',$request->nit)->first())//Sí existe el número de NIT ingresado, no se permite almacenar la información
      {
        return redirect()->route('s_inicio')->with('error','Ya existe el número de Nit');
      }
      else//Sino existe dicho NIT, se permite almacenar la información del cliente
      {
        $cliente                  = new Cliente();
        $cliente->nit             = $request->nit;
        $cliente->nombre          = $request->cliente;
        $cliente->correo          = $request->correo;
        $cliente->telefono        = $request->telefono;
        $cliente->id_departamento = $request->departamento;
        $cliente->id_municipio    = $request->municipio;
        $cliente->id_otros        = $request->otros;
        $cliente->direccion       = $request->direccion;
        $cliente->id_tipo         = 1;
        $cliente->created_at      = Carbon::now();
        $cliente->updated_at      = Carbon::now();
        if($cliente->save())
        {
          $historial              = new Historial();
          $historial->id_usuario  = Auth::id();
          $historial->actividad   = 'Ingreso a '.$request->cliente.' '.'como nuevo cliente al sistema';
          $historial->created_at  = new Carbon();
          $historial->updated_at  = new Carbon();
          $historial->save();
        }
        $id_c                     = $cliente->created_at;
        $id = Cliente::where('created_at',$id_c)->first();
        return redirect()->route('nueva_solicitud',['id'=>$id]);
      }
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------------------------------------------------------------- 

  public function vista_lista_entregas($sucursal)
  {
    /*Funcion que muestra la vista con el listado de entregas solicitadas por el asesor a una sucursal*/
    $sucursal = $sucursal;
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel que muestra las entregas solicitadas por el asesor a una sucursal';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    return view('asesores.listado_entregas',compact('sucursal'));
  }
 
  public function listado_entregas($sucursal)
  {
    /*Funcion que carga los datos de las entregas solicitas por el asesor a una de las sucursales*/
    $listado = DB::table('inventario_web_entregas')
    ->join('inventario_web_clientes','inventario_web_entregas.id_cliente','=','inventario_web_clientes.id')
    ->join('inventario_web_estados','inventario_web_entregas.id_estado','=','inventario_web_estados.id')
    ->leftJoin('inventario_web_camiones','inventario_web_entregas.id_camion','=','inventario_web_camiones.id')
    ->join('users','inventario_web_entregas.id_sucursal','=','users.id')
    ->select(['inventario_web_clientes.nombre as nombre_cliente','inventario_web_entregas.comprobante as comprobante',
    'inventario_web_entregas.created_at as fecha_solicitud',
    'inventario_web_estados.nombre as estado','inventario_web_camiones.marca as marca','inventario_web_camiones.placa as placa',
    'inventario_web_entregas.id as ide','users.name as sucursal'])
    ->where('inventario_web_entregas.id_estado','!=',8)->where('inventario_web_entregas.id_usuario','=',Auth::id());
  return DataTables::of($listado)->make(true);
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

////////////////////////////////////////////////////////////////////////////////Vista para editar una entrega////////////////////////////////////////
public function vista_editar_solicitud($id)
{
  //Vista para editar una solicitud. Solo se permite editar solicitudas que están es espera
  $sucursales     = User::where('roles',3)->get();//Devuelve el listado de sucursales a las que se puede realizar la solicitud
  $solicitudes    = SolicitudEnvio::find($id);//Devuelve la información de la solicitud a editar
  $departamentos  = DB::table('inventario_web_departamentos')->where('id','!=',$solicitudes->id_departamento)
  ->get();//Devuelve un listado de los departamentos guardados.
  if($solicitudes->id_usuario != Auth::id())
  {
    return back()->with('error','¡No puedes editar entregas de otro usuario!');
  }
  else
  {
    return view('asesores.editar_entrega',compact('sucursales','solicitudes','departamentos'));
  }
}
//---------------------------------------------------------------------------------------------------------------------------------------------------

////////////////////////////////////////////////////////////////////////////////Funcion para editar una entrega//////////////////////////////////////
public function editar_solicitud($id,Request $request)
{
  $envio = SolicitudEnvio::find($id);
  if($envio->id_usuario != Auth::id())
  {
    return back()->with('error','No puede editar solicitudes de otro usuario');
  }
  else
  {
    if($s_envio = SolicitudEnvio::where('id',$id)->where('id_estado',1)->orwhere('id',$id)->where('id_estado',9)->first())/*Verifica que la
    solicitud se encuentre en espera o cancelada */
    {
      $s_envio                    = SolicitudEnvio::FindOrFail($id);
      $s_envio->id_cliente        = $request->cliente;
      $s_envio->comprobante       = $request->comprobante;
      $s_envio->id_sucursal       = $request->sucursal;
      $s_envio->fecha_entrega     = $request->fecha_entrega;
      $s_envio->hora              = $request->hora;
      $s_envio->id_estado         = $s_envio->id_estado;
      $s_envio->id_departamento   = $request->departamento;
      $s_envio->id_municipio      = $request->municipio;
      $s_envio->id_otros          = $request->otros;
      $s_envio->direccion         = $request->direccion;
      $s_envio->detalle_entrega   = $request->detalle_entrega;
      $s_envio->detalle_direccion = $request->detalle_direccion;
      //$s_fenvio->updated_at       = new Carbon();
      $s_envio->save();
      return back()->with('success','Guardado con Exito');
    }
    else
    {
      return redirect()->route('s_e_ruta')->with('error','Envió en camino, no se permiten cambios');
      //Si la solicitud se encuentra en otro estado, no se permite realizar modificaciones
    }
  }
}
//---------------------------------------------------------------------------------------------------------------------------------------------------
}
