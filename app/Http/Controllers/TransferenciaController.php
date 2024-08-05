<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use PDF;
use Auth;
use App\User;
use Image;
use App\Estado;
use App\DetMovi;
use App\MoviInve;
use Carbon\Carbon;
use App\Historial;
use App\GrupoUsuario;
use App\Mail\Sucursal;
use App\ProductoSemana;
use App\Transferencias;
use App\HETransferencia;
use App\HDTransferencia;
use App\DetTransferencias;
use App\Mail\Transferencia;
use App\BitacoraTransferencia;
use App\ImagenesTransferencias;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator; 

class TransferenciaController extends Controller
{
    //transferencias_usuarios_bodega

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

//--------------------------- Funciones para ver el listado de transferencias en proceso ----------------------------------------------------------------------
    public function inicio()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso al modulo de transferencias';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)
            ->where('Activa','S')->get();
            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('Activa','S')->get();
            $seriesDevoluciones = DB::select("select cod_serie_movi, nombre
            from series_movi
            where empresa = :empresa
            and para_Transferencias is null
            and cod_tipo_movi = 'C'",['empresa'=>Auth::user()->empresa]);
            return view('transferencias.verTransferencias',compact('saleDe','entraSu','seriesDevoluciones'));
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first())
        {
            $saleDe = DB::table('unidades')->where('cod_unidad',Auth::user()->sucursal)->where('empresa',Auth::user()->empresa)
            ->where('Activa','S')->get();
            $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('Activa','S')->get();
            $seriesDevoluciones = DB::select("select cod_serie_movi, nombre
            from series_movi
            where empresa = :empresa
            and para_Transferencias is null
            and cod_tipo_movi = 'C'",['empresa'=>Auth::user()->empresa]);
            return view('transferencias.verTransferencias',compact('saleDe','entraSu','seriesDevoluciones'));
        }
        elseif(Auth::user()->roles == 17)
        {
            return redirect()->route('bod_trasf_bod')->with('success','Transferencias en bodega');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias() 
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, 
            iwet.fecha_paraCarga, tv.Placa, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
            uni.resolucion_autorizacion as usale, bod.observacion as bsale, tv.propietario,
            iwet.opcionalUno as peso
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_vehiculos as tv on iwet.placa_vehiculo = tv.Placa
            where iwet.id_estado = 13
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and uni.cod_unidad = bod.cod_unidad');
            return DataTables($inventario)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, 
            iwet.fecha_paraCarga, tv.Placa, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
            uni.resolucion_autorizacion as usale, bod.observacion as bsale, tv.propietario,
            iwet.opcionalUno as peso
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_vehiculos as tv on iwet.placa_vehiculo = tv.Placa
            where iwet.id_estado = 13
            and u.empresa = iwet.empresa
            and iwet.cod_unidad = :sucursal
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and uni.cod_unidad = bod.cod_unidad
            or iwet.id_estado = 13
            and u.empresa = iwet.empresa
            and iwet.unidad_transf = :sucursal2
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    public function seleccionar_serie(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first();
        if($permiso == true)
        {
            $term = trim($request->q);

            if (empty($term)) {
                return \Response::json([]);
            }
            $tags = DB::select("select ser.nom_corto, ser.cod_unidad, uni.nombre, ser.cod_serie_movi
            from series_movi_cc as ser,
            unidades as uni 
            where ser.empresa = :empresa
            and uni.empresa = ser.empresa 
            and ser.cod_unidad = uni.cod_unidad
            and cod_tipo_movi = 'F'
            and EnUso = 'S'
            and ser.nom_corto like :nom_co",['nom_co'=>$term,'empresa'=>Auth::user()->empresa]);
            $formatted_tags = [];
            foreach ($tags as $tag) {
                $formatted_tags[] = ['id' => $tag->cod_serie_movi, 'text' => $tag->cod_serie_movi.'/'.$tag->nombre];
            }
            return \Response::json($formatted_tags);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función para cretar transferencias con factura, pedido o sin documento ----------------------------------------------------------
    public function agregar_factura(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',11)->first())
        {
            if($existe = DB::table('movi_cc')->where('num_movi',$request->numero)->where('cod_serie_movi',$request->serie)
            ->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','F')->first())
            {
                $validator = Validator::make($request->all(),[
                    'saleDe'=>'required|numeric',
                    'saleBo'=>'required|numeric',
                    'serie'=>'required',
                    'numero'=>'required|numeric',
                ]);
                if ($validator->fails()) 
                {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                $tran = new Transferencias();
                $tran->empresa               = Auth::user()->empresa;
                $tran->cod_tipo_movi         = 'E';
                $tran->cod_serie_movi        = 'IW';
                $tran->cod_unidad            = $request->saleDe;
                $tran->cod_bodega            = $request->saleBo; 
                $tran->fecha                 = Carbon::now();
                $tran->fecha_opera           = Carbon::now();
                $tran->impreso               = 'N';
                //$tran->cod_motivo            = 95;
                $tran->Clasif_transferencia  = 1;
                $tran->unidad_transf         = $existe->cod_unidad;
                $tran->bodega_Transf         = $existe->cod_bodega;
                $tran->usuario               = Auth::user()->name;
                $tran->fecha_modificacion    = Carbon::now();
                //$tran->observacion         = $request->observacion;
                $tran->observacion           = 'CLIENTE'.' '.utf8_encode($existe->nombre_cliente).' '.
                '('.$existe->cod_cliente.')'.' '.'FACT. #'.' '.$existe->num_movi.' ('.$request->serie.') ';
                //$tran->placa_vehiculo      = $request->placa;
                //$tran->comentario          = $request->comentario;
                //$tran->referencia          = $request->referencia;
                $tran->id_estado             = 13;
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
                    $id = Transferencias::where('created_at',$id_n)->first();
                    $todos_productos =DB::select('select empresa, cod_tipo_movi, cod_serie_movi, num_movi, cod_producto,
                    cod_unidad, cod_bodega, cantidad1, orden
                    from det_movi_inve
                    where num_movi = :num
                    and cod_serie_movi = :serie
                    and empresa = :empresa',['num'=>$request->numero,'serie'=>$request->serie,'empresa'=>Auth::user()->empresa]);
                    foreach ($todos_productos as $tp) 
                    {
                        $detTran                        = new DetTransferencias();
                        $detTran->empresa               = Auth::user()->empresa;
                        $detTran->cod_tipo_movi         = 'E';
                        $detTran->cod_serie_movi        = 'IW';
                        $detTran->num_movi              = $id->id;
                        $detTran->cod_producto          = $tp->cod_producto;
                        $detTran->cod_unidad            = $request->saleDe;
                        $detTran->cod_bodega            = $request->saleBo;
                        $detTran->cantidadSolicitada    = $tp->cantidad1;
                        //$detTran->costo               = 0;
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
                        if($detTran->save())
                        {
                            $historial              = new Historial();
                            $historial->id_usuario  = Auth::id();
                            $historial->actividad   = 'Se genero una nueva transferencia';
                            $historial->created_at  = new Carbon();
                            $historial->updated_at  = new Carbon();
                            $historial->save();
                        }
                    }
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$existe->cod_unidad)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $estado = 'En cola';
                        $numero = $id->$id;
                        $correo = Auth::user()->email;
                        Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo));
                        return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
                    }
                    return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
                }
                return redirect()->route('inicio_transferencias')->with('error','¡No existe la factura solicitada!');
            }
            else if($existe = DB::table('pedidos')->where('cod_serie_movi',$request->serie)->where('num_movi',$request->numero)
            ->where('empresa',Auth::user()->empresa)->first())//Permite generar una transferencia por medio de un pedido 
            {
                $validator = Validator::make($request->all(),[
                    'saleDe'=>'required|numeric',
                    'saleBo'=>'required|numeric',
                    'serie'=>'required',
                    'numero'=>'required|numeric',
                ]);
                if ($validator->fails()) {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                $tran = new Transferencias();
                $tran->empresa               = Auth::user()->empresa;
                $tran->cod_tipo_movi         = 'E';
                $tran->cod_serie_movi        = 'IW';
                $tran->cod_unidad            = $request->saleDe;
                $tran->cod_bodega            = $request->saleBo; 
                $tran->fecha                 = Carbon::now();
                $tran->fecha_opera           = Carbon::now();
                $tran->impreso               = 'N';
                //$tran->cod_motivo            = 95;
                $tran->Clasif_transferencia  = 1;
                $tran->unidad_transf         = $existe->cod_unidad;
                $tran->bodega_Transf         = $existe->cod_bodega;
                $tran->usuario               = Auth::user()->name;
                $tran->fecha_modificacion    = Carbon::now();
                //$tran->observacion         = $request->observacion;
                $tran->observacion           = 'CLIENTE'.' '.utf8_encode($existe->nombre_cliente).' '.
                '('.$existe->cod_cliente.')'.' '.'PEDIDO. #'.' '.$existe->num_movi.' ('.$request->serie.') ';
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
                    $id = Transferencias::where('created_at',$id_n)->first();
                    $todos_productos =DB::select('select empresa, cod_tipo_movi, cod_serie_movi, num_movi, cod_producto,
                    cod_unidad, cod_bodega, cantidad2, orden
                    from det_pedidos
                    where cod_serie_movi = :serie
                    and num_movi = :num
                    and empresa = :empresa',['num'=>$request->numero,'serie'=>$request->serie,'empresa'=>Auth::user()->empresa]);
                    foreach ($todos_productos as $tp) 
                    {
                        $detTran                        = new DetTransferencias();
                        $detTran->empresa               = Auth::user()->empresa;
                        $detTran->cod_tipo_movi         = 'E';
                        $detTran->cod_serie_movi        = 'IW';
                        $detTran->num_movi              = $id->id;
                        $detTran->cod_producto          = $tp->cod_producto;
                        $detTran->cod_unidad            = $request->saleDe;
                        $detTran->cod_bodega            = $request->saleBo;
                        $detTran->cantidadSolicitada    = $tp->cantidad2;
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
                        if($detTran->save())
                        {
                            $historial              = new Historial();
                            $historial->id_usuario  = Auth::id();
                            $historial->actividad   = 'Se genero una nueva transferencia';
                            $historial->created_at  = new Carbon();
                            $historial->updated_at  = new Carbon();
                            $historial->save();
                        }
                    }
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$request->entraSu)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $estado = 'En cola';
                        $numero = $id->id;
                        $correo = Auth::user()->email;
                        Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo));
                        return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
                    }
                    return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
                }
                return redirect()->route('inicio_transferencias')->with('error','¡No existe el pedido ingresado!');
            }
            else if($existe = DB::table('movi_cc')->where('cod_serie_movi',$request->serie)->where('num_movi',$request->numero)
            ->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','C')->first())//Permite generar una transferencia por medio de un pedido 
            {
                $validator = Validator::make($request->all(),[
                    'entraSu'=>'required|numeric',
                    'entraBo'=>'required|numeric',
                    'serie'=>'required',
                    'numero'=>'required|numeric',
                ]);
                if ($validator->fails()) {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                $tran = new Transferencias();
                $tran->empresa               = Auth::user()->empresa;
                $tran->cod_tipo_movi         = 'E';
                $tran->cod_serie_movi        = 'IW';
                $tran->cod_unidad            = $existe->cod_unidad;
                $tran->cod_bodega            = $existe->cod_bodega; 
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
                $tran->observacion           = 'CLIENTE'.' '.utf8_encode($existe->nombre_cliente).' '.
                '('.$existe->cod_cliente.')'.' '.'DEV. #'.' '.$existe->num_movi.' ('.$request->serie.') ';
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
                    $id = Transferencias::where('created_at',$id_n)->first();
                    $todos_productos =DB::select('select empresa, cod_tipo_movi, cod_serie_movi, num_movi, cod_producto,
                    cod_unidad, cod_bodega, cantidad2, orden
                    from det_movi_inve
                    where cod_serie_movi = :serie
                    and num_movi = :num
                    and empresa = :empresa',['num'=>$request->numero,'serie'=>$request->serie,'empresa'=>Auth::user()->empresa]);
                    foreach ($todos_productos as $tp) 
                    {
                        $detTran                        = new DetTransferencias();
                        $detTran->empresa               = Auth::user()->empresa;
                        $detTran->cod_tipo_movi         = 'E';
                        $detTran->cod_serie_movi        = 'IW';
                        $detTran->num_movi              = $id->id;
                        $detTran->cod_producto          = $tp->cod_producto;
                        $detTran->cod_unidad            = $request->saleDe;
                        $detTran->cod_bodega            = $request->saleBo;
                        $detTran->cantidadSolicitada    = $tp->cantidad2;
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
                        if($detTran->save())
                        {
                            $historial              = new Historial();
                            $historial->id_usuario  = Auth::id();
                            $historial->actividad   = 'Se genero una nueva transferencia';
                            $historial->created_at  = new Carbon();
                            $historial->updated_at  = new Carbon();
                            $historial->save();
                        }
                    }
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$request->entraSu)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $estado = 'En cola';
                        $numero = $id->id;
                        $correo = Auth::user()->email;
                        Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo));
                        return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
                    }
                    return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
                }
                return redirect()->route('inicio_transferencias')->with('error','¡No existe el pedido ingresado!');
            }
            //Permite generar una transferencia sin un documento de referencia, carga todos los productos disponibles en CD 
            $validator = Validator::make($request->all(),[
                'saleDe'=>'required|numeric',
                'saleBo'=>'required|numeric',
                'entraSu'=>'required|numeric',
                'entraBo'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return back()
                ->withErrors($validator)
                ->withInput();
            }
            if($request->saleDe == $request->entraSu && $request->saleBo == $request->entraBo)
            {
                return back()->with('error','¡No se permite realizar transferencias que ingresen a la misma bodega de donde salen!');
            }
            $tran = new Transferencias();
            $tran->empresa               = Auth::user()->empresa;
            $tran->cod_tipo_movi         = 'E';
            $tran->cod_serie_movi        = 'IW';
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
            $tran->observacion           = 'en cola';
            //$tran->placa_vehiculo      = $request->placa;
            //$tran->comentario          = $request->comentario;
            //$tran->referencia          = $request->referencia;
            $tran->id_estado             = 13;
            $tran->created_at            = Carbon::now();
            $tran->updated_at            = Carbon::now();
            $tran->fecha_paraCarga       = Carbon::now();
            $tran->fechaEntrega          = Carbon::now();
            $tran->serieFactura          = 0;
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
            $id = Transferencias::where('created_at',$id_n)->first();
            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
            {
                $user = User::where('sucursal','=',$request->entraSu)->where('roles',3)->pluck('email');
                $usuario = Auth::user()->name;
                $fecha = Carbon::now();
                $estado = 'En cola';
                $numero = $id->id;
                $correo = Auth::user()->email;
                Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo));
                return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
            }
            return redirect()->route('editar_cantidades',['id'=>$id->id])->with('success','¡Se ha creado una nueva orden de carga!');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function productos_transferencia($id)
    {
        $productos = DB::select("select iwt.id, iwt.cod_producto, replace(pi.nombre_corto,'ñ','N') as nombre_corto, ic.nombre,  
        pi.nombre_fiscal, floor(iwt.cantidadRecibida) as cantidad, 
        iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,  iwt.noIncluido,
        floor(iwt.cantidad1) as cantidad1, iwt.cantidadSolicitada, mal_estado
        from inventario_web_det_transferencias as iwt,
        productos_inve as pi, 
        inventario_web_categorias as ic,
        where iwt.num_movi = :id
        and iwt.cod_producto = pi.cod_producto
        and iwt.empresa = :empresa
        and iwt.incluido = 1
        and pi.cod_tipo_prod = ic.cod_tipo_prod
        and pi.empresa = iwt.empresa
        and ic.empresa = iwt.empresa
        order by pi.nombre_corto asc",['id'=>$id,'empresa'=>Auth::user()->empresa]);
        return $productos;
    }

    public function ver_producto(Request $request)
    {
        $producto = collect(DB::select("select idt.id, replace(pi.nombre_corto,'ñ','N') as nombre_corto, pi.nombre_fiscal, convert(integer, idt.cantidadRecibida), 
        floor(idt.cantidad1) as cantidad1, idt.mal_estado
        from inventario_web_det_transferencias as idt,
        productos_inve as pi,
        where idt.id = :id
        and idt.cod_producto = pi.cod_producto
        and pi.empresa = :empresa",['id'=>$request->id,'empresa'=>Auth::user()->empresa]));
        $task = '';
        foreach($producto as $pro)
        {
            $task = ['cantidad1'=>$pro->cantidad1,'nombre_corto'=>$pro->nombre_corto.' '.$pro->nombre_fiscal,'cantidad'=>$pro->cantidadRecibida,'mal_estado'=>$pro->mal_estado];
        }
        return $task;
    }

    public function confirmar_producto_transferencia_sucursal(Request $request)
    {
        $verificado = DetTransferencias::findOrFail($request->id);
        $tran = Transferencias::findOrFail($verificado->num_movi);
        $hoy = Carbon::parse($tran->updated_at)->addHours(24);
        if($tran->porcentaje == NULL && $tran->erroresVerificados == NULL)
        {
            if($request->cantidad == '')
            {
                $verificado->cantidadRecibida   = 0;
                $verificado->mal_estado = $request->mal_estado;
            }
            else
            {
                $verificado->cantidadRecibida   = $request->cantidad;
                $verificado->mal_estado = $request->mal_estado;
            } 
            if($verificado->cantidad1 == NULL)
            {
                $verificado->noIncluido = 1;
            }
            $verificado->verificadoSucursal = 1;
            $verificado->updated_at         = new Carbon();
            $verificado->save();
            return $verificado;
        }
        else if($tran->erroresVerificados == null && $hoy > Carbon::now())
        {
            if($request->cantidad == '')
            {
                $verificado->cantidadRecibida   = 0;
                $verificado->mal_estado = $request->mal_estado;
            }
            else
            {
                $verificado->cantidadRecibida   = $request->cantidad;
                $verificado->mal_estado = $request->mal_estado;
            } 
            if($verificado->cantidad1 == NULL)
            {
                $verificado->noIncluido = 1;
            }
            $verificado->verificadoSucursal = 1;
            $verificado->updated_at         = new Carbon();
            $verificado->save();
            return $verificado; 
        }
        else 
        {
            $tran->id_estado = 20;
            $tran->erroresVerificados = 2;
            $tran->opcionalDos        = 'S';
            $tran->save();
            return back()->with('error','¡No se permiten más cambios, transferencia finalizada');
        }
    }

    public function eliminar_producto(Request $request)
    { 
        $nuevo = DetTransferencias::findOrFail($request->id);
        $nuevo->updated_at = new Carbon();
        $nuevo->cantidadSolicitada = null;
        $nuevo->noIncluido = null;
        $nuevo->incluido = null;
        $nuevo->save();
        return $nuevo;
    }

//--------------------------- Funcion para eliminar / editar los productos dentro de una transferencia --------------------------------------------------------
    function historial_de_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',13)->first())
        {
            $encabezado = DB::select('select iweth.num_movi, iweth.observacion as descripcion,usuario, tv.descripcion as placa_vehiculo,comentario, 
            referencia, iwe.nombre as estado, iweth.created_at, fecha_enCola, fecha_enCarga, fecha_cargado, 
            tv.nombre as propietario,fecha_paraCarga, fechaEntrega
            from inventario_web_encabezado_transferencia_historial as iweth
            join inventario_web_estados as iwe on iweth.id_estado = iwe.id
            left join T_Flotas as tv on iweth.placa_vehiculo = tv.Codigo
            where num_movi = :id',['id'=>$id]);

            $productos = DB::select("select num_movi, pi.cod_producto, pi.nombre_corto, pi.nombre_fiscal, wdth.created_at, cantidadSolicitada, id_inserto,
            u.name, case incluido when 1 then 'Insertado' else 'Eliminado' end as accion
            from inventario_web_det_transferencias_historial as wdth,
            productos_inve as pi,
            users as u 
            where num_movi = :id
            and wdth.cod_producto = pi.cod_producto 
            and wdth.empresa = pi.empresa 
            and u.id = wdth.id_inserto",['id'=>$id]);
            return view('transferencias.historial',compact('encabezado','productos'));
        }
        return back()->with('error','No tienes permiso para ingresar');
    }

    public function editar_transferencia($id)
    {
        $existe = Transferencias::find($id);
        if($existe == true)
        {
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',12)->first();
            if($permiso == true && $existe->id_estado < 18)
            {
                //return $historial;
                $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.resolucion_autorizacion as nombre, iwe.nombre as estado,
                iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga, b.observacion as bodega, iwet.grupo,
                iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
                tf.nombre as propietario, iwet.serieFactura, iwet.cod_unidad, iwet.cod_bodega, iwet.usuarioSupervisa, iwet.usuarioSupervisa,
                iwet.placa_vehiculo, iwet.unidad_transf, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.bodega_Transf 
                from inventario_web_encabezado_transferencias as iwet 
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on iwet.bodega_Transf = b.cod_bodega
                join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
                join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
                left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                left join T_Flotas as tf on tv.flota = tf.Codigo
                where iwet.num_movi = :id
                and u.empresa = iwet.empresa
                and u.empresa = b.empresa
                and u.cod_unidad = b.cod_unidad
                and uni.empresa = iwet.empresa
                and uni.cod_unidad = bod.cod_unidad
                and bod.empresa = iwet.empresa',['id'=>$id]);
                foreach($tran as $t)
                {
                    $per = $t->id_estado;
                    $unidad = $t->unidad_transf;
                    $unidad2 = $t->cod_unidad;
                }
                $comprobar = DB::select('select count(id) as transferencias
                from inventario_web_encabezado_transferencias
                where id_estado < 18');
                
                $estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado + 1) as max,
                (iwet.id_estado - 1) as min
                from inventario_web_encabezado_transferencias as iwet
                right join inventario_web_estados as iwe on iwe.id >= min
                where iwet.id = :id
                and iwe.id >= 13
                and iwe.id BETWEEN min and max
                and iwe.id != :per
                and iwe.id < 18',['id'=>$id,'per'=>$per]);
                if($per < 18)
                {
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
                    {
                        $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)
                        ->where('Activa','S')->orderBy('nombre','asc')->get();
                        $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)
                        ->where('Activa','S')->orderBy('nombre','asc')->get();
                        return view('transferencias.EditarTransferencia',compact('tran','id','estados','saleDe','entraSu'));
                    }
                    else if($unidad == Auth::user()->sucursal)
                    {
                        $saleDe = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$unidad2)
                        ->where('Activa','S')->get();
                        $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$unidad)
                        ->where('Activa','S')->get();
                        return view('transferencias.EditarTransferencia',compact('tran','id','estados','saleDe','entraSu'));
                    }
                    return back()->with('error','No puedes modificar transferencias de otras sucursales');
                }
                return back()->with('error','No puedes modificar transferencias de otras sucursales');
            }   
            elseif($existe->id_estado == 17 && $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first() == true &&
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',12)->first() == false)
            {
                return redirect()->route('verficar_transdd',['id'=>$id]);
            }
            return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
        }
        return redirect()->route('inicio_transferencias')->with('error','No existe la transferencia que buscas en el sistema');
    }

    public function productos_en_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
        {
            $productos = DB::select('select iwdt.id, iwc.nombre, pi.nombre_corto, pi.nombre_fiscal, iwdt.cod_producto, convert(integer,iwdt.costo),
            convert(integer,iwdt.cantidadSolicitada) as cantidad, iwdt.id_superviso,
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen   
            from inventario_web_det_transferencias as iwdt
            left join (select  iwdt.cod_producto, sum(iwdt.cantidadSolicitada) reserva
            from inventario_web_det_transferencias as iwdt, 
            inventario_web_encabezado_transferencias as iwet
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
            order by iwc.nombre asc',['id'=>$id]);
            return $productos;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function detalle_producto_transferencia(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
        {
            $detalles = DetTransferencias::find($request->id);
            //return $detalles;
            $producto = DB::select('select iwdt.id, iwc.nombre, pi.nombre_corto, pi.nombre_fiscal, iwdt.cod_producto, convert(integer,iwdt.costo), 
            convert(integer,iwdt.cantidadSolicitada) as cantidad, (convert(integer,iv.existencia1) - convert(integer,coalesce(nv.reserva,0))) as existencia,
            convert(integer,ivs.existencia1) as sucursal, convert(integer,ivs.minimo), convert(integer,ivs.piso_sugerido), iwdt.id_superviso, 
            convert(integer,ivs.maximo), ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
            //iwdt.cantidadSugerida   
            from inventario_web_det_transferencias as iwdt
            left join inventarios as iv on iwdt.cod_producto = iv.cod_producto
            left join (select  iwdt.cod_producto, sum(iwdt.cantidadSolicitada) reserva
            from inventario_web_det_transferencias as iwdt, 
            inventario_web_encabezado_transferencias as iwet
            where iwet.num_movi = iwdt.num_movi
            and iwet.id_estado between 13 and 17
            and iwdt.incluido = 1
            and iwdt.cod_unidad = :unidad
            and iwdt.cod_bodega = :bodega
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
            and iwdt.empresa = iwc.empresa',['id'=>$request->id,'unidad'=>$detalles->cod_unidad,'bodega'=>$detalles->cod_bodega]);
            $detalle = '';
            foreach($producto as $pro)
            {
                $detalle = ['cantidad'=>$pro->cantidad,'nombre_corto'=>$pro->nombre_corto,'nombre_fiscal'=>$pro->nombre_fiscal,
                'existencia'=>$pro->existencia,'sucursal'=>$pro->sucursal, 'bultos'=>$pro->costo,'min'=>$pro->minimo,
            'reo'=>$pro->piso_sugerido,'max'=>$pro->maximo,'id_superviso'=>$pro->id_superviso];
            }
            return $detalle;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function guardar_producto_transferencia(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
        {
            $verificado = DetTransferencias::findOrFail($request->id);
            $historial = new HDTransferencia();
            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',11)->first() == true && $verificado->id_estado < 18)
            {
                $historial->cantidadSolicitada = $request->cantidad;
                $historial->empresa = $verificado->empresa;
                $historial->cod_tipo_movi = $verificado->cod_tipo_movi;
                $historial->cod_serie_movi = $verificado->cod_serie_movi;
                $historial->num_movi = $verificado->num_movi;
                $historial->cod_producto = $verificado->cod_producto;
                $historial->cod_unidad = $verificado->cod_unidad;
                $historial->cod_bodega = $verificado->cod_bodega;
                $historial->cantidad1 = $verificado->cantidad1;
                $historial->costo = $verificado->costo;
                $historial->orden = $verificado->orden;
                $historial->unidad_transf = $verificado->unidad_transf;
                $historial->bodega_Transf = $verificado->bodega_Transf;
                $historial->created_at = Carbon::now();
                $historial->updated_at = Carbon::now();
                $historial->incluido = $verificado->incluido;
                $historial->id_inserto = Auth::id();
                if($request->importante == 1)
                {
                    $historial->id_superviso = $request->importante;
                }
                else 
                {
                    $historial->id_superviso = null;
                }
                $historial->cantidadSugerida = $verificado->cantidadSugerida;
                $historial->save();
                $verificado->cantidadSolicitada    = $request->cantidad;
                $verificado->costo = $request->bultos; 
                if($request->importante == 1)
                {
                    $verificado->id_superviso = $request->importante;
                }
                else 
                {
                    $verificado->id_superviso = null;
                }
                $verificado->updated_at         = new Carbon();
                $verificado->save();
                $peso = 0;
                $productos = DB::select('select iwdt.cod_producto, iwdt.cantidadSolicitada, ((pi.peso/2.204623) * cantidadSolicitada) as peso
                from inventario_web_det_transferencias as iwdt,
                productos_inve as pi
                where iwdt.num_movi = :id
                and iwdt.incluido is not null
                and iwdt.cod_producto = pi.cod_producto
                and iwdt.empresa = pi.empresa',['id'=>$verificado->num_movi]);
                foreach($productos as $p)
                {
                    $peso +=$p->peso;
                }
                $transferencia = DB::table('inventario_web_encabezado_transferencias')->where('num_movi',$verificado->num_movi)->update(['opcionalUno'=>($peso/1000)]);
                return $verificado;
            }
            elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',11)->first() == false && $verificado->unidad_transf == Auth::user()->sucursal && $verificado->id_estado < 18)
            {
                $verificado->cantidadSugerida    = $request->cantidad; 
                $verificado->updated_at         = new Carbon();
                $verificado->save();
                return $verificado;
            }
            return 'error';
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function eliminar_producto_transferencia(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',14)->first();
        $task = $nuevo = DetTransferencias::findOrFail($request->id);
        $historial = new HDTransferencia();
        if($permiso == true || $task->id_inserto == Auth::id())
        {
            $historial->empresa = $task->empresa;
            $historial->cod_tipo_movi = $task->cod_tipo_movi;
            $historial->cod_serie_movi = $task->cod_serie_movi;
            $historial->num_movi = $task->num_movi;
            $historial->cod_producto = $task->cod_producto;
            $historial->cod_unidad = $task->cod_unidad;
            $historial->cod_bodega = $task->cod_bodega;
            $historial->cantidad1 = $task->cantidad1;
            $historial->costo = $task->costo;
            $historial->orden = $task->orden;
            $historial->unidad_transf = $task->unidad_transf;
            $historial->bodega_Transf = $task->bodega_Transf;
            $historial->created_at = Carbon::now();
            $historial->updated_at = Carbon::now();
            $historial->incluido = null;
            $historial->noIncluido = 1;
            $historial->cantidadSolicitada = $nuevo->cantidad;
            $historial->id_inserto = Auth::id();
            $historial->cantidadSugerida = $task->cantidadSugerida;
            $historial->save();
            $nuevo->updated_at = new Carbon();
            $nuevo->incluido = null;
            $nuevo->save();
            $peso = 0;
                $productos = DB::select('select iwdt.cod_producto, iwdt.cantidadSolicitada, ((pi.peso/2.204623) * cantidadSolicitada) as peso
                from inventario_web_det_transferencias as iwdt,
                productos_inve as pi
                where iwdt.num_movi = :id
                and iwdt.incluido is not null
                and iwdt.cod_producto = pi.cod_producto
                and iwdt.empresa = pi.empresa',['id'=>$nuevo->num_movi]);
                foreach($productos as $p)
                {
                    $peso +=$p->peso;
                }
                $transferencia = DB::table('inventario_web_encabezado_transferencias')->where('num_movi',$nuevo->num_movi)->update(['opcionalUno'=>($peso/1000)]);
            return $task;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function editar_encabezado_transferencia(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[15,16])->first())
        {
            $tran = Transferencias::findOrFail($id);
            $historial = new HETransferencia();
            if($tran->cod_unidad == Auth::user()->sucursal || 
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',[16])->first())
            {
                $validator = Validator::make($request->all(),[
                    'observacion'=>'required',
                    'comentario'=>'required',
                    'placa'=>'required',
                    'estado'=>'required|numeric',
                    'referencia'=>'required',
                    'fechaCarga'=>'required',
                    'fechaEntrega'=>'required|date',
                ]);
                if ($validator->fails()) 
                {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                if($request->saleDe == $request->entraSu && $request->saleBo == $request->entraBo)
                { 
                    return back()->with('error','¡No se permite realizar transferencias que ingresen a la misma bodega de donde salen!');
                }
                else 
                {
                    $productos = DetTransferencias::where('num_movi',$id)->where('incluido',1)->count();
                    if($tran->id_estado >= 18)
                    {
                        return back()->with('error','¡No es posible realizar modificaciones!');
                    }
                    elseif($productos == '' && $request->estado == 14)
                    {
                        return back()->with('error','¡No se puede cambiar el estado de una transferencia vacia!');
                    }
                    else
                    {
                        $historial->empresa = $tran->empresa;
                        $historial->cod_tipo_movi = $tran->cod_tipo_movi;
                        $historial->cod_serie_movi = $tran->cod_serie_movi;
                        $historial->num_movi = $tran->num_movi;
                        $historial->fecha = $tran->fecha;
                        $historial->created_at = Carbon::now();
                        $historial->fecha_opera = $tran->fecha_opera;
                        $historial->cod_motivo = $tran->cod_motivo;
                        $historial->serieFactura = $tran->serieFactura;
                        $historial->numeroFactura = $tran->numeroFactura;
                        $historial->cliente = $tran->cliente;
                        //$tran->usuario           = Auth::user()->name;
                        $tran->fecha_modificacion = Carbon::now();
                        $historial->fecha_modificacion = Carbon::now();
                        //$tran->observacion       = $request->observacion;
                        $tran->observacion       = $request->observacion;
                        $historial->observacion  = $request->observacion;
                        $tran->placa_vehiculo    = $request->placa;
                        $historial->placa_vehiculo = $request->placa;
                        $tran->comentario        = $request->comentario;
                        $historial->comentario   = $request->comentario;
                        $tran->referencia        = $request->referencia;
                        $historial->referencia   = $request->referencia;
                        $tran->id_estado         = $request->estado;
                        $historial->id_estado    = $request->estado;
                        $tran->updated_at        = Carbon::now();
                        $historial->updated_at   = Carbon::now();
                        //$tran->fecha_enCola      = Carbon::now();
                        $tran->cod_unidad = $request->saleDe;
                        $tran->cod_bodega = $request->saleBo;
                        $tran->unidad_transf = $request->entraSu;
                        $tran->bodega_Transf = $request->entraBo;
                        $historial->cod_unidad = $request->saleDe;
                        $historial->cod_bodega = $request->saleBo;
                        $historial->unidad_transf = $request->entraSu;
                        $historial->bodega_Transf = $request->entraBo;
                        $tran->usuarioSupervisa = Auth::user()->name;
                        $historial->usuarioSupervisa = Auth::user()->name;
                        $tran->grupo = $request->grupoTransferencia;
                        if($request->fechaEntrega < Carbon::now())
                        {
                            $tran->fechaEntrega = Carbon::now();
                            $historial->fechaEntrega = Carbon::now();
                        }
                        else 
                        {
                            $tran->fechaEntrega = $request->fechaEntrega;
                            $historial->fechaEntrega = $request->fechaEntrega;
                        }
                        if($request->fechaCarga == '')
                        {
                            $tran->fecha_paraCarga = Carbon::now();
                            $historial->fecha_paraCarga = Carbon::now();
                        }
                        else
                        {
                            if($request->fechaCarga < Carbon::now())
                            {
                                $tran->fecha_paraCarga = Carbon::now();
                                $historial->fecha_paraCarga = Carbon::now();
                            }
                            $tran->fecha_paraCarga = Carbon::parse($request->fechaCarga);
                            $historial->fecha_paraCarga = Carbon::parse($request->fechaCarga);
                        }
                        if($request->estado == 14)
                        {
                            $tran->fecha_enCola = Carbon::now();
                            $historial->fecha_enCola = Carbon::now();
                        }
                        elseif($request->estado == 15)
                        {
                            $tran->fechaUno = Carbon::now();
                            $historial->fechaUno = Carbon::now();
                        }
                        elseif($request->estado == 16)
                        {
                            $tran->fecha_enCarga = Carbon::now();
                            $historial->fecha_enCarga = Carbon::now();
                        }
                        elseif($request->estado == 17)
                        {
                            $tran->fecha_cargado = Carbon::now();
                            $historial->fecha_cargado = Carbon::now();
                        }
                        $tran->save();
                        $productos = DB::table('inventario_web_det_transferencias')->where('num_movi',$id)->where('empresa',Auth::user()->empresa)
                        ->update(['cod_unidad'=>$request->saleDe,'cod_bodega'=>$request->saleBo,'unidad_transf'=>$request->entraSu,
                        'bodega_Transf'=>$request->entraBo]);
                        $historial->usuario = Auth::user()->name;
                        $historial->save();
                        $est = DB::select('select nombre
                        from inventario_web_estados
                        where id = :estado',['estado'=>$request->estado]);
                        foreach($est as $e)
                        {
                            $estad = $e->nombre;
                        }
                        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                        {
                            $user = User::where('sucursal','=',$tran->unidad_transf)->where('roles',3)->pluck('email');
                            $usuario = Auth::user()->name;
                            $fecha = $request->fechaEntrega;
                            $estado = $estad;
                            $numero = $id;
                            $correo = Auth::user()->email;
                            Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo));
                            return back()->with('success','¡Transferencia modificada con exito!');
                        }
                        return back()->with('success','¡Transferencia modificada con exito!');
                    }
                    return back()->with('error','No se permite realizar cambios a esta transferencia');
                }
                return back()->with('error','No se permite realizar cambios a esta transferencia');
            }
            return back()->with('error','No se permite realizar cambios a esta transferencia');
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    public function agregar_producto_manual(Request $request,$id)
    {
        if($request->producto == "")
        {
            return back()->with('error','Debe ingresar el código del producto que quiere agregar');
        }
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[14,16])->first())
        {
            foreach($request->producto as $pro)
            {
                $producto = $pro;
            }
            $permitir = DetTransferencias::where('num_movi',$id)->where('cod_producto',$producto)->count('id');
            $encabezado = Transferencias::find($id);
            if($permitir == 0 || $permitir == 0 && Auth::user()->sucursal == $encabezado->unidad_transf)
            {
                $agregar                        = new DetTransferencias();
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
                $historial = new HDTransferencia();
                $historial->empresa = $encabezado->empresa;
                $historial->cod_tipo_movi = $encabezado->cod_tipo_movi;
                $historial->cod_serie_movi = $encabezado->cod_serie_movi;
                $historial->num_movi = $id;
                $historial->cod_producto =$producto;
                $historial->cod_unidad = $encabezado->cod_unidad;
                $historial->cod_bodega = $encabezado->cod_bodega;
                $historial->unidad_transf = $encabezado->unidad_transf;
                $historial->bodega_Transf = $encabezado->bodega_Transf;
                $historial->created_at = Carbon::now();
                $historial->updated_at = Carbon::now();
                $historial->incluido = 1;
                $historial->cantidadSolicitada = 0;
                $historial->id_inserto = Auth::id();
                $historial->cantidadSugerida = 0;
                $historial->save();
                return back()->with('success','Producto agregado con exito');
            }
            elseif($permitir == 1 || $permitir == 1 && Auth::user()->sucursal == $encabezado->unidad_transf)
            {
                $agregar = DetTransferencias::where('num_movi',$id)->where('cod_producto',$producto)->update(['incluido'=>1]);
                $agre = DetTransferencias::where('num_movi',$id)->where('cod_producto',$producto)->first();
                $historial = new HDTransferencia();
                $historial->empresa = $agre->empresa;
                $historial->cod_tipo_movi = $agre->cod_tipo_movi;
                $historial->cod_serie_movi = $agre->cod_serie_movi;
                $historial->num_movi = $agre->num_movi;
                $historial->cod_producto = $agre->cod_producto;
                $historial->cod_unidad = $agre->cod_unidad;
                $historial->cod_bodega = $agre->cod_bodega;
                $historial->cantidad1 = $agre->cantidad1;
                $historial->costo = $agre->costo;
                $historial->orden = $agre->orden;
                $historial->unidad_transf = $agre->unidad_transf;
                $historial->bodega_Transf = $agre->bodega_Transf;
                $historial->created_at = Carbon::now();
                $historial->updated_at = Carbon::now();
                $historial->incluido = 1;
                $historial->cantidadSolicitada = $agre->cantidad;
                $historial->id_inserto = Auth::id();
                $historial->cantidadSugerida = $agre->cantidadSugerida;
                $historial->save();
                return back()->with('success','Producto agregado con exito');
            }
            return back()->with('error','El producto ya existe en la transferencia o está intentando una acción no permitida');
        }
        return back()->with('error','No tienes permisos para accesar');
    }

    public function buscar_producto_manual(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
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
                $formatted_tags[] = ['id' => $tag->cod_producto, 'text' => utf8_encode($tag->nombre_corto).' '.utf8_encode($tag->nombre_fiscal)];
            }
            return \Response::json($formatted_tags);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para agregar productos a una nueva transferencia ------------------------------------------------------------------------
    public function nueva_transferencia($id)
    {
        $existe = Transferencias::find($id);
        if($existe == true)
        {
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first();
            if($permiso == true && $existe->id_estado < 18 || $permiso == true && $existe->id_estado > 18 && $existe->id_estado < 20)
            {
                $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, tv.descripcion as placa_vehiculo, iwe.nombre as estado,
                iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga, b.nombre as bodega,
                iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
                tv.nombre as propietario, iwet.cod_unidad, iwet.cod_bodega, iwet.unidad_transf
                from inventario_web_encabezado_transferencias as iwet 
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on iwet.bodega_transF = b.cod_bodega
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                left join T_Flotas as tv on iwet.placa_vehiculo = tv.Codigo
                where iwet.num_movi = :id
                and u.empresa = iwet.empresa
                and u.empresa = b.empresa
                and u.cod_unidad = b.cod_unidad',['id'=>$id]);
                foreach($tran as $t)
                {
                    $per = $t->id_estado;
                    $unidad = $t->unidad_transf;
                    $unidad2 = $t->cod_unidad;
                }
                if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',16)->first())
                {
                    return view('transferencias.nuevaTransferencia',compact('tran','id'));
                }
                else 
                {
                    if($unidad == Auth::user()->sucursal)
                    {
                        return view('transferencias.nuevaTransferencia',compact('tran','id'));
                    }
                    else
                    {
                        if($unidad2 == Auth::user()->sucursal || $unidad == Auth::user()->sucursal)
                        {
                            return view('transferencias.nuevaTransferencia',compact('tran','id'));
                        }
                        return back()->with('error','No puedes modificar transferencias de otras sucursales'); 
                    }
                    return back()->with('error','No puedes modificar transferencias de otras sucursales');
                }
                return back()->with('error','No puedes modificar transferencias de otras sucursales');
            }
            return redirect()->route('inicio_transferencias')->with('error','¡No es posible realizar modificaciones!');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');   
    }

    public function datos_nueva_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
        {
            $productos = DB::select("select iwdt.id, iwc.nombre, pi.nombre_corto, pi.nombre_fiscal, iwdt.cod_producto, iwdt.costo,
            convert(integer,iwdt.cantidadSolicitada) as cantidad, (convert(integer,iv.existencia1) - convert(integer,coalesce(nv.reserva,0))) as existencia,
            convert(integer,ivs.existencia1) as sucursal, convert(integer,ivs.minimo), convert(integer,ivs.piso_sugerido), 
            convert(integer,ivs.maximo), ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen   
            from inventario_web_det_transferencias as iwdt
            left join inventarios as iv on iwdt.cod_producto = iv.cod_producto
            left join (select  iwdt.cod_producto, sum(iwdt.cantidadSolicitada) reserva
            from inventario_web_det_transferencias as iwdt, 
            inventario_web_encabezado_transferencias as iwet
            where iwet.num_movi = iwdt.num_movi
            and iwet.id_estado between 13 and 17
            and iwdt.incluido = 1
            group by iwdt.cod_producto) as nv on iwdt.cod_producto = nv.cod_producto
            left join inventarios as ivs on iwdt.cod_producto = ivs.cod_producto 
            join productos_inve as pi on iwdt.cod_producto = pi.cod_producto
            join inventario_web_categorias as iwc on pi.cod_tipo_prod = iwc.cod_tipo_prod
            where iwdt.num_movi = :id
            and iwdt.incluido is null
            and iwdt.cod_unidad = iv.cod_unidad
            and iwdt.cod_bodega = iv.cod_bodega
            and iwdt.empresa = iv.empresa
            and iwdt.unidad_transf = ivs.cod_unidad
            and iwdt.bodega_Transf = ivs.cod_bodega
            and iwdt.empresa = ivs.empresa
            and iwdt.empresa = pi.empresa 
            and iwdt.empresa = iwc.empresa
            and iwc.nombre <> 'canuelas de thermopor'
            //and iv.existencia1 > 0
            order by iwc.nombre asc",['id'=>$id]);
            return $productos;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function agregar_producto_transferencia(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[12,16])->first())
        {
            $task = $nuevo = DetTransferencias::findOrFail($request->id);
            $nuevo->updated_at = new Carbon();
            $nuevo->incluido = 1;
            $nuevo->id_inserto = Auth::id();
            $nuevo->save();
            $historial = new HDTransferencia();
            $historial->empresa = $task->empresa;
            $historial->cod_tipo_movi = $task->cod_tipo_movi;
            $historial->cod_serie_movi = $task->cod_serie_movi;
            $historial->num_movi = $task->num_movi;
            $historial->cod_producto = $task->cod_producto;
            $historial->cod_unidad = $task->cod_unidad;
            $historial->cod_bodega = $task->cod_bodega;
            $historial->cantidad1 = $task->cantidad1;
            $historial->costo = $task->costo;
            $historial->orden = $task->orden;
            $historial->unidad_transf = $task->unidad_transf;
            $historial->bodega_Transf = $task->bodega_Transf;
            $historial->created_at = Carbon::now();
            $historial->updated_at = Carbon::now();
            $historial->incluido = 1;
            $historial->cantidadSolicitada = $task->cantidadSolicitada;
            $historial->id_inserto = Auth::id();
            $historial->cantidadSugerida = $task->cantidadSugerida;
            $historial->save();
            return $task;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias que se encuetran en bodega ------------------------------------------------------
    public function transferencias_bodega()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias en bodega';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            $fecha = Carbon::now()->addDays(1);
            $hoy = Carbon::today()->toDateString();
            $next = $fecha->toDateString();
            $proxima = $fecha->addDays(3);
            //return $hoy;
            return view('transferencias.TransferenciasBodega',compact('hoy','proxima','next'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias_bodega()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, 
            tv.propietario+' '+iwet.placa_vehiculo as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.observacion as bodega, iwet.opcionalUno as peso,
            iwet.observacion as DESCRIPCION, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.comentario, iwet.grupo
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 14 and 17
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad 
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and iwet.observacion not LIKE '%exporta%'");
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true &&
        Auth::user()->roles == 3)
        {
            $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, 
            tv.propietario+' '+iwet.placa_vehiculo as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.observacion as bodega, iwet.opcionalUno as peso,
            iwet.observacion as DESCRIPCION, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.comentario, iwet.grupo
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 14 and 17
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.unidad_transf = :sucursal
            and uni.cod_unidad = bod.cod_unidad 
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and iwet.observacion not LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal]);
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true &&
        Auth::user()->roles != 3) 
        {
            $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, 
            tv.propietario+' '+iwet.placa_vehiculo as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.observacion as bodega, iwet.opcionalUno as peso,
            iwet.observacion as DESCRIPCION, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.comentario, iwet.grupo
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 14 and 17
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :sucursal
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and iwet.observacion not LIKE '%exporta%'
            or iwet.id_estado between 14 and 17
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and iwet.unidad_transf = :sucursal2
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and iwet.observacion not LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias que se encuetran en bodega ------------------------------------------------------
public function bodega_exportaciones()
{
    $historial  = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Ingreso a transferencias en bodega';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        $fecha = Carbon::now()->addDays(1);
        $hoy = Carbon::now()->toDateString();
        $next = $fecha->toDateString();
        $proxima = $fecha->addDays(3);
        return view('transferencias.TransferenciasBodegaExpo',compact('hoy','proxima','next'));
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}

public function datos_bodega_exportacion()
{
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
    {
        $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, 
        tv.propietario+' '+iwet.placa_vehiculo as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.observacion as bodega, iwet.opcionalUno as peso,
        iwet.observacion as DESCRIPCION, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.comentario, iwet.grupo
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad 
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        and  iwet.observacion LIKE '%exporta%'");
        return DataTables($transferencia)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true &&
    Auth::user()->roles == 3)
    {
        $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, 
        tv.propietario+' '+iwet.placa_vehiculo as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.observacion as bodega, iwet.opcionalUno as peso,
        iwet.observacion as DESCRIPCION, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.comentario, iwet.grupo
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad 
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        and iwet.cod_unidad = :sucursal
        and  iwet.observacion LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal]);
        return DataTables($transferencia)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true &&
    Auth::user()->roles != 3) 
    {
        $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, 
        tv.propietario+' '+iwet.placa_vehiculo as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.observacion as bodega, iwet.opcionalUno as peso,
        iwet.observacion as DESCRIPCION, uni.resolucion_autorizacion as usale, bod.observacion as bsale, iwet.comentario, iwet.grupo
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad 
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        and iwet.cod_unidad = :sucursal
        and  iwet.observacion LIKE '%exporta%'
        or iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and iwet.unidad_transf = :sucursal2
        and uni.cod_unidad = bod.cod_unidad
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        and  iwet.observacion LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal]);
        return DataTables($transferencia)->make(true);
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias despachadas por bodega ----------------------------------------------------------
    public function despacho_transferencias()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias despachadas';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            return view('transferencias.TransferenciasDespachadas');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias_despacho()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $transferencia = DB::select("select num_movi, iwet.fechaSalida as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, placa_vehiculo,
            iwe.nombre as estado, iwe.id, tv.propietario, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
            uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado = 18
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad 
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            //and bod.fax is null
            and bo.fax is null
            and iwet.observacion not LIKE '%exporta%'");
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true 
        && Auth::user()->roles == 3)
        {
            $transferencia = DB::select("select num_movi, iwet.fechaSalida as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, placa_vehiculo,
            iwe.nombre as estado, iwe.id, tv.propietario, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
            uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado = 18
            and iwet.unidad_transf = :sucursal
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            //and bod.fax is null
            and bo.fax is null
            and iwet.observacion not LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal]);
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true
         && Auth::user()->roles != 3) 
        {
            $transferencia = DB::select("select num_movi, iwet.fechaSalida as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, placa_vehiculo,
            iwe.nombre as estado, iwe.id, tv.propietario, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
            uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado = 18
            and iwet.cod_unidad = :sucursal
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa 
            //and bod.fax is null
            and bo.fax is null
            and iwet.observacion not LIKE '%exporta%'
            or iwet.id_estado = 18
            and iwet.unidad_transf = :sucursal2
            and u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            //and bod.fax is null
            and bo.fax is null
            and iwet.observacion not LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias despachadas para exportaciones --------------------------------------------------
public function despachadas_exportacion()
{
    $historial  = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Ingreso a transferencias despachadas';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        return view('transferencias.TransferenciasDespachadasExpo');
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}

public function datos_despachadas_exportacion()
{
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
    {
        $transferencia = DB::select("select num_movi, iwet.fechaSalida as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, placa_vehiculo,
        iwe.nombre as estado, iwe.id, tv.propietario, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
        uni.resolucion_autorizacion as usale, bod.observacion as bsale
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado = 18
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad 
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        //and bod.fax is null
        and bo.fax is null
        and iwet.observacion LIKE '%exporta%'");
        return DataTables($transferencia)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true 
    && Auth::user()->roles == 3)
    {
        $transferencia = DB::select("select num_movi, iwet.fechaSalida as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, placa_vehiculo,
        iwe.nombre as estado, iwe.id, tv.propietario, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
        uni.resolucion_autorizacion as usale, bod.observacion as bsale
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado = 18
        and iwet.unidad_transf = :sucursal
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        //and bod.fax is null
        and bo.fax is null
        and iwet.observacion LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal]);
        return DataTables($transferencia)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true
     && Auth::user()->roles != 3) 
    {
        $transferencia = DB::select("select num_movi, iwet.fechaSalida as fecha, iwet.usuario, u.resolucion_autorizacion as nombre, placa_vehiculo,
        iwe.nombre as estado, iwe.id, tv.propietario, bo.observacion as bodega, iwet.observacion as DESCRIPCION, 
        uni.resolucion_autorizacion as usale, bod.observacion as bsale
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado = 18
        and iwet.cod_unidad = :sucursal
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa 
        and iwet.observacion LIKE '%exporta%'
        //and bod.fax is null
        and bo.fax is null
        or iwet.id_estado = 18
        and iwet.unidad_transf = :sucursal2
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        //and bod.fax is null
        and bo.fax is null
        and iwet.observacion LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal]);
        return DataTables($transferencia)->make(true);
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias que están en la sucursal finalizadas o a la espera de finalizar -----------------
    public function transferencias_finalizadas()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias finalizadas';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            return view('transferencias.TransferenciasFinalizadas');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias_finalizadas()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true 
        && Auth::user()->roles == 3)
        {
            $transferencia = DB::select('select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, placa_vehiculo, 
            iwe.nombre as estado, iwet.created_at as created_at, iwet.fechaSalida as fechaSalida, iwet.usuario,
            fecha_entregado, tv.propietario, iwet.erroresVerificados, iwet.porcentaje, iwet.opcionalUno,
            iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.unidad_transf = :sucursal
            and iwet.fecha_entregado > :fecha
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa 
            //and bod.fax is null
            and b.fax is null
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true 
        && Auth::user()->sucursal == 11)
        {
            $transferencia = DB::select('select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, placa_vehiculo, 
            iwe.nombre as estado, iwet.created_at as created_at, iwet.fechaSalida as fechaSalida, iwet.opcionalUno,
            fecha_entregado, tv.propietario, iwet.erroresVerificados, iwet.porcentaje, iwet.usuario, 
            iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :sucursal
            and iwet.fecha_entregado > :fecha
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            //and bod.fax is null
            and b.fax is null
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first()) 
        {
            $transferencia = DB::select('select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, placa_vehiculo, 
            iwe.nombre as estado, iwet.created_at as created_at, iwet.fechaSalida as fechaSalida, iwet.opcionalUno,
            fecha_entregado, tv.propietario, iwet.erroresVerificados, iwet.porcentaje, iwet.usuario,
            iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :sucursal
            and iwet.fecha_entregado > :fecha
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            //and bod.fax is null
            and b.fax is null
            and uni.cod_unidad = bod.cod_unidad
            ',['sucursal'=>Auth::user()->sucursal,
            'fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($transferencia)->make(true);
        }
        else 
        {
            return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias que están en la sucursal finalizadas o a la espera de finalizar por fecha -------
    public function transferencias_finalizadas_fecha(Request $request)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias finalizadas filtradas por fecha';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('transferencias.TransferenciasFinalizadasFecha',compact('inicio','fin'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transf_final_fecha($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true 
        && Auth::user()->roles == 3)
        {
            $transferencia = DB::select('select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, placa_vehiculo, 
            iwe.nombre as estado, iwet.created_at as created_at, iwet.fechaSalida as fechaSalida, iwet.usuario, 
            fecha_entregado, tv.propietario, iwet.erroresVerificados, iwet.porcentaje, iwet.opcionalUno,
            iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.unidad_transf = :sucursal
            and iwet.fechaSalida between :inicio and :fin
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa 
            //and bod.fax is null
            and b.fax is null
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true 
        && Auth::user()->roles != 3) 
        {
            $transferencia = DB::select('select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, placa_vehiculo, 
            iwe.nombre as estado, iwet.created_at as created_at, iwet.fechaSalida as fechaSalida, iwet.usuario,
            fecha_entregado, tv.propietario, iwet.erroresVerificados, iwet.porcentaje, iwet.opcionalUno,
            iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :sucursal
            and iwet.fechaSalida between :inicio and :fin
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad
            //and bod.fax is null
            and b.fax is null
            or iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            //and bod.fax is null
            and b.fax is null
            and iwet.unidad_transf = :sucursal2
            and iwet.fechaSalida between :inicio2 and :fin2
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal,
            'inicio'=>$inicio,'inicio2'=>$inicio,'fin'=>$fin,'fin2'=>$fin]);
            return DataTables($transferencia)->make(true);
        }
        elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first()) 
        {
            $transferencia = DB::select('select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, placa_vehiculo, 
            iwe.nombre as estado, iwet.created_at as created_at, iwet.fechaSalida as fechaSalida, iwet.usuario,
            fecha_entregado, tv.propietario, iwet.erroresVerificados, iwet.porcentaje, iwet.opcionalUno,
            iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.cod_unidad = :sucursal
            and iwet.fechaSalida between :inicio and :fin
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            //and bod.fax is null
            and b.fax is null
            and uni.cod_unidad = bod.cod_unidad
            or iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            //and bod.fax is null
            and b.fax is null
            and iwet.unidad_transf = :sucursal2
            and iwet.fechaSalida between :inicio2 and :fin2
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad',['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal,
            'inicio'=>$inicio,'inicio2'=>$inicio,'fin'=>$fin,'fin2'=>$fin]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver las transferencias creadas en otras sucursales -------------------------------------------------------------------
    public function transferencias_de_otras_sucursales()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias en otras sucursales';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            return view('transferencias.VerTransferenciasPlanta');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias_de_otras_sucursales()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $inventario = DB::select("select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, placa_vehiculo, iwe.nombre as estado, 
            iwet.fechaSalida as f, iwet.fecha_paraCarga, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
            iwet.porcentaje, iwet.opcionalDos, iwet.comentario, iwet.created_at, b.observacion as bodega, iwet.usuario, iwet.opcionalUno,
            uni.resolucion_autorizacion as usale, bod.observacion as bsale 
            from inventario_web_encabezado_transferencias as iwet 
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega 
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad  
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa 
            and iwet.cod_unidad != :sucursal
            //and bod.fax is null
            and b.fax is null
            and b.cod_unidad = u.cod_unidad
            and b.empresa = iwet.empresa
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa 
            and uni.cod_unidad = bod.cod_unidad 
            and iwet.fecha_entregado > :fecha
            and iwet.observacion not LIKE '%exporta%'
            order by f desc",['sucursal'=>Auth::user()->sucursal,'fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver las transferencias finalizadas para exportación --------------------------------------------------------------
public function exportaciones_finalizadas()
{
    $historial  = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Ingreso a transferencias en otras sucursales';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        return view('transferencias.VerTransferenciasFinExpo');
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}

public function datos_exportaciones_finalizadas()
{
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        $inventario = DB::select("select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, placa_vehiculo, iwe.nombre as estado, 
        iwet.fechaSalida as f, iwet.fecha_paraCarga, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
        iwet.porcentaje, iwet.opcionalDos, iwet.comentario, iwet.created_at, b.observacion as bodega, iwet.usuario, iwet.opcionalUno,
        uni.resolucion_autorizacion as usale, bod.observacion as bsale 
        from inventario_web_encabezado_transferencias as iwet 
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as b on iwet.bodega_Transf = b.cod_bodega 
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad  
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado between 19 and 20
        and u.empresa = iwet.empresa 
        //and bod.fax is null
        and b.fax is null
        and b.cod_unidad = u.cod_unidad
        and b.empresa = iwet.empresa
        and iwet.empresa = uni.empresa 
        and iwet.empresa = bod.empresa 
        and uni.cod_unidad = bod.cod_unidad 
        and iwet.fecha_entregado > :fecha
        and iwet.observacion LIKE '%exporta%'
        order by f desc",['fecha'=>Carbon::now()->subMonths(6)]);
        return DataTables($inventario)->make(true);
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver las transferencias creadas en otras sucursales -------------------------------------------------------------------
    public function transferencias_otras_sucur_fecha(Request $request)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias en otras sucursales';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('transferencias.VerTransferenciasPlantaFecha',compact('inicio','fin'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function transferencias_otras_sucursales_fecha($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
        {
            $inventario = DB::select("select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, placa_vehiculo, iwe.nombre as estado, 
            iwet.fechaSalida as f, iwet.fecha_paraCarga, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
            iwet.porcentaje, iwet.opcionalDos, iwet.comentario, iwet.created_at, b.observacion as bodega, iwet.usuario, iwet.opcionalUno,
            uni.resolucion_autorizacion as usale, bod.observacion as bsale 
            from inventario_web_encabezado_transferencias as iwet 
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega 
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad  
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado between 19 and 20
            and u.empresa = iwet.empresa 
            and iwet.cod_unidad != :sucursal
            and b.cod_unidad = u.cod_unidad
            and b.empresa = iwet.empresa
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa 
            //and bod.fax is null
            and b.fax is null
            and uni.cod_unidad = bod.cod_unidad 
            and iwet.fechaSalida between :inicio and :fin
            and iwet.observacion not LIKE '%exporta%'
            order by f desc",['sucursal'=>Auth::user()->sucursal,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver las transferencias creadas en otras sucursales -------------------------------------------------------------------
public function exportaciones_finalizadas_fecha(Request $request)
{
    $historial  = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Ingreso a transferencias en otras sucursales';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        $inicio = $request->inicio;
        $fin = $request->fin;
        return view('transferencias.VerTransferenciasExpoFecha',compact('inicio','fin'));
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}

public function datos_exportaciones_finalizadas_fecha($inicio,$fin)
{
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        $inventario = DB::select("select num_movi, fecha, iwet.observacion, u.resolucion_autorizacion as nombre, placa_vehiculo, iwe.nombre as estado, 
        iwet.fechaSalida as f, iwet.fecha_paraCarga, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
        iwet.porcentaje, iwet.opcionalDos, iwet.comentario, iwet.created_at, b.observacion as bodega, iwet.usuario, iwet.opcionalUno,
        uni.resolucion_autorizacion as usale, bod.observacion as bsale 
        from inventario_web_encabezado_transferencias as iwet 
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as b on iwet.bodega_Transf = b.cod_bodega 
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad  
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
        where iwet.id_estado between 19 and 20
        and u.empresa = iwet.empresa 
        and b.cod_unidad = u.cod_unidad
        and b.empresa = iwet.empresa
        and iwet.empresa = uni.empresa 
        and iwet.empresa = bod.empresa 
        //and bod.fax is null
        and b.fax is null
        and uni.cod_unidad = bod.cod_unidad 
        and iwet.fechaSalida between :inicio and :fin
        and iwet.observacion LIKE '%exporta%'
        order by f desc",['inicio'=>$inicio,'fin'=>$fin]);
        return DataTables($inventario)->make(true);
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para verificar las transferencias -----------------------------------------------------------------
    public function verificar_transferencia($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first();
        $existe = Transferencias::find($id);
        if($permiso == true && $existe == true)
        {
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',16)->first();
            if($permiso == true)
            {
                $grupos = User::where('roles',17)->get();
                $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, tv.Placa as placa_vehiculo, iwe.nombre as estado,
                iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga, b.nombre as bodega,
                iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
                tv.propietario, iwet.fechaUno, uni.nombre as usale, bod.nombre as bsale 
                from inventario_web_encabezado_transferencias as iwet
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on iwet.bodega_transF = b.cod_bodega
                join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
                join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                full join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
                where iwet.num_movi = :id
                and u.empresa = iwet.empresa
                and u.cod_unidad = b.cod_unidad
                and u.empresa = b.empresa
                and uni.cod_unidad = bod.cod_unidad
                and uni.empresa = iwet.empresa 
                and bod.empresa = iwet.empresa',['id'=>$id]);
                foreach($tran as $t)
                {
                    $per = $t->id_estado;
                }
                if($per == 17)
                {
                    return view('transferencias.verificar_transferencia',compact('tran','id','grupos'));
                }
                return redirect()->route('trans_bodega')->with('error','¡Solo puede modificar ordenes con estado Cargado!');
            }
            $grupos = User::where('roles',17)->where('sucursal',Auth::user()->sucursal)->get();
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, tv.Placa as placa_vehiculo, iwe.nombre as estado,
            iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga, b.nombre as bodega,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
            tv.propietario, iwet.fechaUno, uni.nombre as usale, bod.nombre as bsale 
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_transF = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
            where iwet.num_movi = :id
            and u.empresa = iwet.empresa
            and u.cod_unidad = b.cod_unidad
            and u.empresa = b.empresa
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa',['id'=>$id]);
            foreach($tran as $t)
            {
                $per = $t->id_estado;
            }
            if($per == 17)
            {
                return view('transferencias.Verificar_transferencia',compact('tran','id','grupos'));
            }
            return redirect()->route('trans_bodega')->with('error','¡Solo puede modificar ordenes con estado Cargado!');
        }
        return redirect()->route('trans_bodega')->with('error','No tienes permisos para accesar');   
    }

    public function guardar_revision_transferencia(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first())
        {
            $vehiculo = DB::table('T_Vehiculos')->where('Placa',$request->placa)->first();
            $peso = DB::select('select iwdt.num_movi, sum(((pi.peso*0.453592) * iwdt.cantidad1)/1000) as peso
            from productos_inve as pi,
            inventario_web_det_transferencias as iwdt
            where pi.empresa = iwdt.empresa
            and pi.cod_producto = iwdt.cod_producto
            and iwdt.incluido = 1
            and iwdt.num_movi = :id
            group by iwdt.num_movi',['id'=>$id]);
            foreach($peso as $p)
            {
                $max = $p->peso;
            }
            if($vehiculo->n_motor < $max && $permiso == DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',18)->first())
            { 
                return back()->with('error','¡El peso de la carga sobrepasa el máximo permitido!');
            }
            $finalizar = Transferencias::find($id);
            $existe = MoviInve::where('num_movi',$id)->where('cod_serie_movi',$finalizar->cod_serie_movi)->where('empresa',Auth::user()->empresa)->count();
            $estado = "Despachado en camino";
            if($finalizar->id_estado >= 18 && $existe == 0)
            {
                return redirect()->route('inicio_transferencias')->with('error','¡Orden en camino o finalizada, no es posible realizar cambios!');
            }
            elseif($finalizar->id_estado < 18 && $existe > 0)
            {
                $pro = DB::table('det_movi_inve')->where('num_movi',$id)->where('cod_serie_movi','IW')->count();
                if($pro > 0)
                {
                    return back()->with('error','Transferencia finalizada en diamante, no se permite realizar cambios');
                }
                else 
                {
                    if(DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',29)->first())
                    {
                        $tran = Transferencias::find($id);
                        $vehiculo = DB::table('T_Vehiculos')->where('Placa',$request->placa)->first();
                        $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY id asc) as orden, num_movi,
                        cantidad1, cod_unidad, cod_bodega, cod_producto, unidad_transf, cod_tipo_movi,
                        cod_serie_movi, bodega_Transf
                        from inventario_web_det_transferencias
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
                            'precio' => 0,
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
                    }
                    else 
                    {
                        $tran = Transferencias::find($id);
                        $vehiculo = DB::table('T_Vehiculos')->where('Placa',$request->placa)->first();
                        $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY iwdt.id asc) as orden, iwdt.num_movi,
                        iwdt.cantidad1, iwdt.cod_unidad, iwdt.cod_bodega, iwdt.cod_producto, 
                        iwdt.unidad_transf, iwdt.cod_tipo_movi, i.existencia1,
                        iwdt.cod_serie_movi, iwdt.bodega_Transf
                        from inventario_web_det_transferencias as iwdt,
                        inventarios as i
                        where num_movi = :id
                        and cantidad1 >= 1
                        and incluido = 1
                        and i.cod_bodega = iwdt.cod_bodega
                        and i.cod_unidad = iwdt.cod_unidad
                        and i.cod_producto = iwdt.cod_producto
                        and i.empresa = iwdt.empresa
                        order by id DESC',['id'=>$id]);
                        foreach($dtran as $d)
                        {
                            if($d->existencia1 <= $d->cantidad1)
                            {
                                $enviar = $d->existencia1;
                            }
                            elseif($d->existencia1 > $d->cantidad1)
                            {
                                $enviar = $d->cantidad1;
                            }
                            $iwdp = DB::table('inventario_web_det_transferencias')->where('cod_producto',$d->cod_producto)
                            ->where('num_movi',$d->num_movi)->update(['cantidad1'=>$enviar]);
                            DB::table('det_movi_inve')->insert([
                            ['empresa' => Auth::user()->empresa,
                            'cod_tipo_movi' => $d->cod_tipo_movi,
                            'cod_serie_movi' => $d->cod_serie_movi,
                            'num_movi' => $d->num_movi,
                            'orden' => $d->orden,
                            'orden_transf' => $d->orden,
                            'clasif_Transferencia' => 1,
                            'cantidad1' => $enviar,
                            'cantidad2' => $enviar,
                            'precio' => 0,
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
                    }
                    $finalizar->usuarioSupervisa    = Auth::user()->name;
                    $finalizar->observacionSup      = $request->observaciones;
                    $finalizar->fechaSalida         = Carbon::now();
                    $finalizar->id_estado           = 18;
                    $finalizar->grupoCarga          = $request->grupo; 
                    $finalizar->cod_motivo          = 5;
                    $finalizar->placa_vehiculo      = $request->placa;
                    $finalizar->descripcion         = $request->piloto;
                    $finalizar->updated_at          = Carbon::now();
                    if($finalizar->save())
                    {
                        $cerrar = MoviInve::where('num_movi',$id)->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','E')
                        ->where('cod_serie_movi','IW')->update(['cod_motivo'=>99]);
                        $historial  = new Historial();
                        $historial->id_usuario  = Auth::id();
                        $historial->actividad   = 'Confirmo de recibido producto de transferencia'. $id;
                        $historial->created_at  = new Carbon();
                        $historial->updated_at  = new Carbon();
                        $historial->save();
                    }
                    $grup = DB::table('users')->where('id',$request->grupo)->first();
                    $grupo = GrupoUsuario::where('idGrupo',$grup->id)->get();
                    foreach($grupo as $gp)
                    {
                        $bitacora = new BitacoraTransferencia();
                        $bitacora->num_movi     = $id;
                        $bitacora->nombre       = $gp->nombre;
                        $bitacora->created_at   = Carbon::now();
                        $bitacora->updated_at   = Carbon::now();
                        $bitacora->save();
                    }
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $numero = $id;
                        $correo = Auth::user()->email;
                        Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo)); 
                        return redirect()->route('despacho_transf')->with('success','Datos agregados con exito');
                    }
                    return redirect()->route('despacho_transf')->with('success','Datos agregados con exito');
                }
            }
            elseif($finalizar->id_estado < 18 && $existe == 0)
            {
                $permitir = DB::select('select count(*) as conteo 
                from inventario_web_det_transferencias
                where num_movi = :id
                and incluido = 1
                and verificadoCarga is null',['id'=>$id]);
                foreach($permitir as $p)
                {
                    $per = $p->conteo;
                }
                if($per == 0) 
                {
                    $tran = Transferencias::find($id);
                    $vehiculo = DB::table('T_Vehiculos')->where('Placa',$request->placa)->first();
                    /*if($vehiculo->Placa == $tran->placa_vehiculo)
                    {*/
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
                        $movi->descripcion = $request->piloto;
                        $movi->cod_motivo = 5;
                        $movi->Clasif_transferencia = 1;
                        $movi->unidad_transf = $tran->unidad_transf;
                        $movi->bodega_Transf = $tran->bodega_Transf;
                        $movi->observacion = $tran->observacion;
                        $movi->usuario = 'web';
                        $movi->placa_vehiculo = $request->placa;
                        $movi->comentario = $tran->comentario;
                        $movi->fecha_modificacion = $tran->fecha_modificacion;
                        $movi->referencia = $tran->referencia;
                        if($movi->save())
                        {
                            if(DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',29)->first())
                            {
                                $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY id asc) as orden, num_movi,
                                cantidad1, cod_unidad, cod_bodega, cod_producto, unidad_transf, cod_tipo_movi,
                                cod_serie_movi, bodega_Transf
                                from inventario_web_det_transferencias
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
                                    'precio' => 0,
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
                            }
                            else 
                            {
                                $dtran = DB::select('select ROW_NUMBER() OVER (ORDER BY iwdt.id asc) as orden, iwdt.num_movi,
                                iwdt.cantidad1, iwdt.cod_unidad, iwdt.cod_bodega, iwdt.cod_producto, 
                                iwdt.unidad_transf, iwdt.cod_tipo_movi, i.existencia1,
                                iwdt.cod_serie_movi, iwdt.bodega_Transf
                                from inventario_web_det_transferencias as iwdt,
                                inventarios as i
                                where num_movi = :id
                                and cantidad1 >= 1
                                and incluido = 1
                                and i.cod_bodega = iwdt.cod_bodega
                                and i.cod_unidad = iwdt.cod_unidad
                                and i.cod_producto = iwdt.cod_producto
                                and i.empresa = iwdt.empresa
                                order by id DESC',['id'=>$id]);
                                foreach($dtran as $d)
                                {
                                    if($d->existencia1 <= $d->cantidad1)
                                    {
                                        $enviar = $d->existencia1;
                                    }
                                    elseif($d->existencia1 > $d->cantidad1)
                                    {
                                        $enviar = $d->cantidad1;
                                    }
                                    $iwdp = DB::table('inventario_web_det_transferencias')->where('cod_producto',$d->cod_producto)
                                    ->where('num_movi',$d->num_movi)->update(['cantidad1'=>$enviar]);
                                    DB::table('det_movi_inve')->insert([
                                    ['empresa' => Auth::user()->empresa,
                                    'cod_tipo_movi' => $d->cod_tipo_movi,
                                    'cod_serie_movi' => $d->cod_serie_movi,
                                    'num_movi' => $d->num_movi,
                                    'orden' => $d->orden,
                                    'orden_transf' => $d->orden,
                                    'clasif_Transferencia' => 1,
                                    'cantidad1' => $enviar,
                                    'cantidad2' => $enviar,
                                    'precio' => 0,
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
                            }
                            $finalizar->usuarioSupervisa    = Auth::user()->name;
                            $finalizar->observacionSup      = $request->observaciones;
                            $finalizar->fechaSalida         = Carbon::now();
                            $finalizar->id_estado           = 18;
                            $finalizar->grupoCarga          = $request->grupo; 
                            $finalizar->cod_motivo          = 5;
                            $finalizar->placa_vehiculo      = $request->placa;
                            $finalizar->descripcion         = $request->piloto;
                            $finalizar->updated_at          = Carbon::now();
                            if($finalizar->save())
                            {
                                $cerrar = MoviInve::where('num_movi',$id)->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','E')
                                ->where('cod_serie_movi','IW')->update(['cod_motivo'=>99]);
                                $historial  = new Historial();
                                $historial->id_usuario  = Auth::id();
                                $historial->actividad   = 'Confirmo de recibido producto de transferencia'. $id;
                                $historial->created_at  = new Carbon();
                                $historial->updated_at  = new Carbon();
                                $historial->save();
                            }
                            $grup = DB::table('users')->where('id',$request->grupo)->first();
                            $grupo = GrupoUsuario::where('idGrupo',$grup->id)->get();
                            foreach($grupo as $gp)
                            {
                                $bitacora = new BitacoraTransferencia();
                                $bitacora->num_movi     = $id;
                                $bitacora->nombre       = $gp->nombre;
                                $bitacora->created_at   = Carbon::now();
                                $bitacora->updated_at   = Carbon::now();
                                $bitacora->save();
                            }
                            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                            {
                                $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                                $usuario = Auth::user()->name;
                                $fecha = Carbon::now();
                                $numero = $id;
                                $correo = Auth::user()->email;
                                Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo)); 
                                return redirect()->route('despacho_transf')->with('success','Datos agregados con exito');
                            }
                            return redirect()->route('despacho_transf')->with('success','Datos agregados con exito');
                        }
                        return back()->with('error','¡Error en transferencia, notifique a soporte!');
                    //}
                    //return back()->with('error','La placa ingresada no pertenece al transporte asignado!');
                }
                return back()->with('error','¡Debe validar todos los productos antes de finalizar!');
            }
            return back()->with('error','¡Reporte este error a Sistemas!');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------- Funcion para ver los detalles de una transferencia ------------------------------------------------------------------------------
    public function ver_transferencia($id)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Reviso detalles de la transferencia'. $id;
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first();
        $existe = Transferencias::find($id);
        if($permiso == true && $existe == true || $existe == true && $existe->cod_unidad == Auth::user()->sucursal || 
        $existe == true && $existe->unidad_transf == Auth::user()->sucursal)
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacionSucursal, u.nombre, placa_vehiculo, iwe.nombre as estado,
            iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fechaEntrega, iwet.fechaUno,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, b.nombre as bodega,
            iwet.usuario, iwet.fechaSalida, iwet.usuarioSupervisa, iwet.fecha_paraCarga, iwet.observacion, us.name,
            iwet.fechaSucursal, iwet.fecha_entregado, iwet.observacionSup, iwet.observacionSucursal, iwet.cod_unidad,
            tv.propietario, iwet.erroresVerificados, iwet.observacionRevision, iwet.porcentaje, iwet.erroresVerificados,
            iwet.observacionSucursal, uni.nombre as usale, bod.nombre as bsale
            from inventario_web_encabezado_transferencias as iwet 
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_transF = b.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
            left join users as us on iwet.id_usuarioRecibe = us.id
            where iwet.num_movi = :id
            and u.empresa = iwet.empresa
            and u.empresa = b.empresa 
            and u.cod_unidad = b.cod_unidad
            and uni.empresa = iwet.empresa
            and uni.cod_unidad = bod.cod_unidad
            and bod.empresa = iwet.empresa',['id'=>$id]);
            foreach($tran as $t)
            {
                $ver = $t->id_estado;
            } 
            $integra = DB::select('select nombre 
            from inventario_web_bitacora_transferencias
            where num_movi = :id',['id'=>$id]);
            if($ver < 18)
            {
                $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidadSolicitada) as cantidad, 
                iwt.incluido, iwt.num_movi, ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen, 
                floor(iwt.costo) as costo
                from inventario_web_det_transferencias as iwt,
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
                return view('transferencias.verTransferencia',compact('tran','productos','id','integra','estados'));
            }
            elseif($ver >= 18 && $ver <= 19)
            {
                $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
                iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi, floor(iwt.costo) as costo,
                ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
                from inventario_web_det_transferencias as iwt,
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
                return view('transferencias.verTransferencia',compact('tran','productos','id','integra','estados'));
            }
            elseif($ver == 20)
            {
                $estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado - 1) as ide
                from inventario_web_encabezado_transferencias as iwet
                right join inventario_web_estados as iwe on iwe.id >= ide
                where iwet.id = :id
                and iwe.id > 18
                and iwe.id < 20',['id'=>$id]);

                $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidadRecibida) as cantidad, 
                iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,  iwt.noIncluido,
                floor(iwt.cantidad1) as cantidad1, iwt.cantidadSolicitada, floor(iwt.costo) as costo, iwt.mal_estado
                from inventario_web_det_transferencias as iwt,
                productos_inve as pi, 
                inventario_web_categorias as ic,
                where iwt.num_movi = :id
                and iwt.cod_producto = pi.cod_producto
                and iwt.empresa = pi.empresa
                and iwt.incluido = 1
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and ic.empresa = iwt.empresa
                order by ic.nombre asc',['id'=>$id]);
                return view('transferencias.verTransferenciaFinalizada',compact('tran','productos','id','integra','estados'));
            }
            elseif($ver == 23)
            {
                return view('transferencias.verTransferenciaAnulada',compact('tran','id'));
            }
            return redirect()->route('inicio_transferencias');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    function anular_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',34)->first())
        {
            $tran = Transferencias::findOrFail($id);
            if($tran->id_estado >= 18)
            {
                $tran->id_usuarioRecibe = Auth::id();
                $tran->id_estado = 23;
                $tran->fecha_entregado  = Carbon::now();
                $tran->fechaSucursal    = Carbon::now();
                $tran->updated_at       = Carbon::now();
                $tran->save();
                $eliminar = DB::select('select * 
                from inventario_web_det_transferencias 
                where num_movi = :id',['id'=>$id]);
                foreach($eliminar as $eli)
                {
                    $del = DetTransferencias::find($eli->id)->delete();
                }
                $delete = DetMovi::where('num_movi',$id)->where('cod_serie_movi','IW')->where('empresa',Auth::user()->empresa)->delete();
                return redirect()-> route('finalizadas_transf')->with('success','¡Transferencia anulada!');
            }
            return back()->with('error','¡No se permite realizar cambios!');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    public function editar_encabezado_transferencia_despachada(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',19)->first())
        {
            $tran = Transferencias::findOrFail($id);
            $historial  = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Modifico encabezado de transferencia despachada número'. $id;
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            if($tran->unidad_transf == Auth::user()->sucursal || $tran->cod_unidad == Auth::user()->sucursal)
            {
                $productos = DetTransferencias::where('num_movi',$id)->where('incluido',1)->count();
                $verificados = DetTransferencias::where('num_movi',$id)->where('verificadoSucursal',1)->count();
                if($productos != $verificados && $request->estado == 20)
                {
                    return back()->with('error','¡No se puede finalizar una transferencia sin validar lo recibido!');
                }
                elseif($productos == $verificados && $request->estado == 20)
                {
                    $porcentaje = DB::select('select convert(int,count(id)) as total, (select convert(int,count(id)) as bien
                    from inventario_web_det_transferencias,
                    where cantidad1 = cantidadRecibida
                    and num_movi = :id) as su
                    from inventario_web_det_transferencias
                    where incluido = 1
                    and num_movi = :idd',['id'=>$id,'idd'=>$id]);
                    foreach($porcentaje as $po)
                    {
                        if($po->su == 0)
                        {
                            $resultado = 0;
                        }
                        $resultado = ($po->su/$po->total)*100;
                    }
					if($tran->id_estado == 20)
                    {
                        return back()->with('error','¡Transferencia finalizada, no se permite realizar cambios!');
                    }
                    $permitir = DB::select('select count(*) as conteo 
                    from inventario_web_det_transferencias
                    where num_movi = :id 
                    and incluido = 1
                    and verificadoSucursal is null',['id'=>$id]);
                    foreach($permitir as $p)
                    {
                        $per = $p->conteo;
                    }
                    $hoy = Carbon::parse($tran->updated_at)->addHours(24);
                    if($request->estado == 20 && $per == 0 && $tran->porcentaje == null && $tran->erroresVerificados == null)
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->porcentaje           = number_format($resultado,2);
                        $tran->fecha_entregado      = Carbon::now();
						//$tran->placa_vehiculo    = $request->placa;
						//$tran->observacion       = $request->descripcion;
						//$tran->comentario        = $request->comentario;
                        $tran->save();
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTransferencias::find($eli->id)->delete();
                        }
                        return redirect()->route('finalizadas_transf')->with('success','¡Transferencia finalizada con exito!');
                    }
                    else if($request->estado == 20 && $per == 0 && $tran->porcentaje != null && $hoy > Carbon::now())
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->porcentaje           = number_format($resultado,2);
                        $tran->erroresVerificados   = 2;
                        $tran->opcionalDos          = 'S';
                        $tran->fecha_entregado      = Carbon::now();
						//$tran->placa_vehiculo    = $request->placa;
						//$tran->observacion       = $request->descripcion;
						//$tran->comentario        = $request->comentario;
                        $tran->save();
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTransferencias::find($eli->id)->delete();
                        }
                        return redirect()->route('finalizadas_transf')->with('success','¡Transferencia finalizada con exito!'); 
                    }
                    else if($request->estado == 20 && $per == 0 && $tran->porcentaje != null && $hoy < Carbon::now())
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->erroresVerificados   = 2;
                        $tran->opcionalDos          = 'S';
                        $tran->fecha_entregado      = Carbon::now();
						//$tran->placa_vehiculo    = $request->placa;
						//$tran->observacion       = $request->descripcion;
						//$tran->comentario        = $request->comentario;
                        $tran->save();
                        return redirect()->route('finalizadas_transf')->with('success','¡Tiempo para verificar sobrepasado, no se aplicaran los cambios!');
                    }
                    else if($request->estado == 20 && $per != 0)
                    {
                        return back()->with('error','Verifique todos los productos de la transferencia');
                    }
                    $tran->id_usuarioRecibe     = Auth::id();
                    $tran->fecha_modificacion   = Carbon::now();
                    $tran->id_estado            = $request->estado;
                    $tran->updated_at           = Carbon::now();
                    $tran->fechaSucursal        = Carbon::now();
                    $tran->fecha_entregado      = Carbon::now();
					//$tran->placa_vehiculo    = $request->placa;
					//$tran->observacion       = $request->descripcion;
					//$tran->comentario        = $request->comentario;
                    $tran->save();
                    return redirect()->route('despacho_transf')->with('success','¡Transferencia modificada con exito!');   
                }
                elseif($request->estado == 19)
                {
                    $tran->observacionSucursal  = $request->observacionSucursal;
                    $tran->id_usuarioRecibe = Auth::id();
                    $tran->id_estado         = $request->estado;
                    $tran->fecha_entregado      = Carbon::now();
					//$tran->placa_vehiculo    = $request->placa;
					//$tran->observacion       = $request->descripcion;
					//$tran->comentario        = $request->comentario;
                    $tran->save();
                    return redirect()->route('validad_tranf',$id)->with('success','Recuerda validar todos los productos de la transferencia');
                }
                $tran->observacionSucursal  = $request->observacionSucursal;
                $tran->id_usuarioRecibe = Auth::id();
                $tran->id_estado         = $request->estado;
                $tran->fecha_entregado      = Carbon::now();
				//$tran->placa_vehiculo    = $request->placa;
				//$tran->observacion       = $request->descripcion;
				//$tran->comentario        = $request->comentario;
                $tran->save();
                return back()->with('success','¡Transferencia modificada con exito!');
            }
            $tran = Transferencias::findOrFail($id);
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',16)->first();
            if($permiso == true)
            {
                $validator = Validator::make($request->all(),[
                    'descripcion'=>'required',
                    'comentario'=>'required',
                    'placa'=>'required',
                    'estado'=>'required|numeric',
                    'referencia'=>'required',
                    'observacionSucursal'=>'required',
                ]);
                if ($validator->fails()) 
                {
                    return back()
                    ->withErrors($validator)
                    ->withInput();
                }
                $productos = DetTransferencias::where('num_movi',$id)->where('incluido',1)->count();
                $verificados = DetTransferencias::where('num_movi',$id)->where('verificadoSucursal',1)->count();
                if($productos != $verificados && $request->estado == 20)
                {
                    return back()->with('error','¡No se puede finalizar una transferencia sin validar lo recibido!');
                }
                elseif($productos == $verificados && $request->estado == 20)
                {
                    $porcentaje = DB::select('select convert(int,count(id)) as total, (select convert(int,count(id)) as bien
                    from inventario_web_det_transferencias,
                    where cantidad1 = cantidadRecibida
                    and num_movi = :id) as su
                    from inventario_web_det_transferencias
                    where incluido = 1
                    and num_movi = :idd',['id'=>$id,'idd'=>$id]);
                    foreach($porcentaje as $po)
                    {
                        if($po->su == 0)
                        {
                            $resultado = 0;
                        }
                        $resultado = ($po->su/$po->total)*100;
                    }
                    if($tran->id_estado == 21)
                    {
                        return back()->with('error','¡Transferencia finalizada, no se permite realizar cambios!');
                    }
                    $permitir = DB::select('select count(*) as conteo 
                    from inventario_web_det_transferencias
                    where num_movi = :id 
                    and incluido = 1
                    and verificadoSucursal is null',['id'=>$id]);
                    foreach($permitir as $p)
                    {
                        $per = $p->conteo;
                    }
                    $hoy = Carbon::parse($tran->updated_at)->addHours(24);
                    if($request->estado == 20 && $per == 0 && $tran->porcentaje == null && $tran->erroresVerificados == null)
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->porcentaje           = number_format($resultado,2);
                        $tran->placa_vehiculo    = $request->placa;
                        $tran->comentario        = $request->comentario;
                        $tran->referencia        = $request->referencia;
                        $tran->fecha_entregado      = Carbon::now();
                        $tran->save();
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTransferencias::find($eli->id)->delete();
                        }
                        return redirect()->route('finalizadas_transf')->with('success','¡Transferencia finalizada con exito!');
                    }
                    else if($request->estado == 20 && $per == 0 && $tran->porcentaje != null && $hoy > Carbon::now())
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->porcentaje           = number_format($resultado,2);
                        $tran->erroresVerificados   = 2;
                        $tran->opcionalDos          = 'S';
                        $tran->placa_vehiculo    = $request->placa;
                        $tran->comentario        = $request->comentario;
                        $tran->referencia        = $request->referencia;
                        $tran->fecha_entregado      = Carbon::now();
                        $tran->save();
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTransferencias::find($eli->id)->delete();
                        }
                        return redirect()->route('finalizadas_transf')->with('success','¡Transferencia finalizada con exito!'); 
                    }
                    else if($request->estado == 20 && $per == 0 && $tran->porcentaje != null && $hoy < Carbon::now())
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->erroresVerificados   = 2;
                        $tran->opcionalDos          = 'S';
                        $tran->placa_vehiculo    = $request->placa;
                        $tran->comentario        = $request->comentario;
                        $tran->referencia        = $request->referencia;
                        $tran->fecha_entregado      = Carbon::now();
                        $tran->save();
                        return redirect()->route('finalizadas_transf')->with('success','¡Tiempo para verificar sobrepasado, no se aplicaran los cambios!');
                    }
                    else if($request->estado == 20 && $per != 0)
                    {
                        return back()->with('error','Verifique todos los productos de la transferencia');
                    }
                    $tran->id_usuarioRecibe     = Auth::id();
                    $tran->fecha_modificacion   = Carbon::now();
                    $tran->id_estado            = $request->estado;
                    $tran->updated_at           = Carbon::now();
                    $tran->fecha_entregado      = Carbon::now();
                    $tran->placa_vehiculo    = $request->placa;
                    $tran->comentario        = $request->comentario;
                    $tran->referencia        = $request->referencia;
                    $tran->save();
                    return redirect()->route('finalizadas_transf')->with('success','¡Transferencia modificada con exito!');   
                }
                elseif($request->estado == 19)
                {
                    $tran = Transferencias::findOrFail($id);
                    //$tran->usuario           = Auth::user()->name;
                    $tran->fecha_modificacion = Carbon::now();
                    //$tran->observacion       = $request->observacion;
                    $tran->observacion       = $request->descripcion;
                    $tran->placa_vehiculo    = $request->placa;
                    $tran->comentario        = $request->comentario;
                    $tran->referencia        = $request->referencia;
                    $tran->id_estado         = $request->estado;
                    $tran->updated_at        = Carbon::now();
                    $tran->observacionSucursal  = $request->observacionSucursal;
                    $tran->fecha_entregado      = Carbon::now();
                    //$tran->fecha_enCola      = Carbon::now();
                    $tran->save();
                    return redirect()->route('validad_tranf',$id)->with('success','Recuerda validar todos los productos de la transferencia');
                }
                $tran = Transferencias::findOrFail($id);
                //$tran->usuario           = Auth::user()->name;
                $tran->fecha_modificacion = Carbon::now();
                //$tran->observacion       = $request->observacion;
                $tran->observacion       = $request->descripcion;
                $tran->placa_vehiculo    = $request->placa;
                $tran->comentario        = $request->comentario;
                $tran->referencia        = $request->referencia;
                $tran->id_estado         = $request->estado;
                $tran->updated_at        = Carbon::now();
                $tran->observacionSucursal  = $request->observacionSucursal;
                $tran->fecha_entregado      = Carbon::now();
                //$tran->fecha_enCola      = Carbon::now();
                $tran->save();
                return back()->with('success','¡Transferencia modificada con exito!');
            }
            return redirect()->route('despacho_transf')->with('error','No tienes permisos para accesar');
        }
        return redirect()->route('despacho_transf')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Validar productos recibidos en la sucursal ------------------------------------------------------------------
    public function validar_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',20)->first())
        { 
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',16)->first();  
            $trans = Transferencias::findOrFail($id);
            if($trans->id_estado == 19 && $trans->cod_unidad == Auth::user()->sucursal ||$trans->id_estado == 19 && $trans->unidad_transf == Auth::user()->sucursal 
            || $trans->id_estado == 19 && $permiso == true)
            {
                $tran = DB::select('select num_movi, iwet.created_at, iwet.observacionSucursal, u.nombre, placa_vehiculo, iwe.nombre as estado,
                iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fechaEntrega, tv.propietario,
                iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
                iwet.usuario, iwet.fechaSalida, iwet.usuarioSupervisa, iwet.fecha_paraCarga, iwet.observacion,
                iwet.fechaSucursal, iwet.fecha_entregado, iwet.observacionSup, iwet.observacionSucursal,
                tv.propietario, iwet.erroresVerificados, iwet.updated_at, iwet.cod_unidad
                from inventario_web_encabezado_transferencias as iwet 
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                full join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
                where iwet.num_movi = :id
                and u.empresa = iwet.empresa',['id'=>$id]);

                foreach($tran as $t)
                {
                    $ver = $t->id_estado;
                }

                $integra = DB::select('select nombre 
                from inventario_web_bitacora_transferencias
                where num_movi = :id',['id'=>$id]);
                if($ver == 19)
                {
                    $estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado - 1) as ide
                    from inventario_web_encabezado_transferencias as iwet
                    right join inventario_web_estados as iwe on iwe.id >= ide
                    where iwet.id = :id
                    and iwe.id > iwet.id_estado
                    and iwe.id < 21',['id'=>$id]);
                    if($trans->cod_unidad == 27 && $trans->cod_bodega == 3 && Auth::user()->roles != 3)
                    {
                        return view('transCompras.validarTransferencia',compact('tran','id','integra','estados'));
                    }
                    return view('sucursales.EditarTransferencia',compact('tran','id','integra','estados'));
                }
            }
            return redirect()->route('finalizadas_transf')->with('error','Solo es posible validar transferencias en Sucursal');
        }
        return redirect()->route('finalizadas_transf')->with('error','No tienes permisos para accesar');
    }

    public function imagenes_de_transferencia($id)
    {
        $imagenes = DB::table('inventario_web_imagenes_transferencias')->where('num_movi',$id)->orderBy('fecha','asc')->get();
        $transferencia = Transferencias::find($id);
        return view('transferencias.imagenes',compact('imagenes','id','transferencia'));
    }

    public function guardar_imagen_transferencia(Request $request,$id)
    {
        $transferencia = Transferencias::find($id);
        $guardarImagen = new ImagenesTransferencias();
        $guardarImagen->num_movi = $transferencia->num_movi;
        $guardarImagen->cod_serie_movi = $transferencia->cod_serie_movi;
        $guardarImagen->empresa = $transferencia->empresa;
        $guardarImagen->id_usuario = Auth::user()->id;
        $guardarImagen->fecha = Carbon::now();
        $guardarImagen->descripcion = $request->descripcion;
        if($request->file('imagen'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('imagen'));
          $guardarImagen->fill(['imagen'=>$url])->save();
        }
        $guardarImagen->save();
        //$ruta = collect(explode('/',$url))->last();
        $real_path = public_path('/'.$url);
        Image::make($real_path)
                ->resize(1280, null, function ($constraint){
                    $constraint->aspectRatio();
                })
                ->save($real_path,72);

        return back()->with('success','Imagen agregada correctamente');
    }

    function editar_descripcion_imagen(Request $request,$id)
    {
        $editar = ImagenesTransferencias::find($id);
        $editar->descripcion = $request->descripcion;
        $editar->save();
        return back()->with('success','¡Editado correctamente!');
    }

    public function listado_en_transferencia($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first();
        $existe = Transferencias::find($id);
        if($permiso == true && $existe == true)
        {
            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',16)->first())
            {
                $productos = DB::select("select idt.id, idt.cod_producto, ic.nombre, pi.nombre_corto, pi.nombre_fiscal, convert(integer,idt.cantidadSolicitada) as cantidadSolicitada, 
                floor(idt.cantidad1) as cantidad1, ((pi.peso*0.453592) * idt.cantidadSolicitada) as peso, 
                (pi.factor_a_unidad_basica * idt.cantidadSolicitada) as volumen, idt.verificadoCarga,
                (cast((pi.peso*0.453592) as decimal(4,2)) * coalesce(convert(integer,idt.cantidad1),0)) as peso2, 
                pi.factor_a_unidad_basica * coalesce(idt.cantidad1,0) as volumen2, idt.mal_estado
                from inventario_web_det_transferencias as idt,
                productos_inve as pi,
                inventario_web_categorias as ic
                where idt.num_movi = :id
                and idt.incluido = 1
                and idt.cod_producto = pi.cod_producto
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and pi.empresa = idt.empresa
                and ic.empresa = idt.empresa",['id'=>$id]);
                return $productos;
                foreach($tran as $t)
                {
                    $per = $t->id_estado;
                }
                if($per == 17)
                {
                    return $productos;
                }
                return error;
            }
            $productos = DB::select("select idt.id, idt.cod_producto, ic.nombre, pi.nombre_corto, pi.nombre_fiscal, convert(integer,idt.cantidadSolicitada) as cantidadSolicitada, 
            floor(idt.cantidad1) as cantidad1, ((pi.peso*0.453592) * idt.cantidadSolicitada) as peso, 
            (pi.factor_a_unidad_basica * idt.cantidadSolicitada) as volumen, idt.verificadoCarga,
            (cast((pi.peso*0.453592) as decimal(4,2)) * coalesce(convert(integer,idt.cantidad1),0)) as peso2, 
            pi.factor_a_unidad_basica * coalesce(idt.cantidad1,0) as volumen2
            from inventario_web_det_transferencias as idt,
            productos_inve as pi,
            inventario_web_categorias as ic
            where idt.num_movi = :id
            and idt.incluido = 1
            and idt.cod_producto = pi.cod_producto
            and pi.cod_tipo_prod = ic.cod_tipo_prod
            and pi.empresa = idt.empresa
            and ic.empresa = idt.empresa",['id'=>$id]);
            return $productos;
            foreach($tran as $t)
            {
                $per = $t->id_estado;
            }
            if($per == 17)
            {
                return $productos;
            }
            return error;
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function confirmar_producto_transferencia(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first())
        {
            $verificado = DetTransferencias::findOrFail($request->id);
            $verificado->cantidad1     = $request->cantidad1; 
            $verificado->verificadoCarga    = 1;
            $verificado->updated_at         = new Carbon();
            $verificado->save();
            return $verificado;
        }
        return error;
    }

    public function detalles_del_codigo(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first())
        {
            $producto = collect(DB::select("select idt.id, replace(pi.nombre_corto,'ñ','N') as nombre_corto, pi.nombre_fiscal, convert(integer, idt.cantidadSolicitada), 
            floor(idt.cantidad1) as cantidad1, convert(integer,inv.existencia1) as existencia1
            from inventario_web_det_transferencias as idt,
            productos_inve as pi,
            inventarios as inv 
            where idt.id = :id
            and idt.cod_producto = pi.cod_producto
            and idt.cod_producto = inv.cod_producto
            and idt.cod_unidad = inv.cod_unidad
            and idt.cod_bodega = inv.cod_bodega
            and idt.empresa = inv.empresa
            and pi.empresa = idt.empresa",['id'=>$request->id]));
            $task = '';
            if($producto == true)
            {
                foreach($producto as $pro)
                {
                    $task = ['cantidad1'=>$pro->cantidad1,'nombre_corto'=>$pro->nombre_corto.' '.$pro->nombre_fiscal,'cantidadSolicitada'=>$pro->cantidadSolicitada,
                    'existencia1'=>$pro->existencia1];
                }
                return $task;
                //Esta función devolverá los datos de una tarea que hayamos seleccionado para cargar el formulario con sus datos2
            }
            $task = ['cantidad1'=>0,'nombre_corto'=>"",'cantidadSolicitada'=>0,
            'existencia1'=>0];
            return $task;
        }
        return error;
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función que permite al usuario encargado de verificar transferencias finalizadas cambiar a estado en sucursal -------------------
    public function regresar_a_sucursal($id)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Habilito de edición para la sucursal de la transferencia número'. $id;
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',21)->first())
        { 
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',16)->first();  
            $tran = Transferencias::findOrFail($id);
            if($tran->id_estado == 20 && $tran->cod_unidad == Auth::user()->sucursal ||$tran->id_estado == 20 && $tran->unidad_transf == Auth::user()->sucursal 
            || $tran->id_estado == 20 && $permiso == true)
            {
                $tran = Transferencias::find($id);
                if($tran->erroresVerificados == null)
                {
                    $tran->id_estado = 19;
                    $tran->updated_at = Carbon::now();
                    $tran->save();
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$tran->unidad_transf)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $estado = 'En sucursal';
                        $numero = $id;
                        Mail::to($user)->send(new Sucursal($usuario,$fecha,$estado,$numero));
                        return back()->with('success','¡Estado de transferencia modificado!');
                    }
                    return back()->with('success','¡Estado de transferencia modificado!');
                }
                return back()->with('error','¡No se permite realizar esta acción!');
            }
            return redirect()->route('finalizadas_transf')->with('error','No tienes permisos para accesar');
        }
        return redirect()->route('finalizadas_transf')->with('error','No tienes permisos para accesar'); 
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para marcar de verificada una transferencia ---------------------------------------------------------
    public function revision_de_transferencia(Request $request,$id)
    {
        $verificar = Transferencias::FindOrFail($id);
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',22)->first())
        { 
            $historial  = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Genero la revisión de la transferencia número'. $id;
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $verificar = Transferencias::FindOrFail($id);
            if($verificar->erroresVerificados == null)
            {
                $verificar->erroresVerificados = 1;
            }
            $verificar->observacionRevision = $request->observaciones;
            $verificar->updated_at          = Carbon::now();
            $verificar->save();
            return back()->with('success','¡Se han guardados los cambios!');
        }
        return redirect()->route('finalizadas_transf')->with('error','No tienes permisos para accesar'); 
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el listado de sucursales para transferencias ---------------------------------------------
    function listado_sucursales_minimo_maximo()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            return view('transferencias.sucursales');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_listado_sucursales_minmax()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $sucursales = user::where('roles',3)->where('empresa',Auth::user()->empresa)->get();
            return DataTables($sucursales)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function existencia_productos($sucursal,$bodega,$todo)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $sucu = DB::table('unidades')->where('cod_unidad',$sucursal)->where('empresa',Auth::user()->empresa)->first();
            $bod = DB::table('bodegas')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$sucursal)->where('cod_bodega',$bodega)->first();
            $historial = new Historial();
            $historial->id_usuario = Auth::user()->id;
            $historial->actividad = 'Consulta Existencia de la sucursal'.' '.$sucu->nombre.' '.'Bodega'.' '.$bod->nombre;
            $historial->created_at = Carbon::now();
            $historial->updated_at = Carbon::now();
            $historial->save();
            if($todo == '1')
            {
                return view('transferencias.sucursal_existencia',compact('sucu','bod','sucursal','bodega'));
            }
            elseif($todo == '2')
            {
                return view('transferencias.sucursal_existencia_minimo',compact('sucu','bod','sucursal','bodega'));
            }
            return view('transferencias.sucursal_existencia_reorden',compact('sucu','bod','sucursal','bodega'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_existencia($sucursal,$bodega)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $datos = DB::select('select i.cod_producto as cod_producto,i.existencia1 as existencia,
            pi.nombre_fiscal as nom_producto,pi.nombre_corto as nom_corto,
            u.nombre as su_nombre,b.nombre as bo_nombre, i.minimo as min,i.maximo as max,
            iwc.nombre as cod_tipo_prod, i.piso_sugerido as reorden,
            (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, u.cod_unidad, b.cod_bodega,
            ((existencia/max)*100) porcentaje 
            from inventarios as i,
            unidades as u,
            bodegas as b,
            productos_inve as pi, 
            inventario_web_categorias as iwc 
            where i.cod_unidad = :suc
            and i.cod_bodega = :bod
            and i.minimo >= 1
            and i.cod_producto = pi.cod_producto
            and i.cod_unidad = u.cod_unidad
            and i.cod_bodega = b.cod_bodega
            and i.cod_unidad = b.cod_unidad
            and pi.cod_tipo_prod = iwc.cod_tipo_prod
            and pi.descontinuado = :des
            and pi.cod_tipo_prod != :servicios 
            and i.empresa = :empresa
            and pi.empresa = i.empresa
            and iwc.empresa = i.empresa
            and u.empresa = i.empresa
            and b.empresa = i.empresa
            order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega,'empresa'=>Auth::user()->empresa]);
            return DataTables::of($datos)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_existencia_minimos($sucursal,$bodega)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $datos = DB::select('select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
            productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
            unidades.nombre as su_nombre,bodegas.nombre as bo_nombre, inventarios.minimo as min,inventarios.maximo as max,
            inventario_web_categorias.nombre as cod_tipo_prod, inventarios.piso_sugerido as reorden,
            (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, unidades.cod_unidad, bodegas.cod_bodega,
            ((existencia/max)*100) porcentaje 
            from inventarios
            join unidades on unidades.cod_unidad = inventarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            join productos_inve on productos_inve.cod_producto = inventarios.cod_producto 
            join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
            where inventarios.cod_unidad = :suc
            and inventarios.cod_bodega = :bod
            and inventarios.minimo <> 0
            and descontinuado = :des
            and existencia <= min
            and unidades.empresa = :empresa
            and bodegas.empresa = unidades.empresa
            and productos_inve.empresa = unidades.empresa
            and inventario_web_categorias.empresa = unidades.empresa
            and productos_inve.cod_tipo_prod <> :servicios
            group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia,
            unidades.cod_unidad, bodegas.cod_bodega
            order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega,'empresa'=>Auth::user()->empresa]);
            return DataTables::of($datos)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_existencia_maximos($sucursal,$bodega)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $datos = DB::select('select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
            productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
            unidades.nombre as su_nombre,bodegas.nombre as bo_nombre, inventarios.minimo as min,inventarios.maximo as max,
            inventario_web_categorias.nombre as cod_tipo_prod, inventarios.piso_sugerido as reorden,
            (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, unidades.cod_unidad, bodegas.cod_bodega,
            ((existencia/max)*100) porcentaje
            from inventarios
            join unidades on unidades.cod_unidad = inventarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            join productos_inve on productos_inve.cod_producto = inventarios.cod_producto 
            join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
            where inventarios.cod_unidad = :suc
            and inventarios.cod_bodega = :bod
            and inventarios.minimo <> 0
            and descontinuado = :des
            and unidades.empresa = :empresa
            and bodegas.empresa = unidades.empresa
            and productos_inve.empresa = unidades.empresa
            and inventario_web_categorias.empresa = unidades.empresa
            and existencia between min and reorden
            and productos_inve.cod_tipo_prod <> :servicios
            group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia,
            unidades.cod_unidad, bodegas.cod_bodega
            order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega,'empresa'=>Auth::user()->empresa]);
            return DataTables::of($datos)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function graficado($sucursal,$bodega,$producto)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $mesd = Carbon::now();
            $fecha_b = Carbon::now();
            $mesu = $fecha_b->subMonths(6);
            $prod = ProductoSemana::where('cod_producto',$producto)->first();
            $pro = $prod->nombre_fiscal;
            $datos = DB::select('select unidades.nombre as sucursal,unidades.cod_unidad as cod_unidad,bodegas.observacion as bodega,bodegas.cod_bodega as cod_bodega,
            bodegas.Cod_Cliente as Cod_Cliente,inventarios_diarios.cod_producto,inventarios_diarios.cantidad as existencia,inventarios_diarios.day as dia,
            inventarios_diarios.minimo as min,inventarios_diarios.maximo as max,inventarios_diarios.fecha as fecha,inventarios_diarios.reorden as reorden,
            inventario_web_productos_semana.nombre_fiscal as nombre_fiscal
            from inventarios_diarios
            join unidades on unidades.cod_unidad = inventarios_diarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios_diarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            join inventario_web_productos_semana on inventarios_diarios.cod_producto = inventario_web_productos_semana.cod_producto
            where inventarios_diarios.cod_producto = :prod
            and inventarios_diarios.cod_unidad = :suc
            and inventarios_diarios.cod_bodega = :bod
            and unidades.empresa = :empresa
            and bodegas.empresa = unidades.empresa
            and fecha between :mesu and :mesd
            group by inventarios_diarios.day,inventarios_diarios.month,unidades.nombre,bodegas.observacion,inventarios_diarios.cod_producto,
            inventarios_diarios.cantidad,min,max,fecha,reorden,
            cod_unidad,cod_bodega,Cod_Cliente,nombre_fiscal
            order by cod_unidad asc,cod_bodega asc,fecha asc',['suc'=>$sucursal,'bod'=>$bodega,'prod'=>$producto,'mesu'=>$mesu,
            'mesd'=>$mesd,'empresa'=>Auth::user()->empresa]);
            $greorden["chart"] = array("type" => "line");
            $min = [];
            $max = [];
            $existencia = [];
            $dias = [];
            $reorden = [];
            $suc = '';
            $promedio = 0;
            $total = 0;
            foreach($datos as $d)
            {
                $min[] =  (floatval($d->min));
                $max[] =  (floatval($d->max));
                $existencia[] =  (floatval($d->existencia));
                $dias[] = [date('d/m/y',strtotime($d->fecha))];
                $reorden[] = (floatval($d->reorden));
                $suc = [$d->sucursal.' '.$d->bodega.', '.$d->nombre_fiscal];
                $total = $total + 1;
            }
            $greorden["title"] = array("text" => $suc);
            $greorden["xAxis"] = array("categories" => $dias);
            $greorden["yAxis"] = array("title" => array("text" => "Min y Max"));
            $greorden["series"] = [
                array("name" => "Min", "data" => $min,"color"=>"red"),
                array("name" => "Max", "data" => $max,"color"=>"black"),
                array("name" => "Existencia", "data" => $existencia,"color"=>"green"),
                array("name" => "Reorden", "data" => $reorden,"color"=>"orange")
                ];
            //return response()->json($yourFirstChart);
            return view('reportes.grafica_ex', compact('greorden','pro'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function existencia_productos_categoria($sucursal,$bodega,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $sucu = DB::table('unidades')->where('cod_unidad',$sucursal)->where('empresa',Auth::user()->empresa)->first();
            $bod = DB::table('bodegas')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$sucursal)->where('cod_bodega',$bodega)->first();
            foreach($request->producto as $key =>$value)
            {
                $cate = Crypt::encryptString($request->producto[$key]);
            }
            return view('transferencias.sucursal_categoriaExistencia',compact('sucu','bod','sucursal','bodega','cate'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_categoria($sucursal,$bodega,$cate)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $datos = DB::select('select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
            productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
            unidades.nombre as su_nombre,bodegas.nombre as bo_nombre, inventarios.minimo as min,inventarios.maximo as max,
            inventario_web_categorias.nombre as cod_tipo_prod, inventarios.piso_sugerido as reorden,
            (existencia - max) as baj_max, (existencia - reorden) as baj_reorden, unidades.cod_unidad, bodegas.cod_bodega,
            ((existencia/max)*100) porcentaje 
            from inventarios
            join unidades on unidades.cod_unidad = inventarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            join productos_inve on productos_inve.cod_producto = inventarios.cod_producto 
            join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
            where inventarios.cod_unidad = :suc
            and inventarios.cod_bodega = :bod
            and inventarios.minimo <> 0
            and descontinuado = :des
            and inventario_web_categorias.empresa = :empresa
            and unidades.empresa = inventario_web_categorias.empresa
            and bodegas.empresa = inventario_web_categorias.empresa
            and productos_inve.empresa = inventario_web_categorias.empresa
            and productos_inve.cod_tipo_prod <> :servicios
            and productos_inve.cod_tipo_prod = :cate
            group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia,
            unidades.cod_unidad, bodegas.cod_bodega
            order by cod_tipo_prod asc,cod_producto asc',
            ['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega,'cate'=>Crypt::decryptString($cate),'empresa'=>Auth::user()->empresa]);
            return DataTables::of($datos)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para crear una nueva transferencia a una sucursal -----------------------------------------------------------------------
    public function crear_transferencia(Request $request, $sucursal,$bodega)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first())
        {
            $tran = new Transferencias();
            $tran->empresa               = Auth::user()->empresa;
            $tran->cod_tipo_movi         = 'E';
            $tran->cod_serie_movi        = 'IW';
            $tran->cod_unidad            = Auth::user()->sucursal;
            $tran->cod_bodega            = Auth::user()->bodega; 
            $tran->fecha                 = Carbon::now();
            $tran->fecha_opera           = Carbon::now();
            $tran->impreso               = 'N';
            $tran->cod_motivo            = 95;
            $tran->Clasif_transferencia  = 1;
            $tran->unidad_transf         = $sucursal;
            $tran->bodega_Transf         = $bodega;
            $tran->usuario               = Auth::user()->name;
            $tran->fecha_modificacion    = Carbon::now();
            //$tran->observacion         = $request->observacion;
            $tran->observacion           = $request->descripcion;
            //$tran->placa_vehiculo      = $request->placa;
            //$tran->comentario          = $request->comentario;
            //$tran->referencia          = $request->referencia;
            $tran->id_estado             = 13;
            $tran->created_at            = Carbon::now();
            $tran->updated_at            = Carbon::now();
            $tran->fecha_paraCarga       = Carbon::parse($request->fechaCarga);
            $tran->fechaEntrega          = $request->fechaEntrega;
            if($tran->save())
            {
                $id_n = $tran->created_at;
                $id = Transferencias::where('created_at',$id_n)->first();
                $todos_productos =DB::select('select i.cod_producto as cod_producto,i.existencia1 as existencia,
                pi.nombre_fiscal as nom_producto,pi.nombre_corto as nom_corto,
                u.nombre as su_nombre,b.nombre as bo_nombre, i.minimo as min,i.maximo as max,
                iwc.nombre as cod_tipo_prod, i.piso_sugerido as reorden,
                (max - existencia) as baj_max, (existencia - reorden) as baj_reorden, u.cod_unidad, b.cod_bodega,
                ((existencia/max)*100) porcentaje 
                from inventarios as i,
                unidades as u,
                bodegas as b,
                productos_inve as pi, 
                inventario_web_categorias as iwc 
                where i.cod_unidad = :suc 
                and i.cod_bodega = :bod
                and i.minimo >= 1
                and i.cod_producto = pi.cod_producto
                and i.cod_unidad = u.cod_unidad
                and i.cod_bodega = b.cod_bodega
                and i.cod_unidad = b.cod_unidad
                and pi.cod_tipo_prod = iwc.cod_tipo_prod
                and pi.descontinuado = :des
                and pi.cod_tipo_prod != :servicios 
                and i.empresa = :empresa
                and pi.empresa = i.empresa 
                and iwc.empresa = i.empresa
                and u.empresa = i.empresa
                and b.empresa = i.empresa
                order by cod_tipo_prod asc,cod_producto asc',['des'=>'N','servicios'=>'servicios','suc'=>$sucursal,'bod'=>$bodega,'empresa'=>Auth::user()->empresa]);
                foreach ($todos_productos as $nuevo) 
                {
                    $detTran                        = new DetTransferencias();
                    $detTran->empresa               = Auth::user()->empresa;
                    $detTran->cod_tipo_movi         = 'E';
                    $detTran->cod_serie_movi        = 'IW';
                    $detTran->num_movi              = $id->id;
                    $detTran->cod_producto          = $nuevo->cod_producto;
                    $detTran->cod_unidad            = Auth::user()->sucursal;
                    $detTran->cod_bodega            = Auth::user()->bodega;
                    $detTran->cantidadSolicitada    = $nuevo->baj_max;
                    //$detTran->costo               = 0;
                    $detTran->orden                 = 0;
                    $detTran->operado_stamp         = Carbon::now();
                    $detTran->clasif_Transferencia  = 1;
                    $detTran->unidad_transf         = $sucursal;
                    $detTran->bodega_transf         = $bodega;
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
                    $detTran->save();
                }
                $historial              = new Historial();
                $historial->id_usuario  = Auth::id();
                $historial->actividad   = 'Se genero una nueva transferencia';
                $historial->created_at  = new Carbon();
                $historial->updated_at  = new Carbon();
                $historial->save();
            }
            return redirect()->route('agre_datos_transf',['id'=>$id])->with('success','¡Se ha creado una nueva orden de carga!');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para generar un documento en PDF de la transferencia -------------------------------------------------------------------- 
    public function imprimir_pdf($id)
    {
        $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo,
        iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
        iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
        iwet.usuarioSupervisa, iwet.fechaSalida, iwet.fechaEntrega, iwet.fecha_entregado, iwet.fechaUno,
        tv.propietario, iwet.observacionSucursal, b.nombre as bodega, iwe.nombre as estado
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as b on iwet.bodega_Transf = b.cod_bodega 
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
        where iwet.num_movi = :id
        and u.empresa = iwet.empresa
        and u.cod_unidad = b.cod_unidad
        and b.empresa = iwet.empresa',['id'=>$id]);

        $per = Transferencias::find($id);

        if($per->id_estado >= 13 && $per->id_estado <= 17)
        {
            $productos = DB::select('select  pi.nombre_corto, pi.nombre_fiscal, floor(iwt.cantidadSolicitada) as cantidad, 
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen, iwt.costo, iwt.id_superviso
            from inventario_web_det_transferencias as iwt,
            productos_inve as pi, 
            where iwt.num_movi = :id
            and iwt.cod_producto = pi.cod_producto
            and iwt.empresa = pi.empresa
            and iwt.incluido = 1
            order by pi.nombre_corto asc',['id'=>$id]);
            $pdf = PDF::loadView('transferencias.imprimir',compact('tran','productos'));
            return $pdf->stream($per->num_movi.'IW.pdf');
        }
        elseif($per->id_estado >= 18 && $per->id_estado <= 19)
        {
            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi, iwt.costo, iwt.id_superviso,
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
            from inventario_web_det_transferencias as iwt,
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
            $pdf = PDF::loadView('transferencias.imprimirF',compact('tran','productos'));
            return $pdf->download($per->num_movi.'IW.pdf');
        }
        elseif($per->id_estado == 20)
        {
            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidadRecibida) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi, iwt.costo, iwt.id_superviso,
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen,
            iwt.cantidadSolicitada, iwt.cantidad1
            from inventario_web_det_transferencias as iwt,
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
            $pdf = PDF::loadView('transferencias.imprimirF',compact('tran','productos'));
            return $pdf->download($per->num_movi.'IW.pdf');
        }
        return back()->with('error','No se permite imprimir transferencias vacias');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para generar un documento en PDF de un grupo de transferencias ---------------------------------------------------------- 
public function imprimir_pdf_grupo($id)
{
    $grupo = Transferencias::find($id);
    $tran = DB::select('select num_movi, iwet.observacion, tv.nombre,
    iwet.comentario, iwet.referencia, iwe.nombre as estado, iwet.fecha_paraCarga, 
    iwet.fechaEntrega, b.nombre as bodega
    from inventario_web_encabezado_transferencias as iwet
    join unidades as u on iwet.unidad_transf = u.cod_unidad
    join bodegas as b on iwet.bodega_Transf = b.cod_bodega 
    join inventario_web_estados as iwe on iwet.id_estado = iwe.id
    left join T_Flotas as tv on iwet.placa_vehiculo = tv.Codigo
    where iwet.grupo = :grupo 
    and u.empresa = iwet.empresa
    and u.cod_unidad = b.cod_unidad
    and b.empresa = iwet.empresa
    and id_estado <= 17',['grupo'=>$grupo->grupo]);

    if($grupo->id_estado >= 13 && $grupo->id_estado <= 17)
    {
        $productos = DB::select('select  pi.nombre_corto, pi.nombre_fiscal, floor(sum(iwt.cantidadSolicitada)) as cantidad, 
        ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen, iwt.costo, iwt.id_superviso
        from inventario_web_det_transferencias as iwt,
        inventario_web_encabezado_transferencias as iwet,
        productos_inve as pi, 
        where iwet.grupo = :grupo
        and iwt.cod_producto = pi.cod_producto
        and iwt.num_movi = iwet.num_movi
        and iwt.empresa = pi.empresa
        and iwt.incluido = 1
        and iwet.id_estado <= 17
        group by pi.nombre_corto, pi.nombre_fiscal, pi.peso, pi.factor_a_unidad_basica, iwt.costo
        order by pi.nombre_corto asc',['grupo'=>$grupo->grupo]);
        $pdf = PDF::loadView('transferencias.imprimirGrupo',compact('tran','productos'));
        return $pdf->stream($grupo->num_movi.'IW.pdf');
    }
    return back()->with('error','No se permite imprimir en formato de grupo');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------- Funciones para ver el listado de usuarios para la aplicación de transferencias --------------------------------------------------
    public function listado_usuarios_transferencias()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',24)->first())
        {
            $integrantes = DB::select('select iwl.id, iwl.nombre, u.name , u.id as gru
            from inventario_web_listadoGrupos as iwl
            left join users as u on iwl.idGrupo = u.id');
            $grupos = User::where('roles',17)->get(); 
            return view('transferencias.listadoUsuarios',compact('integrantes','grupos'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function guardar_nuevo_usuario_grupo(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',24)->first())
        {
            $nuevo = new GrupoUsuario();
            $nuevo->nombre      = $request->nombre;
            $nuevo->idGrupo     = $request->id;
            $nuevo->created_at  = new Carbon();
            $nuevo->save();
            return back()->with('success','¡Nuevo integrante agregado con exito!');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function editar_usuario_grupo(Request $request, $id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',24)->first())
        {
            $editar             = GrupoUsuario::findOrFail($id);
            $editar->nombre     = $request->nombre;
            $editar->idGrupo    = $request->id;
            $editar->updated_at = new Carbon();
            $editar->save();
            return back()->with('success','¡Integrante de grupo modificado con exito!');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

public function editar_estado_de_transferencia_en_bodega(Request $request,$id)
    {
        if(Auth::user()->roles == 17)
        {
            $finalizar = Transferencias::find($id);
            $estado = Estado::find($request->estado);
            if($finalizar->id_estado >= 18)
            {
                return redirect()->route('bod_trasf_bod')->with('error','¡Orden en camino o finalizada, no es posible realizar cambios!');
            }
            elseif($request->estado == 13)
            {
                return back()->with('error','No tienes permiso para realizar este cambio');
            }
            elseif($request->estado == 14 && $finalizar->cod_unidad == Auth::user()->sucursal)
            {
                return back()->with('success','No se realizo ningun cambio de estado');
            }
            elseif($request->estado == 15 && $finalizar->cod_unidad == Auth::user()->sucursal)
            {
                $finalizar->fechaUno = Carbon::now();
                $finalizar->id_estado = $request->estado;
                $finalizar->updated_at = Carbon::now();
                $finalizar->save();
                $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                if($permiso == true)
                {
                    $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                    $usuario = Auth::user()->name;
                    $fecha = Carbon::now();
                    $numero = $id;
                    Mail::to($user)->send(new NotificacionBodega($usuario,$fecha,$estado->nombre,$numero));
                    return back()->with('success','Datos agregados con exito');
                }
                else 
                {
                    return back()->with('success','Datos agregados con exito');
                }
            }
            elseif($request->estado == 16 && $finalizar->cod_unidad == Auth::user()->sucursal)
            {
                $finalizar->fecha_enCarga = Carbon::now();
                $finalizar->id_estado = $request->estado;
                $finalizar->updated_at = Carbon::now();
                $finalizar->save();
                $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                if($permiso == true)
                {
                    $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                    $usuario = Auth::user()->name;
                    $fecha = Carbon::now();
                    $numero = $id;
                    Mail::to($user)->send(new NotificacionBodega($usuario,$fecha,$estado->nombre,$numero));
                    return back()->with('success','Datos agregados con exito');
                }
                else 
                {
                    return back()->with('success','Datos agregados con exito');
                }
            }
            elseif($request->estado == 17 && $finalizar->cod_unidad == Auth::user()->sucursal)
            {
                if(Auth::user()->empresa == 1)
                {
                    $finalizar->fecha_cargado   = Carbon::now();
                    $finalizar->id_estado       = $request->estado;
                    $finalizar->updated_at      = Carbon::now();
                    $finalizar->save();
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $numero = $id;
                        Mail::to($user)->send(new NotificacionBodega($usuario,$fecha,$estado->nombre,$numero));
                        return back()->with('success','Datos agregados con exito');
                    }
                }
                elseif(Auth::user()->empresa == 2)
                {
                    $finalizar->fechaUno = Carbon::now();
                    $finalizar->fecha_enCarga = Carbon::now();
                    $finalizar->fecha_cargado   = Carbon::now();
                    $finalizar->id_estado       = $request->estado;
                    $finalizar->updated_at      = Carbon::now();
                    $finalizar->save();
                    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                    {
                        $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                        $usuario = Auth::user()->name;
                        $fecha = Carbon::now();
                        $numero = $id;
                        Mail::to($user)->send(new NotificacionBodega($usuario,$fecha,$estado->nombre,$numero));
                        return back()->with('success','Datos agregados con exito');
                    }
                }
                return back()->with('success','Datos agregados con exito');
            }
            return redirect()->route('bod_trasf_bod')->with('error','No se realizo ningun cambio de estado');
        }
        return redirect()->route('bod_trasf_bod')->with('error','No tienes permisos para accesar');
    }

    public function busqueda_existencias(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',23)->first();
        if($permiso == true)
        {
            $sucu = $request->sucu;
            $bod = $request->bod;
            $ayer = Carbon::yesterday();
            $hoy = Carbon::now();
            $sucursal = DB::table('unidades')->where('cod_unidad',$sucu)->where('empresa',Auth::user()->empresa)->first();
            $bodega = DB::table('bodegas')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$sucu)->where('cod_bodega',$bod)->first();
            $datos = DB::select("select inventarios.cod_producto as cod_producto,inventarios.existencia1 as existencia,
            inventario_web_productos_semana.nombre_fiscal as nom_producto,
            inventario_web_productos_semana.nombre_corto as nom_corto,unidades.nombre as su_nombre,bodegas.nombre as bo_nombre,
            inventarios.minimo as min,inventarios.maximo as max,inventario_web_productos_semana.cod_tipo_prod as cod_tipo_prod,
            inventarios.piso_sugerido as reorden
            from inventarios
            join unidades on unidades.cod_unidad = inventarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            join inventario_web_productos_semana on inventario_web_productos_semana.cod_producto = inventarios.cod_producto and inventario_web_productos_semana.cod_tipo_prod <> 'servicios'
            join productos_inve on productos_inve.cod_producto = inventario_web_productos_semana.cod_producto 
            where inventarios.cod_unidad = suc
            and inventarios.cod_bodega = bod
            and inventarios.minimo <> 0
            and unidades.empresa = :empresa
            and bodegas.empresa = unidades.empresa
            and productos_inve.empresa = unidades.empresa
            and(inventario_web_productos_semana.cod_tipo_prod like '%'+cod+'%')
            group by inventarios.cod_producto,nom_producto,nom_corto,su_nombre,cod_tipo_prod,bo_nombre,min,max,reorden,existencia
            order by cod_tipo_prod asc,cod_producto asc",
            [$request->sucu,$request->bod,$request->cod,'empresa'=>Auth::user()->empresa]);
            return view('reportes.sucursal_busqueda',compact('datos','sucu','bod','sucursal','bodega'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

//-------------------------------------------------------------------------------------------------------------------------------------------------------------  

//--------------------------- Ni idea pero para algo tiene que servir -----------------------------------------------------------------------------------------
    public function finalizar_revision_transferencia($id)
    {
        if(Auth::user()->roles == 16)
        {
            $finalizar = Transferencias::find($id);
            $grupos = User::where('roles',17)->where('sucursal',Auth::user()->sucursal)->get();
            return view('suptransferencia.revisarTransferencia',compact('id','finalizar','grupos'));
        }
        else if(Auth::id() == 44)
        {
            $finalizar = Transferencias::find($id);
            $grupos = User::where('roles',17)->get();
            return view('suptransferencia.revisarTransferencia',compact('id','finalizar','grupos'));
        }
        return redirect()->route('home');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite la busqueda de placas para asignar a las transferencias -----------------------------------------------------
    public function placas(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = DB::table('T_Vehiculos')->where('propietario','like','%'. $term .'%')->where('n_motor','!=','')
        ->orwhere('Placa','like','%'. $term .'%')->limit(10)->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            $formatted_tags[] = ['id' => utf8_encode($tag->Placa), 'text' => utf8_encode($tag->Placa).' '.utf8_encode($tag->propietario)];
        }
        return \Response::json($formatted_tags);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion que permite asignar el propietario del transporte a la transferencia en proceso de carga --------------------------------
    public function propietarios(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = DB::table('T_Flotas')
        ->join('T_Vehiculos','T_Flotas.Codigo','=','T_Vehiculos.flota')
        ->select('T_Flotas.nombre','T_Vehiculos.Placa')->where('T_Flotas.nombre','like','%'. $term .'%')
        ->orwhere('T_Vehiculos.Placa','like','%'. $term .'%')
        ->limit(10)->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            $formatted_tags[] = ['id' => $tag->Placa, 'text' => $tag->nombre.' '.$tag->Placa];
        }
        return \Response::json($formatted_tags);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------


    public function transferencias_usuarios_bodega()
    {
        if(Auth::user()->roles == 17)
        {
            return view('transferencias.inicio_bodega');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
    
    public function datos_transferencias_usuarios_bodega()
    {
        if(Auth::user()->roles == 17)
        {
            $inventario = DB::select('select num_movi, fecha, iwet.observacion, u.nombre, tv.descripcion as placa_vehiculo, iwe.nombre as estado,
            tv.nombre as propietario, iwet.fecha_paraCarga, iwet.fecha_cargado, iwet.comentario, iwet.grupo, iwe.id as id_estado
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Flotas as tv on iwet.placa_vehiculo = tv.Codigo
            where iwet.id_estado >= 14
            and iwet.id_estado < 18
            and u.empresa = iwet.empresa
            and iwet.cod_unidad = :user
            order by fecha asc',['user'=>Auth::user()->sucursal]);
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    public function editar_transferencia_en_bodega($id)
    {
        if(Auth::user()->roles == 17)
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, tv.descripcion as placa_vehiculo, iwe.nombre as estado,
            iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
            tv.nombre as propietario, iwet.referencia, iwet.fechaUno
            from inventario_web_encabezado_transferencias as iwet 
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Flotas as tv on iwet.placa_vehiculo = tv.Codigo
            where iwet.num_movi = :id
            and u.empresa = iwet.empresa
            and iwet.cod_unidad = :sucursal',['id'=>$id,'sucursal'=>Auth::user()->sucursal]);
            //return $tran;
            if($tran == [])
            {
                return redirect()->route('bod_trasf_bod')->with('error','¡Solo puede modificar ordenes de tu misma sucursal!');
            }
            else
            {
                foreach($tran as $t)
                {
                    $per = $t->id_estado;
                }
                $productos = DB::select('select idt.id, idt.cod_producto, ic.nombre, pi.nombre_corto, pi.nombre_fiscal, idt.cantidadSolicitada, floor(idt.cantidad1) as cantidad1,
                ((pi.peso*0.453592) * idt.cantidadSolicitada) as peso, (pi.peso_o_volumen * idt.cantidadSolicitada) as volumen, idt.verificadoCarga,
                ((pi.peso*0.453592) * idt.cantidad1) as peso2, (pi.peso_o_volumen * idt.cantidad1) as volumen2
                from inventario_web_det_transferencias as idt,
                productos_inve as pi,
                inventario_web_categorias as ic
                where idt.num_movi = :id
                and idt.incluido = 1
                and idt.cod_producto = pi.cod_producto
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and pi.empresa = idt.empresa
                and ic.empresa = idt.empresa',['id'=>$id]);
                if(Auth::user()->empresa == 1)
                {
                    $estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado + 1) as ide
                    from inventario_web_encabezado_transferencias as iwet
                    right join inventario_web_estados as iwe on iwe.id >= iwet.id_estado
                    where iwet.id = :id
                    and iwe.id <= ide',['id'=>$id]);
                }
                elseif(Auth::user()->empresa == 2)
                {
                    $estados = DB::select('select iwe.id, iwe.nombre
                    from inventario_web_estados as iwe
                    where iwe.id = 17');
                }
                if($per >= 14 && $per <= 18)
                {
                    return view('transferencias.editarTransferenciaBodega',compact('tran','productos','id','estados'));
                }
                return redirect()->route('bod_trasf_bod')->with('error','¡Solo puede modificar ordenes con estado Cargado!');
            }
            return redirect()->route('bod_trasf_bod')->with('error','¡No puedes modificar está transferencia!'); 
        }
        return redirect()->route('bod_trasf_bod')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias que están en la sucursal finalizadas o a la espera de finalizar -----------------
    public function transferencias_anuladas()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias anuladas';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            return view('transferencias.TransferenciasAnuladas');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias_anuladas()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            $transferencia = DB::select('select num_movi, CONVERT(date,fecha,103) as fecha, iwet.observacion, u.nombre, b.nombre as bodega, placa_vehiculo, 
            iwe.nombre as estado,
            CONVERT(char,iwet.created_at,101) as created_at, CONVERT(char,iwet.fechaSalida,101) as fechaSalida, 
            CONVERT(char,iwet.fecha_entregado,101) as fecha_entregado, tv.propietario, iwet.erroresVerificados,
            iwet.porcentaje, iwet.opcionalDos, uni.nombre as usale, bod.nombre as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and iwet.fecha_entregado > :fecha
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad
            and iwet.id_estado = 23',['fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($transferencia)->make(true);
        }
            return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función para cambiar de estado a una transferencia desde el panel de transferencias en cola -------------------------------------
    function programar_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',50)->first())
        {
            $transferencia = Transferencias::findOrFail($id);
            $detalle = DetTransferencias::where('num_movi',$id)->where('empresa',Auth::user()->empresa)
            ->where('cod_serie_movi',$transferencia->cod_serie_movi)->count('id');
            if($detalle > 0 && $transferencia->id_estado == 13)
            {
                $transferencia->id_estado = 14;
                $transferencia->updated_at = Carbon::now();
                $transferencia->save();
                $est = DB::select('select nombre
                from inventario_web_estados
                where id = 14',['fecha_modificacion'=>Carbon::now(),'updated_at'=>Carbon::now()]);
                $historial = new HETransferencia();
                $historial->empresa = $transferencia->empresa;
                $historial->cod_tipo_movi = $transferencia->cod_tipo_movi;
                $historial->cod_serie_movi = $transferencia->cod_serie_movi;
                $historial->num_movi = $transferencia->num_movi;
                $historial->fecha = $transferencia->fecha;
                $historial->created_at = $transferencia->created_at;
                $historial->fecha_opera = $transferencia->fecha_opera;
                $historial->cod_motivo = $transferencia->cod_motivo;
                $historial->serieFactura = $transferencia->serieFactura;
                $historial->numeroFactura = $transferencia->numeroFactura;
                $historial->cliente = $transferencia->cliente;
                $historial->fecha_modificacion = Carbon::now();
                $historial->observacion  = $transferencia->observacion;
                $historial->placa_vehiculo = $transferencia->placa_vehiculo;
                $historial->comentario   = $transferencia->comentario;
                $historial->referencia   = $transferencia->referencia;
                $historial->id_estado    = 14;
                $historial->updated_at   = Carbon::now();
                $historial->cod_unidad = $transferencia->cod_unidad;
                $historial->cod_bodega = $transferencia->cod_bodega;
                $historial->unidad_transf = $transferencia->unidad_transf;
                $historial->bodega_Transf = $transferencia->bodega_Transf;
                $historial->fechaEntrega = $transferencia->fechaEntrega;
                $historial->fecha_paraCarga = $transferencia->fecha_paraCarga;
                $historial->fecha_enCola = $transferencia->fecha_enCola;
                $historial->fechaUno = $transferencia->fechaUno;
                $historial->fecha_enCarga = $transferencia->fecha_enCarga;
                $historial->fecha_cargado = $transferencia->fecha_cargado;
                $historial->usuario = Auth::user()->name;
                $historial->id_estado = 14;
                $historial->save();
                foreach($est as $e)
                {
                    $estad = $e->nombre;
                }
                if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first())
                {
                    $user = User::where('sucursal','=',$transferencia->unidad_transf)->where('roles',3)->pluck('email');
                    $usuario = Auth::user()->name;
                    $fecha = $transferencia->fechaEntrega;
                    $estado = $estad;
                    $numero = $id;
                    $correo = Auth::user()->email;
                    Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo));
                    return back()->with('success','¡Transferencia modificada con exito!');
                }
                return back()->with('success','¡Transferencia modificada con exito!');
            }
            return back()->with('error','¡No es posible programar transferencias vacias!');
        }
        return back()->with('error','¡No tienes permiso para realizar este cambio!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de transferencias que se encuetran en bodega ------------------------------------------------------
public function reporte_transferencias4()
{
    $historial  = new Historial();
    $historial->id_usuario  = Auth::id();
    $historial->actividad   = 'Ingreso a transferencias en bodega';
    $historial->created_at  = new Carbon();
    $historial->updated_at  = new Carbon();
    $historial->save();
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
    {
        $fecha = Carbon::now()->addDays(1);
        $hoy = Carbon::today()->toDateString();
        $next = $fecha->toDateString();
        $proxima = $fecha->addDays(3);
        //return $hoy;
        return view('transferencias.TransferenciasBodega',compact('hoy','proxima','next'));
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}

public function datos_reporte_transferencias4()
{
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',9)->first())
    {
        $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.nombre, 
        tv.descripcion as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.nombre as bodega, 
        iwet.observacion as DESCRIPCION, uni.nombre as usale, bod.nombre as bsale
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Flotas as tv on tv.Codigo = iwet.placa_vehiculo
        where iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and uni.cod_unidad = bod.cod_unidad 
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        
        and iwet.observacion not LIKE '%exporta%'");
        return DataTables($transferencia)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true &&
    Auth::user()->roles == 3)
    {
        $transferencia = DB::select("select num_movi, iwet.fechaEntrega  as fecha, iwet.usuario, u.nombre, tv.descripcion as placa_vehiculo,
        iwe.nombre as estado, iwe.id, bo.nombre as bodega, iwet.observacion as DESCRIPCION, uni.nombre as usale, bod.nombre as bsale 
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Flotas as tv on tv.Codigo = iwet.placa_vehiculo
        where iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and iwet.unidad_transf = :sucursal
        and uni.cod_unidad = bod.cod_unidad 
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        and iwet.observacion not LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal]);
        return DataTables($transferencia)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true &&
    Auth::user()->roles != 3) 
    {
        $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.nombre, tv.descripcion as placa_vehiculo,
        iwe.nombre as estado, iwe.id, bo.nombre as bodega, iwet.observacion as DESCRIPCION, uni.nombre as usale, bod.nombre as bsale
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        left join T_Flotas as tv on tv.Codigo = iwet.placa_vehiculo
        where iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and iwet.cod_unidad = :sucursal
        and uni.cod_unidad = bod.cod_unidad
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        
        and iwet.observacion not LIKE '%exporta%'
        or iwet.id_estado between 14 and 17
        and u.empresa = iwet.empresa
        and bo.empresa = u.empresa
        and bo.cod_unidad = u.cod_unidad
        and iwet.unidad_transf = :sucursal2
        and uni.cod_unidad = bod.cod_unidad
        and uni.empresa = iwet.empresa 
        and bod.empresa = iwet.empresa
        
        and iwet.observacion not LIKE '%exporta%'",['sucursal'=>Auth::user()->sucursal,'sucursal2'=>Auth::user()->sucursal]);
        return DataTables($transferencia)->make(true);
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función para ver el reporte de todas las transferencias realizadas en el sistema ------------------------------------------------
    function reporte_transferencias()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first())
        {
            return view('transferencias.ReporteTransferencias');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    function datos_reporte_transferencias()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first())
        {
            $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.nombre, iwet.erroresVerificados, iwet.porcentaje, iwet.opcionalDos,
            tv.descripcion as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.nombre as bodega, iwet.observacion as DESCRIPCION, uni.nombre as usale, bod.nombre as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Flotas as tv on tv.Codigo = iwet.placa_vehiculo
            where  u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = :empresa
            and bod.empresa = iwet.empresa
            and iwet.fecha > :fecha",['empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->subMonths(3)]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el reporte de transferencias general fecha -------------------------------------------------------------------------

    function reporte_transferencias_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first())
        {
            $inicio = $request -> inicio;
            $fin = $request -> fin;
            return view('transferencias.ReporteTransferenciasFecha', compact('inicio', 'fin'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

 

    function datos_reporte_transferencias_fecha($inicio, $fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first())
        {
            $transferencia = DB::select("select num_movi, iwet.fechaEntrega as fecha, iwet.usuario, u.nombre, iwet.erroresVerificados, iwet.porcentaje, iwet.opcionalDos,
            tv.descripcion as placa_vehiculo,iwe.nombre as estado, iwe.id, bo.nombre as bodega, iwet.observacion as DESCRIPCION, uni.nombre as usale, bod.nombre as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Flotas as tv on tv.Codigo = iwet.placa_vehiculo
            where  u.empresa = iwet.empresa
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.cod_unidad = bod.cod_unidad
            and uni.empresa = :empresa
            and bod.empresa = iwet.empresa
            and iwet.fecha between :inicio and :fin",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}