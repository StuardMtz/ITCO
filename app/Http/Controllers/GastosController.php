<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use DB;
use PDF;
use Auth;
use App\User;
use App\Gastos;
use App\Persona;
use App\Historial;
use Carbon\Carbon;
use App\liquidacion;
use App\Mail\OperarGasto;
use App\Mail\SolicitudGasto;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class GastosController extends Controller
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
//----------------------- Funciones que permiten ver el listado de gastos pendientes de aprobación --------------------------------------------------------
    function inicio()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $tipo_gastos = DB::table('inventario_web_tipo_gastos')->orderby('nombre')->get();
            $vehiculos = DB::table('inventario_web_camiones')->where('id_estado','>',20)->get();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso al modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.inicio',compact('tipo_gastos','vehiculos'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_gastos_inicio()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, p.descripcion+' '+iwpg.nombre as proveedor, u.name, iwgs.serie_documento, iwgs.monto, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_usuario
            where id_estado = 24
            and id_usuario = :user",['user'=>Auth::id()]);
            return DataTables($gastos)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------- Funciones que permiten ver el listado de gastos pendientes de aprobación por los usuarios que pueden autorizar gastos a otros usuarios
    function listado_usuarios_gastos_pendientes()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first()
            || Auth::user()->roles == 0)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la vista de liquidaciones por autorizar en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.listado_usuarios_gaspen');
        }
        return redirect()->route('inicio_gastos_espera')->with('error','¡No tienes permiso para ingresar!');
    }

    function gastos_pendientes_autorizacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first()
            || Auth::user()->roles == 0)
        {
            $tipo_gastos = DB::table('inventario_web_tipo_gastos')->get();
            $vehiculos = DB::table('inventario_web_camiones')->where('id_estado','>',20)->get();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso al modulo de gastos pendientes de autorizar';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.sin_autorizar',compact('tipo_gastos','vehiculos','id'));
        }
        /**/
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_gastos_pendientes_autorizacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, p.descripcion+' '+iwpg.nombre as proveedor, u.name, iwgs.serie_documento, iwgs.monto, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_usuario
            where id_estado = 24
            and id_usuario = :user",['user'=>$id]);
            return DataTables($gastos)->make(true);
        }
        return DataTables('error')->make(true);
       /* else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, p.descripcion+' '+iwpg.nombre as proveedor, u.name, iwgs.serie_documento, iwgs.monto, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_usuario
            where id_estado = 24
            and u.roles in (".implode(",",$roles).")");
            return DataTables($gastos)->make(true);
        }*/
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el detalle de un gasto en espera -----------------------------------------------------------------------------
    function ver_informacion_gasto($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $estado = Gastos::find($id);
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a visualizar los detalles de un gasto en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            if($estado->id_estado == 24)
            {
                $gastos = DB::select("select iwgs.id as numero, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
                p.descripcion+' '+ iwpg.nombre as proveedor, iwe.nombre, iwgs.cantidad, iwgs.fecha_documento, iwgs.iva, iwtg.nombre,
                iwc.marca, iwc.placa, p.nit, iwpg.id, p.proveedor as cod_proveedor, iwpg.cui, u.name as usuario, us.name,
                iwgs.serie_documento, iwgs.no_retencion, iwgs.fecha_autorizacion
                from inventario_web_gastos_sucursales as iwgs
                left join proveedor as p on p.proveedor = iwgs.id_proveedor
                left join inventario_web_camiones as iwc on iwgs.id_vehiculo = iwc.id
                left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
                join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
                join inventario_web_tipo_gastos as iwtg on iwgs.id_tipo_gasto = iwtg.id
                join users as u on u.id = iwgs.id_usuario
                left join users as us on us.id = iwgs.id_autoriza
                where iwgs.id_estado = 24
                and iwgs.id = :id",['id'=>$id]);
                return view('gastos.ver_gasto',compact('gastos','id'));
            }
            elseif($estado->id_estado == 25)
            {
                return redirect()->route('vegas_op',['id'=>$id]);
            }
            elseif($estado->id_estado == 29)
            {
                return redirect()->route('vegas_op',['id'=>$id]);
            }
            return redirect()->route('inicio_gastos_espera')->with('error','No tienes permisos para accesar');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite filtrar un proveedor para agregarlo dentro de un gasto ------------------------------------------------------
    function codigo_proveedores(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $tags = DB::table('proveedor')->where('proveedor','like','%'. $term .'%')
            ->orwhere('descripcion','like','%'. $term .'%')->orwhere('nit','like','%'. $term .'%')->limit(10)->get();
            $formatted_tags = [];
            foreach ($tags as $tag) {
                $formatted_tags[] = ['id' => $tag->proveedor, 'text' => $tag->proveedor.' - '.utf8_encode($tag->descripcion).'('.$tag->nit.')'];
            }
            return \Response::json($formatted_tags);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite filtrar un proveedor para agregarlo dentro de un gasto ------------------------------------------------------
    function codigo_cui_gastos(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $tags = DB::table('inventario_web_personas_gastos')->where('id','like','%'. $term .'%')
            ->orwhere('cui','like','%'. $term .'%')->orwhere('nombre','like','%'. $term .'%')->limit(10)->get();
            $formatted_tags = [];
            foreach ($tags as $tag) {
                $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->id.' - '.$tag->nombre.'('.$tag->cui.')'];
            }
            return \Response::json($formatted_tags);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite guardar una nueva solicitud de gasto para las sucursales ----------------------------------------------------
    function crear_nuevo_gasto(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            if(Gastos::where('serie_documento',$request->serie_documento)->where('no_documento',$request->no_documento)->
            where('id_proveedor',$request->cod_proveedor)->where('monto',$request->monto)->where('fecha_documento',$request->fecha_documento)
            ->where('id_estado',24)->where('id_tipo_gasto',$request->tipo_gasto)->first())
            {
                return back()->with('error','No se permiten duplicar gastos...');
            }
            $correlativo = '';
            $correlativo = Gastos::where('serie_documento',Auth::user()->name)->orderby('id','desc')->first();
            $gasto                   = new Gastos();
            $gasto->empresa          = Auth::user()->empresa;
            if($request->cod_proveedor == '')
            {
                if($correlativo != '')
                {
                    $gasto->id_persona      = $request->num_cui;
                    $gasto->no_documento    = $correlativo->no_documento+1;
                    $gasto->serie_documento = Auth::user()->name;
                }
                else
                {
                    $gasto->id_persona      = $request->num_cui;
                    $gasto->no_documento    = 1;
                    $gasto->serie_documento = Auth::user()->name;
                }
            }
            else
            {
                $gasto->id_proveedor    = $request->cod_proveedor;
                $gasto->no_documento    = $request->no_documento;
                $gasto->serie_documento = $request->serie_documento;
            }
            $gasto->descripcion      = $request->descripcion;
            $gasto->monto            = $request->monto;
            $gasto->iva              = $request->retencion;
            $gasto->fecha_documento  = $request->fecha_documento;
            $gasto->fecha_registrado = Carbon::now();
            $gasto->id_usuario       = Auth::id();
            $gasto->id_tipo_gasto    = $request->tipo_gasto;
            $gasto->id_vehiculo      = $request->vehiculo;
            $gasto->contra_de_pago   = $request->contra_pago;
            $gasto->id_estado        = 24;
            $gasto->no_retencion     = $request->no_retencion;
            //$gasto->monto_factura    = $request->monto_factura;
            $gasto->save();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Registro un nuevo gasto en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
            if($permiso == true)
            {
                $user           = User::where('tipo_correo','=',1)->pluck('email');
                $usuario        = Auth::user()->name;
                $fecha          = Carbon::now();
                $estado         = 'En espera';
                $descripcion    = $request->descripcion;
                Mail::to($user)->send(new SolicitudGasto($usuario,$fecha,$descripcion,$estado));
                return back()->with('success','¡Se a guardado la solicitud de gasto');
            }
            return back()->with('success','¡Se a guardado la solicitud de gasto');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para autorizar o denegar una solicitud de gastos ------------------------------------------------------------------------
    function autorizar_gastos($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57,58])->first())
        {
            $usuario = Gastos::find($id);
            if($usuario->id_usuario == Auth::id() &&
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',58)->first() == false)
            {
                return back()->with('error','No puedes autorizar tus propios gastos');
            }
            $gastos = DB::select("select iwgs.id as numero, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            p.descripcion+' '+ iwpg.nombre as proveedor, iwe.nombre, iwgs.cantidad, iwgs.fecha_documento, iwgs.iva, iwtg.nombre as tipo_nombre,
            iwc.marca, iwc.placa, p.nit, iwpg.id, p.proveedor as cod_proveedor, iwpg.cui,
            iwgs.serie_documento, u.name, iwgs.no_retencion
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_camiones as iwc on iwgs.id_vehiculo = iwc.id
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwgs.id_tipo_gasto = iwtg.id
            join users as u on u.id = iwgs.id_usuario
            where iwgs.id_estado = 24
            and iwgs.id = :id",['id'=>$id]);
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la opción para autorizar o rechazar un gasto en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $gastos_a = [];
            foreach($gastos as $g)
            {
                $gastos_a [] = ['numero'=>$g->numero,'no_documento'=>$g->no_documento,'descripcion'=>$g->descripcion,'monto'=>$g->monto,'id_estado'=>$g->id_estado,'fecha_registrado'=>$g->fecha_registrado,
                    'proveedor'=>utf8_encode($g->proveedor),'nombre'=>$g->nombre,'cantidad'=>$g->cantidad,'fecha_documento'=>$g->fecha_documento,'tipo_nombre'=>$g->tipo_nombre,'iva'=>$g->iva,'id'=>$g->id,
                    'marca'=>$g->marca,'placa'=>$g->placa,'nit'=>$g->nit,'cod_proveedor'=>$g->cod_proveedor,'cui'=>$g->cui,'serie_documento'=>$g->serie_documento,'name'=>$g->name,'no_retencion'=>$g->no_retencion
                ];
            }
            return view('gastos.autorizar',compact('gastos','gastos_a','id'));
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function guardar_autorizacion_gastos($id,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57,58])->first())
        {
            $gasto = Gastos::findOrFail($id);
            $gasto->id_estado           = $request->estado;
            $gasto->comentarios         = $request->comentarios;
            $gasto->id_autoriza         = Auth::id();
            $gasto->fecha_autorizacion  = Carbon::now();
            $gasto->save();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Autorizo o rechazo una solicitud de gasto en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
            {
                $user           = User::where('id','=',$gasto->id_usuario)->pluck('email');
                $usuario        = Auth::user()->name;
                $fecha          = Carbon::now();
                $descripcion    = $gasto->descripcion;
                Mail::to($user)->send(new OperarGasto($usuario,$fecha,$descripcion));
                return redirect()->route('gas_pen_aut',$gasto->id_usuario)->with('success','Cambios realizados correctamente');
            }
            return redirect()->route('gas_pen_aut',$gasto->id_usuario)->with('success','Cambios realizados correctamente');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de gastos autorizados por el usuario que esta visualizando la vista --------------------------------
    function mis_gastos_autorizados()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a visualizar sus gastos autorizados en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.gastos_auto_suc');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_mis_gastos_autorizados()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.serie_documento, iwgs.no_retencion, iwgs.iva,
            p.descripcion+''+iwpg.nombre as proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_usuario = :user
            and id_estado = 25",['user'=>Auth::id()]);
            return DataTables($gastos)->make(true);
        }
        return 'error';
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de gastos autorizados por los diferentes usuarios con permiso para autorizar o rechazar -----------
    public function listado_sucursales_gastos()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            return view('gastos.listado_usuarios');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    public function datos_listado_sucursales_gastos()
    {
        if(Auth::user()->roles == 0)
        {
            $sucursales = DB::select("select u.id, u.name
            from users as u
            left join inventario_web_gastos_sucursales as iwgs on u.id = iwgs.id_usuario
            where iwgs.id_estado = 24
            group by u.id, u.name");
            return DataTables::of($sucursales)->make(true);
        } 
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $sucursales = DB::select("select u.id, u.name
            from users as u
            left join inventario_web_gastos_sucursales as iwgs on u.id = iwgs.id_usuario
            where iwgs.id_estado = 24
            and u.roles in (".implode(",",$roles).")
            group by u.id, u.name");
            return DataTables::of($sucursales)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para realizar esta acción');
    }
    public function datos_listado_sucursales_liquidaciones()
    {
        if(Auth::user()->roles == 0)
        {
            $sucursales = DB::select('select u.id, u.name
            from users as u
            left join inventario_web_liquidaciones as iwl on iwl.id_usuario = u.id
            where iwl.id_estado = 27
            group by u.id, u.name');
            return DataTables::of($sucursales)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $sucursales = DB::select('select u.id, u.name
            from users as u
            left join inventario_web_liquidaciones as iwl on iwl.id_usuario = u.id
            where iwl.id_estado = 27
            and u.roles in( '.implode(',',$roles).')
            group by u.id, u.name');
            return DataTables::of($sucursales)->make(true);
        }
    }

    function gastos_autorizados($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[43,44])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a visualizar los gastos autorizados en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.gastos_auto',compact('id'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_gastos_autorizados($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[43,44])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and id_usuario = :user",['user'=>$id]);
            return DataTables($gastos)->make(true);
        }
        $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
        iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago,
        p.descripcion+''+iwpg.nombre as proveedor, iwgs.iva
        from inventario_web_gastos_sucursales as iwgs
        left join proveedor as p on p.proveedor = iwgs.id_proveedor
        left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
        join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
        join users as u on u.id = iwgs.id_autoriza
        join users as us on us.id = iwgs.id_usuario
        where id_estado = 1000
        and id_usuario = :user",['user'=>'99999']);
        return DataTables($gastos)->make(true);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de gastos de una sucursal filtrados por un rango de fechas ------------------------------------------
    function gastos_autorizados_por_fecha($id,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[43,44])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a visualizar los gastos autorizados en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('gastos.gastos_auto_fecha',compact('id','inicio','fin'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_gastos_autorizados_por_fecha($id,$inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[43,44])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and id_usuario = :user
            and iwgs.fecha_registrado between :inicio and :fin",['user'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($gastos)->make(true);
        }
        $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
        iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago,
        p.descripcion+''+iwpg.nombre as proveedor, iwgs.iva
        from inventario_web_gastos_sucursales as iwgs
        left join proveedor as p on p.proveedor = iwgs.id_proveedor
        left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
        join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
        join users as u on u.id = iwgs.id_autoriza
        join users as us on us.id = iwgs.id_usuario
        where id_estado = 1000
        and id_usuario = :user",['user'=>'999999999']);
        return DataTables($gastos)->make(true);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver un gasto que ya fue operado ------------------------------------------------------------------------------------
    function ver_gastos_operados($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $gastos = DB::select("select iwgs.id as numero, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            p.descripcion+' '+ iwpg.nombre as proveedor, iwe.nombre, iwgs.cantidad, iwgs.fecha_documento, iwgs.iva, iwtg.nombre,
            iwc.marca, iwc.placa, p.nit, iwpg.id, p.proveedor as cod_proveedor, iwpg.cui, iwgs.fecha_autorizacion,
            iwgs.contra_de_pago, u.name as usuario, us.name, iwgs.serie_documento, iwgs.no_retencion
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_camiones as iwc on iwgs.id_vehiculo = iwc.id
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwgs.id_tipo_gasto = iwtg.id
            join users as u on u.id = iwgs.id_usuario
            join users as us on us.id = iwgs.id_autoriza
            where iwgs.id = :id",['id'=>$id]);

            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo la información de un gasto operado en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.ver_gasto_op',compact('gastos','id'));
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de liquidaciones del usuario que está visualizando ------------------------------------------------
    function liquidaciones()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la vista de liquidaciones en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.liquidaciones_suc');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_liquidaciones()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            where id_usuario = :user
            and u.empresa = :empresa",['user'=>Auth::id(),'empresa'=>Auth::user()->empresa]);
            return DataTables($liquidacion)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de liquiedaciones de usuarios que pueden autorizar otras liquidaciones ----------------------------
    function listado_usuarios_liquidaciones()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first()
            || Auth::user()->roles == 0)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la vista de liquidaciones por autorizar en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.listado_usuarios_liqui');
        }
        return redirect()->route('inicio_gastos_espera')->with('error','¡No tienes permiso para ingresar!');
    }

    function liquidaciones_otras_liquidaciones($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la vista de liquidaciones por autorizar en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.liquidaciones',compact('id'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_otras_liquidaciones($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            where id_usuario = :user
            and u.empresa = :empresa
            and iwl.id_estado > 26",['user'=>$id,'empresa'=>Auth::user()->empresa]);
            return DataTables($liquidacion)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de liquidaciones filtradas por fecha --------------------------------------------------------------
    function liquidaciones_otras_liquidaciones_fecha($id,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la vista de liquidaciones por autorizar en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('gastos.liquidacionesFecha',compact('id','inicio','fin'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_otras_liquidaciones_fecha($id, $inicio, $fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            where id_usuario = :user
            and u.empresa = :empresa
            and iwl.id_estado >= 27
            and iwl.fecha_creacion between :inicio and :fin",['user'=>$id,'empresa'=>Auth::user()->empresa,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($liquidacion)->make(true);
        }
        $liquidacion = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
        p.descripcion as proveedor, iwe.nombre
        from inventario_web_gastos_sucursales as iwgs,
        proveedor as p,
        inventario_web_estados as iwe
        where id_usuario = :user
        and id_estado = 2
        and p.proveedor = iwgs.id_proveedor
        and iwe.id = iwgs.id_estado",['user'=>'9999999']);
        return DataTables($liquidacion)->make(true);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite guardar una nueva persona para solicitud de gastos ----------------------------------------------------------
    function guardar_persona(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',42)->first())
        {
            if($existe = Persona::where('cui',$request->num_cui)->first() == true)
            {
                return back()->with('error','¡El número de CUI ya fue registrado, no se permite duplicar!');
            }
            $persona                   = new Persona();
            $persona->cui           = $request->num_cui;
            $persona->nombre        = $request->nombre;
            $persona->save();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Registro a un nuevo proveedor por medio de CUI en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return back()->with('success','¡Se a guardado la información correctamente');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para generar el encabezado de una nueva liquidación de gastos -----------------------------------------------------------
    function nueva_liquidacion(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $nueva_liquidacion                      = new Liquidacion();
            $nueva_liquidacion->responsable         = $request->responsable;
            $nueva_liquidacion->id_sucursal         = Auth::user()->sucursal;
            $nueva_liquidacion->fecha_inicial       = $request->fecha_inicial;
            $nueva_liquidacion->fecha_final         = $request->fecha_final;
            $nueva_liquidacion->fecha_creacion      = Carbon::now();
            $nueva_liquidacion->observaciones       = $request->observaciones;
            $nueva_liquidacion->id_estado           = 26;
            $nueva_liquidacion->id_usuario          = Auth::id();
            $nueva_liquidacion->save();
            $id_time = $nueva_liquidacion->fecha_creacion;
            $id = Liquidacion::where('fecha_creacion',$id_time)->first();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Registro una nueva liquidación en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return redirect()->route('edit_liquid',['id'=>$id])->with('success','¡Nueva liquidación creada, agrege los gastos a liquidar!');
        }
        return redirect()->route('liquidaciones')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para agregar o eliminar los gastos que se incluiran dentro de la liquidación --------------------------------------------
    function editar_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $encabezado = Liquidacion::find($id);
            if($encabezado->id_estado == 26 && $encabezado->id_usuario == Auth::id())
            {
                $historial              = new Historial();
                $historial->id_usuario  = Auth::id();
                $historial->actividad   = 'Ingreso a la vista editar liquidación en el modulo de gastos';
                $historial->created_at  = new Carbon();
                $historial->updated_at  = new Carbon();
                $historial->save();
                return view('gastos.editar_liquidacion',compact('encabezado','id'));
            }
            return redirect()->route('ve_dliqui',['id'=>$id]);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function listado_de_gastos_en_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $gastos_ = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.fecha_documento,
            iwe.nombre, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.fecha_registrado,
            p.descripcion+''+iwpg.nombre as proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and iwgs.id_liquidacion = :id
            and iwgs.id_usuario = :user",['id'=>$id,'user'=>Auth::id()]);
            $gastos = [];
            foreach($gastos_ as $g)
            {
                $gastos[] = ['id'=>$g->id,'no_documento'=>$g->no_documento,'descripcion'=>$g->descripcion,'monto'=>$g->monto,'fecha_documento'=>$g->fecha_documento,
                'nombre'=>$g->nombre,'fecha_autorizacion'=>$g->fecha_autorizacion,'name'=>$g->name,'usuario'=>$g->usuario,'contra_de_pago'=>$g->contra_de_pago,
                'proveedor'=>utf8_encode($g->proveedor)];
            }
            return \Response::json($gastos);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function listado_gastos_pendientes()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $gastos_ = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.fecha_documento,
            iwe.nombre, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago,
            p.descripcion+''+iwpg.nombre as proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and iwgs.id_liquidacion is null
            and iwgs.id_usuario = :user",['user'=>Auth::id()]);
            $gastos = [];
            foreach($gastos_ as $g)
            {
                $gastos[] = ['id'=>$g->id,'no_documento'=>$g->no_documento,'descripcion'=>$g->descripcion,'monto'=>$g->monto,'fecha_documento'=>$g->fecha_documento,
                'nombre'=>$g->nombre,'fecha_autorizacion'=>$g->fecha_autorizacion,'name'=>$g->name,'usuario'=>$g->usuario,'contra_de_pago'=>$g->contra_de_pago,
                'proveedor'=>utf8_encode($g->proveedor)];
            }
            return \Response::json($gastos);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function agregar_gastos_a_liquidacion($id,$liqui)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $agregar = Gastos::find($id);
            $agregar->id_liquidacion = $liqui;
            $agregar->save();
            return $agregar;
        }
        return redirect()->route('liquidaciones')->with('error','No tienes permisos para accesar');
    }

    function eliminar_gastos_de_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $agregar = Gastos::find($id);
            $agregar->id_liquidacion = null;
            $agregar->save();
            return $agregar;
        }
        return redirect()->route('liquidaciones')->with('error','No tienes permisos para accesar');
    }

    function cambiar_estado_liquidacion($id)
    {
        $liquidacion = Liquidacion::find($id);
        $gastos = Gastos::where('id_liquidacion',$id)->count('id');
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',42)->first() && $liquidacion->id_estado == 26
        && $gastos > 0)
        {
            $liquidacion->id_estado = 27;
            $liquidacion->fecha_finalizada = Carbon::now();
            $liquidacion->save();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Modifico el estado de una liquidación en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return redirect()->route('ve_dliqui',['id'=>$id])->with('success','¡Liquidación colocada en espera!');
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[43,44])->first() && $liquidacion->id_estado == 26
        && $gastos > 0)
        {
            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',43) 
            && $liquidacion->id_usuario == Auth::id())
            {
                $liquidacion->id_estado = 27;
            }
            else 
            {
                $liquidacion->id_estado = 28;
            }
            $liquidacion->fecha_finalizada = Carbon::now();
            $liquidacion->id_usuario_revisa = Auth::id();
            $liquidacion->fecha_revision    = Carbon::now();
            $liquidacion->save();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Autorizo una liquidación en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return redirect()->route('ve_dliqui',['id'=>$id])->with('success','¡Liquidación colocada en espera!');
        }
        return back()->with('error','No puedes cerrar una liquidación vacia');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el detalle de gastos dentro de una liquidación ---------------------------------------------------------------
    function ver_detalles_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.observaciones, iwl.fecha_finalizada, iwl.fecha_revision
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            where iwl.id = :id
            and u.empresa = 1",['id'=>$id]);
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo los detalles de una liquidación en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.ver_liquidacion',compact('liquidacion','id'));
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_ver_detalles_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.fecha_documento,
            iwe.nombre, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.fecha_registrado,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.serie_documento, iwgs.iva, iwgs.no_retencion
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and iwgs.id_liquidacion = :id",['id'=>$id]);
            return DataTables($gastos)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Imprimir datos de liquidación finalizada para envio a zona 11 -------------------------------------------------------------------
    function imprimir_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[42,43,44])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, us.name as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.observaciones, iwl.fecha_finalizada, iwl.fecha_revision, u.resolucion_autorizacion as nombre
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            where iwl.id = :id
            and u.empresa = :empresa",['id'=>$id,'empresa'=>Auth::user()->empresa]);

            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.fecha_documento,
            iwe.nombre, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.fecha_registrado,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.serie_documento, iwgs.iva, iwgs.no_retencion, p.proveedor as cod_proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and iwgs.id_liquidacion = :id",['id'=>$id]);
            $pdf = PDF::loadView('gastos.imprimirLiquidacion',compact('liquidacion','gastos'));
            return $pdf->download($id.'-'.'Liq'.'-.pdf');
            //return view('gastos.imprimirLiquidacion',compact('liquidacion','gastos'));
        }
       return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite marcar de revisada una liquidación de gastos ----------------------------------------------------------------

    function revision_de_liquidacion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[43,44])->first())
        {
            $liquidacion = Liquidacion::find($id);
            if($liquidacion->id_usuario == Auth::id() && $liquidacion->id_estado <= 27 &&
                $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',43)->first())
            {
                return back()->with('error','No puedes validar tus propias liquidaciones');
            }
            $liquidacion->id_usuario_revisa = Auth::id();
            $liquidacion->id_estado         = 28;
            $liquidacion->fecha_revision    = Carbon::now();
            $liquidacion->save();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Autorizo una liquidación en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return back()->with('success','¡Liquidación revisada!');
        }
        return back()->with('error','¡No es posible realizar modificaciones!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para editar el encabezado de una liquidación ----------------------------------------------------------------------------
    function editar_encabezado_liquidacion($id,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',42)->first())
        {
            $nueva_liquidacion                      = Liquidacion::find($id);
            if($nueva_liquidacion->id_estado == 26)
            {
                $nueva_liquidacion->responsable         = $request->responsable;
                $nueva_liquidacion->fecha_inicial       = $request->fecha_inicial;
                $nueva_liquidacion->fecha_final         = $request->fecha_final;
                $nueva_liquidacion->observaciones       = $request->observaciones;
                $nueva_liquidacion->save();
                $historial              = new Historial();
                $historial->id_usuario  = Auth::id();
                $historial->actividad   = 'Modifico los datos de encabezado de una liquidación en el modulo de gastos';
                $historial->created_at  = new Carbon();
                $historial->updated_at  = new Carbon();
                $historial->save();
                return back()->with('success','¡Los datos fueron modificados correctamente!');
            }
            return back()->with('error','¡No es posible modificar la liquidación!');
        }
        return redirect()->route('ve_dliqui',$id)->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------- Funciones que permiten ver el listado de gastos que no fueron aprovados para ser liquidados -----------------------------------------
    function gastos_rechazados()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo los gastos que fueron rechazados en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.gastos_rechazados');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_gastos_rechazados()
    {
        if(Auth::user()->roles == 0)
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.serie_documento, iwgs.no_retencion, iwgs.iva,
            p.descripcion+''+iwpg.nombre as proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 29
            and iwgs.fecha_registrado > :fecha",['fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($gastos)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',56)->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.serie_documento, iwgs.no_retencion, iwgs.iva,
            p.descripcion+''+iwpg.nombre as proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_usuario = :user
            and id_estado = 29
            and fecha_registrado > :fecha",['user'=>Auth::id(),'fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($gastos)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, (iwgs.monto - coalesce(iwgs.iva,0)) as monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.serie_documento, iwgs.no_retencion, iwgs.iva,
            p.descripcion+''+iwpg.nombre as proveedor
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 29
            and us.roles in (".implode(',',$roles).")
            and iwgs.fecha_registrado > :fecha",['fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($gastos)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de liquiedaciones de usuarios sin filtrar por usuario ---------------------------------------------
    function rep_liquidaciones_otras_liquidaciones()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a la vista de liquidaciones por autorizar en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.rep_liquidaciones');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function rep_datos_otras_liquidaciones()
    {
        if(Auth::user()->roles == 0)
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.id_estado,
            count(awgs.id_liquidacion) as docs, sum(awgs.monto) as suma
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            left join inventario_web_gastos_sucursales as awgs on iwl.id = awgs.id_liquidacion
            where u.empresa = 1
            and iwl.id_estado >= 26
            group by iwl.id, iwl.responsable, sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, estado, us.name, iwl.id_estado",['empresa'=>Auth::user()->empresa]);
            return DataTables($liquidacion)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.id_estado,
            count(awgs.id_liquidacion) as docs, sum(awgs.monto) as suma
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            left join inventario_web_gastos_sucursales as awgs on iwl.id = awgs.id_liquidacion
            where u.empresa = :empresa
            and iwl.id_estado >= 26
            and us.id = :user
            group by iwl.id, iwl.responsable, sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, estado, us.name, iwl.id_estado",['empresa'=>Auth::user()->empresa,'user'=>Auth::id()]);
            return DataTables($liquidacion)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.id_estado,
            count(awgs.id_liquidacion) as docs, sum(awgs.monto) as suma
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            left join inventario_web_gastos_sucursales as awgs on iwl.id = awgs.id_liquidacion
            where u.empresa = 1
            and iwl.id_estado >= 26
            and us.roles in (".implode(',',$roles).")
            group by iwl.id, iwl.responsable, sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, estado, us.name, iwl.id_estado",['empresa'=>Auth::user()->empresa]);
            return DataTables($liquidacion)->make(true);
        }
    }
    //--------------------------- Funciones fecha -------------------------------------------------------------------------------------------------------------
    function rep_liquidaciones_otras_liquidaciones_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $inicio = $request -> inicio;
            $fin    = $request -> fin;
            return view('gastos.rep_liquidaciones_fecha', compact('inicio','fin'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function rep_datos_otras_liquidaciones_fecha($inicio, $fin)
    {
        if(Auth::user()->roles == 0)
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.id_estado,
            count(awgs.id_liquidacion) as docs, sum(awgs.monto) as suma
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            left join inventario_web_gastos_sucursales as awgs on iwl.id = awgs.id_liquidacion
            where u.empresa = :empresa
            and iwl.id_estado >= 26
            and iwl.fecha_inicial between :inicio and :fin
            group by iwl.id, iwl.responsable, sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, estado, us.name, iwl.id_estado",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin]);
            return DataTables($liquidacion)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56])->first())
        {
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.id_estado,
            count(awgs.id_liquidacion) as docs, sum(awgs.monto) as suma
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            left join inventario_web_gastos_sucursales as awgs on iwl.id = awgs.id_liquidacion
            where u.empresa = :empresa
            and iwl.id_estado >= 26
            and us.id = :user
            and iwl.fecha_inicial between :inicio and :fin
            group by iwl.id, iwl.responsable, sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, estado, us.name, iwl.id_estado",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin,'user'=>Auth::id()]);
            return DataTables($liquidacion)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $liquidacion = DB::select("select iwl.id, iwl.responsable, u.nombre as sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, iwe.nombre as estado, us.name, iwl.id_estado,
            count(awgs.id_liquidacion) as docs, sum(awgs.monto) as suma
            from inventario_web_liquidaciones as iwl
            join unidades as u on u.cod_unidad = iwl.id_sucursal
            join inventario_web_estados as iwe on iwe.id = iwl.id_estado
            left join users as us on iwl.id_usuario_revisa = us.id
            left join inventario_web_gastos_sucursales as awgs on iwl.id = awgs.id_liquidacion
            where u.empresa = :empresa
            and iwl.id_estado >= 26
            and us.roles in (".implode(',',$roles).")
            and iwl.fecha_inicial between :inicio and :fin
            group by iwl.id, iwl.responsable, sucursal, iwl.fecha_inicial, iwl.fecha_final,
            iwl.fecha_creacion, estado, us.name, iwl.id_estado",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin]);
            return DataTables($liquidacion)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------- Funciones para ver el listado de gastos autorizados por los diferentes usuarios con permiso para autorizar o rechazar -----------
    function rep_gastos_autorizados()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso a visualizar los gastos autorizados en el modulo de gastos';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return view('gastos.rep_gastos_auto');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function rep_datos_gastos_autorizados()
    {
        if(Auth::user()->roles == 0)
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento, iwtg.nombre as tipo,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwtg.id = iwgs.id_tipo_gasto
            left join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado >= 24
            and iwgs.fecha_registrado > :fecha",['fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($gastos)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento, iwtg.nombre as tipo,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwtg.id = iwgs.id_tipo_gasto
            left join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado >= 24
            and iwgs.id_usuario = :user
            and iwgs.fecha_registrado > :fecha",['user'=>Auth::id(),'fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($gastos)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento, iwtg.nombre as tipo,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwtg.id = iwgs.id_tipo_gasto
            left join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado >= 24
            and us.roles in (".implode(",",$roles).")
            and iwgs.fecha_registrado > :fecha",['fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($gastos)->make(true);
        }
    }
    //--------------------------- Funciones fecha -------------------------------------------------------------------------------------------------------------
    function rep_gastos_autorizados_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $inicio = $request -> inicio;
            $fin    = $request -> fin;
            return view('gastos.rep_gastos_auto_fecha', compact('inicio','fin'));
        }
       return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function rep_datos_gastos_autorizados_fecha($inicio, $fin)
    {
        if(Auth::user()->roles == 0)
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento, iwtg.nombre as tipo,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwtg.id = iwgs.id_tipo_gasto
            left join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where u.empresa = :empresa
            and id_estado >= 24
            and iwgs.fecha_registrado between :inicio and :fin",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin]);
            return DataTables($gastos)->make(true);
        }
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56])->first())
        {
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento, iwtg.nombre as tipo,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwtg.id = iwgs.id_tipo_gasto
            left join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where u.empresa = :empresa
            and id_estado >= 24
            and us.id = :user
            and iwgs.fecha_registrado between :inicio and :fin",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin,
            'user'=>Auth::id()]);
            return DataTables($gastos)->make(true);
        }
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[57])->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $gastos = DB::select("select iwgs.id, iwgs.no_documento, iwgs.descripcion, iwgs.monto, iwgs.id_estado, iwgs.fecha_registrado,
            iwe.nombre, iwgs.cantidad, iwgs.fecha_autorizacion, u.name, us.name as usuario, iwgs.contra_de_pago, iwgs.serie_documento, iwtg.nombre as tipo,
            p.descripcion+''+iwpg.nombre as proveedor, iwgs.no_retencion, iwgs.iva
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join inventario_web_tipo_gastos as iwtg on iwtg.id = iwgs.id_tipo_gasto
            left join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where u.empresa = ".Auth::user()->empresa."
            and id_estado >= 24
            and us.roles in (".implode(",",$roles).")
            and iwgs.fecha_registrado between :inicio and :fin",['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($gastos)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
    function resumen_de_gastos()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            return view('gastos.resumen');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_resumen_de_gastos()
    {
        if(Auth::user()->roles == 0)
        {
            $gastos = DB::select('SELECT cod_unidad, name, Primero_registrado, Ultimo_registrado, Mes, Year, Tipo, Cantidad_total, Monto_total, keym
            FROM DBA.BI_GASTOS_WEB');
            return DataTables::of($gastos)->addColumn('details_url', function($gastos){
            return url('dtg/'. $gastos->keym);
            })->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',56)->first())
        {
            $gastos = DB::select('SELECT cod_unidad, name, Primero_registrado, Ultimo_registrado, Mes, Year, Tipo, Cantidad_total, Monto_total, keym
            FROM DBA.BI_GASTOS_WEB
            WHERE id = :user',['user'=>Auth::id()]);
            return DataTables::of($gastos)->addColumn('details_url', function($gastos){
            return url('dtg/'. $gastos->keym);
            })->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',57)->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $gastos = DB::select('SELECT cod_unidad, name, Primero_registrado, Ultimo_registrado, Mes, Year, Tipo, Cantidad_total, Monto_total, keym
            FROM DBA.BI_GASTOS_WEB
            WHERE roles  in ('.implode(',',$roles).')');
            return DataTables::of($gastos)->addColumn('details_url', function($gastos){
            return url('dtg/'. $gastos->keym);
            })->make(true);
        }
    }

    function detalle_resumen_de_gastos($keym)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            $detalles = DB::select("select iwgs.id, iwgs.serie_documento, iwgs.no_documento, iwgs.descripcion, p.descripcion+' '+iwpg.nombre as proveedor,
            (convert(char(12),iwgs.fecha_registrado,103)) as Registrado, (convert(char(12),iwgs.fecha_autorizacion,103)) as Autorizado, u.name, iwgs.no_retencion,
            iwgs.iva, iwgs.monto, us.id||us.sucursal||us.bodega||MONTH(iwgs.fecha_registrado)||YEAR(iwgs.fecha_registrado)||iwgs.id_tipo_gasto as keym
            from inventario_web_gastos_sucursales as iwgs
            left join proveedor as p on p.proveedor = iwgs.id_proveedor
            left join inventario_web_personas_gastos as iwpg on iwpg.id = iwgs.id_persona
            join inventario_web_estados as iwe on iwe.id = iwgs.id_estado
            join users as u on u.id = iwgs.id_autoriza
            join users as us on us.id = iwgs.id_usuario
            where id_estado = 25
            and keym = :keym",['keym'=>$keym]);
            return DataTables($detalles)->make(true);
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
    function resumen_total_de_gastos()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[56,57])->first())
        {
            return view('gastos.resumen_total');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_resumen_total_de_gastos()
    {
        if(Auth::user()->roles == 0)
        {
            $gastos = DB::select('SELECT cod_unidad, name,  Mes, Year, Cantidad_total, Monto_total  FROM DBA.BI_RESUMEN_GASTOS_WEB');
            return DataTables($gastos)->make(true);
        }
        else if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',56)->first())
        {
            $gastos = DB::select('SELECT cod_unidad, name,  Mes, Year, Cantidad_total, Monto_total  FROM DBA.BI_RESUMEN_GASTOS_WEB
            WHERE id = :user',['user'=>Auth::id()]);
            return DataTables($gastos)->make(true);
        }
        else if ($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',57)->first())
        {
            $roles_a_mostrar = DB::select('select *
            from inventario_web_permisos_roles_usuarios_filtrados 
            where id_usuario = :user
            and id_modulo = :modulo',['user'=>Auth::id(),'modulo'=>10]);
            foreach($roles_a_mostrar as $rols)
            {
                $roles[] = (number_format($rols->id_rol,0));
            }
            $gastos = DB::select('SELECT cod_unidad, name,  Mes, Year, Cantidad_total, Monto_total  FROM DBA.BI_RESUMEN_GASTOS_WEB
            WHERE roles in ('.implode(',',$roles).')');
            return DataTables($gastos)->make(true);
        }
    }
}
