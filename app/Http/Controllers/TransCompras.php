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
use App\Transferencias;
use App\HETransferencia;
use App\HDTransferencia;
use App\DetTransferencias;
use App\Mail\Transferencia;
use App\BitacoraTransferencia;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator; 

class TransCompras extends Controller
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
//--------------------------- Funciones para ver las transferencias en proceso --------------------------------------------------------------------------------
    function inicio()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso al modulo de transferencias';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first())
        {
            $saleDe = DB::table('unidades')->where('primer_cliente',1000)->where('empresa',Auth::user()->empresa)
            ->where('Activa','S')->get();
            $saleBo = DB::table('bodegas')->where('empresa',Auth::user()->empresa)->where('fax',1000)
            ->where('ACTIVA','S')->get();
            $entraSu = DB::table('unidades')->where('cod_unidad','!=',15)->where('empresa',Auth::user()->empresa)
            ->where('Activa','S')->get();
            $seriesCompras = DB::select("select cod_serie_movi, nombre
            from series_movi
            where empresa = :empresa
            and para_Transferencias is null
            and cod_tipo_movi = 'P'
            and EnUso != 'N'",['empresa'=>Auth::user()->empresa]);
            return view('transCompras.inicio',compact('saleDe','entraSu','saleBo','seriesCompras'));
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    function datos_inicio()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first())
        {
            $inventario = DB::select('select num_movi, iwet.created_at, iwet.usuario, u.resolucion_autorizacion as nombre, iwe.nombre as estado, iwet.fecha_paraCarga,
            iwet.observacion as DESCRIPCION, bo.observacion as bodega, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            join bodegas as bo on iwet.bodega_Transf = bo.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            where iwet.id_estado = 14
            and u.empresa = iwet.empresa
            and uni.primer_cliente = 1000
            and bo.empresa = u.empresa
            and bo.cod_unidad = u.cod_unidad
            and uni.empresa = iwet.empresa 
            and bod.empresa = iwet.empresa
            and uni.cod_unidad = bod.cod_unidad
            and bod.fax = 1000');
            return DataTables($inventario)->make(true);
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para crear una nueva transferencia de compras ---------------------------------------------------------------------------
    function crear_transferencia(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true &&
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',11)->first() == true)
        {
            if($existe = DB::table('movi_inve')->where('cod_serie_movi',$request->serie)->where('num_movi',$request->numero)
            ->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','P')->first())//Permite generar una transferencia por medio de un pedido 
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
                $cliente = DB::table('clientes')->where('empresa',Auth::user()->empresa)->where('cod_cliente',$existe->cod_cliente)->first();
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
                $tran->observacion           = $existe->observacion;
                //$tran->placa_vehiculo      = $request->placa; jsSK8n23jzr, du2mhjQL1ZT
                //$tran->comentario          = $request->comentario;
                //$tran->referencia          = $request->referencia;
                $tran->id_estado             = 14;
                $tran->created_at            = Carbon::now();
                $tran->updated_at            = Carbon::now();
                $tran->fecha_paraCarga       = Carbon::now();
                $tran->fechaEntrega          = Carbon::now();
                $tran->serieFactura          = $request->serie;
                $tran->numeroFactura         = $request->numero;
                //$tran->cliente               = $existe->nombre_cliente;
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
                        $detTran->cod_unidad            = $existe->cod_unidad;
                        $detTran->cod_bodega            = $existe->cod_bodega; 
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
                        return redirect()->route('transc_editar',['id'=>$id])->with('success','¡Se ha creado una nueva orden de carga!');
                    }
                    return redirect()->route('transc_editar',['id'=>$id])->with('success','¡Se ha creado una nueva orden de carga!');
                }
                return back()->with('error','¡No existe el pedido ingresado!');
            }
            elseif($request->saleDe != '' && $request->saleBo != '')
            {
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
                $tran->cod_motivo            = 95;
                $tran->Clasif_transferencia  = 1;
                $tran->unidad_transf         = $request->entraSu;
                $tran->bodega_Transf         = $request->entraBo;
                $tran->usuario               = Auth::user()->name;
                $tran->fecha_modificacion    = Carbon::now();
                //$tran->observacion         = $request->observacion;
                $tran->observacion           = $request->descripcion;
                //$tran->placa_vehiculo      = $request->placa;
                //$tran->comentario          = $request->comentario;
                //$tran->referencia          = $request->referencia;
                $tran->id_estado             = 14;
                $tran->created_at            = Carbon::now();
                $tran->updated_at            = Carbon::now();
                $tran->fecha_paraCarga       = Carbon::now();
                $tran->fechaEntrega          = Carbon::now();
                $tran->save();
                $historial              = new Historial();
                $historial->id_usuario  = Auth::id();
                $historial->actividad   = 'Se genero una nueva transferencia';
                $historial->created_at  = new Carbon();
                $historial->updated_at  = new Carbon();
                $historial->save();
                $id_n = $tran->created_at;
                $id = Transferencias::where('created_at',$id_n)->first();
                return redirect()->route('transc_editar',['id'=>$id])->with('success','¡Se ha creado una nueva orden de carga!');
            }
            return back()->with('error','No se encontraron coincidencias, no es posible generar transferencia');
        }
        return redirect()->route('transc_inicio')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para editar una transferencia en proceso ------------------------------------------------------------------------------
    function editar_transferencia($id)
    {
        if($existe = Transferencias::find($id))
        {
            if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true && $existe->id_estado < 18
            && $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',[12,16])->first() == true)
            {
                //return $historial;
                $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, tv.Placa as placa_vehiculo, iwe.nombre as estado,
                iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga, b.nombre as bodega,
                iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega,
                tv.propietario, iwet.serieFactura, iwet.cod_unidad, iwet.cod_bodega, iwet.usuarioSupervisa,
                iwet.placa_vehiculo as cod_placa, iwet.unidad_transf, uni.nombre as usale, bod.nombre as bsale, iwet.bodega_Transf
                from inventario_web_encabezado_transferencias as iwet 
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on iwet.bodega_Transf = b.cod_bodega
                join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
                join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
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
                if($existe->id_estado == 14)
                {
                    $entraSu = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad','!=',15)
                    ->orderBy('nombre','asc')->get();
                    return view('transCompras.EditarTransferencia',compact('tran','id','entraSu'));
                }
                return redirect()->route('transc_inicio')->with('error','¡No es posible realizar modificaciones!');
            }
            elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',8)->first() == true)
            {
                return redirect()->route('transc_ver_transfina',['id'=>$id]);
            }
            return redirect()->route('transc_inicio')->with('error','No tienes permisos para accesar');
        }
        return redirect()->route('transc_inicio')->with('error','No existe la transferencia que buscas en el sistema');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para modificar el encabezado de una transferencia de compras ------------------------------------------------------------
    function editar_encabezado_transferencia(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true && 
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',15)->first() == true)
        {
            $tran = Transferencias::findOrFail($id);
            $historial = new HETransferencia();
            $validator = Validator::make($request->all(),[
                'observacion'=>'required',
                'comentario'=>'required',
                'placa'=>'required',
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
                    $historial->cod_unidad = $tran->cod_unidad;
                    $historial->cod_bodega = $tran->cod_bodega;
                    $historial->fecha = $tran->fecha;
                    $historial->created_at = Carbon::now();
                    $historial->fecha_opera = $tran->fecha_opera;
                    $historial->cod_motivo = $tran->cod_motivo;
                    $historial->unidad_transf = $request->entraSu;
                    $tran->unidad_transf = $request->entraSu;
                    $tran->bodega_Transf = $request->entraBo;
                    $historial->bodega_Transf = $request->entraBo;
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
                    $tran->id_estado         = 14;
                    $historial->id_estado    = 14;
                    $tran->updated_at        = Carbon::now();
                    $historial->updated_at   = Carbon::now();
                    //$tran->fecha_enCola      = Carbon::now();
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
                    $tran->usuarioSupervisa  = $request->verificador;
                    $historial->usuarioSupervisa = $request->verificador;
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
                        else 
                        {
                            $tran->fecha_paraCarga = Carbon::parse($request->fechaCarga);
                            $historial->fecha_paraCarga = Carbon::parse($request->fechaCarga);
                        } 
                    }
                    $tran->fecha_enCola = Carbon::now();
                    $historial->fecha_enCola = Carbon::now();
                    $tran->save();
                    $productos = DB::table('inventario_web_det_transferencias')->where('num_movi',$id)->where('empresa',Auth::user()->empresa)
                    ->update(['cod_unidad'=>$request->saleDe,'cod_bodega'=>$request->saleBo,'unidad_transf'=>$request->entraSu,
                    'bodega_Transf'=>$request->entraBo]);
                    $historial->usuario = Auth::user()->name;
                    $historial->save();
                    return back()->with('success','¡Transferencia modificada con exito!');
                }
                return back()->with('error','No se permite realizar cambios a esta transferencia');
            }
            return back()->with('error','No se permite realizar cambios a esta transferencia');
        }
        return redirect()->route('transc_inicio')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Permite verificar una transferencia de ingreso por compras ----------------------------------------------------------------------
    function verificar_transferencia($id)
    {
        $existe = Transferencias::find($id);
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true 
        && $existe == true && $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17)->first() == true)
        {
            $grupos = User::where('email','like','grupocomp%')->get();
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
            if($existe->cod_unidad == 27 && $existe->cod_bodega == 3 && $existe->id_estado < 20)
            {
                return view('transCompras.Verificar_transferencia',compact('tran','id','grupos'));
            }
            return redirect()->route('transc_inicio')->with('error','¡Orden finalizada, no se permiten cambios!');
        }  
        return back()->with('error','No tienes permisos para accesar');   
    }

    function guardar_revision_transferencia(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35) == true &&
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',17) == true)
        {
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',18)->first();
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
            if($vehiculo->n_motor < $max && $permiso == true)
            {
                
                return back()->with('error','¡El peso de la carga sobrepasa el máximo permitido!');
            }
            else 
            {
                $finalizar = Transferencias::find($id);
                $existe = MoviInve::where('num_movi',$id)->where('cod_serie_movi',$finalizar->cod_serie_movi)->where('empresa',Auth::user()->empresa)->count();
                $estado = "Despachado en camino";
                if($finalizar->id_estado >= 18 && $existe == 0)
                {
                    return redirect()->route('transc_inicio')->with('error','¡Orden en camino o finalizada, no es posible realizar cambios!');
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
                        if(DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',29)->first() == true)
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
                                'costo_pivot' => 0,
                                'iva_vehiculos' => 'N',
                                'exento_iva' => 'N',
                                'dimension4' => 0,
                                'valor_impuesto_NoIVA' => 0]]);
                            }
                            $finalizar->usuarioSupervisa    = Auth::user()->name;
                            $finalizar->observacionSup      = $request->observaciones;
                            $finalizar->fechaSalida         = Carbon::now();
                            $finalizar->fecha_entregado     = Carbon::now();
                            $finalizar->id_estado           = 18;
                            $finalizar->grupoCarga          = $request->grupo; 
                            $finalizar->cod_motivo          = 5;
                            $finalizar->placa_vehiculo      = $request->placa;
                            $finalizar->DESCRIPCION         = $request->piloto;
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
                            $grup = DB::table('users')->where('name',$request->grupo)->first();
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
                            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                            if($permiso == true)
                            {
                                $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                                $usuario = Auth::user()->name;
                                $fecha = Carbon::now();
                                $numero = $id;
                                $correo = Auth::user()->email;
                                Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo)); 
                                return redirect()->route('transc_finalizadas')->with('success','Datos agregados con exito');
                            }
                            return redirect()->route('transc_finalizadas')->with('success','Datos agregados con exito');
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
                                'costo_pivot' => 0,
                                'iva_vehiculos' => 'N',
                                'exento_iva' => 'N',
                                'dimension4' => 0,
                                'valor_impuesto_NoIVA' => 0]]);
                            }
                        }
                        $finalizar->usuarioSupervisa    = Auth::user()->name;
                        $finalizar->observacionSup      = $request->observaciones;
                        $finalizar->fechaSalida         = Carbon::now();
                        $finalizar->fecha_entregado     = Carbon::now();
                        $finalizar->id_estado           = 18;
                        $finalizar->grupoCarga          = $request->grupo; 
                        $finalizar->cod_motivo          = 5;
                        $finalizar->placa_vehiculo      = $request->placa;
                        $finalizar->DESCRIPCION         = $request->piloto;
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
                        $grup = DB::table('users')->where('name',$request->grupo)->first();
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
                        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                        if($permiso == true)
                        {
                            $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                            $usuario = Auth::user()->name;
                            $fecha = Carbon::now();
                            $numero = $id;
                            $correo = Auth::user()->email;
                            Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo)); 
                            return redirect()->route('transc_finalizadas')->with('success','Datos agregados con exito');
                        }
                        return redirect()->route('transc_finalizadas')->with('success','Datos agregados con exito');
                    }
                    return redirect()->route('transc_finalizadas')->with('error','Datos no modificados');
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
                        //if($vehiculo->flota == $tran->placa_vehiculo)
                        //{
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
                            $movi->observacion = $request->piloto;
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
                                if(DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',29)->first() == true)
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
                                        'costo_pivot' => 0,
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
                                        'costo_pivot' => 0,
                                        'iva_vehiculos' => 'N',
                                        'exento_iva' => 'N',
                                        'dimension4' => 0,
                                        'valor_impuesto_NoIVA' => 0]]);
                                    }
                                }
                                $finalizar->usuarioSupervisa    = Auth::user()->name;
                                $finalizar->observacionSup      = $request->observaciones;
                                $finalizar->fechaSalida         = Carbon::now();
                                $finalizar->fecha_entregado     = Carbon::now();
                                $finalizar->id_estado           = 18;
                                $finalizar->grupoCarga          = $request->grupo; 
                                $finalizar->cod_motivo          = 5;
                                $finalizar->placa_vehiculo      = $request->placa;
                                $finalizar->DESCRIPCION         = $request->piloto;
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
                                $grup = DB::table('users')->where('name',$request->grupo)->first();
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
                                $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
                                if($permiso == true)
                                {
                                    $user = User::where('sucursal','=',$finalizar->unidad_transf)->where('roles',3)->pluck('email');
                                    $usuario = Auth::user()->name;
                                    $fecha = Carbon::now();
                                    $numero = $id;
                                    $correo = Auth::user()->email;
                                    Mail::to($user)->send(new Transferencia($usuario,$fecha,$estado,$numero,$correo)); 
                                    return redirect()->route('transc_finalizadas')->with('success','Datos agregados con exito');
                                }
                                return redirect()->route('transc_finalizadas')->with('success','Datos agregados con exito');
                            }
                            return back()->with('error','¡Error en transferencia, notifique a soporte!');
                       // }
                        //return back()->with('error','La placa ingresada no pertenece al transporte asignado!');
                    }
                    return back()->with('error','¡Debe validar todos los productos antes de finalizar!');
                }
                return back()->with('','¡Reporte este error a Sistemas!');
            }
            return back()->with('error','¡Reporte este error a Sistemas!');
        }
        return redirect()->route('transc_inicio')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para generar un documento en PDF de la transferencia -------------------------------------------------------------------- 
    public function imprimir_pdf($id)
    {
        $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
        iwet.observacion as descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
        iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
        iwet.usuarioSupervisa, iwet.fechaSalida, iwet.fechaEntrega, iwet.fecha_entregado, iwet.fechaUno,
        tv.propietario, b.nombre as bodega, uni.nombre as usale, bod.nombre as bsale,
        iwet.observacionSucursal
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join bodegas as b on iwet.bodega_Transf = b.cod_bodega
        join unidades as uni on iwet.cod_unidad = uni.cod_unidad 
        join bodegas as bod on iwet.cod_bodega = bod.cod_bodega 
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id 
        left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
        where iwet.num_movi = :id
        and u.empresa = iwet.empresa
        and u.cod_unidad = b.cod_unidad
        and b.empresa = iwet.empresa
        and uni.empresa = iwet.empresa 
        and uni.cod_unidad = bod.cod_unidad
        and bod.empresa = iwet.empresa',['id'=>$id]);

        $per = Transferencias::find($id);

        if($per->id_estado >= 14 && $per->id_estado <= 17)
        {
            $productos = DB::select('select  pi.nombre_corto, pi.nombre_fiscal, floor(iwt.cantidadSolicitada) as cantidad, 
            ((pi.peso/2.204623) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen, iwt.costo
            from inventario_web_det_transferencias as iwt,
            productos_inve as pi, 
            where iwt.num_movi = :id
            and iwt.cod_producto = pi.cod_producto
            and iwt.empresa = pi.empresa
            and iwt.incluido = 1
            order by pi.nombre_corto asc',['id'=>$id]);
            $usuario = Auth::user()->name;
            $pdf = PDF::loadView('transCompras.imprimir',compact('tran','productos','usuario'));
            return $pdf->download($per->observacion.'.pdf');
        }
        elseif($per->id_estado >= 18)
        {
            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidadRecibida) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi, iwt.costo,
            ((pi.peso/2.204623) * cantidad1) as peso, (pi.factor_a_unidad_basica * cantidad1) as volumen,
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
            $usuario = Auth::user()->name;
            $pdf = PDF::loadView('transCompras.imprimirF',compact('tran','productos','usuario'));
            return $pdf->download($per->observacion.'.pdf');
        }
        return back()->with('error','No se permite imprimir transferencias vacias o con estado ¡Creada!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para la vista de transferencias por compras finalizadas ---------------------------------------------------------------
public function transferencias_finalizadas()
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias finalizadas';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first())
        {
            return view('transCompras.TransferenciasFinalizadas');
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    function datos_transferencias_finalizadas()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first())
        {
            $transferencia = DB::select('select num_movi, CONVERT(date,fecha,103) as fecha, iwet.observacion, u.resolucion_autorizacion as nombre, b.observacion as bodega, 
            placa_vehiculo, iwe.nombre as estado, iwet.id_estado,
            CONVERT(char,iwet.created_at,101) as created_at, CONVERT(char,iwet.fechaSalida,101) as fechaSalida, 
            iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
            iwet.porcentaje, iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado >= 18
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and uni.primer_cliente = 1000
            and iwet.fecha_entregado > :fecha
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad
            and bod.fax = 1000',['fecha'=>Carbon::now()->subMonths(6)]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver una transferencia por compra finalizada ------------------------------------------------------------------------
    public function ver_transferencia($id)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Reviso detalles de la transferencia'. $id;
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        $existe = Transferencias::find($id);
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true && $existe == true
        && $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacionSucursal, u.nombre, placa_vehiculo, iwe.nombre as estado,
            iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fechaEntrega, iwet.fechaUno,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, b.nombre as bodega,
            iwet.usuario, iwet.fechaSalida, iwet.usuarioSupervisa, iwet.fecha_paraCarga, iwet.observacion, us.name,
            iwet.fechaSucursal, iwet.fecha_entregado, iwet.observacionSup, iwet.observacionSucursal, iwet.cod_unidad,
            tv.propietario, iwet.erroresVerificados, iwet.observacionRevision, iwet.porcentaje,
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
            $estados = DB::select('select iwe.id, iwe.nombre, (iwet.id_estado - 1) as ide
            from inventario_web_encabezado_transferencias as iwet
            right join inventario_web_estados as iwe on iwe.id >= ide
            where iwet.id = :id
            and iwe.id > 18
            and iwe.id < 21',['id'=>$id]);

            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidadRecibida) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,  iwt.noIncluido, mal_estado,
            floor(iwt.cantidad1) as cantidad1, iwt.cantidadSolicitada, floor(iwt.costo) as costo
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
            return view('transCompras.verTransferenciaFinalizada',compact('tran','productos','id','integra','estados'));
        }
        return redirect()->route('transc_inicio')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcioens para ver las transferencias finalizadas filtradas por fecha  ----------------------------------------------------------
    public function transferencias_finalizadas_fecha(Request $request)
    {
        $historial  = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Ingreso a transferencias finalizadas filtradas por fecha';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true
        && $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('transCompras.TransferenciasFinalizadasFecha',compact('inicio','fin'));
        }
        return redirect()->route('transc_inicio')->with('error','No tienes permisos para accesar');
    }

    public function datos_transf_final_fecha($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true 
        && $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[8,9])->first())
        {
            $transferencia = DB::select('select num_movi, CONVERT(char,fecha,101) as fecha, iwet.observacion, u.resolucion_autorizacion as nombre, 
            b.observacion as bodega, placa_vehiculo, iwe.nombre as estado, iwet.id_estado,
            CONVERT(char,iwet.created_at,101) as created_at, CONVERT(char,iwet.fechaSalida,101) as fechaSalida, 
            iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
            iwet.porcentaje, iwet.opcionalDos, uni.resolucion_autorizacion as usale, bod.observacion as bsale
            from inventario_web_encabezado_transferencias as iwet
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join bodegas as b on iwet.bodega_Transf = b.cod_bodega
            join unidades as uni on iwet.cod_unidad = uni.cod_unidad
            join bodegas as bod on iwet.cod_bodega = bod.cod_bodega
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
            where iwet.id_estado >= 18
            and u.empresa = iwet.empresa
            and b.empresa = iwet.empresa
            and b.cod_unidad = u.cod_unidad
            and uni.primer_cliente = 1000
            and iwet.fechaSalida between :inicio and :fin
            and iwet.empresa = uni.empresa 
            and iwet.empresa = bod.empresa
            and uni.cod_unidad = bod.cod_unidad
            and bod.fax = 1000',['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($transferencia)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Finalizar transferencias por compras despachadas --------------------------------------------------------------------------------
    public function editar_encabezado_transferencia_despachada(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first() == true &&
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',19)->first() == true)
        {
            $tran = Transferencias::findOrFail($id);
            $historial  = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Modifico encabezado de transferencia despachada número'. $id;
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            
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
                    else
                    {
                        $resultado = ($po->su/$po->total)*100;
                    }
                }
                if($tran->id_estado == 20)
                {
                    return back()->with('error','¡Transferencia finalizada, no se permite realizar cambios!');
                }
                else
                {
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
                        $tran->observacion       = $request->descripcion;
                        $tran->comentario        = $request->comentario;
                        $tran->save();
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTransferencias::find($eli->id)->delete();
                        }
                        return redirect()->route('transc_finalizadas')->with('success','¡Transferencia finalizada con exito!');
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
                        $tran->observacion       = $request->descripcion;
                        $tran->comentario        = $request->comentario;
                        $tran->save();
                        $eliminar = DB::select('select * 
                        from inventario_web_det_transferencias 
                        where incluido is null
                        and num_movi = :id',['id'=>$id]);
                        foreach($eliminar as $eli)
                        {
                            $del = DetTransferencias::find($eli->id)->delete();
                        }
                        return redirect()->route('transc_finalizadas')->with('success','¡Transferencia finalizada con exito!'); 
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
                        $tran->placa_vehiculo       = $request->placa;
                        $tran->observacion          = $request->descripcion;
                        $tran->comentario           = $request->comentario;
                        $tran->save();
                        return redirect()->route('transc_finalizadas')->with('success','¡Tiempo para verificar sobrepasado, no se aplicaran los cambios!');
                    }
                    else if($request->estado == 20 && $per != 0)
                    {
                        return back()->with('error','Verifique todos los productos de la transferencia');
                    }
                    else
                    {
                        $tran->id_usuarioRecibe     = Auth::id();
                        $tran->fecha_modificacion   = Carbon::now();
                        $tran->id_estado            = $request->estado;
                        $tran->updated_at           = Carbon::now();
                        $tran->fechaSucursal        = Carbon::now();
                        $tran->placa_vehiculo       = $request->placa;
                        $tran->observacion          = $request->descripcion;
                        $tran->comentario           = $request->comentario;
                        $tran->save();
                        return redirect()->route('transc_finalizadas')->with('success','¡Transferencia modificada con exito!');   
                    }
                }
            }
            elseif($request->estado == 19)
            {
                $tran->observacionSucursal  = $request->observacionSucursal;
                $tran->id_usuarioRecibe = Auth::id();
                $tran->id_estado         = $request->estado;
                $tran->fecha_entregado      = Carbon::now();
                $tran->placa_vehiculo    = $request->placa;
                $tran->observacion       = $request->descripcion;
                $tran->comentario        = $request->comentario;
                $tran->save();
                return redirect()->route('validad_tranf',$id)->with('success','Recuerda validar todos los productos de la transferencia');
            }
            else 
            {
                $tran->observacionSucursal  = $request->observacionSucursal;
                $tran->id_usuarioRecibe = Auth::id();
                $tran->id_estado         = $request->estado;
                $tran->fecha_entregado      = Carbon::now();
                $tran->placa_vehiculo    = $request->placa;
                $tran->observacion       = $request->descripcion;
                $tran->comentario        = $request->comentario;
                $tran->save();
                return back()->with('success','¡Transferencia modificada con exito!');
            }
            return redirect()->route('transc_finalizadas')->with('error','No puedes modificar transferencias de otras sucursales');
        }
        return redirect()->route('transc_finalizadas')->with('error','No puedes modificar transferencias de otras sucursales');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para anular una transferencia -------------------------------------------------------------------------------------------
    function anular_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',34)->first())
        {
            $tran = Transferencias::findOrFail($id);
            if($tran->id_estado >= 18)
            {
                $tran->id_usuarioRecibe = Auth::id();
                $tran->id_estado = 23;
                $tran->fecha_entregado = Carbon::now();
                $tran->fechaSucursal = Carbon::now();
                $tran->save();
                $eliminar = DB::select('select * 
                from inventario_web_det_transferencias 
                where num_movi = :id',['id'=>$id]);
                foreach($eliminar as $eli)
                {
                    $del = DetTransferencias::find($eli->id)->delete();
                }
                $delete = DetMovi::where('num_movi',$id)->where('cod_serie_movi','IW')->where('empresa',Auth::user()->empresa)->delete();
                return redirect()-> route('transc_finalizadas')->with('success','¡Transferencia anulada!');
            }
            return back()->with('error','¡No se permite realizar cambios!');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de productos marcados con unidades en mal estado ----------------------------------------------------
    function reporte_producto_mal_estado()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            return view('transCompras.inicio_reporte_mal_estado');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    function datos_producto_mal_estado()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $productos = DB::select('select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, sum(iwdt.mal_estado) as mal_estado
            from inventario_web_det_transferencias as iwdt,
            productos_inve as pi
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado is not null
            and iwdt.empresa = pi.empresa 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at > :fecha
            group by iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal',['empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->submonths(2)]);
            return DataTables::of($productos)->addColumn('details_url', function($productos){
                return url('ddrepromes/'.$productos->cod_producto);
            })->make(true);
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    function detalle_datos_producto_mal_estado($cod_producto)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $detalles = DB::select('select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwdt.mal_estado, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion
            from inventario_web_det_transferencias as iwdt,
            productos_inve as pi,
            DBA.inventario_web_encabezado_transferencias as iwct
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado > 0
            and iwdt.empresa = pi.empresa 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at > :fecha
            and iwdt.cod_producto = :cod_producto
            and iwdt.num_movi = iwct.num_movi',['empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->submonths(2),
            'cod_producto'=>$cod_producto]);
            return DataTables::of($detalles)->make(true);
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }
//------------------------------------------------------------------------------------------------------------------------------------------------------------- 

//--------------------------- Funciones para ver el detalle de productos dañados con imagenes -----------------------------------------------------------------
    function detalles_productos_con_imagenes($cod_producto)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $datos = DB::select("select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion, if iwit.imagen is null then 'storage/sin_imagen.jpg' else 
            iwit.imagen endif as imagen
            from inventario_web_det_transferencias as iwdt
            join productos_inve as pi on iwdt.empresa = pi.empresa 
            join inventario_web_encabezado_transferencias as iwct on iwdt.num_movi = iwct.num_movi
            left join inventario_web_imagenes_transferencias as iwit on iwct.num_movi = iwit.num_movi
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado > 0 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at > :fecha
            and iwdt.cod_producto = :cod_producto
            order by fechaSucursal asc",['empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->submonths(2),
            'cod_producto'=>$cod_producto]);
            $inicio = 0;
            $fin    = 0; 
            $detalles = [];
            foreach($datos as $d)
            {
                $detalles[] = ['nombre_corto'=>$d->nombre_corto,'nombre_fiscal'=>$d->nombre_fiscal,'num_movi'=>$d->num_movi,
            'observacion'=>$d->observacion,'fechaSucursal'=>$d->fechaSucursal,'imagen'=>base64_encode(file_get_contents($d->imagen))];
            }
            if($detalles != '')
            {
                return view('transCompras.detalle_productos_imagenes',compact('detalles','cod_producto','inicio','fin'));
            }
            return back()->with('error','¡No hay datos que mostrar!');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    function pdf_detalles_productos_con_imagenes($cod_producto)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            
            $datos = DB::select("select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion, if iwit.imagen is null then 'storage/sin_imagen.jpg' else 
            iwit.imagen endif as imagen, sum(iwdt.mal_estado) as mal_estado
            from inventario_web_det_transferencias as iwdt
            join productos_inve as pi on iwdt.empresa = pi.empresa 
            join inventario_web_encabezado_transferencias as iwct on iwdt.num_movi = iwct.num_movi
            left join inventario_web_imagenes_transferencias as iwit on iwct.num_movi = iwit.num_movi
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado > 0 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at > :fecha
            and iwdt.cod_producto = :cod_producto
            group by iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion, if iwit.imagen is null then 'storage/sin_imagen.jpg' else 
            iwit.imagen endif 
            order by fechaSucursal asc",['empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->submonths(2),
            'cod_producto'=>$cod_producto]);
            $detalles = [];
            foreach($datos as $d)
            {
                $detalles[] = ['nombre_corto'=>$d->nombre_corto,'nombre_fiscal'=>$d->nombre_fiscal,'num_movi'=>$d->num_movi,
                'observacion'=>$d->observacion,'fechaSucursal'=>$d->fechaSucursal,'mal_estado'=>$d->mal_estado,
                'imagen'=>base64_encode(file_get_contents($d->imagen))];
            }
            if($detalles != '')
            {
                $pdf = PDF::loadView('transCompras.pdfReporte',compact('detalles'));
                return $pdf->download('reporteDañado.pdf');
            }
            return back()->with('error','¡No hay datos que mostrar!');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de productos marcados con unidades en mal estado filtrado por fecha ---------------------------------
    function reporte_producto_mal_estado_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('transCompras.reporte_mal_estado_fecha',compact('inicio','fin'));
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    function fecha_reporte_producto_mal_estado_fecha($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $productos = DB::select('select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, sum(iwdt.mal_estado) as mal_estado,
            min(iwdt.created_at) as inicio, max(iwdt.created_at) as fin 
            from inventario_web_det_transferencias as iwdt,
            productos_inve as pi
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado is not null
            and iwdt.empresa = pi.empresa 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at between :inicio and :fin
            group by iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal',['empresa'=>Auth::user()->empresa,'inicio'=>$inicio,
            'fin'=>$fin]);
            return DataTables::of($productos)->addColumn('details_url', function($productos){
            return url('fecddrepromes/'.$productos->cod_producto.'/'.$productos->inicio.'/'.$productos->fin);
            })->make(true);
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    function detalle_datos_producto_mal_estado_fecha($cod_producto,$inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $detalles = DB::select('select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwdt.mal_estado, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion
            from inventario_web_det_transferencias as iwdt,
            productos_inve as pi,
            DBA.inventario_web_encabezado_transferencias as iwct
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado > 0
            and iwdt.empresa = pi.empresa 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at between :inicio and :fin
            and iwdt.cod_producto = :cod_producto
            and iwdt.num_movi = iwct.num_movi',['empresa'=>Auth::user()->empresa,'inicio'=>$inicio,'fin'=>$fin,
            'cod_producto'=>$cod_producto]);
            return DataTables::of($detalles)->make(true);
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el detalle de productos dañados con imagenes -----------------------------------------------------------------
    function detalles_productos_con_imagenes_fecha($cod_producto,$inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $datos = DB::select("select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion, if iwit.imagen is null then 'storage/sin_imagen.jpg' else 
            iwit.imagen endif as imagen
            from inventario_web_det_transferencias as iwdt
            join productos_inve as pi on iwdt.empresa = pi.empresa 
            join inventario_web_encabezado_transferencias as iwct on iwdt.num_movi = iwct.num_movi
            left join inventario_web_imagenes_transferencias as iwit on iwct.num_movi = iwit.num_movi
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado > 0 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at between :inicio and :fin
            and iwdt.cod_producto = :cod_producto
            order by fechaSucursal asc",['empresa'=>Auth::user()->empresa,'inicio'=>$inicio,'fin'=>$fin,
            'cod_producto'=>$cod_producto]);
            $detalles = [];
            foreach($datos as $d)
            {
                $detalles[] = ['nombre_corto'=>$d->nombre_corto,'nombre_fiscal'=>$d->nombre_fiscal,'num_movi'=>$d->num_movi,
            'observacion'=>$d->observacion,'fechaSucursal'=>$d->fechaSucursal,'imagen'=>base64_encode(file_get_contents($d->imagen))];
            }
            //return $detalles;
            if($detalles != '')
            {
                return view('transCompras.detalle_productos_imagenes',compact('detalles','inicio','fin','cod_producto'));
            }
            return back()->with('error','¡No hay datos que mostrar!');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }

    function pdf_detalles_productos_con_imagenes_fechas($cod_producto,$inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',39)->first())
        {
            $datos = DB::select("select iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion, if iwit.imagen is null then 'storage/sin_imagen.jpg' else 
            iwit.imagen endif as imagen, sum(iwdt.mal_estado) as mal_estado
            from inventario_web_det_transferencias as iwdt
            join productos_inve as pi on iwdt.empresa = pi.empresa 
            join inventario_web_encabezado_transferencias as iwct on iwdt.num_movi = iwct.num_movi
            left join inventario_web_imagenes_transferencias as iwit on iwct.num_movi = iwit.num_movi
            where iwdt.empresa = :empresa
            and iwdt.cod_unidad is null 
            and iwdt.mal_estado > 0 
            and iwdt.cod_producto = pi.cod_producto
            and iwdt.created_at between :inicio and :fin
            and iwdt.cod_producto = :cod_producto
            group by iwdt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, iwct.num_movi,
            iwct.fechaSucursal, iwct.observacion, if iwit.imagen is null then 'storage/sin_imagen.jpg' else 
            iwit.imagen endif 
            order by fechaSucursal asc",['empresa'=>Auth::user()->empresa,'inicio'=>$inicio,'fin'=>$fin,
            'cod_producto'=>$cod_producto]);
            $detalles = [];
            foreach($datos as $d)
            {
                $detalles[] = ['nombre_corto'=>$d->nombre_corto,'nombre_fiscal'=>$d->nombre_fiscal,'num_movi'=>$d->num_movi,
                'observacion'=>$d->observacion,'fechaSucursal'=>$d->fechaSucursal,'mal_estado'=>$d->mal_estado,
                'imagen'=>base64_encode(file_get_contents($d->imagen))];
            }
            //return $detalles;
            if($detalles != '')
            {
                $pdf = PDF::loadView('transCompras.pdfReporte',compact('detalles'));
                return $pdf->download('reporteDañado.pdf');
            }
            return back()->with('error','¡No hay datos que mostrar!');
        }
        return back()->with('error','¡No tienes permiso para realizar esta acción!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para marcar transferencia cuando se a realizado una corrección en diamante por error en ingreso -------------------------
    function marcar_correccion_transferencia($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first())
        {
            $marcar = Transferencias::findOrFail($id);
            $marcar->erroresVerificados = 1;
            $marcar->save();
            return back()->with('success','Se a actualizado correctamente');
        }
        return back()->with('error','No tienes permisos para realizar está acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}