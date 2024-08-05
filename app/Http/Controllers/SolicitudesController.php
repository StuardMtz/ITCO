<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Ruta;
use App\User;
use App\Estado;
use App\Camion;
use App\Cliente;
use App\Bitacora;
use Carbon\Carbon;
use App\Historial;
use App\Municipio;
use App\SolicitudEnvio;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

//
class SolicitudesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->user = Auth::user();
      return $next($request);
    });
  }
  //
  //--------------------------------------------- Vista solicitudes para Clientes en espera -------------------------------------------------------------------
  public function inicio()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwc.nombre, u.name, iwe.fecha_entrega, iwe.hora, iwe.comprobante, iwe.id_departamento, iwe.id_usuario
      from inventario_web_entregas as iwe,
      users as u,
      inventario_web_clientes as iwc
      where id_estado in (1,9)
      and id_usuario = :us
      and u.id = iwe.id_sucursal
      and iwc.id = iwe.id_cliente
      or iwe.id_sucursal = :su
      and u.id = iwe.id_sucursal
      and iwc.id = iwe.id_cliente
      and id_estado in (1)",['us'=>Auth::id(),'su'=>Auth::id()]);//Devuleve las solicitudes que se encuentran en espera o las que fueron canceladas
      //Muestra el listado de camiones disponibles en la sucursal para asignar una ruta
      $camiones = DB::select('select c.marca, c.placa, c.tonelaje, c.tipo_camion, e.nombre, c.id
      from inventario_web_camiones as c,
      inventario_web_estados as e
      where c.id_estado = e.id
      and c.id_sucursal = :sucursal
      and c.id_estado in (21,22)',['sucursal'=>Auth::id()]);
      //Las rutas contienen las solicitudes y entrgas que debe realizar un piloto, estan marcadas con 1 cuando no han sido finalizadas
      $rutas = DB::select('select iwr.id, iwr.fecha_entrega, iwc.placa, count(iwe.id) as pendientes
      from inventario_web_rutas as iwr
      join inventario_web_camiones as iwc on iwr.id_camion = iwc.id
      left join inventario_web_entregas as iwe on iwr.id = iwe.id_ruta
      where iwr.id_usuario = :usuario
      and iwr.id_estado = 1
      group by iwr.id, iwr.fecha_entrega, iwc.placa
      order by iwr.id',['usuario'=>Auth::id()]);
      //modal
      $rutasp = DB::select('select iwc.id, iwr.id AS ruta_id, iwr.fecha_entrega, iwc.placa, iwc.tonelaje, u.name, COUNT(iwe.id) AS pendientes
      FROM inventario_web_camiones AS iwc
      LEFT JOIN inventario_web_rutas AS iwr ON iwc.id = iwr.id_camion AND iwr.id_estado = 1
      LEFT JOIN inventario_web_entregas AS iwe ON iwr.id = iwe.id_ruta
      LEFT JOIN users AS u ON iwc.id_sucursal = u.id
      WHERE iwc.espacio > 0
      AND iwc.id_estado != 12
      GROUP BY iwc.id, iwr.id, iwr.fecha_entrega, u.name, iwc.placa, iwc.tonelaje
      ORDER BY iwr.fecha_entrega desc');
      return view('solicitudes.inicio',compact('solicitudes','camiones','rutas','rutasp'));
      //Estados; 1:En Espera, 9:Cancelado
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

    function todas_las_rutas_modal()
    {
      $rutas_modal = DB::select('select iwc.id, coalesce(iwr.id,0) AS ruta_id, iwr.fecha_entrega, iwc.placa, iwc.tonelaje, u.name, COUNT(iwe.id) AS pendientes
      FROM inventario_web_camiones AS iwc
      LEFT JOIN inventario_web_rutas AS iwr ON iwc.id = iwr.id_camion AND iwr.id_estado = 1
      LEFT JOIN inventario_web_entregas AS iwe ON iwr.id = iwe.id_ruta
      LEFT JOIN users AS u ON iwc.id_sucursal = u.id
      WHERE iwc.espacio > 0
      AND iwc.id_estado != 12
      GROUP BY iwc.id, iwr.id, iwr.fecha_entrega, u.name, iwc.placa, iwc.tonelaje
      ORDER BY iwr.fecha_entrega desc');
      return DataTables::of($rutas_modal)->addColumn('details_url', function($rutas_modal){
        return url('v_det_ruta_/'. $rutas_modal->ruta_id);
      })->make(true);
    }

      function detalle_rutas($ruta_id)
      {
        $detalles = DB::select('SELECT
        iwen.id, iwen.comprobante, iwcl.nombre, iwen.fecha_asignacion, iwe.nombre as estado, iwe.porcentaje, iwen.fecha_entrega
        FROM
        inventario_web_entregas AS iwen,
        inventario_web_estados AS iwe,
        inventario_web_clientes As iwcl
        WHERE
        iwen.id_ruta = :ruta_id
        AND
        iwen.id_estado BETWEEN 2 AND 8
        and iwen.id_estado = iwe.id
        and iwen.id_cliente = iwcl.id',['ruta_id'=>$ruta_id]);
        return DataTables::of($detalles)->make(true);
      }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Vista para editar una entrega -------------------------------------------------------------------------------
  public function vista_editar_solicitud($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      //Vista para editar una solicitud. Solo se permite editar solicitudas que están es espera
      $sucursales     = User::where('roles',3)->where('empresa',Auth::user()->empresa)->get();//Devuelve el listado de sucursales a las que se puede realizar la solicitud
      $solicitudes    = SolicitudEnvio::find($id);//Devuelve la información de la solicitud a editar
      $departamentos  = DB::table('inventario_web_departamentos')->where('id','!=',$solicitudes->id_departamento)
      ->get();//Devuelve un listado de los departamentos guardados.
      if($solicitudes->id_usuario != Auth::id())
      {
        return back()->with('error','¡No puedes editar entregas de otro usuario!');
      }
      return view('solicitudes.editar_entrega',compact('sucursales','solicitudes','departamentos','id'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  //--------------------------------------------- Funcion para guardar cambios en una solicitud ---------------------------------------------------------------
  public function editar_solicitud($id,Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $envio = SolicitudEnvio::find($id);
      if($envio->id_usuario != Auth::id())
      {
        return redirect()->route('entregas_en_espera')->with('error','No puede editar solicitudes de otro usuario');
      }
      else
      {
        if($s_envio = SolicitudEnvio::where('id',$id)->where('id_estado',1)->orwhere('id',$id)->where('id_estado',9)->first())/*Verifica que la
        solicitud se encuentre en espera o cancelada */
        {
          $validator = Validator::make($request->all(),[
            'comprobante'=>'required',
            'sucursal'=>'required',
            'fecha_entrega'=>'required|date',
            'hora'=>'required',
            'departamento'=>'required',
            'municipio'=>'required',
            'otros'=>'required',
            'direccion'=>'required',
          ]);

          if ($validator->fails()) {
            return back()
            ->withErrors($validator)
            ->withInput();
          }
          else
          {
            $s_envio                    = SolicitudEnvio::FindOrFail($id);
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
            $s_envio->updated_at       = new Carbon();
            if($s_envio->save())
            {
              $historial              = new Historial();
              $historial->id_usuario  = Auth::id();
              $historial->actividad   = 'Realizo cambios en el la solicitud de entrega número '. $id;
              $historial->created_at  = Carbon::now();
              $historial->updated_at  = Carbon::now();
              $historial->save();
            }
            return redirect()->route('entregas_en_espera')->with('success','Guardado con Exito');
          }
          return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return redirect()->route('entregas_en_espera')->with('error','Envió en camino, no se permiten cambios');
         //Si la solicitud se encuentra en otro estado, no se permite realizar modificaciones
      }
      return back()->with('error','Parece que algo fallo, refresca la página...');
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Funcion para eliminar una solicitud  ------------------------------------------------------------------------
  public function eliminar_entrega_en_espera($id,Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $envio = SolicitudEnvio::find($id);
      if($envio->id_usuario != Auth::id())
      {
        return redirect()->route('entregas_en_espera')->with('error','No puede editar solicitudes de otro usuario');
      }
      else
      {
        if($s_envio = SolicitudEnvio::where('id',$id)->where('id_estado',1)->orwhere('id',$id)->where('id_estado',9)->first())/*Verifica que la
        solicitud se encuentre en espera o cancelada */
        {
          $s_envio                    = SolicitudEnvio::FindOrFail($id);
          $s_envio->id_estado         = 12;
          $s_envio->updated_at       = new Carbon();
          if($s_envio->save())
          {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Elimino la solicitud de entrega número '. $id;
            $historial->created_at  = Carbon::now();
            $historial->updated_at  = Carbon::now();
            $historial->save();
          }
          return redirect()->route('entregas_en_espera')->with('success','La entrega fue eliminada correctamente');
        }
        return redirect()->route('entregas_en_espera')->with('error','Envió en camino, no se permiten cambios');
          //Si la solicitud se encuentra en otro estado, no se permite realizar modificaciones
      }
      return back()->with('error','Parece que algo fallo, refresca la página...');
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Funciones para ver las solicitudes en ruta ------------------------------------------------------------------
  public function solicitudes_en_ruta()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      return view('solicitudes.solicitudes_en_ruta');
      //Estados; 2:Asigado, 3:Preparando Carga, 4:Esperando Salida, 5:En Ruta, 6:En Destino, 7:Descarga Destino
    }
     return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function datos_solicitudes_en_ruta()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $solicitudes = DB::select('select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca
      where iwe.id_usuario = :usuario
      and iwe.id_cliente = iwec.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado BETWEEN  2 and 7',['usuario'=>Auth::id()]);
      return DataTables::of($solicitudes)->make(true);
      //Estados; 2:Asigado, 3:Preparando Carga, 4:Esperando Salida, 5:En Ruta, 6:En Destino, 7:Descarga Destino
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Funciones para visualizar las entregas que ya fueron entregadas ---------------------------------------------
  public function solicitudes_entregadas()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      return view('solicitudes.solicitudes_entregadas');
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function datos_solicitudes_entregadas()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, iwe.comentarios,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca
      where iwe.id_usuario = :usuario
      and iwe.id_cliente = iwec.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado = 8",['usuario'=>Auth::id()]);
      return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Funciones para visualizar las entregas que ya fueron entregadas ---------------------------------------------
  public function solicitudes_entregadas_fecha(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $inicio = $request->inicio;
      $fin = $request->fin;
      return view('solicitudes.solicitudes_entregadas_fecha',compact('inicio','fin'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function datos_solicitudes_entregadas_fecha($inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, iwe.comentarios,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca
      where iwe.id_usuario = :usuario
      and iwe.id_cliente = iwec.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado = 8
      and iwe.fecha_entregado between :inicio and :fin",['usuario'=>Auth::id(),'inicio'=>$inicio,'fin'=>$fin]);
      return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Funcion para ver los detalles de un envío -------------------------------------------------------------------
  public function ver_solicitud($id)
    {
      if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
      {
        $envio = DB::select("select iwe.id, iwc.nombre, iwe.comprobante, u.name, iwe.hora, iwe.fecha_carga, iwe.fecha_ruta, iwe.fecha_destino, iwe.fecha_entregado, iwee.porcentaje,
        iwcm.placa, iwd.nombre as departamento, iwm.nombre as municipio, iwa.nombre as aldea, iwe.direccion, iwe.comentarios, iwe.detalle_entrega, iwe.detalle_direccion,
        iwe.fecha_asignacion, iwe.created_at, mod(datediff(hh,iwe.created_at,iwe.fecha_asignacion),60) as tiempo_esperah, iwe.fecha_entrega, iwe.latitud,
        mod(datediff(mi,iwe.created_at,iwe.fecha_asignacion),60) as tiempo_esperam, mod(datediff(hh,iwe.fecha_asignacion,iwe.fecha_carga),60) as tiempo_scargah,
        mod(datediff(mi,iwe.fecha_asignacion,iwe.fecha_carga),60) as tiempo_scargam, mod(datediff(hh,iwe.fecha_carga,iwe.fecha_ruta),60) as tiempo_cargah,
        mod(datediff(mi,iwe.fecha_carga,iwe.fecha_ruta),60) as tiempo_cargam, mod(datediff(hh,iwe.fecha_ruta,iwe.fecha_entregado),60) as tiempo_rutah,
        mod(datediff(mi,iwe.fecha_ruta,iwe.fecha_entregado),60) as tiempo_rutam, mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as tiempo_camionh,
        mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as tiempo_camionm, convert(varchar(10),tiempo_esperah)+' horas '+ convert(varchar(10),tiempo_esperam)+' minutos' as tiempo_en_espera_asignacion,
        convert(varchar(10),tiempo_scargah)+' horas '+ convert(varchar(10),tiempo_scargam)+' minutos' as tiempo_en_espera_carga,
        convert(varchar(10),tiempo_cargah)+' horas '+ convert(varchar(10),tiempo_cargam)+' minutos' as tiempo_carga,
        convert(varchar(10),tiempo_rutah)+' horas '+ convert(varchar(10),tiempo_rutam)+' minutos' as tiempo_en_ruta,
        convert(varchar(10),tiempo_camionh)+' horas '+ convert(varchar(10),tiempo_camionm)+' minutos' as tiempo_desde_carga_a_entrega
        from inventario_web_entregas as iwe
        join users as u on iwe.id_usuario = u.id
        join inventario_web_clientes as iwc on iwe.id_cliente = iwc.id
        left join inventario_web_camiones as iwcm on iwe.id_camion = iwcm.id
        join inventario_web_departamentos as iwd on iwe.id_departamento = iwd.id
        join inventario_web_municipios as iwm on iwe.id_municipio = iwm.id
        join inventario_web_aldeas_otros as iwa on iwe.id_otros = iwa.id
        join inventario_web_estados as iwee on iwe.id_estado = iwee.id
        where iwe.id = :id",['id'=>$id]);//Devuleve toda la información que contiene una entrega
        return view('solicitudes.ver_envio',compact('envio','id'));
      }
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

  //--------------------------------------------- Funciones para ver el listado de clientes disponibles para solicitar envios ---------------------------------
  public function vista_clientes()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      return view('solicitudes.listado_de_clientes');//Devuelve a la vista que contiene todos los clientes almacenados
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function datos_listado_de_clientes()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $clientes = DB::table('inventario_web_clientes')->where('id_tipo',1);//Devuelve el listado de clientes almacenado
      return DataTables::of($clientes)->make(true);//Envía los datos a la vista_clientes
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------
//s_inicio
//----------------------------------------------- Funciones para editar la información de un cliente ----------------------------------------------------------
  public function vista_editar_cliente($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $cliente        = Cliente::find($id);//Devuelve la información del cliente
      $departamentos  = DB::table('inventario_web_departamentos')->where('id','!=',$cliente->id_departamento)
      ->get();//Devuelve el listado de departamentos almacenado
      return view('solicitudes.editar_cliente',compact('cliente','departamentos','id'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function guardar_edicion_del_cliente($id,Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $validator = Validator::make($request->all(),[
        'cliente'=>'required',
        'departamento'=>'required',
        'municipio'=>'required',
        'otros'=>'required',
        'direccion'=>'required',
      ]);

      if ($validator->fails()) {
        return back()
        ->withErrors($validator)
        ->withInput();
      }
      else
      {
        //Se almacena los datos editados del cliente
        $cliente                  = Cliente::FindOrFail($id);
        $cliente->nit             = $request->nit;
        $cliente->nombre          = $request->cliente;
        $cliente->correo          = $request->correo;
        $cliente->telefono        = $request->telefono;
        $cliente->id_departamento = $request->departamento;
        $cliente->id_municipio    = $request->municipio;
        $cliente->id_otros        = $request->otros;
        $cliente->direccion       = $request->direccion;
        $cliente->updated_at      = Carbon::now();
        $cliente->save();
        $historial              = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Edito la información del cliente '. $request->cliente;
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        return redirect()->route('vista_clientes');
      }
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
  //-----------------------------------------------------------------------------------------------------------------------------------------------------------

// ---------------------------------------------- Vista para solicitar una nuega entrega o generar una entrega ------------------------------------------------
  public function generar_guardar_solicitud($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      //Se llama solicitud cuando un asesor o sucursal solicita la entrega de producto a una sucursal
      $sucursales     = User::where('roles',3)->where('empresa',Auth::user()->empresa)->get();/*Devuelve las sucursales a las que se puede solicitar una entrega para ser
      entregada a un cliente. No aplica a entregas entre sucursales */
      $cliente        = DB::select("select cl.id, cl.nit, cl.nombre, cl.correo, cl.telefono, cl.direccion, de.nombre as departamento,
      de.id as id_departamento, mu.nombre as municipio, mu.id as id_municipio, al.nombre as aldea, al.id as id_aldea
      from inventario_web_clientes as cl,
      inventario_web_departamentos as de,
      inventario_web_municipios as mu,
      inventario_web_aldeas_otros as al
      where cl.id = :id
      and cl.id_departamento = de.id
      and cl.id_municipio = mu.id
      and cl.id_otros = al.id",['id'=>$id]);//Devuelve la información registrada de un cliente
      $departamentos  = DB::table('inventario_web_departamentos')->get();//Devuelve un listado de los departamentos guardados.
      return view('solicitudes.generar_nueva_entrega',compact('sucursales','cliente','departamentos','id'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function guardar_nueva_solicitud(Request $request,$id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $validator = Validator::make($request->all(),[
        'comprobante'=>'required',
        'sucursal'=>'required',
        'fecha_entrega'=>'required|date',
        'hora'=>'required',
        'departamento'=>'required',
        'municipio'=>'required',
        'otros'=>'required',
        'direccion'=>'required',
      ]);

      if ($validator->fails()) {
        return back()
        ->withErrors($validator)
        ->withInput();
      }
      else
      {
        $s_envio                    = new SolicitudEnvio();
        $s_envio->id_cliente        = $id;
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
        $s_envio->created_at        = new Carbon();
        $s_envio->updated_at        = new Carbon();
        if($s_envio->save())
        {
          $historial              = new Historial();
          $historial->id_usuario  = Auth::id();
          $historial->actividad   = 'Creo la solicitud de entrega número '. $id;
          $historial->created_at  = Carbon::now();
          $historial->updated_at  = Carbon::now();
          $historial->save();
        }//Guarda la información para una nueva solicitud de entrega a un cliente
        return redirect()->route('vista_clientes')->with('success','Nueva Solicitud de Entrega creada.');
      }
      return back()->with('error','Parece que algo fallo, refresca la página...');
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Vista para agregar un nuevo cliente al sistema --------------------------------------------------------------
  public function nuevo_cliente()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $departamentos = DB::table('inventario_web_departamentos')->get();//Devuelve el listado de departamentos almacenados
      //Muestra el formulario para agregar un nuevo cliente
      return view('solicitudes.nuevo_cliente',compact('departamentos'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function guardar_datos_nuevo_cliente(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      if($request->nit == '')//Si el usuario no posee NIT, se almacena la información, no valida que el cliente exista.
      {
        $validator = Validator::make($request->all(),[
          'cliente'=>'required',
          'departamento'=>'required',
          'municipio'=>'required',
          'otros'=>'required',
          'direccion'=>'required',
        ]);

        if ($validator->fails()) {
          return back()
          ->withErrors($validator)
          ->withInput();
        }
        else
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
          $cliente->created_at 		= new Carbon();
          $cliente->updated_at 		= new Carbon();
          if($cliente->save())
          {
            $id_c                     = $cliente->created_at;
            $id = Cliente::where('created_at',$id_c)->first();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Agrego al sistema de entregas al cliente número '. $id;
            $historial->created_at  = Carbon::now();
            $historial->updated_at  = Carbon::now();
            $historial->save();
          }
          return redirect()->route('nueva_solicitud',['id'=>$id]);
        }
      }
      elseif($request->nit != '')//Si el cliente cuenta con número de NIT, se valida si el número ya existe
      {
        if($no_repetir = Cliente::where('nit',$request->nit)->first())//Sí existe el número de NIT ingresado, no se permite almacenar la información
        {
          return redirect()->route('vista_clientes')->with('error','Ya existe el número de Nit');
        }
        else//Sino existe dicho NIT, se permite almacenar la información del cliente
        {
          $validator = Validator::make($request->all(),[
            'cliente'=>'required',
            'departamento'=>'required',
            'municipio'=>'required',
            'otros'=>'required',
            'direccion'=>'required',
          ]);

          if ($validator->fails()) {
            return back()
            ->withErrors($validator)
            ->withInput();
          }
          else
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
            $cliente->created_at 		= new Carbon();
            $cliente->updated_at 		= new Carbon();
            if($cliente->save())
            {
              $id_c                     = $cliente->created_at;
              $id = Cliente::where('created_at',$id_c)->first();
              $historial              = new Historial();
              $historial->id_usuario  = Auth::id();
              $historial->actividad   = 'Agrego al sistema de entregas al cliente número '. $id;
              $historial->created_at  = Carbon::now();
              $historial->updated_at  = Carbon::now();
              $historial->save();
            }
            return redirect()->route('nueva_solicitud',['id'=>$id]);}
        }
      }
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver las entregas por cliente -----------------------------------------------------------------
  public function entregas_por_cliente($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      return view('solicitudes.entregas_clientes',compact('id'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function total_entregas($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      /*Carga la información de las entregas realizadas a un cliente en especifico*/
      $entregas = DB::select("select iwec.nombre, iwe.comprobante, iwe.created_at, iwe.fecha_entregado, iwee.nombre as estado, iwe.id,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as horas, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_ruta),60) as minutos,
      convert(varchar(5),horas)+' horas '+convert(varchar(5),minutos)+' minutos' as tiempo, iwe.fecha_carga
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_estados as iwee
      where iwe.id_cliente = iwec.id
      and iwe.id_estado = iwee.id
      and iwe.id_cliente = :id",['id'=>$id]);
      return DataTables::of($entregas)->make(true);
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Permite modificar la fecha de la ruta -----------------------------------------------------------------------
  public function agregar_fecha($id,Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $ruta = Ruta::find($id);//Devuelve los datos de la ruta
      $ruta->fecha_entrega = $request->fecha_entrega;//Se agrega la fecha en la que se debe realizar la ruta el piloto
      $ruta->updated_at = Carbon::now();
      if($ruta->save())
      {
        $historial              = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Agrego fecha de entrega a la ruta número '. $id;
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
      }
      return back()->with('success','Fecha agregada correctamente');
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para agregar o eliminar entregas a una ruta ---------------------------------------------------------
  public function vista_editar_ruta($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      //Muestra la vista para editar las rutas que no han sido finalizadas
      $rut = Ruta::find($id);//Carga la información de la ruta a modificar
      $soli_agregadas = SolicitudEnvio::where('id_ruta',$id)->get();//Carga las entregas y envíos que están agregados a la ruta
      $solicitudes    = SolicitudEnvio::where('id_sucursal',Auth::id())->where('id_estado',1)->get();//Muestra las solicitudes y entregas sin agregar
      return view('solicitudes.vista_editar_ruta',compact('solicitudes','soli_agregadas','id','rut'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Permite generar una nueva ruta al camion---------------------------------------------------------------------
  public function crear_nueva_ruta($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      //Permite almacenar los datos de una nueva ruta
      if($camion = Camion::where('id',$id)->where('id_estado',21)->where('id_sucursal',Auth::id())->first())//Si el camión está disponible, se permite utilizarlo para la ruta
      {
        $nueva_ruta             = new Ruta();
        $nueva_ruta->id_camion  = $id;
        $nueva_ruta->id_usuario = Auth::id();//Se guarda el ID de quien crear la ruta
        $nueva_ruta->id_estado  = 1;
        $nueva_ruta->created_at = Carbon::now();
        $nueva_ruta->updated_at = Carbon::now();
        $nueva_ruta->fecha_entrega   = Carbon::now();
        if($nueva_ruta->save())
        {
          $id_ruta = Ruta::where('created_at',$nueva_ruta->created_at)->first();
          $historial              = new Historial();
          $historial->id_usuario  = Auth::id();
          $historial->actividad   = 'Creo la ruta número '. $id_ruta->id;
          $historial->created_at  = Carbon::now();
          $historial->updated_at  = Carbon::now();
          $historial->save();
        }
        return redirect()->route('v_e_ruta',['id'=>$id_ruta->id])->with('success','No olvide agregar fecha a la ruta');
      }
      return redirect()->route('entregas_en_espera')->with('error','Camion en taller o no pertenece a su sucursal');//si el camión no está disponible se rechaza la ruta
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//------------------------------------------------------ Muestra las entregas de cada camion ----------------------------------------------------------------
  public function entregas_por_camion($id)
  {
    /*Muestra la vista con las entregas realizadas por el camión seleccionado*/
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      return view('solicitudes.entregas_por_camion',compact('id'));
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }

  public function datos_por_camion($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      /*Carga la información de las entregas realizadas por el camión seleccionado*/
      $entregas = DB::select("select iwe.id, iwe.comprobante, iwe.fecha_ruta, iwe.fecha_entregado,
      iwc.placa, iwcl.nombre, datediff(hh,iwe.fecha_carga,iwe.fecha_entregado) as hora,
      mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+ convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_camiones as iwc,
      inventario_web_clientes as iwcl
      where iwe.id_camion = iwc.id
      and iwe.id_cliente = iwcl.id
      and iwe.id_camion = :id
      and iwe.id_estado = 8",['id'=>$id]);//Muestra las entregas realizadas por cada camion que pertenezca a la sucursal*/
      return DataTables::of($entregas)->make(true);//Envía los datos a la vista_clientes
    }
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para agregar un nuevo camion ----------------------------------------------------------------------
//   public function vista_agregar_camion()
//   {
//     if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
//     {
//       //Muestra el formulario para agregar un nuevo camión dentro de la sucursal
//       $pilotos = User::where('roles',5)->where('sucursal',Auth::user()->sucursal)->get();//Muestra los pilotos que están asinados a la misma sucursal
//       return view('solicitudes.agregar_camion',compact('pilotos'));
//     }
//     else
//     {
//       return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
//     }
//   }

//   public function guardar_camion(Request $request)
//   {
//     if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
//     {
//       //Funcion para guardar datos del camion
//       if($camiones = Camion::where('placa',$request->placa)->first())//Verifica si el número de placa ya está en uso.
//       {
//         return back()->with('error','El número de placa ya existe en el sistema');//Si existe redirigue al formulario mostrando el error.
//       }
//       elseif($fletes = Camion::where('id_piloto',$request->piloto)->first())//Verifica que un piloto solo pueda estar asignado a un camión.
//       {
//         return back()->with('error','Este usuario ya está asignado a otro camión');
//       }
//       else
//       {
//         $nuevo_camion               = new Camion();
//         $nuevo_camion->marca        = $request->marca;
//         $nuevo_camion->placa        = $request->placa;
//         $nuevo_camion->tonelaje     = $request->tonelaje;
//         $nuevo_camion->id_estado    = 21;
//         $nuevo_camion->id_sucursal  = Auth::id();
//         $nuevo_camion->tipo_camion  = $request->tipo;
//         $nuevo_camion->espacio      = $request->volumen;
//         $nuevo_camion->id_piloto    = $request->piloto;
//         $nuevo_camion->created_at   = new Carbon();
//         $nuevo_camion->updated_at   = new Carbon();
//         if($nuevo_camion->save())
//         {
//           $historial              = new Historial();
//           $historial->id_usuario  = Auth::id();
//           $historial->actividad   = 'Agrego un camión con placas número '. $request->placa;
//           $historial->created_at  = Carbon::now();
//           $historial->updated_at  = Carbon::now();
//           $historial->save();
//         }
//         return redirect()->route('entregas_en_espera')->with('success','El nuevo camión se agrego correctamente');
//       }
//     }
//     else
//     {
//       return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
//     }
//   }

//----------------------------------------------- Funciones para editar la información de los camiones ------------------------------------------------------
//   public function vista_editar_camion($id)
//   {
//     if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first())
//     {
//         //Muestra el formulario para editar la informacion de un camion
//         $camion = DB::select("select iwc.id, iwc.marca, iwc.placa, iwc.tonelaje, iwc.id_sucursal, iwc.tipo_camion,
//         iwe.nombre, iwe.id as id_estado, iwc.espacio, u.name
//         from inventario_web_camiones as iwc,
//         inventario_web_estados as iwe,
//         users as u
//         where iwc.id = :id
//         and iwc.id_estado = iwe.id
//         and iwc.id_sucursal = u.id",['id'=>$id]);//Devuelve la información del camión a editar
//         foreach($camion as $c)
//         {
//           $estado = $c->id_estado;
//           $usuario = $c->id_sucursal;
//         }
//         $sucursales = User::where('roles',3)->where('id','!=',$usuario)->get();
//         $estados = Estado::where('id','!=',$estado)->whereIn('id',[21,22])->get();//Cambia el estado de un camión
//         return view('solicitudes.editar_camion',compact('camion','estados','id','sucursales'));
//     }
//     else
//     {
//       return redirect()->route('edi_cam_adm')->with('error','No tienes permisos para accesar');
//     }
//   }

//   public function editar_camion($id,Request $request)
//   {
//     if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
//     {
//         $editar->marca        = $request->marca;
//         $editar->placa        = $request->placa;
//         $editar->tonelaje     = $request->tonelaje;
//         $editar->id_estado    = $request->estado;
//         $editar->tipo_camion  = $request->tipo;
//         $editar->espacio      = $request->volumen;
//         $editar->id_sucursal  = $request->sucursal;
//         if($editar->save())
//         {
//           $historial              = new Historial();
//           $historial->id_usuario  = Auth::id();
//           $historial->actividad   = 'Agrego un camión con placas número '. $request->placa;
//           $historial->created_at  = Carbon::now();
//           $historial->updated_at  = Carbon::now();
//           $historial->save();
//         }
//         return redirect()->route('edi_cam_adm')->with('success','Cambios guardados correctamente');
//     }
//     else
//     {
//       return redirect()->route('edi_cam_adm')->with('error','No tienes permisos para accesar');
//     }
//   }
//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Permite visualizar las entregas contenidas dentro de una ruta -----------------------------------------------
  public function ver_entregas_de_ruta($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      //Muestra las entregas o solicitudes contenidas dentro de una ruta
      $soli_agregadas = SolicitudEnvio::where('id_ruta',$id)->get();
      return view('solicitudes.entregas_de_ruta',compact('soli_agregadas'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para finalizar una ruta y las entrega dentro de la misma --------------------------------------------
  public function finalizar_ruta_y_entregas($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      if($ruta = Ruta::where('id',$id)->where('id_estado',1)->first() && $solicitud = SolicitudEnvio::where('id_ruta',$id)->where('id_estado','<',8)->count() == 0)//Se verifica que la ruta se encuentre en espera para poder finalizarla
      {
        $f_ruta             = Ruta::FindOrFail($id);
        $f_ruta->id_estado  = 8;
        $f_ruta->fecha_fin  = Carbon::now();
        $f_ruta->updated_at = Carbon::now();
        if($f_ruta->save())
        {
          $historial              = new Historial();
          $historial->id_usuario  = Auth::id();
          $historial->actividad   = 'Finalizo la ruta número '. $id;
          $historial->created_at  = Carbon::now();
          $historial->updated_at  = Carbon::now();
          $historial->save();
          $entregas = SolicitudEnvio::where('id_ruta',$id)->get();
          foreach ($entregas as $actu)
          {
            if($actu->id_estado >= 2 && $actu->id_estado < 8)/*Cuando las entregas y solicitudes se asignan a un fletero, los estados de las mismas no cambian, de ser así
            al momento de finalizar una ruta, las entregas y solicitudes pasan al estado de entregado de forma automatica*/
            {
              $fecha = new Carbon();
              $actu->id_estado        = 8;
              $actu->fecha_entregado  = $fecha;
              $actu->updated_at       = Carbon::now();
              $actu->save();
            }
          }
        }
        else
        {
          return back()->with('error','No es posible finalizar la ruta');
        }
        return redirect()->route('entregas_en_espera')->with('success','Se ha finalizado la ruta y sus entregas');
      }
      else
      {
        //Si la ruta ya se encuentra finailizada, no se permiten los cambios
        return redirect()->route('entregas_en_espera')->with('error','No se permiten cambios');
      }
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-----------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para editar las entregas en ruta ----------------------------------------------------------------
  public function cancelar_solicitud($id,Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      if($no = SolicitudEnvio::where('id',$id)->where('id_sucursal',Auth::id())->where('id_estado',8)
        ->orwhere('id',$id)->where('id_usuario',Auth::id())->where('id_estado',8)->first())//Verifica que la entrega no haya sido finalizada
      {
        //Si la entrega fue marcada como finalizada, ya no se permiten las modificaciones
        return redirect()->route('solicitudes_en_ruta')->with('error','No se permite modificar datos la entrega');
      }
      else
      {
        //Muestra el formulario para modificar una entrega
        $entrega = SolicitudEnvio::find($id);
        $estados = DB::table('inventario_web_estados')->where('id','>',$entrega->id_estado)->where('id','<',10)->get();
        return view('solicitudes.finalizar_entrega',compact('entrega','estados','id'));
      }
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function g_cancelar_solicitud($id, Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      if($no = SolicitudEnvio::where('id',$id)->where('id_sucursal',Auth::id())->where('id_estado',8)
        ->orwhere('id',$id)->where('id_usuario',Auth::id())->where('id_estado',8)->first())//Verifica que la entrega no haya sido finalizada
      {
        //Si la entrega fue marcada como finalizada, ya no se permiten las modificaciones
        return redirect()->route('solicitudes_en_ruta')->with('error','No se permite modificar datos la entrega');
      }
      else
      {
        $fecha = new Carbon();
        $editar = SolicitudEnvio::FindOrFail($id);
        switch($request->estado){
          case (2):
            $editar->id_estado    = $request->estado;
            $editar->updated_at   = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $editar->id_estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (3):
            //Si el estado enviado es 3, se actualiza la fecha de carga entro de la entrega
            $editar->fecha_carga  = Carbon::now();
            $editar->id_estado    = $request->estado;
            $editar->updated_at   = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $request->estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (4):
            //Si el estado enviado es 4, se acualiza la fecha de carga dentro de la entrega
            $editar->fecha_parqueo  = Carbon::now();
            $editar->id_estado      = $request->estado;
            $editar->updated_at     = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $request->estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (5):
            //Si el estado enviado es 5, se actualiza la fecha de ruta entro de la entrega
            $editar->fecha_ruta = Carbon::now();
            $editar->id_estado  = $request->estado;
            $editar->updated_at = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $request->estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (6):
            //Si el estado enviado es 6, se acutaliza la fecha de destino dentro de la entrega
            $editar->fecha_destino  = Carbon::now();
            $editar->id_estado      = $request->estado;
            $editar->updated_at     = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $request->estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (7):
            //Si el estado enviado es 7, se actualiza la fecha de descarga dentro de la entrega
            $editar->fecha_descarga = Carbon::now();
            $editar->id_estado      = $request->estado;
            $editar->updated_at     = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $request->estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (8):
            //Si el estado enviado es 8, se actualiza la fecha de entrega dentro de la entrega :v
            $editar->fecha_entregado  = Carbon::now();
            $editar->id_estado        = $request->estado;
            $editar->longitud         = '';
            $editar->latitud          = '';
            $editar->updated_at       = Carbon::now();
            $editar->save();
            $bitacora               = new Bitacora();
            $bitacora->id_entrega   = $id;
            $bitacora->id_estado    = $request->estado;
            $bitacora->comentario   = $request->comentario;
            $bitacora->longitud     = '';
            $bitacora->latitud      = '';
            $bitacora->id_sucursal  = Auth::user()->sucursal;
            $bitacora->created_at   = Carbon::now();
            $bitacora->updated_at   = Carbon::now();
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          case (9):
            //El estado nueve es para cancelar un envío
            $editar->id_estado      = $request->estado;
            $editar->save();
            $bitacora             = new Bitacora();
            $bitacora->id_entrega = $id;
            $bitacora->id_estado  = $request->estado;
            $bitacora->comentario = $request->comentario;
            $bitacora->longitud   = '';
            $bitacora->latitud    = '';
            $bitacora->id_sucursal     = Auth::user()->sucursal;
            $bitacora->save();
            return back()->with('success','Los datos se modificaron correctamente');
            break;
          default;
            return back()->with('error','No es posible realizar ninguna acción');
        }
        if($request->estado == 2)//Si el estado enviado es 2, la entrega no sufre mayor cambio
        {
          $editar->id_estado    = $request->estado;
          $editar->updated_at   = Carbon::now();
          $editar->save();
          $bitacora               = new Bitacora();
          $bitacora->id_entrega   = $id;
          $bitacora->id_estado    = $editar->id_estado;
          $bitacora->comentario   = $request->comentario;
          $bitacora->longitud     = '';
          $bitacora->latitud      = '';
          $bitacora->id_sucursal  = Auth::user()->sucursal;
          $bitacora->created_at   = Carbon::now();
          $bitacora->updated_at   = Carbon::now();
          $bitacora->save();
          return back()->with('success','Los datos se modificaron correctamente');
        }
      }
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el listado de sucursales disponibles -----------------------------------------------------
  public function listado_de_sucursales_entregas()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      return view('solicitudes.listado_sucursales');
    }
    else
    {
      return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_listado_de_sucursales()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $sucursales = User::where('roles',3)->where('empresa',Auth::user()->empresa)->get();
      return DataTables::of($sucursales)->make(true);
    }
    else
    {
      return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver las entregas actuales dentro de la sucursales --------------------------------------------
  public function entregas_dentro_de_la_sucursal($sucursal)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwc.nombre, u.name, iwe.fecha_entrega, iwe.hora, iwe.comprobante, iwe.id_departamento, iwe.id_usuario,
      iwe.direccion
      from inventario_web_entregas as iwe,
      users as u,
      inventario_web_clientes as iwc
      where id_estado in (1,9)
      and id_usuario = :us
      and u.id = iwe.id_sucursal
      and iwc.id = iwe.id_cliente
      or iwe.id_sucursal = :su
      and u.id = iwe.id_sucursal
      and iwc.id = iwe.id_cliente
      and id_estado in (1,9)
      order by iwe.id desc",['us'=>$sucursal,'su'=>$sucursal]);//Devuleve las solicitudes que se encuentran en espera o las que fueron canceladas
      //Muestra el listado de camiones disponibles en la sucursal para asignar una ruta
      $camiones = DB::select('select c.marca, c.placa, c.tonelaje, c.tipo_camion, e.nombre, c.id
      from inventario_web_camiones as c,
      inventario_web_estados as e
      where c.id_estado = e.id
      and c.id_sucursal = :sucursal
      and c.id_estado in (21,22)',['sucursal'=>$sucursal]);
      //Las rutas contienen las solicitudes y entrgas que debe realizar un piloto, estan marcadas con 1 cuando no han sido finalizadas
      $rutas = DB::select('select iwr.id, iwr.fecha_entrega, iwc.placa, count(iwe.id) as pendientes
      from inventario_web_rutas as iwr
      join inventario_web_camiones as iwc on iwr.id_camion = iwc.id
      left join inventario_web_entregas as iwe on iwr.id = iwe.id_ruta
      where iwr.id_usuario = :usuario
      and iwr.id_estado = 1
      group by iwr.id, iwr.fecha_entrega, iwc.placa
      order by iwr.id',['usuario'=>$sucursal]);
      $sucursal = User::find($sucursal);
      return view('solicitudes.entregas_en_otra_sucursal',compact('solicitudes','camiones','rutas','sucursal'));
      //Estados; 1:En Espera, 9:Cancelado
    }
    else
    {
      return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//listado_de_sucursales_entregas
//----------------------------------------------- Funciones para ver todas las entregas realizadas por una sucursal -------------------------------------------
  public function todas_las_sucursales_reporte()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      return view('solicitudes.listado_sucursales_reporte');
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_listado_de_sucursales_reporete()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $sucursales = User::where('roles',3)->where('empresa',Auth::user()->empresa)->get();
      return DataTables::of($sucursales)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el listado de entregas realizadas o solicitadas por sucursal -----------------------------
  public function entregas_realizadas_o_solicitadas($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $sucursal = User::find($id);
      return view('solicitudes.todas_las_entregas_sucursal',compact('id','sucursal'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_entregas_realizadas_o_solicitadas($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_municipios as iwm,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca,
      users as u,
      users as us
      where iwe.id_usuario = :usuario
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id
      or iwe.id_sucursal = :usuario2
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id",['usuario'=>$id,'usuario2'=>$id]);
      return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ver las entregas realizadas por sucursal -------------------------------------------------------
  function mapa_entregas_por_sucursal($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes entregas';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      $co = DB::table('inventario_web_entregas')->where('id_estado',8)
      ->where('id_usuario',$id)->get();
      foreach ($co as $c) {
        $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
      }
      return view('solicitudes.marcador_entregas',compact('lat'));
      //return response()->json($lat);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ver las entregas realizadas por sucursal -------------------------------------------------------
function mapa_entregas_por_camion($id)
{
  if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
  {
    $historial              = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes entregas';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    $co = DB::table('inventario_web_entregas')->where('id_estado',8)
    ->where('id_camion',$id)->get();
    foreach ($co as $c) {
      $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
    }
    return view('solicitudes.marcador_entregas',compact('lat'));
    //return response()->json($lat);
  }
  else
  {
    return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
  }
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el listado de entregas realizadas o solicitadas por sucursal -----------------------------
  public function reporte_entregas_sucursal_fecha(Request $request,$id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $sucursal = User::find($id);
      $inicio = $request->inicio;
      $fin = $request->fin;
      return view('solicitudes.todas_las_entregas_sucursal_fecha',compact('id','sucursal','inicio','fin'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_reporte_entregas_sucursal_fecha($id,$inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_municipios as iwm,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca,
      users as u,
      users as us
      where iwe.id_usuario = :usuario
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.created_at between :inicio and :fin
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id
      or iwe.id_sucursal = :usuario2
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.created_at between :inicio2 and :fin2
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id",['usuario'=>$id,'usuario2'=>$id,'inicio'=>$inicio,'inicio2'=>$inicio,'fin'=>$fin,'fin2'=>$fin]);
      return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para el mapa por sucursal por fecha -----------------------------------------------------------------
  function mapa_entregas_por_sucursal_fecha($id,$inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes entregas';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      $co = DB::table('inventario_web_entregas')->where('id_estado',8)
      ->where('id_usuario',$id)->wherebetween('created_at',[$inicio,$fin])->get();
      foreach ($co as $c) {
        $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
      }
      return view('solicitudes.marcador_entregas',compact('lat'));
      //return response()->json($lat);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para el mapa por sucursal por fecha -----------------------------------------------------------------
  function mapa_entregas_por_camion_fecha($id,$inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes entregas';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      $co = DB::table('inventario_web_entregas')->where('id_estado',8)
      ->where('id_camion',$id)->wherebetween('created_at',[$inicio,$fin])->get();
      foreach ($co as $c) {
        $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
      }
      return view('solicitudes.marcador_entregas',compact('lat'));
      //return response()->json($lat);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el total de entregas realizadas a los diferences municipios ------------------------------
  public function entrega_por_municipios()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Muestra la vista con los datos de las entregas realizadas en cada uno de los municipios*/
      return view('solicitudes.entregas_por_municipio');
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_entrega_por_municipios()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Carga los datos de las entregas realizadas por cada departamento*/
      $entregas = DB::select('select d.nombre, m.nombre as municipio, count(i.id) as entregas, m.id
      from  inventario_web_entregas as i,
      inventario_web_municipios as m,
      inventario_web_departamentos as d,
      where m.id = i.id_municipio
      and d.id = i.id_departamento
      and i.id_estado = 8
      and i.created_at > :fecha
      group by d.nombre, m.nombre, m.id
      order by d.nombre',['fecha'=>Carbon::now()->subMonths(6)]);
      return DataTables::of($entregas)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el total de entregas realizadas a los diferences municipios ------------------------------
  public function entregas_por_municipios_fecha(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Muestra la vista con los datos de las entregas realizadas en cada uno de los municipios*/
      $inicio = $request->inicio;
      $fin = $request->fin;
      return view('solicitudes.entregas_por_municipio_fecha',compact('inicio','fin'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_entregas_municipios_fecha($inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Carga los datos de las entregas realizadas por cada departamento*/
      $entregas = DB::select('select d.nombre, m.nombre as municipio, count(i.id) as entregas, m.id
      from  inventario_web_entregas as i,
      inventario_web_municipios as m,
      inventario_web_departamentos as d,
      where m.id = i.id_municipio
      and d.id = i.id_departamento
      and i.id_estado = 8
      and i.created_at between :inicio and :fin
      group by d.nombre, m.nombre, m.id
      order by d.nombre',['inicio'=>$inicio,'fin'=>$fin]);
      return DataTables::of($entregas)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el reporte de entregas por municipio y aldea ---------------------------------------------
  public function entregas_por_municipio_aldea($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $municipio = Municipio::find($id);
      return view('solicitudes.reporte_entregas_muni_aldea',compact('id','municipio'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_entrega_aldeas($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Carga los datos de las entregas realizadas a las aldeas dentro de un deparamento*/
      $aldeasEntregas = DB::select("select iwa.nombre as aldea, iwe.id, u.name, iwe.fecha_asignacion, iwc.nombre,
      iwe.fecha_carga, iwe.fecha_ruta, iwe.fecha_destino, iwe.fecha_entregado,
      datediff(hh,iwe.fecha_carga,iwe.fecha_entregado) as hora,
      mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+ convert(varchar(10),minutos)+' minutos' as tiempo
      from  inventario_web_entregas as iwe,
      inventario_web_aldeas_otros as iwa,
      users as u,
      inventario_web_clientes as iwc
      where iwa.id = iwe.id_otros
      and iwe.id_municipio = :id
      and iwe.id_estado = 8
      and iwe.id_sucursal = u.id
      and iwe.id_cliente = iwc.id",['id'=>$id]);
      return DataTables::of($aldeasEntregas)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el reporte de entregas por municipio y aldea por fecha -----------------------------------
  public function entregas_por_municipio_aldea_fecha($id,Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $inicio = $request->inicio;
      $fin = $request->fin;
      $municipio = Municipio::find($id);
      return view('solicitudes.reporte_entregas_muni_aldea_fecha',compact('id','municipio','inicio','fin'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_entrega_aldeas_fecha($id,$inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Carga los datos de las entregas realizadas a las aldeas dentro de un deparamento*/
      $aldeasEntregas = DB::select("select iwa.nombre as aldea, iwe.id, u.name, iwe.fecha_asignacion, iwc.nombre,
      iwe.fecha_carga, iwe.fecha_ruta, iwe.fecha_destino, iwe.fecha_entregado,
      datediff(hh,iwe.fecha_carga,iwe.fecha_entregado) as hora,
      mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+ convert(varchar(10),minutos)+' minutos' as tiempo
      from  inventario_web_entregas as iwe,
      inventario_web_aldeas_otros as iwa,
      users as u,
      inventario_web_clientes as iwc
      where iwa.id = iwe.id_otros
      and iwe.id_municipio = :id
      and iwe.id_estado = 8
      and iwe.id_sucursal = u.id
      and iwe.id_cliente = iwc.id
      and iwe.fecha_asignacion between :inicio and :fin",['id'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
      return DataTables::of($aldeasEntregas)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Función para visualizar el mapa con los marcadores de entregas ----------------------------------------------
  public function mapa_marcador_entregas()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Muestra un mapa de calor con los puntos de mayor concentración de las entregas realizadas*/
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes entregas';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      $co = DB::table('inventario_web_entregas')->where('id_estado',8)->get();
      foreach ($co as $c) {
        $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
      }
      return view('solicitudes.marcador_entregas',compact('lat'));
      //return response()->json($lat);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Función para visualizar el mapa con los marcadores de entregas por rango de fecha ---------------------------
  public function mapa_marcador_entregas_por_fecha($inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      /*Muestra un mapa de calor con los puntos de mayor concentración de las entregas realizadas*/
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes entregas';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      $co = DB::table('inventario_web_entregas')->where('id_estado',8)->whereBetween('created_at',[$inicio,$fin])->get();
      foreach ($co as $c) {
        $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
      }
      return view('solicitudes.marcador_entregas',compact('lat'));
      //return response()->json($lat);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el listado de entregas realizadas o solicitadas por sucursal -----------------------------
  public function reporte_entregas_por_fecha_general(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $inicio = $request->inicio;
      $fin = $request->fin;
      return view('solicitudes.todas_las_entregas_fecha',compact('inicio','fin'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  public function datos_reporte_entregas_por_fecha($inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_municipios as iwm,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca,
      users as u,
      users as us
      where iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.created_at between :inicio and :fin
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id",['inicio'=>$inicio,'fin'=>$fin]);
      return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function ver_mapa($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
    {
      /*Muestra un mapa con la ubicación de una entrega en especifico*/
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Visualizo el panel que muestra el mapa con las ubicaciones marcadas durante la entrega';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      $co = DB::table('inventario_web_entregas')
      ->join('inventario_web_bitacora_entrega','inventario_web_entregas.id','=','inventario_web_bitacora_entrega.id_entrega')
      ->select(['inventario_web_bitacora_entrega.latitud','inventario_web_bitacora_entrega.longitud','inventario_web_entregas.direccion'])
      ->where('inventario_web_bitacora_entrega.id_entrega','=',$id)->get();
      foreach ($co as $c) {
        $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=> $c->direccion );
      }
      /* /
      /* $co = SolicitudEnvio::get();
      foreach ($co as $c) {
      $lat[]  = array("lat"=>$c->latitud,"lng"=>$c->longitud,"dire"=>$c->direccion);
      }*/
      /*return response()->json($lat);*/
      return view('solicitudes.ver_mapa',compact('lat'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de camiones para el historial de entregas por camion ----------------------------------------------
  function listado_de_camiones()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      return view('solicitudes.listado_camiones');
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  function datos_listado_camiones()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $camiones = DB::table('inventario_web_camiones');
      return DataTables::of($camiones)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  //------------------------- Funciones para ver el listado de entregar realizadas por camion -----------------------------------------------------------------
  function entregas_x_camion($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      return view('solicitudes.entregas_x_camion',compact('id'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  function datos_entregas_x_camion($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $camiones = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_municipios as iwm,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca,
      users as u,
      users as us
      where iwe.id_camion = :camion
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id
      /*or iwe.id_sucursal = :usuario2
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id*/",['camion'=>$id]);
      return DataTables::of($camiones)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//------------------------- Funciones para ver el listado de entregar realizadas por camion filtrado por fecha ------------------------------------------------
  function entregas_x_camion_fecha(Request $request,$id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $inicio = $request->inicio;
      $fin = $request->fin;
      return view('solicitudes.entregas_x_camion_fecha',compact('id','inicio','fin'));
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }

  function datos_entregas_x_camion_fecha($id,$inicio,$fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
    {
      $camiones = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
      iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
      mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
      convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
      from inventario_web_entregas as iwe,
      inventario_web_clientes as iwec,
      inventario_web_municipios as iwm,
      inventario_web_aldeas_otros as iwea,
      inventario_web_estados as iwes,
      inventario_web_camiones as iweca,
      users as u,
      users as us
      where iwe.id_camion = :camion
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id
      and iwe.created_at between :inicio and :fin
      /*or iwe.id_sucursal = :usuario2
      and iwe.id_cliente = iwec.id
      and iwe.id_municipio = iwm.id
      and iwe.id_otros = iwea.id
      and iwe.id_estado = iwes.id
      and iwe.id_camion = iweca.id
      and iwe.id_estado between 1 and 8
      and iwe.id_usuario = u.id
      and iwe.id_sucursal = us.id*/",['camion'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
      return DataTables::of($camiones)->make(true);
    }
    else
    {
      return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//buscar_fecha_solicitud
  /////////////////////////////////////////////////Vista solicitudes para Clientes en espera///////////////////////////////////////////////////////////
  public function por_recibir()
  {
    $solicitudes = SolicitudEnvio::where('id_entregar',Auth::id())->orderBy('id','desc')->paginate(15);//Devuleve las solicitudes que se encuentran en espera o las que fueron canceladas
    return view('solicitudes.por_recibir',compact('solicitudes'));
    //Estados; 1:En Espera, 9:Cancelado
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------


  ////////////////  OPCIONES DE BUSQUEDA EN SOLICITUDES ///////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////Vista de Entregas por comprobante////////////////////////////////////
  public function buscar_comprobante_solicitud($id, Request $request)
  {
    $id       = $id;
    $estados  = Estado::get();//Devuelve el listado de estados
    $entregas = SolicitudEnvio::where('id_cliente',$id)->where('comprobante', 'like','%' .$request->comprobante.'%')
    ->where('id_usuario',Auth::id())->orderBy('id','desc')
    ->get();//Devuelve todas las solicitudes segú el comprobante ingresado
    return view('solicitudes.entregas_por_cliente_busquedas',compact('entregas','id','estados'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Vista de entregas por estado/////////////////////////////////////////
  public function buscar_estados_solicitud($id, Request $request)
  {
    $id       = $id;
    $estados  = Estado::get();//Devuelve el listado de estados
    $entregas = SolicitudEnvio::where('id_cliente',$id)->where('id_estado',$request->estado)->where('id_usuario',Auth::id())
    ->orderBy('id','desc')
    ->get();//Devuelve todas las solicitudes con el estado ingresado
    return view('solicitudes.entregas_por_cliente_busquedas',compact('entregas','id','estados'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------



  ////////////////////////////////////////////////////////////////////////////////Vista de entregas por fecha//////////////////////////////////////////
  public function buscar_fecha_entregas(Request $request)
  {
    $solicitudes = SolicitudEnvio::where('id_estado',8)->whereBetween('created_at',array($request->fecha_inicial,$request->fecha_final))->
    where('id_usuario',Auth::id())->orderBy('id','desc')->get();//Devuelve todas las solicitudes en el rango de fecha seleccionado
    return view('solicitudes.entregas_busquedas',compact('solicitudes'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  /////////////////////////////////////////////////////////Funciones para agrear / editar datos de las Sucursales /////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////Vista Listado de Sucursales//////////////////////////////////////////
    public function vista_sucursales()
    {
      return view('solicitudes.sucursales');//
    }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Funcion que devuelve el listado de sucursales////////////////////////
    public function datos_sucursales()
    {
      $sucursales = DB::table('users')->where('roles',3);//Devuelve el listado de sucursales almacenadas
      return DataTables::of($sucursales)->make(true);
    }
  //---------------------------------------------------------------------------------------------------------------------------------------------------
  /*//////////////////////////////////////////////////////////////////////////////Agregar nueva Sucursal///////////////////////////////////////////////
    public function vista_agregar_sucursal()
    {
      $sucursales     = DB::table('unidades')->where('empresa',1)->get();//Devuelve el listado de sucursales almacenadas
      $departamentos  = DB::table('inventario_web_departamentos')->get();//Devuelve el listado de departamentos almacenados
      return view('solicitudes.nueva_sucursal',compact('sucursales','departamentos'));//Muestra el formulario para agregar una nueva sucursal
    }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  /*//////////////////////////////////////////////////////////////////////////////Funcion para guardar una nueva sucursal//////////////////////////////
    public function agregar_sucursal(Request $request)
    {
      if($u = DB::table('inventario_web_clientes')->where('cod_unidad',$request->cod_unidad)->first())//Verifica que la sucursal no esté agregada
      {
        return back()->with('error','Ya existe esta Sucursal');
      }
      else//Si la sucursal no existe, permite guardar la información
      {
        $unidades = DB::table('unidades')->where('empresa',1)->where('cod_unidad',$request->cod_unidad)->first();
        //Devuelve la informacion de la sucursal para almacenarla como un cliente
        $n_sucursal                   = new Cliente();
        $n_sucursal->nombre           = $unidades->nombre;
        $n_sucursal->correo           = $request->correo;
        $n_sucursal->telefono         = $unidades->telefono;
        $n_sucursal->id_departamento  = $request->departamento;
        $n_sucursal->id_municipio     = $request->municipio;
        $n_sucursal->id_otros         = $request->otros;
        $n_sucursal->direccion        = $unidades->direccion;
        $n_sucursal->id_tipo          = 2;//Las sucursales se almacenan con tipo de cliente 2
        $n_sucursal->cod_unidad       = $unidades->cod_unidad;
        $n_sucursal->save();
        return redirect()->route('v_sucursales');
      }
    }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  /*//////////////////////////////////////////////////////////////////////////////Vista editar datos sucursal//////////////////////////////////////////
    public function vista_editar_sucursal($id)
    {
      $sucursal       = Cliente::find($id);//Devuelve la información de la sucursal
      $departamentos  = DB::table('inventario_web_departamentos')->get();//Devuelve el listado de departamentos almacenados
      if(Auth::user()->roles == 5)
      {
        return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para realizar esta acción');
      }
      else
      {
        return view('solicitudes.editar_sucursal',compact('sucursal','departamentos'));
      }
    }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  /*//////////////////////////////////////////////////////////////////////////////Guardar datos de edicion de sucursal ////////////////////////////////
    public function editar_sucursal($id,Request $request)
    {
      $editar = Cliente::FindOrFail($id);//Devuelve la informacion de la sucursal a editar
      $editar->correo           = $request->correo;
      $editar->id_departamento  = $request->departamento;
      $editar->id_municipio     = $request->municipio;
      $editar->id_otros         = $request->otros;
      if(Auth::user()->roles == 5)
      {
        return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para realizar esta acción');
      }
      else
      {
        $editar->save();//Almacena los cambios realizados
        return redirect()->route('v_sucursales');
      }
    }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////Formulario para solicitd de envios entre sucursales /////////////////
  public function vista_solicitud_sucursal($id)
  {
    //Cuando una sucursal enviara producto a otra sucursal, se hace por medio de este formulario, ejemplo CD a Zona 11
    //Este formulario no es para solicitar o hacer entregas a clientes, solo entre sucursales
    $sucursales     = User::where('roles',3)->get();//Devuelve el listado de sucursales a las que se puede hacer un envío
    $sucursal       = User::find($id);//Devuelve la información de la sucursal seleccionada
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para realizar esta acción');
    }
    else
    {
      return view('solicitudes.nueva_solicitud_sucursal',compact('sucursal','sucursales'));
      //Formulario para crear un envío a una sucursal
    }
  }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////Guardar información de un nuevo envío entre sucursales //////////////
  public function guardar_nueva_entrega(Request $request)
  {
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para realizar esta acción');
      //No permite a los pilotos ingresar a esta opcion
    }
    else
    {
      //Permite crear una nueva entrega entre sucursales, es decir cuando una sucursal le envío producto a otra sucursal
      $sucursal = User::find($request->cliente);
      $entrega                  = new SolicitudEnvio();
      $entrega->id_entregar     = $sucursal->id;
      $entrega->comprobante     = $request->comprobante;
      $entrega->id_usuario      = Auth::id();
      $entrega->fecha_entrega   = $request->fecha_entrega;
      $entrega->hora            = $request->hora;
      $entrega->id_estado       = 1;
      $entrega->detalle_entrega = $request->detalle_entrega;
      $entrega->id_sucursal     = $request->id_sucursal;
      $entrega->save();
      return redirect()->route('v_sucursales')->with('success','Entrega creada con Exito');
    }
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Vista para editar una entrega////////////////////////////////////////
  public function vista_editar_entrega($id)
  {
    $sucursales     = User::where('roles',3)->get();//Devuelve listado de sucursales
    $solicitudes    = SolicitudEnvio::find($id);//Devuelve la información de la entrega a editar
    $departamentos  = DB::table('inventario_web_departamentos')->where('id','!=',$solicitudes->id_departamento)->get();/*Devuelve el listado de
    departamentos almacenados */
    if(Auth::user()->roles == 5)
    {
      return redirect()->route('p_inicio')->with('error','Usted no tiene permisos para realizar esta acción');
    }
    else
    {
      return view('solicitudes.editar_entrega_sucursal',compact('sucursales','solicitudes','departamentos'));
    }
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Funcion para editar una entrega//////////////////////////////////////
  public function editar_entrega_sucursal($id,Request $request)
  {
    $s_envio = SolicitudEnvio::FindOrFail($id);
    if($s_envio->id_usuario != Auth::id())
    {
      return redirect()->route('v_sucursales')->with('error','Usted no puede realizar esta acción');/*Se verifica que solo quien creo el envío
      pueda realizar cambios al mismo*/
    }
    else
    {
      if($envio = SolicitudEnvio::where('id',$id)->where('id_estado',1)->orwhere('id',$id)->where('id_estado',9)->first())/*Se verifica que
      la entrega se encuentre en espera o cancelado para que puede ser modificado*/
      {
        $s_envio->comprobante       = $request->comprobante;
        $s_envio->id_sucursal       = $request->id_sucursal;
        $s_envio->fecha_entrega     = $request->fecha_entrega;
        $s_envio->hora              = $request->hora;
        $s_envio->id_estado         = $s_envio->id_estado;
        $s_envio->detalle_entrega   = $request->detalle_entrega;
        $s_envio->save();
        return redirect()->route('v_sucursales')->with('success','Editado con Exito');
      }
      else
      {
        return redirect()->route('s_e_ruta')->with('error','Envió en camino, no se permiten cambios');
        //Si la entrega se encuentra finalizada no podra ser editada
      }
    }
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Vista de Entregas por Sucursal///////////////////////////////////////
  public function vista_entregas_por_sucursal($id)
  {
    $id       = $id;
    $estados  = Estado::get();//Devuelve el lsitado de estados
    $entregas = SolicitudEnvio::where('id_cliente',$id)->where('id_usuario',Auth::id())->orderBy('id','desc')
    ->paginate(50);//Devuelve el listado de entregas a una sucursal
    return view('solicitudes.entregas_por_sucursal',compact('entregas','id','estados'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------



  ///////////////////////////////////////////////////Busquedas dentro de los envíos a sucursales //////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////Vista de Entregas por comprobante////////////////////////////////////
  public function buscar_comprobante_entrega($id, Request $request)
  {
    $id       = $id;
    $estados  = Estado::get();//Devuelve el listado de los estados
    $entregas = SolicitudEnvio::where('id_cliente',$id)->where('comprobante', 'like','%' .$request->comprobante.'%')->orderBy('id','desc')
    ->get();//devuelve el resultado por el comprobante ingresado
    return view('solicitudes.entregas_por_sucursal_busquedas',compact('entregas','id','estados'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Vista de entregas por estado/////////////////////////////////////////
  public function buscar_estados_entrega($id, Request $request)
  {
    $id       = $id;
    $estados  = Estado::get();//Devuelve el listado de los estados
    $entregas = SolicitudEnvio::where('id_cliente',$id)->where('id_estado',$request->estado)->orderBy('id','desc')
    ->get();//Devuelve las entregas con el estado seleccionado
    return view('solicitudes.entregas_por_sucursal_busquedas',compact('entregas','id','estados'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Vista de entregas por fecha//////////////////////////////////////////
  public function buscar_fecha_entrega($id, Request $request)
  {
    $id       = $id;
    $estados  = Estado::get();//DDevuelve el listado de los estados
    $entregas = SolicitudEnvio::where('id_cliente',$id)->whereBetween('created_at',array($request->fecha_inicial,$request->fecha_final))
    ->orderBy('id','desc')->get();//Devuelve las entregas contenidas en el reango de fechas seleccionadas
    return view('solicitudes.entregas_por_sucursal_busquedas',compact('entregas','id','estados'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ///////////////////// Funciones para uso de Select Dinamicos ////////////////////////////////////////////////////////////////////////////////////////
  public function municipios($id)
  {
    //Devuelve los municipios contenidos en un departamento
    return DB::table('inventario_web_municipios')->where('id_departamento',$id)->get();
  }

  public function otros($id)
  {
    //Devuelve las aldeas y otros contenidos en los municipios
    return DB::table('inventario_web_aldeas_otros')->where('id_municipio',$id)->get();
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------



  //-----------------------------------------------Llistado de sucursales para las entregas ---------------------------------------------------------
  public function sucursales_listado()
  {
    return view('solicitudes.sel_sucursal');
  }

  ////////////////////////////////////////////////////////////////////////////////Vista Solicitudes Entregadas/////////////////////////////////////////
  public function entregas_por_sucursal($suc,$bod)
  {
    return view('solicitudes.entregadasrl',compact('suc','bod'));
  }
  //---------------------------------------------------------------------------------------------------------------------------------------------------

  ////////////////////////////////////////////////////////////////////////////////Funcion que devuelve el listado de los clientes//////////////////////
  public function datos_entregas_por_sucursal($suc,$bod)
  {
    $user = User::where('sucursal',$suc)->where('bodega',$bod)->where('roles',3)->first();
    $solicitudes = DB::table('inventario_web_entregas')//Devuelve el listado de las solicitudes entregadas
    ->join('inventario_web_clientes','inventario_web_entregas.id_cliente','=','inventario_web_clientes.id')
    ->join('inventario_web_camiones','inventario_web_entregas.id_camion','=','inventario_web_camiones.id')
    ->join('inventario_web_aldeas_otros','inventario_web_entregas.id_otros','=','inventario_web_aldeas_otros.id')
    ->select(['inventario_web_entregas.id','inventario_web_entregas.comprobante','inventario_web_entregas.created_at',
    'inventario_web_entregas.fecha_entregado','inventario_web_entregas.id_camion','inventario_web_clientes.nombre','inventario_web_camiones.placa',
    'inventario_web_aldeas_otros.nombre as ubicacion'])
    ->where('inventario_web_entregas.id_sucursal',$user->id)->where('inventario_web_entregas.id_estado',8);
    return DataTables::of($solicitudes)->make(true);//Envía los datos a la vista_clientes
    //$data = Cliente::all();
    //return response()->json(['data'=>$data]);
  }
  //RG---------------------------------------------------------------------------------------------------------------------------------------------------
    public function reporte_entregas_g()
    {
      if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
      {
        return view('solicitudes.reporte');
      }
      else
      {
        return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
      }
    }

    public function datos_reporte_entregas_g()
    {
      if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
      {
        $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
        iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
        mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
        convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
        from inventario_web_entregas as iwe,
        inventario_web_clientes as iwec,
        inventario_web_municipios as iwm,
        inventario_web_aldeas_otros as iwea,
        inventario_web_estados as iwes,
        inventario_web_camiones as iweca,
        users as u,
        users as us
        where iwe.id_cliente = iwec.id
        and iwe.id_municipio = iwm.id
        and iwe.id_otros = iwea.id
        and iwe.id_estado = iwes.id
        and iwe.id_camion = iweca.id
        and iwe.id_estado between 1 and 8
        and iwe.id_usuario = u.id
        and iwe.id_sucursal = us.id
        and iwe.id_cliente = iwec.id
        and iwe.id_municipio = iwm.id
        and iwe.id_otros = iwea.id
        and iwe.id_estado = iwes.id
        and iwe.id_camion = iweca.id
        and iwe.id_estado between 1 and 8
        and iwe.id_usuario = u.id
        and iwe.id_sucursal = us.id
        and iwe.created_at > :fecha",['fecha'=>Carbon::now()->subMonths(3)]);
        return DataTables::of($solicitudes)->make(true);
      }
      else
      {
        return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
      }
    }
    //---------------------------------------------------------------------------------------------------------------------------------------------------
  public function reporte_entregas_gf(Request $request)
    {
      if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
      {
        $inicio = $request -> inicio;
        $fin    = $request -> fin;
        return view('solicitudes.reporte_fecha',compact('inicio','fin'));
      }
      else
      {
        return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
      }
    }

    public function datos_reporte_entregas_gf($inicio, $fin)
    {
      if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',7)->first())
      {
        $solicitudes = DB::select("select iwe.id, iwe.comprobante, iwe.created_at,iwec.nombre as cliente, iwea.nombre as aldea, iwe.id_usuario, iwe.id_sucursal,
        iwes.nombre as estado, iweca.placa, iwe.direccion, iwe.fecha_entregado, u.name, us.name as solicito, iwm.nombre as municipio,
        mod(datediff(hh,iwe.fecha_carga,iwe.fecha_entregado),60) as hora, mod(datediff(mi,iwe.fecha_carga,iwe.fecha_entregado),60) as minutos,
        convert(varchar(10),hora)+' horas '+convert(varchar(10),minutos)+' minutos' as tiempo
        from inventario_web_entregas as iwe,
        inventario_web_clientes as iwec,
        inventario_web_municipios as iwm,
        inventario_web_aldeas_otros as iwea,
        inventario_web_estados as iwes,
        inventario_web_camiones as iweca,
        users as u,
        users as us
        where iwe.id_cliente = iwec.id
        and iwe.id_municipio = iwm.id
        and iwe.id_otros = iwea.id
        and iwe.id_estado = iwes.id
        and iwe.id_camion = iweca.id
        and iwe.id_estado between 1 and 8
        and iwe.created_at between :inicio and :fin
        and iwe.id_usuario = u.id
        and iwe.id_sucursal = us.id
        and iwe.id_cliente = iwec.id
        and iwe.id_municipio = iwm.id
        and iwe.id_otros = iwea.id
        and iwe.id_estado = iwes.id
        and iwe.id_camion = iweca.id
        and iwe.id_estado between 1 and 8
        and iwe.id_usuario = u.id
        and iwe.id_sucursal = us.id",['inicio'=>$inicio,'fin'=>$fin]);
        return DataTables::of($solicitudes)->make(true);
      }
      else
      {
        return redirect()->route('entregas_en_espera')->with('error','No tienes permisos para accesar');
      }
    }
  //---------------------------------------------------------------------------------------------------------------------------------------------------
}
