<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Auth;
use App\User;
use App\Estado;
use App\DetMovi;
use App\MoviInve;
use Carbon\Carbon;
use App\Historial;
use App\GrupoUsuario;
use App\Mail\Sucursal;
use App\ProductoSemana;
use App\TransferenciaSucursal;
use App\DetTranSucursales;
use App\Mail\tranSucursales;
use App\BitacoraTransferencia;
use Yajra\DataTables\DataTables;
use App\Mail\tranSucFinalizadas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
//datos_tran_pendientes_sucursales
class TransferenciasSucursales extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver las transferencias realizadas por un usuario en estado de espera ---------------------------------------------
    function mis_transferencias_espera()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso al modulo de transferencias';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first())
        {
            $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();
            $series = DB::select("select * 
            from series_movi
            where empresa = :empresa 
            and cod_tipo_movi = 'E'
            and cod_serie_movi = 'AJ'
            or empresa = :empresa2
            and cod_tipo_movi = 'E' 
            and cod_serie_movi = 'TJ'",['empresa'=>Auth::user()->empresa,'empresa2'=>Auth::user()->empresa]);
            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();
            return view('TranSucursales.enEspera',compact('saleDe','entraSu','series'));
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first()) 
        {
            $saleDe = DB::table('unidades')->where('cod_unidad',Auth::user()->sucursal)->where('empresa',Auth::user()->empresa)->get();
            $series = DB::select("select * 
            from series_movi
            where empresa = :empresa
            and cod_tipo_movi = 'E' 
            and cod_serie_movi = 'TJ'",['empresa'=>Auth::user()->empresa]);
            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();
            return view('TranSucursales.enEspera',compact('saleDe','entraSu','series'));
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_mis_transferencias_espera()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, 
            iwet.fecha_paraCarga, bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, iwet.id_usuarioRecibe, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            where iwet.id_estado = 13 
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.unidad_transf = :sucursal',['sucursal'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            iwet.DESCRIPCION, bo.observacion as bodega, iwet.id, iwet.id_usuarioRecibe, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.cod_unidad = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.cod_bodega = bo.cod_bodega
            where iwet.id_estado = 13
            and u.empresa = iwet.empresa
            and iwet.unidad_transf = :sucursal
            and iwet.bodega_Transf = :bodega
            and bo.empresa = u.empresa 
            and bo.cod_unidad = u.cod_unidad',['sucursal'=>Auth::user()->sucursal,'bodega'=>Auth::user()->bodega]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función para cretar transferencias con factura, pedido o sin documento ----------------------------------------------------------
    public function crear_transferencia_sucursales(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            if($existe = DB::table('movi_cc')->where('num_movi',$request->numero)->where('cod_serie_movi',$request->serie)
            ->where('empresa',Auth::user()->empresa)->first())
            {
                $validator = Validator::make($request->all(),[
                    'saleDe'=>'required|numeric',
                    'saleBo'=>'required|numeric',
                    'serie'=>'required',
                    'numero'=>'required|numeric',
                    //'cod_serie'=>'required',
                ]);
                if ($validator->fails()) 
                {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                else 
                {
                    $tran = new TransferenciaSucursal();
                    $tran->empresa               = Auth::user()->empresa;
                    $tran->cod_tipo_movi         = 'E';
                    $tran->cod_serie_movi        = 'TZ';
                    $tran->cod_unidad            = $request->saleDe;
                    $tran->cod_bodega            = $request->saleBo; 
                    $tran->fecha                 = Carbon::now();
                    $tran->fecha_opera           = Carbon::now();
                    $tran->impreso               = 'N';
                    //$tran->cod_motivo            = 95;
                    $tran->Clasif_transferencia  = 1;
                    $tran->unidad_transf         = $request->entraSu;
                    $tran->bodega_Transf         = $request->entraBo;
                    $tran->usuario               = Auth::user()->name;
                    $tran->fecha_modificacion    = Carbon::now();
                    //$tran->observacion         = $request->observacion;
                    $tran->DESCRIPCION           = $request->descripcion;
                    //$tran->placa_vehiculo      = $request->placa;
                    //$tran->comentario          = $request->comentario;
                    //$tran->referencia          = $request->referencia;
                    $tran->id_estado             = 13;//an ap 
                    $tran->created_at            = Carbon::now();
                    $tran->updated_at            = Carbon::now();
                    $tran->fecha_paraCarga       = Carbon::now();
                    $tran->fechaEntrega          = Carbon::now();
                    $tran->serieFactura          = $existe->cod_serie_movi;
                    $tran->numeroFactura         = $existe->num_movi;
                    $tran->cliente               = $existe->nombre_cliente;
                    if($tran->save())
                    {
                        $id_n = $tran->created_at;
                        $id = TransferenciaSucursal::where('created_at',$id_n)->first();
                        $todos_productos = DB::select('select empresa, cod_tipo_movi, cod_serie_movi, num_movi, cod_producto,
                        cod_unidad, cod_bodega, cantidad1, orden, precio
                        from det_movi_inve
                        where num_movi = :num
                        and cod_serie_movi = :serie
                        and empresa = :empresa',['num'=>$request->numero,'serie'=>$request->serie,'empresa'=>Auth::user()->empresa]);
                        foreach ($todos_productos as $tp) 
                        {
                            $detTran                        = new DetTranSucursales();
                            $detTran->empresa               = Auth::user()->empresa;
                            $detTran->cod_tipo_movi         = 'E';
                            $detTran->cod_serie_movi        = 'TZ';
                            $detTran->num_movi              = $id->id;
                            $detTran->cod_producto          = $tp->cod_producto;
                            $detTran->cod_unidad            = $request->saleDe;
                            $detTran->cod_bodega            = $request->saleBo;
                            $detTran->cantidad1             = $tp->cantidad1;
                            $detTran->costo                 = $tp->precio;
                            $detTran->orden                 = $tp->orden;
                            $detTran->operado_stamp         = Carbon::now();
                            $detTran->clasif_Transferencia  = 1;
                            $detTran->unidad_transf         = $tp->cod_unidad;
                            $detTran->bodega_transf         = $tp->cod_bodega;
                            //$detTran->costoforzado        = 0;
                            //$detTran->costo_pivot         = 0; 
                            //$detTran->iva_vehiculos       = 'N';
                            //$detTran->exento_iva          = 'N';
                            $detTran->created_at            = Carbon::now();
                            $detTran->updated_at            = Carbon::now();
                            //$detTran->existencia          = $nuevo->existencia;
                            //$detTran->min                 = $nuevo->min;
                            //$detTran->max                 = $nuevo->max;
                            //$detTran->reorden             = $nuevo->reorden;
                            $detTran->id_inserto            = Auth::id();
                            $detTran->incluido              = 1;
                            $detTran->save();
                            $historial              = new Historial();
                            $historial->id_usuario  = Auth::id();
                            $historial->actividad   = 'Se genero una nueva transferencia';
                            $historial->created_at  = new Carbon();
                            $historial->updated_at  = new Carbon();
                            $historial->save();
                        }
                        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                        {
                            $user = User::where('sucursal','=',$request->saleDe)->whereIn('roles',[3,4])->pluck('email');
                            $usuario = Auth::user()->name;
                            $fecha = Carbon::now();
                            $estado = 'Creada';
                            $numero = $id->$id;
                            $correo = Auth::user()->email;
                            Mail::to($user)->send(new tranSucursales($usuario,$fecha));
                            if(Mail::failures())
                            {
                                return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                            }
                            return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                        }
                        return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                    }
                    return redirect()->route('transferencias_en_espera')->with('error','¡Intente de nuevo!');
                }
                return redirect()->route('transferencias_en_espera')->with('error','¡Intente de nuevo!');
            }
            else if($existe = DB::table('pedidos')->where('cod_serie_movi',$request->serie)->where('num_movi',$request->numero)
            ->where('empresa',Auth::user()->empresa)->first())//Permite generar una transferencia por medio de un pedido 
            {
                $validator = Validator::make($request->all(),[
                    'saleDe'=>'required|numeric',
                    'saleBo'=>'required|numeric',
                    'entraSu'=>'required|numeric',
                    'entraBo'=>'required|numeric',
                    'serie'=>'required',
                    'numero'=>'required|numeric',
                    //'cod_serie'=>'required',
                ]);
                if ($validator->fails()) {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                else 
                {
                    $tran = new TransferenciaSucursal();
                    $tran->empresa               = Auth::user()->empresa;
                    $tran->cod_tipo_movi         = 'E';
                    $tran->cod_serie_movi        = 'TZ';
                    $tran->cod_unidad            = $request->saleDe;
                    $tran->cod_bodega            = $request->saleBo; 
                    $tran->fecha                 = Carbon::now();
                    $tran->fecha_opera           = Carbon::now();
                    $tran->impreso               = 'N';
                    //$tran->cod_motivo            = 95;
                    $tran->Clasif_transferencia  = 1;
                    $tran->unidad_transf         = $request->entraSu;
                    $tran->bodega_Transf         = $request->entraBo;
                    $tran->usuario               = Auth::user()->name;
                    $tran->fecha_modificacion    = Carbon::now();
                    //$tran->observacion         = $request->observacion;
                    $tran->DESCRIPCION           = $request->descripcion;
                    //$tran->placa_vehiculo      = $request->placa; jsSK8n23jzr, du2mhjQL1ZT
                    //$tran->comentario          = $request->comentario;
                    //$tran->referencia          = $request->referencia;
                    $tran->id_estado             = 13;
                    $tran->created_at            = Carbon::now();
                    $tran->updated_at            = Carbon::now();
                    $tran->fecha_paraCarga       = Carbon::now();
                    $tran->fechaEntrega          = Carbon::now();
                    $tran->serieFactura          = $request->serie;
                    $tran->numeroFactura         = $request->numero;
                    $tran->cliente               = $existe->nombre_cliente;
                    if($tran->save())
                    {
                        $id_n = $tran->created_at;
                        $id = TransferenciaSucursal::where('created_at',$id_n)->first();
                        $todos_productos =DB::select('select empresa, cod_tipo_movi, cod_serie_movi, num_movi, cod_producto,
                        cod_unidad, cod_bodega, cantidad2, orden
                        from det_pedidos
                        where cod_serie_movi = :serie
                        and num_movi = :num
                        and empresa = :empresa',['num'=>$request->numero,'serie'=>$request->serie,'empresa'=>Auth::user()->empresa]);
                        foreach ($todos_productos as $tp) 
                        {
                            $detTran                        = new DetTranSucursales();
                            $detTran->empresa               = Auth::user()->empresa;
                            $detTran->cod_tipo_movi         = 'E';
                            $detTran->cod_serie_movi        = 'TZ';
                            $detTran->num_movi              = $id->id;
                            $detTran->cod_producto          = $tp->cod_producto;
                            $detTran->cod_unidad            = $request->saleDe;
                            $detTran->cod_bodega            = $request->saleBo;
                            $detTran->cantidad1             = $tp->cantidad2;
                            //$detTran->costo               = 0;
                            $detTran->orden                 = $tp->orden;
                            $detTran->operado_stamp         = Carbon::now();
                            $detTran->clasif_Transferencia  = 1;
                            $detTran->unidad_transf         = $request->entraSu;
                            $detTran->bodega_transf         = $request->entraBo;
                            //$detTran->costoforzado        = 0;
                            //$detTran->costo_pivot         = 0; 
                            //$detTran->iva_vehiculos       = 'N';
                            //$detTran->exento_iva          = 'N';
                            $detTran->created_at            = Carbon::now();
                            $detTran->updated_at            = Carbon::now();
                            //$detTran->existencia          = $nuevo->existencia;
                            //$detTran->min                 = $nuevo->min;
                            //$detTran->max                 = $nuevo->max;
                            //$detTran->reorden             = $nuevo->reorden;
                            $detTran->id_inserto            = Auth::id();
                            $detTran->incluido              = 1;
                            $detTran->save();
                            $historial              = new Historial();
                            $historial->id_usuario  = Auth::id();
                            $historial->actividad   = 'Se genero una nueva transferencia';
                            $historial->created_at  = new Carbon();
                            $historial->updated_at  = new Carbon();
                            $historial->save();
                        }
                        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                        {
                            $user = User::where('sucursal','=',$request->entraSu)->whereIn('roles',[3,4])->pluck('email');
                            $usuario = Auth::user()->name;
                            $fecha = Carbon::now();
                            $estado = 'Creada';
                            $numero = $id->id;
                            $correo = Auth::user()->email;
                            Mail::to($user)->send(new tranSucursales($usuario,$fecha));
                            if(Mail::failures())
                            {
                                return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                            }
                            return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                        }
                        return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                    }
                    return redirect()->route('transferencias_en_espera')->with('error','¡Intente de nuevo!');  
                }
                return redirect()->route('transferencias_en_espera')->with('error','¡Intente de nuevo!');
            }
            else//Permite generar una transferencia sin un documento de referencia, carga todos los productos disponibles en CD 
            {
                $validator = Validator::make($request->all(),[
                    'saleDe'=>'required|numeric',
                    'saleBo'=>'required|numeric',
                    'entraSu'=>'required|numeric',
                    'entraBo'=>'required|numeric',
                    //'cod_serie'=>'required',
                ]);
                if ($validator->fails()) {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                else 
                {
                    $tran = new TransferenciaSucursal();
                    $tran->empresa               = Auth::user()->empresa;
                    $tran->cod_tipo_movi         = 'E';
                    $tran->cod_serie_movi        = 'TZ';
                    $tran->cod_unidad            = $request->saleDe;
                    $tran->cod_bodega            = $request->saleBo;  
                    $tran->fecha                 = Carbon::now();
                    $tran->fecha_opera           = Carbon::now();
                    $tran->impreso               = 'N';
                    //$tran->cod_motivo            = 95;
                    $tran->Clasif_transferencia  = 1;
                    $tran->unidad_transf         = $request->entraSu;
                    $tran->bodega_Transf         = $request->entraBo;
                    $tran->usuario               = Auth::user()->name;
                    $tran->fecha_modificacion    = Carbon::now();
                    //$tran->observacion         = $request->observacion;
                    $tran->DESCRIPCION           = $request->descripcion;
                    //$tran->placa_vehiculo      = $request->placa;
                    //$tran->comentario          = $request->comentario;
                    //$tran->referencia          = $request->referencia;
                    $tran->id_estado             = 13;
                    $tran->created_at            = Carbon::now();
                    $tran->updated_at            = Carbon::now();
                    $tran->fecha_paraCarga       = Carbon::now();
                    $tran->fechaEntrega          = Carbon::now();
                    //$tran->serieFactura          = 0;
                    if($tran->save())
                    {
                        $historial              = new Historial();
                        $historial->id_usuario  = Auth::id();
                        $historial->actividad   = 'Se genero una nueva transferencia';
                        $historial->created_at  = new Carbon();
                        $historial->updated_at  = new Carbon();
                        $historial->save();
                    }
                    $id_n = $tran->created_at;
                    $id = TransferenciaSucursal::where('created_at',$id_n)->first();
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$request->saleDe)->where('bodega',$request->saleBo)->whereIn('roles',[3,4])->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $estado = 'Creada';
                        $numero = $id->id;
                        $correo = Auth::user()->email;
                        Mail::to($user)->send(new tranSucursales($usuario,$fecha));
                        if(Mail::failures())
                        {
                            return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                        }
                        return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                    }
                    return redirect()->route('editar_trans_sucursal',['id'=>$id->id])->with('success','¡Se ha creado una nueva transferencia!');
                }
                return redirect()->route('transferencias_en_espera')->with('error','¡Intente de nuevo!');
            }
            return redirect()->route('transferencias_en_espera')->with('error','¡Intente de nuevo!');
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    public function buscar_producto_manual(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $tags = DB::table('productos_inve')->where('nombre_fiscal','like','%'. $term .'%')->where('empresa',Auth::user()->empresa)
            ->orwhere('nombre_corto','like','%'. $term .'%')->where('empresa',Auth::user()->empresa)->limit(50)
            ->orderby('nombre_corto','asc')->get();
            $formatted_tags = [];
            foreach ($tags as $tag) {
                $formatted_tags[] = ['id' => $tag->cod_producto, 'text' => utf8_encode($tag->nombre_corto).' '.$tag->nombre_fiscal];
            }
            return \Response::json($formatted_tags);
        }
        return back()->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función para notificar que una transferencia se encuentra pendiente de autorizar ------------------------------------------------
    function notificar_transferencia_atrasada($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            $trans = TransferenciaSucursal::find($id);
            if($trans->fecha_enCola == null && $trans->fecha_enCarga == null)
            {
                $user = User::where('sucursal','=',$trans->cod_unidad)->where('bodega',$trans->cod_bodega)->whereIn('roles',[3,4])->pluck('email');
                $usuario = Auth::user()->name;
                $fecha = Carbon::now();
                Mail::to($user)->send(new tranSucursales($usuario,$fecha));
                $trans->fecha_enCola = Carbon::now();
                $trans->save();
                if(Mail::failures())
                {
                    return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
                } 
                return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
            }
            elseif($trans->fecha_enCola != null && $trans->fecha_enCarga == null)
            {
                $tr = TransferenciaSucursal::find($id);
                if(Carbon::parse($tr->fecha_enCola)->addMinutes(20) < Carbon::now())
                {
                    $user = User::where('sucursal','=',$trans->cod_unidad)->where('bodega',$trans->cod_bodega)->whereIn('roles',[3,4])->pluck('email');
                    $usuario = Auth::user()->name;
                    $fecha = Carbon::now();
                    Mail::to($user)->send(new tranSucursales($usuario,$fecha));
                    $tr->fecha_enCarga = Carbon::now();
                    $tr->save();
                    if(Mail::failures())
                    {
                        return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
                    }
                    return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
                }
                else 
                {
                    $user = User::where('sucursal','=',$trans->cod_unidad)->where('bodega',$trans->cod_bodega)->whereIn('roles',[3,4])->pluck('email');
                    $usuario = Auth::user()->name;
                    $fecha = Carbon::now();
                    Mail::to($user)->send(new tranSucursales($usuario,$fecha));
                    if(Mail::failures())
                    {
                        return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
                    } 
                    return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
                } 
                return redirect()->route('editar_trans_sucursal',$id)->with('success','¡Notificación de atraso enviada!');
            }
            else
            {
                $finalizar = TransferenciaSucursal::find($id);
                $existe = MoviInve::where('num_movi',$id)->where('cod_serie_movi',$finalizar->cod_serie_movi)->where('empresa',Auth::user()->empresa)->count();
                //$estado = "Despachado en camino";
                if($finalizar->id_estado >= 18 && $existe == 0)
                {
                    return back()->with('error','¡Transferencia finalizada, no es posible realizar cambios!');
                }
                elseif($finalizar->id_estado < 18 && $existe == 0)
                {
                    $tran = TransferenciaSucursal::find($id);
                    $movi = new MoviInve();
                    $movi->empresa = $tran->empresa;
                    $movi->cod_tipo_movi = $tran->cod_tipo_movi;
                    $movi->cod_serie_movi = $tran->cod_serie_movi;
                    $movi->num_movi = $tran->num_movi;
                    $movi->cod_unidad = $tran->cod_unidad;
                    $movi->cod_bodega = $tran->cod_bodega;
                    $movi->fecha = Carbon::now();
                    $movi->fecha_opera = $tran->fecha_opera;
                    $movi->impreso = 'N';
                    $movi->observacion = $tran->observacion;
                    $movi->cod_motivo = 5;
                    $movi->Clasif_transferencia = 1;
                    $movi->unidad_transf = $tran->unidad_transf;
                    $movi->bodega_Transf = $tran->bodega_Transf;
                    $movi->Descripcion = $tran->DESCRIPCION;
                    $movi->usuario = 'web';
                    //$movi->placa_vehiculo = $request->placa;
                    $movi->comentario = $tran->comentario;
                    $movi->fecha_modificacion = $tran->fecha_modificacion;
                    $movi->referencia = $tran->referencia;
                    if($movi->save())
                    {
                        $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY id asc) as orden, num_movi,
                        cantidad1, cod_unidad, cod_bodega, cod_producto, unidad_transf, cod_tipo_movi,
                        cod_serie_movi, bodega_Transf, costo
                        from inventario_web_det_transferencias_sucursales
                        where num_movi = :id
                        and cantidad1 >= 1
                        and incluido = 1
                        order by id DESC',['id'=>$id]);
                        foreach($dtran as $d)
                        {
                            DB::table('det_movi_inve')->insert([
                            ['empresa' => Auth::user()->empresa,
                            'cod_tipo_movi' => $d->cod_tipo_movi,
                            'cod_serie_movi' => $d->cod_serie_movi,
                            'num_movi' => $d->num_movi,
                            'orden' => $d->orden,
                            'orden_transf' => $d->orden,
                            'clasif_Transferencia' => 1,
                            'cantidad1' => $d->cantidad1,
                            'cantidad2' => $d->cantidad1,
                            'precio' => $d->costo,
                            'cod_unidad' => $tran->cod_unidad,
                            'cod_bodega' => $tran->cod_bodega,
                            'cod_producto' => $d->cod_producto,
                            'costo_us' => 0,
                            'unidad_transf' => $tran->unidad_transf,
                            'bodega_Transf' => $tran->bodega_Transf,
                            'costoforzado' => 'N',
                            //'costo_pivot' => 0,
                            'iva_vehiculos' => 'N',
                            'exento_iva' => 'N',
                            'dimension4' => 0,
                            'valor_impuesto_NoIVA' => 0]]);
                        }
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias_sucursales 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTranSucursales::find($eli->id)->delete();
                        }
                        $finalizar->fecha_modificacion  = Carbon::now();
                        //$finalizar->DESCRIPCION         = $request->descripcion;
                        $finalizar->id_estado           = 20;
                        $finalizar->comentario          = 'Transferencia finalizada por el sistema';
                        //$finalizar->referencia          = $request->referencia;
                        $finalizar->updated_at          = Carbon::now();
                        $finalizar->cod_motivo          = 5;
                        $finalizar->num_movi            = $tran->num_movi;
                        $finalizar->fechaSucursal       = Carbon::now();
                        $finalizar->id_usuarioRecibe    = Auth::id();
                        if($finalizar->save())
                        {
                            $cerrar = MoviInve::where('num_movi',$finalizar->num_movi)->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','E')
                            ->where('cod_serie_movi',$finalizar->cod_serie_movi)->update(['cod_motivo'=>99]);
                            $historial  = new Historial();
                            $historial->id_usuario  = Auth::id();
                            $historial->actividad   = 'Confirmo de recibido producto de transferencia'. $id;
                            $historial->created_at  = new Carbon();
                            $historial->updated_at  = new Carbon();
                            $historial->save();
                        }
                        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                        if($permiso == true)
                        {
                            $user = User::where('sucursal','=',$tran->unidad_transf)->where('bodega','=',$tran->bodega_Transf)->whereIn('roles',[3,4])->pluck('email');
                            $usuario = Auth::user()->name;
                            $fecha = Carbon::now();
                            $numero = $id;
                            $correo = Auth::user()->email;
                            Mail::to($user)->send(new tranSucFinalizadas($usuario,$fecha)); 
                            if(Mail::failures())
                            {
                                return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                            }
                            return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                        }
                        return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                    }
                    return back()->with('error','Parece que algo fallo, refresca la página...');
                }
                return back()->with('error','Parece que algo fallo, refresca la página...');
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso realizar esta acción!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para eliminar / editar los productos dentro de una transferencia --------------------------------------------------------
    function editar_transferencias_sucursales($id)
    {
        $existe = TransferenciaSucursal::find($id);
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        $permiso2 = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first();
        if($existe == true && $permiso == true)
        {
            if($existe->id_estado < 18 && $existe->unidad_transf == Auth::user()->sucursal || $permiso2 == true)
            {
                $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, tv.descripcion as placa_vehiculo, iwe.nombre as estado,
                iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga, b.nombre as bodega,
                iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
                tv.nombre as propietario, iwet.serieFactura, iwet.cod_unidad, iwet.cod_bodega, iwet.usuarioSupervisa,
                iwet.placa_vehiculo as cod_placa, iwet.unidad_transf, iwet.cod_serie_movi, iwet.id_usuarioRecibe, 
                uni.nombre as sale, bod.nombre as bsale, iwet.serieFactura, iwet.numeroFactura, iwet.bodega_Transf
                from inventario_web_encabezado_transferencias_sucursales as iwet 
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on iwet.bodega_Transf = b.cod_bodega
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                left join T_Flotas as tv on iwet.placa_vehiculo = tv.Codigo
                join unidades as uni on iwet.cod_unidad = uni.cod_unidad
                join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
                where iwet.id = :id
                and u.empresa = iwet.empresa
                and u.empresa = b.empresa
                and u.cod_unidad = b.cod_unidad
                and uni.empresa = iwet.empresa 
                and uni.empresa = bod.empresa 
                and uni.cod_unidad = bod.cod_unidad',['id'=>$id]);
                foreach($tran as $t)
                {
                    $per = $t->id_estado;
                    $unidad = $t->unidad_transf;
                    $unidad2 = $t->cod_unidad;
                }
                $comprobar = DB::select('select count(id) as transferencias
                from inventario_web_encabezado_transferencias_sucursales
                where id_estado < 18');
                /*$estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado + 1) as max,
                (iwet.id_estado - 1) as min
                from inventario_web_encabezado_transferencias_sucursales as iwet
                right join inventario_web_estados as iwe on iwe.id >= min
                where iwet.id = :id
                and iwe.id >= 13
                and iwe.id BETWEEN min and max
                and iwe.id != :per
                and iwe.id < 18',['id'=>$id,'per'=>$per]);*/
                $estados = DB::select('select *
                from inventario_web_estados 
                where id = 13
                or id = 20');
                if($per < 18)
                {
                    $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first();
                    if($permiso == true)
                    {
                        $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad','!=',15)
                            ->orderBy('nombre','asc')->get();
                        $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad','!=',15)->get();
                        return view('TranSucursales.EditarTransferencia',compact('tran','id','estados','saleDe','entraSu'));
                    }
                    else 
                    {
                        if($unidad == Auth::user()->sucursal)
                        {
                            $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad','!=',15)
                            ->orderBy('nombre','asc')->get();
                            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$unidad)->get();
                            return view('TranSucursales.EditarTransferencia',compact('tran','id','estados','saleDe','entraSu'));
                        }
                        elseif($unidad2 == Auth::user()->sucursal)
                        {
                            $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$unidad2)
                            ->orderBy('nombre','asc')->get();
                            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$unidad)->get();
                            return view('TranSucursales.EditarTransferencia',compact('tran','id','estados','saleDe','entraSu'));
                        }
                        return back()->with('error','No puedes modificar transferencias de otras sucursales'); 
                    }
                }
                return redirect()->route('transferencias_en_espera')->with('error','¡No es posible realizar modificaciones!');
            }
            elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first() == true && $existe->id_estado < 18 && $existe->unidad_transf == Auth::user()->sucursal)
            {
                return redirect()->route('VeTranSuc',$id)->with('error','¡Pendiente de autorizar!');
            }
            elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first() == true && $existe->id_estado == 20)
            {
                return redirect()->route('VeTranSuc');
            }
            return back()->with('error','No tienes permisos para accesar');
        }
        return back()->with('error','No existe la transferencia que buscas en el sistema');
    }

    function editar_encabezado_transferencia(Request $request,$id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        $permiso2 = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first();
        $tran = TransferenciaSucursal::findOrFail($id);
        if($permiso == true)
        {
            if($tran->cod_unidad == Auth::user()->sucursal || $tran->unidad_transf == Auth::user()->sucursal || $permiso2 == true)
            {
                $productos = DetTranSucursales::where('num_movi',$id)->where('incluido',1)->count();
                if($tran->id_estado >= 18)
                {
                    return back()->with('error','¡No es posible realizar modificaciones!');
                }
                elseif($productos == '' && $request->estado == 20)
                {
                    return back()->with('error','¡No se puede cambiar el estado de una transferencia vacia!');
                }
                else
                {
                    if($request->estado == 20)
                    {
                        $finalizar = TransferenciaSucursal::find($id);
                        $existe = MoviInve::where('num_movi',$id)->where('cod_serie_movi',$finalizar->cod_serie_movi)->where('empresa',Auth::user()->empresa)->count();
                        //$estado = "Despachado en camino";
                        if($finalizar->id_estado >= 18 && $existe == 0)
                        {
                            return back()->with('error','¡Transferencia finalizada, no es posible realizar cambios!');
                        }
                        elseif($finalizar->id_estado < 18 && $existe == 0)
                        {
                            $tran = TransferenciaSucursal::find($id);
                            $movi = new MoviInve();
                            $movi->empresa = $tran->empresa;
                            $movi->cod_tipo_movi = $tran->cod_tipo_movi;
                            $movi->cod_serie_movi = $tran->cod_serie_movi;
                            $movi->num_movi = $tran->num_movi;
                            $movi->cod_unidad = $tran->cod_unidad;
                            $movi->cod_bodega = $tran->cod_bodega;
                            $movi->fecha = Carbon::now();
                            $movi->fecha_opera = $tran->fecha_opera;
                            $movi->impreso = 'N';
                            $movi->observacion = $tran->observacion;
                            $movi->cod_motivo = 5;
                            $movi->Clasif_transferencia = 1;
                            $movi->unidad_transf = $tran->unidad_transf;
                            $movi->bodega_Transf = $tran->bodega_Transf;
                            $movi->Descripcion = $tran->DESCRIPCION;
                            $movi->usuario = 'web';
                            $movi->placa_vehiculo = $request->placa;
                            $movi->comentario = $tran->comentario;
                            $movi->fecha_modificacion = $tran->fecha_modificacion;
                            $movi->referencia = $tran->referencia;
                            if($movi->save())
                            {
                                $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY id asc) as orden, num_movi,
                                cantidad1, cod_unidad, cod_bodega, cod_producto, unidad_transf, cod_tipo_movi,
                                cod_serie_movi, bodega_Transf, costo
                                from inventario_web_det_transferencias_sucursales
                                where num_movi = :id
                                and cantidad1 >= 1
                                and incluido = 1
                                order by id DESC',['id'=>$id]);
                                foreach($dtran as $d)
                                {
                                    DB::table('det_movi_inve')->insert([
                                    ['empresa' => Auth::user()->empresa,
                                    'cod_tipo_movi' => $d->cod_tipo_movi,
                                    'cod_serie_movi' => $d->cod_serie_movi,
                                    'num_movi' => $d->num_movi,
                                    'orden' => $d->orden,
                                    'orden_transf' => $d->orden,
                                    'clasif_Transferencia' => 1,
                                    'cantidad1' => $d->cantidad1,
                                    'cantidad2' => $d->cantidad1,
                                    'precio' => $d->costo,
                                    'cod_unidad' => $tran->cod_unidad,
                                    'cod_bodega' => $tran->cod_bodega,
                                    'cod_producto' => $d->cod_producto,
                                    'costo_us' => 0,
                                    'unidad_transf' => $tran->unidad_transf,
                                    'bodega_Transf' => $tran->bodega_Transf,
                                    'costoforzado' => 'N',
                                    //'costo_pivot' => 0,
                                    'iva_vehiculos' => 'N',
                                    'exento_iva' => 'N',
                                    'dimension4' => 0,
                                    'valor_impuesto_NoIVA' => 0]]);
                                }
                                $eliminar = DB::select('select * 
                                from inventario_web_det_transferencias_sucursales 
                                where incluido is null
                                and num_movi = :id',['id'=>$id]);
                                foreach($eliminar as $eli)
                                {
                                    $del = DetTranSucursales::find($eli->id)->delete();
                                }
                                $finalizar->fecha_modificacion  = Carbon::now();
                                //$finalizar->DESCRIPCION         = $request->descripcion;
                                $finalizar->id_estado           = $request->estado;
                                $finalizar->comentario          = $request->comentario;
                                //$finalizar->referencia          = $request->referencia;
                                $finalizar->id_estado           = $request->estado;
                                $finalizar->updated_at          = Carbon::now();
                                $finalizar->cod_motivo          = 5;
                                $finalizar->num_movi            = $tran->num_movi;
                                $finalizar->fechaSucursal       = Carbon::now();
                                $finalizar->id_usuarioRecibe    = Auth::id();
                                if($finalizar->save())
                                {
                                    $cerrar = MoviInve::where('num_movi',$finalizar->num_movi)->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','E')
                                    ->where('cod_serie_movi',$finalizar->cod_serie_movi)->update(['cod_motivo'=>99]);
                                    $historial  = new Historial();
                                    $historial->id_usuario  = Auth::id();
                                    $historial->actividad   = 'Confirmo de recibido producto de transferencia'. $id;
                                    $historial->created_at  = new Carbon();
                                    $historial->updated_at  = new Carbon();
                                    $historial->save();
                                }
                                $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                                if($permiso == true)
                                {
                                    $user = User::where('sucursal','=',$tran->unidad_transf)->where('bodega','=',$tran->bodega_Transf)->whereIn('roles',[3,4])->pluck('email');
                                    $usuario = Auth::user()->name;
                                    $fecha = Carbon::now();
                                    $numero = $id;
                                    $correo = Auth::user()->email;
                                    Mail::to($user)->send(new tranSucFinalizadas($usuario,$fecha)); 
                                    if(Mail::failures())
                                    {
                                        return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                                    }
                                    return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                                }
                                return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                            }
                        }
                        elseif($finalizar->id_estado < 18 && $existe > 0)
                        {
                            $pro = DB::table('det_movi_inve')->where('num_movi',$finalizar->num_movi)->where('cod_serie_movi',$finalizar->cod_serie_movi)
                            ->where('empresa',Auth::user()->empresa)->count();
                            if($pro > 0)
                            {
                                $finalizar->id_estado           = $request->estado;
                                $finalizar->save();
                                return back()->with('error','¡Transferencia finalizada, no se permiten modificaciones!');
                            }
                            elseif($pro == 0) 
                            {
                                $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY id asc) as orden, num_movi,
                                cantidad1, cod_unidad, cod_bodega, cod_producto, unidad_transf, cod_tipo_movi,
                                cod_serie_movi, bodega_Transf, costo
                                from inventario_web_det_transferencias_sucursales
                                where num_movi = :id
                                and cantidad1 >= 1
                                and incluido = 1
                                order by id DESC',['id'=>$id]);
                                foreach($dtran as $d)
                                {
                                    DB::table('det_movi_inve')->insert([
                                    ['empresa' => Auth::user()->empresa,
                                    'cod_tipo_movi' => $d->cod_tipo_movi,
                                    'cod_serie_movi' => $d->cod_serie_movi,
                                    'num_movi' => $tran->num_movi,
                                    'orden' => $d->orden,
                                    'orden_transf' => $d->orden,
                                    'clasif_Transferencia' => 1,
                                    'cantidad1' => $d->cantidad1,
                                    'cantidad2' => $d->cantidad1,
                                    'precio' => $d->costo,
                                    'cod_unidad' => $tran->cod_unidad,
                                    'cod_bodega' => $tran->cod_bodega,
                                    'cod_producto' => $d->cod_producto,
                                    'costo_us' => 0,
                                    'unidad_transf' => $tran->unidad_transf,
                                    'bodega_Transf' => $tran->bodega_Transf,
                                    'costoforzado' => 'N',
                                    //'costo_pivot' => 0,
                                    'iva_vehiculos' => 'N',
                                    'exento_iva' => 'N',
                                    'dimension4' => 0,
                                    'valor_impuesto_NoIVA' => 0]]);
                                }
                                $finalizar->fecha_modificacion  = Carbon::now();
                                //$finalizar->DESCRIPCION         = $request->descripcion;
                                $finalizar->id_estado           = $request->estado;
                                $finalizar->comentario         = $request->comentario;
                                //$finalizar->referencia          = $request->referencia;
                               // $finalizar->id_estado           = $request->estado;
                                $finalizar->updated_at          = Carbon::now();
                                $finalizar->cod_motivo          = 99;
                                $finalizar->fechaSucursal       = Carbon::now();
                                $eliminar = DB::select('select * 
                                from inventario_web_det_transferencias_sucursales 
                                where incluido is null
                                and num_movi = :id',['id'=>$id]);
                                foreach($eliminar as $eli)
                                {
                                    $del = DetTranSucursales::find($eli->id)->delete();
                                }
                                if($finalizar->save())
                                {
                                    $cerrar = MoviInve::where('num_movi',$finalizar->num_movi)->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','E')
                                    ->where('cod_serie_movi',$finalizar->cod_serie_movi)->update(['cod_motivo'=>99,'num_movi'=>$finalizar->num_movi]);
                                    $historial  = new Historial();
                                    $historial->id_usuario  = Auth::id();
                                    $historial->actividad   = 'Confirmo de recibido producto de transferencia'. $id;
                                    $historial->created_at  = new Carbon();
                                    $historial->updated_at  = new Carbon();
                                    $historial->save();
                                }
                                if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                                {
                                    $user = User::where('sucursal','=',$tran->unidad_transf)->where('bodega','=',$tran->bodega_Transf)->whereIn('roles',[3,4])->pluck('email');
                                    $usuario = Auth::user()->name;
                                    $fecha = Carbon::now();
                                    $numero = $id;
                                    $correo = Auth::user()->email;
                                    Mail::to($user)->send(new tranSucFinalizadas($usuario,$fecha)); 
                                    if(Mail::failures())
                                    {
                                        return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                                    }
                                    return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                                }
                                return redirect()->route('tran_final_suc')->with('success','Transferencia realizada correctamente');
                            }
                            return back()->with('error','Error inesperado, intente de nuevo');
                        }
                        return back()->with('error','Al parecer algo salio mal, intente de nuevo');
                    }
                    elseif($request->estado == 13)
                    {
                        $tran->fecha_modificacion = Carbon::now();
                        $tran->DESCRIPCION       = $request->descripcion;
                        $tran->id_estado         = $request->estado;
                        $tran->observacion        = $request->observacion;
                        //$tran->referencia        = $request->referencia;
                        $tran->id_estado         = $request->estado;
                        $tran->updated_at        = Carbon::now();
                        $tran->cod_unidad = $request->saleDe;
                        $tran->cod_bodega = $request->saleBo;
                        $tran->unidad_transf = $request->entraSu;
                        $tran->bodega_Transf = $request->entraBo;
                        $tran->serieFactura = $request->serie;
                        $tran->numeroFactura = $request->numero;
                        $tran->save();
                        $productos = DB::table('inventario_web_det_transferencias_sucursales')->where('num_movi',$id)->where('empresa',Auth::user()->empresa)
                        ->update(['cod_unidad'=>$request->saleDe,'cod_bodega'=>$request->saleBo,'unidad_transf'=>$request->entraSu,
                        'bodega_Transf'=>$request->entraBo]);
                        $est = DB::select('select nombre
                        from inventario_web_estados
                        where id = :estado',['estado'=>$request->estado]);
                        foreach($est as $e)
                        {
                            $estad = $e->nombre;
                        }
                        return back()->with('success','¡Transferencia modificada con exito!');
                    }
                    return back()->with('error','Transferencia no autorizada, no se permite finalizar');
                }
            }
            return back()->with('error','No tienes permisos para accesar');
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function agregar_producto_manual_sucursal(Request $request,$id)
    {
        if($request->producto == "")
        {
            return back()->with('error','Debe ingresar el código del producto que quiere agregar');
        }
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        if($permiso == true)
        {
            foreach($request->producto as $pro)
            {
                $producto = $pro;
            }
            $permitir = DetTranSucursales::where('num_movi',$id)->where('cod_producto',$producto)->count('id');
            $encabezado = TransferenciaSucursal::find($id);
            if($permitir == 0 && Auth::user()->sucursal == $encabezado->cod_unidad || $permitir == 0 && Auth::user()->sucursal == $encabezado->unidad_transf ||
                $permiso == true)
            {
                $agregar                        = new DetTranSucursales();
                $agregar->empresa               = $encabezado->empresa;
                $agregar->cod_tipo_movi         = $encabezado->cod_tipo_movi;
                $agregar->cod_serie_movi        = $encabezado->cod_serie_movi;
                $agregar->num_movi              = $id;
                $agregar->cod_producto          = $producto;
                $agregar->cod_unidad            = $encabezado->cod_unidad;
                $agregar->cod_bodega            = $encabezado->cod_bodega;
                $agregar->operado_stamp         = Carbon::now();
                $agregar->clasif_Transferencia  = 1;
                $agregar->unidad_transf         = $encabezado->unidad_transf;
                $agregar->bodega_Transf         = $encabezado->bodega_Transf;
                $agregar->created_at            = Carbon::now();
                $agregar->updated_at            = Carbon::now();
                $agregar->incluido              = 1;
                $agregar->cantidadSolicitada    = 0;
                $agregar->id_inserto            = Auth::id();
                $agregar->save();
                return back()->with('success','Producto agregado con exito');
            }
            elseif($permitir == 1 && Auth::user()->sucursal == $encabezado->unidad_transf || $permitir == 1 && $permitir == 0 
            && Auth::user()->sucursal == $encabezado->cod_unidad  || $permiso == true)
            {
                $agregar = DetTranSucursales::where('num_movi',$id)->where('cod_producto',$producto)->update(['incluido'=>1]);
                return back()->with('success','Producto agregado con exito');
            }
            return back()->with('error','El producto ya existe en la transferencia o está intentando una acción no permitida');
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function productos_en_transferencia($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        if($permiso == true )
        {
            $productos = DB::select('select iwdt.id, iwc.nombre, pi.nombre_corto, pi.nombre_fiscal, iwdt.cod_producto, convert(integer,iwdt.costo),
            convert(integer,iwdt.cantidad1) as cantidad, 
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen   
            from inventario_web_det_transferencias_sucursales as iwdt
            left join (select  iwdt.cod_producto, sum(iwdt.cantidad1) reserva
            from inventario_web_det_transferencias_sucursales as iwdt, 
            inventario_web_encabezado_transferencias_sucursales as iwet
            where iwet.num_movi = iwdt.num_movi
            and iwet.id_estado between 14 and 17
            and iwdt.incluido = 1
            group by iwdt.cod_producto) as nv on iwdt.cod_producto = nv.cod_producto
            join productos_inve as pi on iwdt.cod_producto = pi.cod_producto
            join inventario_web_categorias as iwc on pi.cod_tipo_prod = iwc.cod_tipo_prod
            where iwdt.num_movi = :id
            and iwdt.incluido = 1
            and iwdt.empresa = pi.empresa 
            and iwdt.empresa = iwc.empresa
            order by iwdt.id asc',['id'=>$id]);
            return $productos;
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    public function guardar_producto_transferencia(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        if($permiso == true)
        {
            $verificado = DetTranSucursales::findOrFail($request->id);
            if(Auth::user()->sucursal == $verificado->unidad_transf && $verificado->id_estado < 18 || $permiso == true)
            {
                if($verificado->orden == null)
                {
                    $precio = DB::table('productos_inve')->where('empresa',Auth::user()->empresa)
                    ->where('cod_producto',$verificado->cod_producto)->first();
                    $verificado->cantidad1 = $request->cantidad;
                    $verificado->costo = ($precio->precio * $request->cantidad); 
                    $verificado->updated_at         = new Carbon();
                    $verificado->save();
                    return $verificado;
                }
                else 
                {
                    if($verificado->costo == 0)
                    {
                        $precio = DB::table('productos_inve')->where('empresa',Auth::user()->empresa)
                        ->where('cod_producto',$verificado->cod_producto)->first();
                        $verificado->cantidad1 = $request->cantidad;
                        $verificado->costo = ($precio->precio * $request->cantidad); 
                        $verificado->updated_at         = new Carbon();
                        $verificado->save();
                        return $verificado;
                    }
                    $precio = ($verificado->costo / $verificado->cantidad1);
                    $verificado->cantidad1 = $request->cantidad;
                    $verificado->costo = ($precio * $request->cantidad); 
                    $verificado->updated_at         = new Carbon();
                    $verificado->save();
                    return $verificado;
                }
            }
            elseif(Auth::user()->sucursal == $verificado->unidad_transf && $verificado->id_estado < 18 || $permiso == true)
            {
                $verificado->cantidadSugerida    = $request->cantidad; 
                $verificado->updated_at         = new Carbon();
                $verificado->save();
                return $verificado;
            }
            return error;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function eliminar_producto_transferencia(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        $task = $nuevo = DetTranSucursales::findOrFail($request->id);
        if($permiso == true && $task->id_inserto == Auth::id())
        {
            $nuevo->updated_at = new Carbon();
            $nuevo->incluido = null;
            $nuevo->save();
            return $task;
        }
        return 'Sin permiso';
    }

    public function detalle_producto_transferencia(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        if($permiso == true)
        {
            $producto = DB::select('select iwdt.id, iwc.nombre, pi.nombre_corto, pi.nombre_fiscal, iwdt.cod_producto, convert(integer,iwdt.costo), 
            convert(integer,iwdt.cantidadSolicitada) as cantidad, (convert(integer,iv.existencia1) - convert(integer,coalesce(nv.reserva,0))) as existencia,
            convert(integer,ivs.existencia1) as sucursal, convert(integer,ivs.minimo), convert(integer,ivs.piso_sugerido), 
            convert(integer,ivs.maximo), ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
            //iwdt.cantidadSugerida   
            from inventario_web_det_transferencias_sucursales as iwdt
            left join inventarios as iv on iwdt.cod_producto = iv.cod_producto
            left join (select  iwdt.cod_producto, sum(iwdt.cantidadSolicitada) reserva
            from inventario_web_det_transferencias_sucursales as iwdt, 
            inventario_web_encabezado_transferencias_sucursales as iwet
            where iwet.num_movi = iwdt.num_movi
            and iwet.id_estado between 14 and 17
            and iwdt.incluido = 1
            group by iwdt.cod_producto) as nv on iwdt.cod_producto = nv.cod_producto
            left join inventarios as ivs on iwdt.cod_producto = ivs.cod_producto 
            join productos_inve as pi on iwdt.cod_producto = pi.cod_producto
            join inventario_web_categorias as iwc on pi.cod_tipo_prod = iwc.cod_tipo_prod
            where iwdt.id = :id
            and iwdt.incluido = 1
            and iwdt.cod_unidad = iv.cod_unidad
            and iwdt.cod_bodega = iv.cod_bodega
            and iwdt.empresa = iv.empresa
            and iwdt.unidad_transf = ivs.cod_unidad
            and iwdt.bodega_Transf = ivs.cod_bodega
            and iwdt.empresa = ivs.empresa
            and iwdt.empresa = pi.empresa 
            and iwdt.empresa = iwc.empresa',['id'=>$request->id]);
            $detalle = '';
            foreach($producto as $pro)
            {
                $detalle = ['cantidad'=>$pro->cantidad,'nombre_corto'=>$pro->nombre_corto,'nombre_fiscal'=>$pro->nombre_fiscal,
                'existencia'=>$pro->existencia,'sucursal'=>$pro->sucursal, 'bultos'=>$pro->costo,'min'=>$pro->minimo,
                'reo'=>$pro->piso_sugerido,'max'=>$pro->maximo/*,'cantidadSu'=>$pro->cantidadSugerida*/];
            }
            return $detalle;
        }
        return back()->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver los detalles de una transferencia ------------------------------------------------------------------------------
    function ver_transferencia($id)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Reviso detalles de la transferencia'. $id;
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        $existe = TransferenciaSucursal::find($id);
        if($permiso == true && $existe == true || $existe == true && $existe->cod_unidad == Auth::user()->sucursal || 
        $existe == true && $existe->unidad_transf == Auth::user()->sucursal)
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacionSucursal, u.nombre, placa_vehiculo, iwe.nombre as estado,
            iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fechaEntrega, iwet.fechaUno,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, b.nombre as bodega,
             iwet.fechaSalida, iwet.usuarioSupervisa, iwet.fecha_paraCarga, iwet.observacion, us.name,
            iwet.fechaSucursal, iwet.fecha_entregado, iwet.observacionSup, iwet.observacionSucursal, iwet.cod_unidad,
            tv.propietario, iwet.erroresVerificados, iwet.observacionRevision, iwet.porcentaje, iwet.erroresVerificados,
            iwet.observacionSucursal, iwet.unidad_transf, iwet.id_usuarioRecibe, iwet.id, iwet.serieFactura, iwet.numeroFactura,
            iwet.cliente, uni.nombre as sale, bod.nombre as bsale
            from inventario_web_encabezado_transferencias_sucursales as iwet 
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_transF = b.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
            left join users as us on iwet.id_usuarioRecibe = us.id
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            where iwet.id = :id
            and u.empresa = iwet.empresa
            and u.empresa = b.empresa 
            and u.cod_unidad = b.cod_unidad
            and uni.empresa = iwet.empresa
            and uni.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad',['id'=>$id]);
            foreach($tran as $t)
            {
                $ver = $t->id_estado;
            } 
            $integra = DB::select('select nombre 
            from inventario_web_bitacora_transferencias
            where num_movi = :id',['id'=>$id]);
            if($ver < 18)
            {
                $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
                iwt.incluido, iwt.num_movi, ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen, 
                floor(iwt.costo) as costo
                from inventario_web_det_transferencias_sucursales as iwt,
                productos_inve as pi, 
                inventario_web_categorias as ic,
                where iwt.num_movi = :id
                and iwt.cod_producto = pi.cod_producto
                and iwt.incluido = 1
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and pi.empresa = iwt.empresa
                and ic.empresa = iwt.empresa
                order by ic.nombre asc',['id'=>$id]);
                $estados = DB::table('inventario_web_estados')->where('id',100)->get();
                return view('TranSucursales.verTransferencia',compact('tran','productos','id','integra','historial','estados'));
            }
            elseif($ver >= 18 && $ver <= 19)
            {
                $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
                iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi, floor(iwt.costo) as costo,
                ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
                from inventario_web_det_transferencias_sucursales as iwt,
                productos_inve as pi, 
                inventario_web_categorias as ic,
                where iwt.num_movi = :id
                and iwt.cod_producto = pi.cod_producto
                and iwt.empresa = pi.empresa
                and iwt.incluido = 1
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and ic.empresa = iwt.empresa
                order by ic.nombre asc',['id'=>$id]);
                $estados = DB::select('select iwe.id, iwe.nombre
                from inventario_web_estados as iwe 
                where iwe.id > 18
                and iwe.id < 21',['id'=>$id]);
                return view('TranSucursales.verTransferencia',compact('tran','productos','id','integra','estados','historial'));
            }
            elseif($ver == 20)
            {
                $estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado - 1) as ide
                from inventario_web_encabezado_transferencias as iwet
                right join inventario_web_estados as iwe on iwe.id >= ide
                where iwet.id = :id
                and iwe.id > 18
                and iwe.id < 20',['id'=>$id]);

                $productosE = DB::select('select iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, iwt.num_movi, 
                floor(iwt.cantidad1) as cantidad1, floor(iwt.costo) as costo
                from det_movi_inve as iwt,
                productos_inve as pi, 
                inventario_web_categorias as ic,
                where iwt.num_movi = :id
                and iwt.cod_producto = pi.cod_producto
                and iwt.empresa = pi.empresa
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and ic.empresa = iwt.empresa
                and iwt.cod_serie_movi = :se
                and iwt.cod_tipo_movi = :ti
                order by ic.nombre asc',['id'=>$existe->num_movi,'se'=>$existe->cod_serie_movi,'ti'=>'E']);
                $productosI = DB::select('select iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, iwt.num_movi, 
                floor(iwt.cantidad1) as cantidad1, floor(iwt.costo) as costo
                from det_movi_inve as iwt,
                productos_inve as pi, 
                inventario_web_categorias as ic,
                where iwt.num_movi = :id
                and iwt.cod_producto = pi.cod_producto
                and iwt.empresa = pi.empresa
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and ic.empresa = iwt.empresa
                and iwt.cod_serie_movi = :se
                and iwt.cod_tipo_movi = :ti
                order by ic.nombre asc',['id'=>$existe->num_movi,'se'=>$existe->cod_serie_movi,'ti'=>'I']);
                return view('TranSucursales.verTransferenciaFinalizada',compact('tran','productosE','id','integra','estados','historial','productosI'));
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return back()->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para generar un documento en PDF de la transferencia -------------------------------------------------------------------- 
    function imprimir_pdf($id)
    {
        $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
        iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
        iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
        iwet.usuarioSupervisa, iwet.fechaSalida, iwet.fechaEntrega, iwet.fecha_entregado, iwet.fechaUno,
        tv.propietario, iwet.id, uni.nombre as sale, bod.nombre as bsale
        from inventario_web_encabezado_transferencias_sucursales as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id 
        left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega 
        where iwet.id = :id
        and u.empresa = iwet.empresa
        and uni.empresa = iwet.empresa
        and uni.empresa = bod.empresa
        and uni.cod_unidad = bod.cod_unidad',['id'=>$id]);

        $per = TransferenciaSucursal::find($id);

        if($per->id_estado >= 13 && $per->id_estado <= 17)
        {
            $productos = DB::select('select  pi.nombre_corto, pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
            from inventario_web_det_transferencias_sucursales as iwt,
            productos_inve as pi, 
            where iwt.num_movi = :id
            and iwt.cod_producto = pi.cod_producto
            and iwt.empresa = pi.empresa
            and iwt.incluido = 1
            order by pi.nombre_corto asc',['id'=>$id]);
            $usuario = Auth::user()->name;
            $pdf = PDF::loadView('TranSucursales.imprimir',compact('tran','productos','usuario'));
            return $pdf->download('transferencia.pdf');
        }
        elseif($per->id_estado >= 18 && $per->id_estado <= 19)
        {
            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
            from inventario_web_det_transferencias_sucursales as iwt,
            productos_inve as pi, 
            inventario_web_categorias as ic,
            where iwt.num_movi = :id
            and iwt.cod_producto = pi.cod_producto
            and iwt.empresa = pi.empresa
            and iwt.incluido = 1
            and iwt.cantidad1 > 0
            and pi.cod_tipo_prod = ic.cod_tipo_prod
            and ic.empresa = iwt.empresa
            order by pi.nombre_corto asc',['id'=>$id]);
            $usuario = Auth::user()->name;
            $pdf = PDF::loadView('TranSucursales.imprimirF',compact('tran','productos','usuario'));
            return $pdf->download('transferencia.pdf');
        }
        elseif($per->id_estado == 20)
        {
            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen,
            iwt.cantidadSolicitada, iwt.cantidad1
            from inventario_web_det_transferencias_sucursales as iwt,
            productos_inve as pi, 
            inventario_web_categorias as ic,
            where iwt.num_movi = :id
            and iwt.cod_producto = pi.cod_producto
            and iwt.empresa = pi.empresa
            and iwt.incluido = 1
            and pi.cod_tipo_prod = ic.cod_tipo_prod
            and ic.empresa = iwt.empresa
            and iwt.noIncluido is null
            order by ic.nombre desc',['id'=>$id]);
            $usuario = Auth::user()->name;
            $pdf = PDF::loadView('TranSucursales.imprimirF',compact('tran','productos','usuario'));
            return $pdf->download('transferencia.pdf');
        }
        return back()->with('error','No se permite imprimir transferencias vacias o con estado ¡Creada!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias pendientes de ser autorizadas ---------------------------------------------------
    function transferencias_pendientes_sucursales()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first();
        if($permiso == true)
        {
            $historial  = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Ingreso al modulo de transferencias';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first();
            if($permiso == true)
            {
                $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();
            }
            else 
            {
                $saleDe = DB::table('unidades')->where('cod_unidad',Auth::user()->sucursal)->where('empresa',Auth::user()->empresa)->get();
            }
            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();
            return view('TranSucursales.TransPendientes',compact('saleDe','entraSu'));
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_tran_pendientes_sucursales() 
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first() == true)
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, iwet.id_usuarioRecibe, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            where iwet.id_estado = 13
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = iwet.cod_bodega
            and iwet.cod_unidad = :sucursal',['sucursal'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first() == true)
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, iwet.id_usuarioRecibe, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            where iwet.id_estado = 13
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = iwet.cod_bodega
            and iwet.cod_unidad = :sucursal',['sucursal'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver las transferencias finalizadas realizadas por el mismo usuario -----------------------------------------------
    function trans_finalizadas_sucursales()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            return view('TranSucursales.finalizadas');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_tran_sucursales_fin()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, 
            CONVERT(date,iwet.fecha_paraCarga), bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, CONVERT(date,iwet.fechaSucursal,101), 
            uni.resolucion_autorizacion as sale, bod.observacion as bsale, iwet.numeroFactura, serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            where iwet.id_estado = 20
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.empresa = iwet.empresa
            and uni.empresa = bod.empresa 
            and uni.cod_unidad = bod.cod_unidad
            and iwet.unidad_transf = :sucursal',['sucursal'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, 
            CONVERT(date,iwet.fecha_paraCarga), bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, CONVERT(date,iwet.fechaSucursal,101), 
            uni.resolucion_autorizacion as sale, bod.observacion as bsale, iwet.numeroFactura, serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            where iwet.id_estado = 20
            and u.empresa = iwet.empresa
            and iwet.unidad_transf = :sucursal
            and iwet.bodega_Transf = :bodega
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.empresa = iwet.empresa
            and uni.empresa = bod.empresa 
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'bodega'=>Auth::user()->bodega]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para cargar las transferencias realizadas a la bodega del usuario que las está visualizando ---------------------------
    function transferencias_a_mi_bodega()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            return view('TranSucursales.amiBodega');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_trans_a_mi_bodega()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            iwet.DESCRIPCION, bo.observacion as bodega, iwet.id, iwet.fechaSucursal, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.cod_unidad = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.cod_bodega = bo.cod_bodega
            where iwet.id_estado = 20
            and u.empresa = iwet.empresa
            and iwet.cod_unidad = :sucursal
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad',['sucursal'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',30)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            iwet.DESCRIPCION, bo.observacion as bodega, iwet.id, iwet.fechaSucursal, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.cod_unidad = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.cod_bodega = bo.cod_bodega
            where iwet.id_estado = 20
            and u.empresa = iwet.empresa
            and iwet.cod_unidad = :sucursal
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad',['sucursal'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de sucursales para el reporte de transferencias entre sucursales ----------------------------------
    function reporte_transferencias_sucursales()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            return view('TranSucursales.reporte.listadoSucursales');
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function datos_reporte_transferencia_sucursales()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            $sucursales = user::where('roles',3)->where('empresa',Auth::user()->empresa)->get();
            return DataTables($sucursales)->make(true);
        }
        return back()->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones reporte transferencias por sucursal -----------------------------------------------------------------------------------
    function transferencias_realizadas_por_sucursal($suc,$bod)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            return view('TranSucursales.reporte.transSucursales',compact('suc','bod'));
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function datos_tran_realizadas_sucursal($suc,$bod)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, CONVERT(date,iwet.fechaSucursal), iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            where iwet.id_estado > 12 
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :suc
            and iwet.cod_bodega = :bod',['suc'=>$suc,'bod'=>$bod]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el reporte de transferencias por sucursal filtrado por fecha -------------------------------------------------
    function transferencias_realizadas_por_fecha_sucursal($suc,$bod,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin; 
            return view('TranSucursales.reporte.transSucursalesFecha',compact('suc','bod','inicio','fin'));
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function datos_tran_realizadas_sucursal_fecha($suc,$bod,$inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            bo.observacion as bodega, iwet.DESCRIPCION, iwet.id, CONVERT(date,iwet.fechaSucursal), iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            where iwet.id_estado = 20
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :suc
            and iwet.cod_bodega = :bod
            and iwet.fechaSucursal between :inicio and :fin',['suc'=>$suc,'bod'=>$bod,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el reporte de transferencias por sucursal filtrado por fecha -------------------------------------------------
    function reporte_transferencias_por_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin; 
            return view('TranSucursales.reporte.transReporteFecha',compact('inicio','fin'));
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    function datos_reporte_transferencias_por_fecha($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',32)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            bo.nombre as bodega, iwet.DESCRIPCION, iwet.id, iwet.fechaSucursal, iwet.numeroFactura, iwet.serieFactura
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            where iwet.id_estado = 20
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.fechaSucursal between :inicio and :fin',['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//--------------------------- Funciones para ver el reporte general -------------------------------------------------------------------------------------------
    function transferencias_reporte()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            return view('TranSucursales.reporte');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_trans_reporte()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            iwet.DESCRIPCION, bo.observacion as bodega, iwet.id, iwet.fechaSucursal, iwet.numeroFactura, iwet.serieFactura, iwe.id as id_estado
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.cod_unidad = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.cod_bodega = bo.cod_bodega
            where u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.created_at > :fecha',['fecha'=>Carbon::now()->subMonths(3)]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//------------------------------------------------ Fecha ------------------------------------------------------------------------------------------------------
    function transferencias_reporte_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[30,31])->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('TranSucursales.reporte_fecha', compact('inicio','fin'));
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
    function datos_trans_reporte_fecha($inicio, $fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',31)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            iwet.DESCRIPCION, bo.observacion as bodega, iwet.id, iwet.fechaSucursal, iwet.numeroFactura, iwet.serieFactura, iwe.id as id_estado
            from inventario_web_encabezado_transferencias_sucursales as iwet
            join unidades as u on iwet.cod_unidad = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.cod_bodega = bo.cod_bodega
            where u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.created_at between :inicio and :fin',['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}