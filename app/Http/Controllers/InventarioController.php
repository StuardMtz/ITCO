<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Auth;
use App\User;
use Cookie;
use App\Semana;
use App\DetMovi;
use App\MoviInve;
use Carbon\Carbon;
use App\Productos;
use App\Historial;
use App\Inventario;
use App\ProductoSemana;
use App\Transferencias;
use App\InventarioGeneral;
use App\DetTransferencias;
use App\Mail\Transferencia;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

//---------------------------------------------------------- Función inicio -----------------------------------------------------------------------------------
    function inventarios_pendientes_de_realizar()
    {
        //Funcion para visualizar los inventarios que los usuarios están trabajando y no han finalizados
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            /**if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',50)->first())
            {
                $inventarios = DB::select("SELECT * FROM DBA.inventario_web_reportes_inventarios 
                where inventario_web_reportes_inventarios.created_at > :fecha
                and inventario_web_reportes_inventarios.estado = 'En proceso'",['fecha'=>Carbon::now()->subMonths(6)]);
                $fecha = Carbon::now()->toDateString();
                return view('inventarios.inicio',compact('inventarios','fecha'))->with('success','Bienvenido al sistema de inventarios');
            }*/
            if ($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[1,4])->first())
            {
                $inventarios = DB::select("SELECT * FROM DBA.inventario_web_reportes_inventarios 
                where inventario_web_reportes_inventarios.usuario = :usuario
                and inventario_web_reportes_inventarios.created_at > :fecha
                and inventario_web_reportes_inventarios.estado = 'En proceso'",['usuario'=>Auth::id(),'fecha'=>Carbon::now()->subMonths(6)]);
                $fecha = Carbon::now()->toDateString();
                return view('inventarios.inicio',compact('inventarios','fecha'))->with('success','Bienvenido al sistema de inventarios');
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------- Función para visualizar los datos de un inventario -----------------------------------------------
    function ver_inventario_completo($id)
    {
        //Funcion para visualizar el proceso de un inventario hasta que se finaliza 
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $datos = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, 
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final, us.roles,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas, 
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia 
            from inventario_web_encabezado as iwe,
            users as us, 
            unidades as uni, 
            bodegas as bo 
            where iwe.sucursal = uni.cod_unidad
            and us.id = iwe.usuario
            and iwe.bodega = bo.cod_bodega            
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.empresa = uni.empresa
            and iwe.id = :enca",['enca'=>$id, 'empresa'=>Auth::user()->empresa]);//se obtienen los datos del encabezadod del inventario
            $rep =  DB::select('select count() as contado, min(updated_at) as primero, max(updated_at) as ultimo,
            sum(mal_estado) as mal_estado
            from inventario_web
            where no_encabezado = :id
            and existencia_fisica is not null',['id'=>$id]);
            foreach($rep as $re)
            {
                $ver        = $re->contado;
                $inicio     = $re->primero;
                $final      = $re->ultimo;
                $mal_estado = $re->mal_estado; 
            }
            return view('inventarios.ver_inventario',compact('datos','id','ver','inicio','final','mal_estado'));
        }
        return view('inventarios.inicio')->with('error','No tienes permisos para accesar');
    }

    function inventario_datos($id)
    {
        $inventario = DB::select("select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, convert(bigint,existencia_teorica),
        coalesce(convert(int,existencia_fisica),0) as existencia_fisica,  coalesce(mal_estado,0) as mal_estado, 
        ((abs(convert(int,existencia_fisica)) - abs(convert(int,existencia_teorica)))) as diferencias, convert(varchar(2),null)+ '<button type=button class=btn-danger data-toggle=modal data-target=#create>Editar</button>' as btn 
        from inventario_web 
        where no_encabezado = :encabezado
        and ((coalesce(existencia_fisica,0) <> abs(existencia_teorica)))
        order by categoria asc",['encabezado'=>$id]);
        return $inventario;
    }

    function detalles_producto_inventario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',3)->first())
        {
            $inventario = DB::select('select iw.nombre_corto, iw.nombre_fiscal, convert(numeric(10,0),i.existencia1) as existencia_real,
            iw.existencia_teorica, coalesce(iw.existencia_fisica,0) as existencia_fisica, 
            coalesce(iw.mal_estado,0) as mal_estado
            from inventarios as i,
            unidades as u,
            bodegas as b,
            inventario_web as iw,
            inventario_web_encabezado as iwe
            where i.empresa = u.empresa
            and u.empresa = b.empresa
            and i.cod_unidad = u.cod_unidad
            and u.cod_unidad = b.cod_unidad
            and i.cod_bodega = b.cod_bodega
            and i.empresa = :empresa
            and i.cod_unidad = iwe.sucursal
            and i.cod_bodega = iwe.bodega
            and iw.id = :id
            and iw.cod_producto = i.cod_producto
            and iw.no_encabezado = iwe.id',['id'=>$id,'empresa'=>Auth::user()->empresa]);
            $detalle = ''; 
            foreach($inventario as $h)
            {
                $detalle = ['nombre_corto'=>$h->nombre_corto,'nombre_fiscal'=>$h->nombre_fiscal,'existencia_real'=>$h->existencia_real,
                'existencia_teorica'=>$h->existencia_teorica,'existencia_fisica'=>$h->existencia_fisica,'mal_estado'=>$h->mal_estado,];
            }
            return $detalle;
        }
        return back()->with('error','¡No tiene permiso para acceder a la ruta!');
    }

    function historial_conteo_producto($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',3)->first())
        {
            $inventario = InventarioGeneral::find($id);
            $historial = DB::select('select pi.nombre_corto, pi.nombre_fiscal, iwd.existencia, iwd.existencia_fisica, iwd.descripcion,
            iwd.created_at, iwd.mal_estado, (iwd.existencia - abs(iwd.existencia_fisica)) as diferencia
            from inventario_web_detalle as iwd,
            productos_inve as pi
            where iwd.no_encabezado = :enca 
            and iwd.cod_producto = :prod
            and pi.cod_producto = iwd.cod_producto
            and pi.empresa = :empresa
            order by iwd.created_at desc',['enca'=>$inventario->no_encabezado,'prod'=>$inventario->cod_producto,'empresa'=>Auth::user()->empresa]);
            return $historial;
        }
        return back()->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

    function detalle_inventarios($no_encabezado,$cod_producto)
    {
        $detalles = DB::select("select iwd.existencia_fisica, iwd.descripcion, iwd.created_at, us.name, iwd.existencia, (iwd.existencia - abs(iwd.existencia_fisica)) as diferencia 
        from inventario_web_detalle as iwd,
        users as us 
        where iwd.usuario = us.id 
        and iwd.no_encabezado = :enca
        and iwd.cod_producto = :codp
        order by iwd.created_at",['enca'=>$no_encabezado,'codp'=>$cod_producto]);
        return DataTables::of($detalles)->make(true);
    }

//---------------------------------------------------------- Funcion para actualizar datos del encabezado del inventario --------------------------------------
    function actualizar_inventario($id,Request $request)
    {
        //Funcion que permite regresar a proceso un inventario,  cambiar las fechas de realización de un inventario
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',2)->first())
        {
            $editar = Inventario::FindOrFail($id);
    	    $editar->fecha_inicial = $request->fecha_inicial;
            $editar->fecha_final = $request->fecha_final;
            $editar->estado = $request->estado;
    	    if($editar->save())
            {
                $historial              = new Historial();
                $historial->id_usuario  = Auth::user()->id;
                $historial->actividad   = 'Realizo cambios en el inventario númeor '. $id;
                $historial->created_at  = Carbon::now();
                $historial->updated_at  = Carbon::now();
                $historial->save();
            }
    	    return redirect()->route('ver_inventario',['id'=>$id])->with('success','Inventario actualizado con exito');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------- Funcion para finalizar un inventario en proceso --------------------------------------------------
    function finalizar_inventario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $reporte = Inventario::Find($id);
            $rep =  DB::select('select contado, daniado, diferencia, no_diferencias, cast((no_diferencias * 100.0 / contado) as decimal(10, 2)) as exactitud
            from(select count() as contado, sum(mal_estado) as daniado, 
            sum(case when (abs(coalesce(existencia_fisica,0)) - abs(existencia_teorica)) <> 0 then 1 else 0 end) as diferencia,
            sum(case when (abs(coalesce(existencia_fisica,0)) - abs(existencia_teorica)) = 0 then 1 else 0 end) as no_diferencias
            from inventario_web
            where no_encabezado = :id
            and existencia_fisica is not null) as subquery;',['id'=>$id]);
            foreach($rep as $re)
            {
                $exactitud           = $re->exactitud;
                $contado             = $re->contado;
                $daniado             = $re->daniado;
                $diferencia          = $re->diferencia;
            }
            $reporte->exactitud                  = $exactitud;
            $reporte->productos_con_diferencia   = $diferencia;
            $reporte->productos_mal_estado       = $daniado;
            $reporte->productos_total_inventario = $contado;
            $reporte->save();
            $porcentaje = 0;
            $mal_estado = 0;
            $calculo_porcentaje = DB::select('select count(iw.id) as total, (select count(inw.existencia_fisica) as contado
            from inventario_web as inw
            where inw.no_encabezado = :no_encabezado
            and inw.existencia_fisica is not null), sum(mal_estado) as mal_estado
            from inventario_web as iw, 
            where iw.existencia_teorica <> 0
            and iw.no_encabezado = :no_encabezado2',['no_encabezado'=>$id,'no_encabezado2'=>$id]);
            foreach($calculo_porcentaje as $por)
            {
                $contado = 0;
                $total = 0; 
                if($por->contado == 0 || $por->total == 0)
                {
                    $porcentaje = 0;
                }
                else
                {
                    $porcentaje = (number_format($por->contado)/number_format($por->total))*100;
                }
                
            }
            if(Auth::user()->roles == 3)
            {
                //cuando un usuario de una sucursal da por finalizado un inventario, se le notifica por correo al usuario encargado de revizar los mismos. 
                $datos = Inventario::findOrFail($id);
                $datos->estado = 'Finalizado';
                if($porcentaje == 0)
                {
                    $datos->porcentaje = 0;
                    $datos->productos_mal_estado = $daniado;
                }
                else
                {
                    $datos->porcentaje = $porcentaje;
                    $datos->productos_mal_estado = $daniado;
                }
                $datos->updated_at = new Carbon();
                $datos->save();
                //----------------- Historial ------------------------------------
                $historial              = new Historial();
                $historial->id_usuario  = Auth::user()->id;
                $historial->actividad   = 'Finalizo el inventario númeor '. $id;
                $historial->created_at  = Carbon::now();
                $historial->updated_at  = Carbon::now();
                $historial->save();
                //----------------------------------------------------------------
                $fecha = new Carbon();
                $user = User::where('id','=',33)->pluck('email');//se obtiene el correo del usuario a quien va dirijido el correo
                $suc = DB::table('unidades')->where('cod_unidad','=',$datos->sucursal)->first();//se obtiene el nombre de la sucursal a la cual pertenece el inventario
                $sucursal = $suc->nombre;
				//Mail::to($user)->send(new InventarioFinalizado($sucursal,$fecha));// se realiza el envio del correo al encargado de revisar los mismos
                $eliminar = InventarioGeneral::where('no_encabezado',$id)->whereNull('existencia_fisica')->delete();
                return back()->with('success','El inventario se ha finalizado con exito');
            }
            else
            {
                //cuando un usuario de otro rol finaliza el inventario, este no realiza ninguna otra accion
                $datos = Inventario::findOrFail($id);
                if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',2)->first())
                {
                    /*Finaliza el inventario con un porcentaje del 100% para los usuarios con rol de supervisor*/
                    $datos->estado = 'Finalizado';
                    $datos->porcentaje = 100;
                    $datos->productos_mal_estado = $daniado;
                    $datos->updated_at = new Carbon();
                    $datos->save();
                }
                else
                {
                    if($porcentaje == 0)
                    {
                        $datos->porcentaje = 0;
                        $datos->estado = 'Finalizado';
                        $datos->updated_at = new Carbon();
                        $datos->productos_mal_estado = $daniado;
                        $datos->porcentaje = 0;
                        $datos->save();
                    }
                    else
                    {
                        $datos->estado = 'Finalizado';
                        $datos->productos_mal_estado = $daniado;
                        $datos->updated_at = new Carbon();
                        $datos->porcentaje = $porcentaje;
                        $datos->save();
                    }
                }
                //----------------- Historial ------------------------------------
                $historial              = new Historial();
                $historial->id_usuario  = Auth::user()->id;
                $historial->actividad   = 'Finalizo el inventario númeor '. $id;
                $historial->created_at  = Carbon::now();
                $historial->updated_at  = Carbon::now();
                $historial->save();
                $eliminar = InventarioGeneral::where('no_encabezado',$id)->whereNull('existencia_fisica')->delete();
                //----------------------------------------------------------------
                return back()->with('success','El inventario se ha finalizado con exito');
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para realizar un inventario por parte del usuario ---------------------------------------------------
    function productos_del_inventario($numero)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $datos = Inventario::find($numero);
            if(DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',4)->first())
            {
                return view('inventarios.productos_del_inventario',compact('datos','numero'));
            }
            else 
            {
                if($datos->usuario == Auth::id() && $datos->fecha_final >= Carbon::now()->toDateString())/*|| $datos->sucursal == Auth::user()->sucursal || Auth::user()->roles == 1*/
                {
                    return view('inventarios.productos_del_inventario',compact('numero','datos'));
                }
                return back()->with('error','No tienes permiso para realizar cambios o se supero la fecha limite del inventario');
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function porcentaje_inventario($id)
    {
        $porcentaje = Inventario::find($id);
        return number_format($porcentaje->porcentaje,2);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ingresar la existencia fisica de un producto ---------------------------------------------------
    function agregar_existencia_vista($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $detalles = DB::select('select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, existencia_teorica,
            existencia_fisica,  mal_estado, ((abs(existencia_fisica) - abs(existencia_teorica))) as diferencias, no_encabezado 
            from inventario_web 
            where id = :id',['id'=>$id]);
            foreach($detalles as $detalle)
            {
                $no_encabezado = $detalle->no_encabezado;
                $cod_producto = $detalle->cod_producto;
            }
            $datos = Inventario::find($no_encabezado);
            $existencia = DB::select('select p.cod_producto as codigo, convert(numeric(10,0),i.existencia1) as existencia,p.nombre_fiscal as nombre,
            p.nombre_corto as nombre_corto
            from productos_inve as p
            ,inventarios as i
            ,unidades as u
            ,bodegas as b
            ,tipos_prod as t
            where i.empresa = p.empresa
            and i.empresa = u.empresa
            and u.empresa = b.empresa
            and i.cod_producto = p.cod_producto
            and i.cod_unidad = u.cod_unidad
            and u.cod_unidad = b.cod_unidad
            and i.cod_bodega = b.cod_bodega
            and p.cod_tipo_prod = t.cod_tipo_prod
            and p.empresa = t.empresa
            and i.empresa = :empresa 
            and i.cod_unidad = :sucursal 
            and i.cod_bodega = :bodega 
            and p.cod_producto = :cod',['sucursal'=>$datos->sucursal,'bodega'
            =>$datos->bodega,'cod'=>$cod_producto,'empresa'=>Auth::user()->empresa]);

            $historial = DB::select('select pi.nombre_corto, pi.nombre_fiscal, iwd.existencia, iwd.existencia_fisica, iwd.descripcion,
            iwd.created_at, iwd.mal_estado, (iwd.existencia - abs(iwd.existencia_fisica)) as diferencia
            from inventario_web_detalle as iwd,
            productos_inve as pi
            where iwd.no_encabezado = :enca 
            and iwd.cod_producto = :prod
            and pi.cod_producto = iwd.cod_producto
            and pi.empresa = :empresa
            order by iwd.created_at desc',['enca'=>$no_encabezado,'prod'=>$cod_producto,'empresa'=>Auth::user()->empresa]);
            if(DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
            {
                return view('inventarios.agregar_existencia',compact('existencia','detalles','id','historial'));
            }
            else 
            {
                if($datos->usuario == Auth::id() || $datos->sucursal == Auth::user()->sucursal)
                {
                    return view('inventarios.agregar_existencia',compact('existencia','detalles','id','historial'));
                }
                return redirect()->route('inventarios_pendientes')->with('error','No tienes permiso para realizar cambios!');
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para guardar el existencia fisica de un producto en el inventario -----------------------------------
    function guardar_existencia_producto(Request $request)
    {
        /*Funcion para guardar la existencia fisica de un producto durante el conteo de inventario */
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',3)->first())
        {
            /*Permite agregar la existencia fisica de un producto a los usuarios de las sucursales y supervisores*/
            $inventario = InventarioGeneral::find($request->id);

            /*Actualiza el porcentaje de realización del inventario */
            $producto = new Productos();
            $producto->no_encabezado      = $inventario->no_encabezado;
            $producto->cod_producto       = $inventario->cod_producto;
            $producto->existencia_fisica  = $request->existencia_fisica_g;
            $producto->descripcion        = $request->observaciones;
            $producto->mal_estado         = $request->mal_estado_g;
            $producto->existencia         = $request->existencia_real_g;
            $producto->usuario            = Auth::id();
            $producto->created_at         = Carbon::now();
            $producto->updated_at         = Carbon::now();
            $producto->empresa            = Auth::user()->empresa;
            if($producto->save())
            {
                $conteoProductos = DB::select("select (sum(contados)/sum(pendientes))*100 as porcentaje
                from (select count(*) as pendientes, sum(existencia_teorica)*0 as contados
                from inventario_web 
                where no_encabezado = :enca 
                and existencia_teorica <> 0 
                union
                select sum(coalesce(existencia_teorica,0))*0 as pendientes, count(*) as contados
                from inventario_web 
                where no_encabezado = :encab 
                and existencia_teorica <> 0
                and existencia_fisica is not null) as por",['enca'=>$inventario->no_encabezado,'encab'=>$inventario->no_encabezado]);
                foreach($conteoProductos as $ct)
                {
                    $porcentaje = $ct->porcentaje;
                }
                if($request->mal_estado_g==2)
                {
                    /*Actualiza la existencia fisica, teorica y de productos dañados durante el conteo de un inventario*/
                    $act = InventarioGeneral::where('id',$request->id)
                    ->update(['existencia_teorica'=>$request->existencia_real_g,'existencia_fisica'=>$inventario->existencia_fisica+$request->existencia_fisica_g,
                    'mal_estado'=>$inventario->mal_estado+$request->existencia_fisica_g,'updated_at'=>Carbon::now()]);
                    $dat = Inventario::where('id',$inventario->no_encabezado)->update(['porcentaje'=>$porcentaje]);
                    return $inventario;
                }
                /*Actualiza la existencia fisica y la teorica de un producto durante el conteo*/
                $act = InventarioGeneral::where('id',$request->id)->update(['existencia_teorica'=>$request->existencia_real_g,
                'existencia_fisica'=>$inventario->existencia_fisica+$request->existencia_fisica_g,'updated_at'=>Carbon::now()]);
                $dat = Inventario::where('id',$inventario->no_encabezado)->update(['porcentaje'=>$porcentaje]);
                return $inventario;
            }
            return back()->with('error','Parece que algo fallo, refresca la página...');
        }
        return back()->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para realizar busqueda de productos dentro del inventario -------------------------------------------
    function busquedas_inventario($id,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            //esta funcion es utilizada para poder realizar las busquedas de los productos
            $id = $id;
            $categoria = Crypt::encryptString($request->producto);
            return view('inventarios.resultado_busqueda',compact('id','categoria'));
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_busquedas($id,$categoria)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $inventario = DB::select('select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, existencia_teorica,
            existencia_fisica,  mal_estado, ((abs(existencia_fisica) - abs(existencia_teorica))) as diferencias 
            from inventario_web 
            where no_encabezado = :encabezado
            and categoria = :categoria
            and existencia_teorica <> 0',['encabezado'=>$id,'categoria'=>Crypt::decryptString($categoria)]);
            //se hace un llamado al procedimiento almacenado para obtener el resultado de la busqueda
            return DataTables::of($inventario)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para visualizar los productos con existencia en cero ------------------------------------------------
    function productos_cero($encabezado)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            return view('inventarios.existencia_cero',compact('encabezado'));
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_inventario_cero($encabezado)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $inventario = DB::select("select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, convert(int,existencia_teorica),
            coalesce(convert(int,existencia_fisica),0) as existencia_fisica,  coalesce(mal_estado,0) as mal_estado, 
            ((abs(convert(int,existencia_fisica)) - abs(convert(int,existencia_teorica)))) as diferencias,
            convert(varchar(2),null)+ '<button type=button class=btn-danger data-toggle=modal data-target=#create>Editar</button>' as btn  
            from inventario_web 
            where no_encabezado = :encabezado
            and ((coalesce(existencia_fisica,0) = abs(existencia_teorica)))
            ",['encabezado'=>$encabezado]);
            return$inventario;
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function listado_de_productos($id) 
    {
        $producto = DB::select("select pi.cod_producto, pi.nombre_corto, pi.nombre_fiscal
        from productos_inve as pi
        where pi.empresa = :empresa
        and pi.cod_producto not in (select cod_producto
        from inventario_web
        where no_encabezado = :encabezado)
        and pi.precio <> 0
        and pi.descontinuado = 'N'
        and pi.cod_tipo_prod != 'SUMINISTROS'
        and pi.cod_tipo_prod != 'SER01'
        and pi.cod_tipo_prod != 'FLETE'
        and pi.cod_tipo_prod != 'Lamina Cintas'
        and pi.Marca is not null
        order by pi.nombre_corto asc",['empresa'=>Auth::user()->empresa,'encabezado'=>$id]);
        foreach ($producto as $tag) {
        $productos[] = ['id' => $tag->cod_producto, 'nombre_corto' => utf8_encode($tag->nombre_corto), 'nombre_fiscal' => utf8_encode($tag->nombre_fiscal)];
        }
        return $productos;
    }

    function seleccionar_bodega($cod_unidad)
    {
        return DB::table('bodegas')->select('cod_bodega','observacion')->where('cod_unidad',$cod_unidad)->where('empresa',Auth::user()->empresa)->where('cod_bodega','>',0)
        ->where('ACTIVA','S')->get();//recupera las bodegas pertenecientes a una sucursal
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para visualizar el listado de inventarios finalizados ---------------------------------------------
    function finalizados()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            return view('inventarios.inventarios_finalizados');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_finalizados()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            if(Auth::user()->roles == 3)
            {
                $inventarios = DB::select("select iwe.id as numero, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
                iwe.estado, iwe.sucursal, uni.resolucion_autorizacion as sucursal, bo.observacion as bodega, iwe.creado, iwe.apellidos, iwe.productos_mal_estado,
                iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
                datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas, 
                mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
                convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo,
                iwe.productos_mal_estado as daniado, iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud
                from inventario_web_encabezado as iwe,
                users as us,
                unidades as uni,
                bodegas as bo
                where iwe.usuario = :usuario
                and iwe.estado = 'Finalizado'
                and us.id = iwe.usuario
                and uni.cod_unidad = iwe.sucursal 
                and uni.empresa = :empresa
                and bo.cod_bodega = iwe.bodega 
                and uni.cod_unidad = bo.cod_unidad 
                and bo.empresa = uni.empresa
                or iwe.sucursal = :sucursal
                and iwe.bodega = :bodega
                and iwe.estado = 'Finalizado'
                and iwe.usuario = us.id  
                and iwe.sucursal = uni.cod_unidad 
                and iwe.bodega = bo.cod_bodega    
                and uni.empresa = :empresa2                
                and uni.cod_unidad = bo.cod_unidad 
                and uni.empresa = bo.empresa  
                and iwe.created_at > :fecha
                order by numero desc",['usuario'=>Auth::id(),'sucursal'=>Auth::user()->sucursal,'empresa'=>Auth::user()->empresa,
                'empresa2'=>Auth::user()->empresa,'bodega'=>Auth::user()->bodega,'fecha'=>Carbon::now()->submonths(4)]);
                return DataTables::of($inventarios)->make(true);
            }
            $inventarios = DB::select("select iwe.id as numero, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.resolucion_autorizacion as sucursal, bo.observacion as bodega, iwe.creado, iwe.apellidos, iwe.productos_mal_estado,
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas, 
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo,
            iwe.productos_mal_estado as daniado, iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud 
            from inventario_web_encabezado as iwe,
            users as us,
            unidades as uni,
            bodegas as bo
            where iwe.sucursal = :usuario
            and iwe.estado = 'Finalizado'
            and us.id = iwe.usuario
            and uni.cod_unidad = iwe.sucursal 
            and uni.empresa = :empresa
            and uni.cod_unidad = bo.cod_unidad
            and bo.cod_bodega = iwe.bodega   
            and bo.empresa = uni.empresa
            and iwe.created_at > :fecha
            order by numero desc",['usuario'=>Auth::user()->sucursal,'empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->submonths(4)]);
            return DataTables::of($inventarios)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para poder generar un nuevo inventario general ----------------------------------------------------
    function crear_inventario()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',4)->first())
        {
            //permite crear un nuevo inventario
            $sucursales = DB::table("unidades")->where('empresa',Auth::user()->empresa)->orderby('cod_unidad','asc')->get();//consulta que trae todas las sucursales que pertenecen a la empresa 1
            return view('inventarios.crear_inventario',compact('sucursales'))->with('success','Favor llenar todas la casillas');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function guardar_inventario_general(Request $request)
    {	
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',4)->first())
        {
            /*Funcion para guardar los datos del encabezado de un nuevo inventario general*/
            $inventario                     = new Inventario();
            $inventario->encargado          = $request->encargado;
            $inventario->apellidos          = $request->apellidos;
            $inventario->no_identificacion  = $request->no_identificacion;
            $inventario->estado             = 'En proceso';
            $inventario->sucursal           = $request->sucursal;
            $inventario->bodega             = $request->bodega;
            $inventario->usuario            = Auth::id();
            $inventario->creado             = 'No'; 
            $inventario->created_at         = Carbon::now();
            $inventario->updated_at         = Carbon::now();
            if($inventario->save())
            {
                $historial              = new Historial();
                $historial->id_usuario  = Auth::id();
                $historial->actividad   = 'Genero un nuevo inventario general para '.$request->encargado.' '.$request->apellidos;
                $historial->created_at  = new Carbon();
                $historial->updated_at  = new Carbon();
                $historial->save();
            }
            return redirect()->route('inventarios_pendientes')->with('success','Inventario general creado correctamente');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Fución para eliminar inventarios antes de cargar los productos ----------------------------------------------
    function eliminar_inventario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',4)->first())
        {
            $eliminar = Inventario::where('id',$id)->where('creado','No')->delete();
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Elimino un inventario general número '.$id;
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            return back()->with('success','Inventario eliminado correctamente');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Función para cargar los productos dentro de un inventario ---------------------------------------------------
    function cargar_productos_en_inventario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',3)->first())
        {
            $datos = Inventario::findOrFail($id);
            if($datos->semana == NULL && $datos->creado == 'No')
            {
                $datos->creado = 'Si';
                $datos->updated_at = Carbon::now();
                $datos->save(); 
                /*Funcion para cargar los productos a un nuevo inventario general */
                $todos_productos = DB::select("select p.cod_producto,i.existencia1 as existencia,p.nombre,p.nombre_fiscal,p.descontinuado as descontinuado,p.nombre_corto as nombre_corto,p.cod_tipo_prod,t.nombre as TABLA_APLICABLE
                from productos_inve as p
                ,inventarios as i
                ,inventario_web_categorias as t
                where i.empresa = p.empresa
                and i.cod_producto = p.cod_producto
                and p.cod_tipo_prod = t.cod_tipo_prod and p.cod_tipo_prod <> 'FLETE'
                and p.empresa = t.empresa
                and i.empresa = :empresa
                and i.cod_unidad = :sucursal
                and i.cod_bodega = :bodega
                and descontinuado = 'N'
                and i.existencia1 <> 0",['sucursal'=>$datos->sucursal,'bodega'=>$datos->bodega,'empresa'=>Auth::user()->empresa]);
                foreach ($todos_productos as $nuevo) 
                {
                    $nuevin                     = new InventarioGeneral();
                    $nuevin->cod_producto       = $nuevo->cod_producto;
                    $nuevin->nombre_corto       = $nuevo->nombre_corto;
                    $nuevin->nombre_fiscal      = $nuevo->nombre_fiscal;
                    $nuevin->existencia_teorica = $nuevo->existencia;
                    $nuevin->categoria          = $nuevo->TABLA_APLICABLE;
                    $nuevin->no_encabezado      = $datos->id;
                    $nuevin->created_at         = Carbon::now();
                    $nuevin->updated_at         = Carbon::now();
                    $nuevin->empresa             = Auth::user()->empresa;
                    $nuevin->save();
                }
                return redirect()->route('inventarios_pendientes')->with('success','Inventario general creado correctamente');
            }
            elseif($datos->semana == NULL && $datos->creado == 'Si')
            {
                return redirect()->route('inventarios_pendientes')->with('error','No se permite cargar más de una vez los productos');
            }
            elseif($datos->semana != NULL && $datos->creado == 'No')
            {
                $datos->creado = 'Si';
                $datos->updated_at = Carbon::now();
                $datos->save(); 
                if($datos->semana == 'suministros')
                {
                    $todos_productos =  DB::select("select iws.cod_producto, iws.cod_tipo_prod, iws.nombre_fiscal, iws.nombre_corto, i.existencia1 
                    from inventario_web_productos_semana as iws,
                    inventarios as i
                    where semana = :se
                    and iws.cod_producto = i.cod_producto
                    and i.cod_unidad = :suc
                    and i.cod_bodega = :bod 
                    and i.existencia1 <> 0
                    and i.empresa = :empresa
                    ",['se'=>$datos->semana,'suc'=>$datos->sucursal,'bod'=>$datos->bodega,'empresa'=>Auth::user()->empresa,]);
                }
                else 
                {
                    $todos_productos =  DB::select("select iws.cod_producto, iws.cod_tipo_prod, iws.nombre_fiscal, iws.nombre_corto, i.existencia1 
                    from inventario_web_productos_semana as iws,
                    inventarios as i
                    where semana = :se
                    and iws.cod_producto = i.cod_producto
                    and i.cod_unidad = :suc
                    and i.cod_bodega = :bod 
                    and i.existencia1 <> 0
                    and i.empresa = :empresa
                    or iws.semana = :sie
                    and iws.cod_producto = i.cod_producto
                    and i.cod_unidad = :su
                    and i.cod_bodega = :bo 
                    and i.existencia1 <> 0
                    and i.empresa = :empresa2",['se'=>$datos->semana,'sie'=>'Siempre','suc'=>$datos->sucursal,'bod'=>$datos->bodega,'su'=>$datos->sucursal,
                    'bo'=>$datos->bodega,'empresa'=>Auth::user()->empresa,'empresa2'=>Auth::user()->empresa]);
                }
                foreach ($todos_productos as $nuevo) 
                {
                    $nuevin = new InventarioGeneral();
                    $nuevin->cod_producto       = $nuevo->cod_producto;
                    $nuevin->nombre_corto       = $nuevo->nombre_corto;
                    $nuevin->nombre_fiscal      = $nuevo->nombre_fiscal;
                    $nuevin->existencia_teorica = $nuevo->existencia1;
                    $nuevin->categoria          = $nuevo->cod_tipo_prod;
                    $nuevin->created_at         = Carbon::now();
                    $nuevin->updated_at         = Carbon::now();
                    $nuevin->no_encabezado      = $id;
                    $nuevin->empresa            = Auth::user()->empresa;
                    $nuevin->save();
                } 
                return redirect()->route('inventarios_pendientes')->with('success','Inventario semanal creado correctamente');
            }
            elseif($datos->semana != NULL && $datos->creado == 'Si')
            {
                return redirect()->route('inventarios_pendientes')->with('error','Se a producido un error durante el procedimiento');
            }
            return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
        } 
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ver el listado de usuarios que pueden realizar inventarios -------------------------------------
    function listado_reporte_inventarios()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        {
            return view('inventarios.listado_reportes');
        } 
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_listado_de_usuarios_y_sucursales()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        {
            $sucursales = DB::table('users')
            ->select(['name','email','sucursal','bodega','id'])->where('roles',3)->orwhere('roles',1)->orderby('roles','desc');
            return DataTables::of($sucursales)->make(true);
        } 
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver los inventarios realizados por el usuario consultado -------------------------------------
    function inve_por_sucursal($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        { 
            return view('inventarios.inventarios_reporte',compact('id'));
        } 
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function inventario_por_sucursal($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        { 
            $inventarios = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, 
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas, 
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo,
            iwe.productos_mal_estado as daniado,
            iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud, iwe.porcentaje, iwe.semana
            from inventario_web_encabezado as iwe,
            users as us,
            unidades as uni, 
            bodegas as bo  
            where iwe.usuario = :id
            and us.id = iwe.usuario
            and uni.cod_unidad = iwe.sucursal
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.cod_bodega = iwe.bodega 
            and bo.empresa = uni.empresa
            //and us.roles = 3
            order by iwe.id desc",['id'=>$id,'empresa'=>Auth::user()->empresa]);
            return DataTables::of($inventarios)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver los inventarios realizados por un usuario en una fecha en concreto -----------------------
    function vista_inventarios_por_fecha(Request $request,$id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        { 
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('inventarios.inventarios_por_fecha',compact('inicio','fin','id'));
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_inventarios_fecha($id,$inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        { 
            $inventarios = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, iwe.productos_mal_estado,
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas, 
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo, 
            iwe.productos_mal_estado as daniado, iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud 
            from inventario_web_encabezado as iwe,
            users as us, 
            unidades as uni, 
            bodegas as bo 
            where iwe.sucursal = uni.cod_unidad
            and us.id = iwe.usuario
            and iwe.bodega = bo.cod_bodega            
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.empresa = uni.empresa
            and iwe.created_at between :inicio and :fin
            and iwe.usuario = :id",['inicio'=>$inicio,'fin'=>$fin,'id'=>$id,'empresa'=>Auth::user()->empresa]); 
            return DataTables::of($inventarios)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver los inventarios realizados por un usuario en una fecha en concreto -----------------------
    function inventarios_por_fecha_general(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        { 
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('inventarios.inventarios_por_fecha_general',compact('inicio','fin'));
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_inventario_fecha_general($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        { 
            $inventarios = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, iwe.productos_mal_estado,
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas, 
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo,
            iwe.productos_mal_estado as daniado, iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud 
            from inventario_web_encabezado as iwe,
            users as us, 
            unidades as uni, 
            bodegas as bo 
            where iwe.sucursal = uni.cod_unidad
            and us.id = iwe.usuario
            and iwe.bodega = bo.cod_bodega            
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.empresa = uni.empresa
            and iwe.created_at between :inicio and :fin",['inicio'=>$inicio,'fin'=>$fin,'empresa'=>Auth::user()->empresa]); 
            return DataTables::of($inventarios)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción'); 
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para imprimir en PDF los inventarios --------------------------------------------------------------
    function pdf($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $datos = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, 
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final
            from inventario_web_encabezado as iwe,
            users as us, 
            unidades as uni, 
            bodegas as bo 
            where iwe.sucursal = uni.cod_unidad
            and us.id = iwe.usuario
            and iwe.bodega = bo.cod_bodega            
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.empresa = uni.empresa
            and iwe.id = :enca",['enca'=>$id,'empresa'=>Auth::user()->empresa]);//se obtienen los datos del encabezadod del inventario
            $ver_inv =  DB::select('select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, existencia_teorica,
            existencia_fisica,  mal_estado, ((abs(existencia_fisica) - abs(existencia_teorica))) as diferencias 
            from inventario_web 
            where no_encabezado = :encabezado
            and existencia_fisica is not null
            order by categoria asc',['encabezado'=>$id]);//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
            $pdf = PDF::loadView('inventarios.pdf',compact('ver_inv','datos','id'));
            return $pdf->download('inventario.pdf');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function pdf_diferencias_positivas($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $datos = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, 
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final
            from inventario_web_encabezado as iwe,
            users as us, 
            unidades as uni, 
            bodegas as bo 
            where iwe.sucursal = uni.cod_unidad
            and us.id = iwe.usuario
            and iwe.bodega = bo.cod_bodega            
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.empresa = uni.empresa
            and iwe.id = :enca",['enca'=>$id,'empresa'=>Auth::user()->empresa]);//se obtienen los datos del encabezadod del inventario
            $ver_inv =  DB::select('select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, existencia_teorica,
            existencia_fisica,  mal_estado, ((abs(existencia_fisica) - abs(existencia_teorica))) as diferencias 
            from inventario_web 
            where no_encabezado = :encabezado
            and existencia_fisica is not null
            order by categoria asc',['encabezado'=>$id]);//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
            $pdf = PDF::loadView('inventarios.pdf_diferencias_positivas',compact('ver_inv','datos','id'));
            return $pdf->download('inventario.pdf');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    public function pdf_diferencias_negativas($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',1)->first())
        {
            $datos = DB::select("select iwe.id, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.nombre as sucursal, bo.nombre as bodega, iwe.creado, iwe.apellidos, 
            iwe.no_identificacion,iwe.porcentaje, iwe.fecha_inicial, uni.direccion, iwe.fecha_final
            from inventario_web_encabezado as iwe,
            users as us, 
            unidades as uni, 
            bodegas as bo 
            where iwe.sucursal = uni.cod_unidad
            and us.id = iwe.usuario
            and iwe.bodega = bo.cod_bodega            
            and uni.cod_unidad = bo.cod_unidad 
            and uni.empresa = :empresa
            and bo.empresa = uni.empresa
            and iwe.id = :enca",['enca'=>$id,'empresa'=>Auth::user()->empresa]);//se obtienen los datos del encabezadod del inventario
            $ver_inv =  DB::select('select id, no_encabezado, cod_producto, categoria, nombre_corto, nombre_fiscal, existencia_teorica,
            existencia_fisica,  mal_estado, ((abs(existencia_fisica) - abs(existencia_teorica))) as diferencias 
            from inventario_web 
            where no_encabezado = :encabezado
            and existencia_fisica is not null
            order by categoria asc',['encabezado'=>$id]);//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
            $pdf = PDF::loadView('inventarios.pdf_diferencias_negativas',compact('ver_inv','datos','id'));
            return $pdf->download('inventario.pdf');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_de_inventario($id)
    {
        $inventarios = DB::select('select no_encabezado, id, categoria, cod_producto, nombre_corto, nombre_fiscal, existencia_teorica,  existencia_fisica,
        mal_estado, empresa, ((abs(existencia_fisica) - abs(existencia_teorica))) as diferencias 
        from inventario_web
        where no_encabezado = :id
        and existencia_fisica is not null',['id'=>$id]);
        return DataTables::of($inventarios)->addColumn('details_url', function($inventarios){
            return url('det_invent/'. $inventarios->no_encabezado .'/' .$inventarios->cod_producto);
        })->make(true);  
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para reporte de productos contados por fecha en inventarios -----------------------------------------------------------
    function buscar_producto_inventario(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[5])->first())
        {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $tags = DB::table('productos_inve')->where('nombre_fiscal','like','%'. $term .'%')
            ->orwhere('nombre_corto','like','%'. $term .'%')->limit(10)->get();
            $formatted_tags = [];
            foreach ($tags as $tag) {
                $formatted_tags[] = ['id' => $tag->cod_producto, 'text' => utf8_encode($tag->nombre_corto).' - '.$tag->nombre_fiscal];
            }
            return \Response::json($formatted_tags);
        }
        return back()->json_encode('error');
    }

    function ultima_vez_contado_en_inventario($id, Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[5])->first())
        {
            $producto = DB::select('select iw.no_encabezado, iw.nombre_corto, iw.nombre_fiscal, iw.existencia_teorica, 
            iw.existencia_fisica, iw.mal_estado, iw.updated_at, iwe.semana, u.name
            from inventario_web as iw,
            inventario_web_encabezado as iwe,
            users as u
            where iw.no_encabezado = iwe.id
            and iw.cod_producto = :cod_producto
            and iwe.usuario = u.id
            and iw.updated_at > :fecha
            and iwe.usuario = :id
            order by iw.updated_at desc',['cod_producto'=>$request->cod_producto,'fecha'=>Carbon::now()->subMonths(24),'id'=>$id]);
            //return $request;
            return view('inventarios.productoFecha',compact('producto','id'));
        }
        return back()->with('error','No tienes permisos para ingresar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
//----------------------------------------------- Funciones para visualizar el listado de inventarios general -------------------------------------------------
    function reporte_inventarios()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        {
            return view('inventarios.reporte_inventarios');
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }

    function datos_reporte_inventarios()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        {      
            $inventarios = DB::select("select iwe.id as numero, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, uni.resolucion_autorizacion as sucursal, bo.observacion as bodega, iwe.creado, iwe.apellidos, iwe.productos_mal_estado,
            iwe.no_identificacion, iwe.porcentaje as realizado , iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas,
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo,
            iwe.productos_mal_estado as daniado, iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud
            from inventario_web_encabezado as iwe,
            users as us,
            unidades as uni,
            bodegas as bo
            where us.id = iwe.usuario
            and uni.cod_unidad = iwe.sucursal
            and uni.empresa = :empresa
            and bo.cod_bodega = iwe.bodega
            and uni.cod_unidad = bo.cod_unidad
            and bo.empresa = uni.empresa  
            and iwe.created_at > :fecha",['empresa'=>Auth::user()->empresa,'fecha'=>Carbon::now()->subMonths(3)]);
            return DataTables::of($inventarios)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para visualizar el reporte general de inventarios filtrados por fecha -------------------------------------------------
    function reporte_inventarios_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        {
            $inicio = $request -> inicio;
            $fin = $request -> fin;
            return view('inventarios.reporte_inventarios_fecha', compact('inicio', 'fin'));
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
 

    function datos_reporte_inventarios_fecha($inicio, $fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',5)->first())
        {      
            $inventarios = DB::select("select iwe.id as numero, iwe.encargado, iwe.created_at,iwe.updated_at, us.name as nombre, iwe.bodega,
            iwe.estado, iwe.sucursal, uni.resolucion_autorizacion as sucursal, bo.observacion as bodega, iwe.creado, iwe.apellidos, iwe.productos_mal_estado,
            iwe.no_identificacion, iwe.porcentaje as realizado , iwe.fecha_inicial, uni.direccion, iwe.fecha_final, iwe.semana,
            datediff(dd,iwe.created_at,iwe.updated_at) as dias, mod(datediff(hh,iwe.created_at,iwe.updated_at),24) as horas,
            mod(datediff(mi,iwe.created_at,iwe.updated_at),60) as minutos,
            convert(varchar(10),dias)+' dias '+convert(varchar(20),horas)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia_tiempo,
            iwe.productos_mal_estado as daniado, iwe.productos_total_inventario as contado, iwe.productos_con_diferencia as diferencia, iwe.exactitud
            from inventario_web_encabezado as iwe,
            users as us,
            unidades as uni,
            bodegas as bo
            where us.id = iwe.usuario
            and uni.cod_unidad = iwe.sucursal
            and uni.empresa = :empresa
            and bo.cod_bodega = iwe.bodega
            and uni.cod_unidad = bo.cod_unidad
            and bo.empresa = uni.empresa  
            and iwe.created_at between :inicio and :fin",['empresa'=>Auth::user()->empresa, 'inicio'=>$inicio, "fin"=>$fin]);
            return DataTables::of($inventarios)->make(true);
        }
        return redirect()->route('inventarios_pendientes')->with('error','No tienes permisos para realizar esta acción');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

    /*public function datos_inventario_cd($encabezado)
    {
        $inventario = DB::select('select iw.id, iw.no_encabezado, iw.cod_producto, iw.categoria, iw.nombre_corto, iw.nombre_fiscal, iw.existencia_teorica,
        iw.existencia_fisica, iw.mal_estado, ((abs(iw.existencia_fisica) - abs(iw.existencia_teorica))) as diferencias, total.cantidad
        from(
        select det.cod_producto, sum(det.cantidad1) as cantidad
        from movi_inve as mo,
        det_movi_inve as det,
        where det.num_movi = mo.num_movi
        and mo.cod_motivo = 94
        and mo.cod_tipo_movi = :tipo
        and mo.cod_serie_movi= :serie
        and det.cod_tipo_movi = :tipo2
    	and det.cod_serie_movi = :serie2
        group by det.cod_producto) as total
        full outer join inventario_web as iw on total.cod_producto = iw.cod_producto
        where iw.no_encabezado = :encabezado
        and existencia_teorica <> 0
        group by iw.id, iw.no_encabezado, iw.cod_producto, iw.categoria, iw.nombre_corto, iw.nombre_fiscal, iw.existencia_teorica,
        iw.existencia_fisica, iw.mal_estado, total.cantidad',['encabezado'=>$encabezado,'tipo'=>'E','serie'=>'TA','tipo2'=>'E','serie2'=>'TA']);
        return DataTables($inventario)->make(true);
    }

    public function formulario_inventario($id,$cod)
    {
        //esta funcion genera la vista para el ingreso de datos para los productos al momento de realizar el inventario
        $cod = $cod;//esta variable atrapa el valor del codigo del producto
        $id = $id;//esta variable atrapa el id del inventario que se esta realizando
        $datos = Inventario::find($id);//acá por medio de la variable del ID anterior, recuperamos el número de la sucursal y la bodega a la cual se le está realizando el inventario
        $todos_productos = DB::select('call inventario_web_producto_existencia(?,?,?)',array($datos->sucursal,$datos->bodega,$cod));//se hace el llamado al procedimiento almacenado que realiza la consulta de la existencia actual de. producto al cual estamos operando
        $existencia = Productos::where('no_encabezado',$id)->where('cod_producto',$cod)->get();//esta consulta devuelve todos los registros del producto pertenecientes al ID del inventario que se está realizando
        $suma = 0;//variable para realizar suma total de la cantidad del producto
        $diferencia = 0;//variable que almacena la diferencia entre la existencia teorica con la existencia fisica de un producto
        foreach ($existencia as $total) //permite realizar la suma de el producto que se está operando
        {
            $suma += $total->existencia_fisica;//atrapa el valor anterior, más el nuevo y realiza la suma
            $diferencia= $suma- $total->existencia;//la diferencia se calcula restando al total de la suma, el total de la existencia teorica
        }
        if(Auth::user()->roles == 3)
        {
            return view('sucursales.producto',compact('todos_productos','cod','id','existencia','suma','diferencia','datos'));
        }
        elseif(Auth::user()->roles == 1)
        {
          return view('formularios.producto',compact('todos_productos','cod','id','existencia','suma','diferencia','datos'));
        }
        {
            return view('panel.producto',compact('todos_productos','cod','id','existencia','suma','diferencia','datos'));
        }
    }

//categoria_nombre

    public function categoria_codigo()
    {
        //productos_modificar
        $categoria_codigo = DB::select('call inventario_web_productos_inve_agregar_codigo()');
    }
	
	public function inve_por_supervisor($id)
    {
        return view('productos.inventarios',compact('id'));
    }

    public function datos_por_supervisor($id)
    {
        $inventarios = DB::select('select inventario_web_encabezado.id as numero,inventario_web_encabezado.encargado as encargado,
        inventario_web_encabezado.created_at as created_at,inventario_web_encabezado.updated_at as updated_at,users.name as nombre,
        inventario_web_encabezado.bodega as bodega,inventario_web_encabezado.estado as estado,inventario_web_encabezado.sucursal as sucursal,
        unidades.nombre as uninombre,bodegas.nombre as bonombre,inventario_web_encabezado.creado as creado,
        inventario_web_encabezado.porcentaje as porcentaje
        from inventario_web_encabezado
        join users on users.id = inventario_web_encabezado.usuario
        join unidades on unidades.cod_unidad = inventario_web_encabezado.sucursal and empresa = 1
        join bodegas on bodegas.cod_bodega = inventario_web_encabezado.bodega and unidades.cod_unidad = bodegas.cod_unidad and bodegas.empresa = 1
        where inventario_web_encabezado.usuario = :usuario
        order by numero desc',['usuario'=>$id]);
        return DataTables::of($inventarios)->make(true);
    }
	
	public function inve_por_fecha(Request $request)
    {
        $inicio = $request->fecha_inicial;
        $fin = $request->fecha_final;
        if(Auth::user()->roles == 1)
        {
            $inventarios = DB::select('call inventario_web_fechas(?,?)',array($request->fecha_inicial,$request->fecha_final));
            return view('inventarios',compact('inventarios'));
        }
        else
        { 
            return view('panel.inventarios_fecha',compact('inicio','fin'));
        }
    }

    public function datos_inventarios_por_fecha($inicio,$fin)
    {
        $inventarios = DB::select('select inventario_web_encabezado.id as numero,inventario_web_encabezado.encargado as encargado,
        inventario_web_encabezado.created_at as created_at,inventario_web_encabezado.updated_at as updated_at,users.name as nombre,
        inventario_web_encabezado.bodega as bodega,inventario_web_encabezado.estado as estado,inventario_web_encabezado.sucursal as sucursal,
        unidades.nombre as uninombre,bodegas.nombre as bonombre,inventario_web_encabezado.creado as creado,inventario_web_encabezado.semana as semana,
        inventario_web_encabezado.fecha_inicial as fecha_inicial,inventario_web_encabezado.fecha_final,inventario_web_encabezado.porcentaje as porcentaje
        from inventario_web_encabezado
        join users on users.id = inventario_web_encabezado.usuario
        join unidades on unidades.cod_unidad = inventario_web_encabezado.sucursal and empresa = 1
        join bodegas on bodegas.cod_bodega = inventario_web_encabezado.bodega and unidades.cod_unidad = bodegas.cod_unidad and bodegas.empresa = 1
        where fecha_inicial between :inicio and :fin
        order by numero desc',['inicio'=>$inicio,'fin'=>$fin]);
        return DataTables::of($inventarios)->make(true);
    }

    
	
	public function inve_por_fecha_supervisor(Request $request)
    {
        $id = $request->id;
        $inventarios = DB::select('call inventario_web_fechas_supervisores(?,?,?)',array($request->fecha_inicial,$request->fecha_final,$request->id));
        return view('productos.inventarios',compact('inventarios','id'));
    }

    public function sucursal_inventario($id,$cod)
    {
         //esta funcion genera la vista para el ingreso de datos para los productos al momento de realizar el inventario
        $cod = $cod;//esta variable atrapa el valor del codigo del producto
        $id = $id;//esta variable atrapa el id del inventario que se esta realizando
        $datos = Inventario::find($id);//acá por medio de la variable del ID anterior, recuperamos el número de la sucursal y la bodega a la cual se le está realizando el inventario
        $todos_productos = DB::select('call inventario_web_producto_existencia(?,?,?)',array($datos->sucursal,$datos->bodega,$cod));//se hace el llamado al procedimiento almacenado que realiza la consulta de la existencia actual de. producto al cual estamos operando

        $existencia = DB::table('inventario_web_detalle')->where('no_encabezado',$id)->where('cod_producto',$cod)->get();//esta consulta devuelve todos los registros del producto pertenecientes al ID del inventario que se está realizando
        $suma = 0;//variable para realizar suma total de la cantidad del producto
        $diferencia = 0;//variable que almacena la diferencia entre la existencia teorica con la existencia fisica de un producto
        foreach ($existencia as $total) //permite realizar la suma de el producto que se está operando
        {
            $suma += $total->existencia_fisica;//atrapa el valor anterior, más el nuevo y realiza la suma
            $diferencia= $suma- $total->existencia;//la diferencia se calcula restando al total de la suma, el total de la existencia teorica
        }
        return view('formularios.producto_sucursal',compact('todos_productos','cod','id','existencia','suma','diferencia','datos'));
    }
	
	public function imprimir($id)
    {
        $id = $id;//atrapa el ID del inventario
            $dato = Inventario::find($id);//
            $datos = DB::select('call inventario_web_encabezado_completo(?)',array($id));//se obtienen los datos del encabezadod del inventario
            $ver_inv =  DB::table('inventario_web')->where('no_encabezado',$id)
            ->where('existencia_fisica','!=',NULL)->groupby('categoria','id','cod_producto','nombre_corto','nombre_fiscal','existencia_teorica','no_encabezado',
            'created_at','updated_at','existencia_fisica','mal_estado'/*,'empresa')->orderby('categoria','asc')->get();//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
            $o = DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_teorica','>=',1)->orwhere('no_encabezado',$id)->where('existencia_teorica','<',0)->count('id');//esta consulta permite traer todos los productos que seran mostrados en la vista, para poder realizar el inventario
            $os = DB::select('call inventario_web_operado(?)',array($id));//esta consulta permite traer todos los productos que seran mostrados en la vista, para poder realizar el inventario
            foreach ($os as $os){
            $ver = $os->total;
            }
            $suma = ($ver / $o)*100;
        return view('productos.imprimir',compact('ver_inv','datos','id','suma'));
    }


    public function transferencias_pendientes()
    {
        return view('sucursales.transferencias');
    }

    public function datos_transferencias()
    {
        $inventario = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado, iwet.fechaEntrega,
        iwet.unidad_transf, tv.propietario
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        full join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
        where iwet.id_estado between 14 and 19
        and iwet.unidad_transf = :us
        and u.empresa = 1',['us'=>Auth::user()->sucursal]);
        return DataTables($inventario)->make(true);
    }



    public function transferencias_finalizadas()
    {
        return view('sucursales.transferenciasFinalizadas');
    }

    public function datos_transferencias_finalizadas()
    {
        $inventario = DB::select('select num_movi, fecha, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
        iwet.created_at, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        full join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa  
        where iwet.id_estado = 20
        and u.empresa = 1
        and iwet.unidad_transf = :id',['id'=>Auth::user()->sucursal]);
        return DataTables($inventario)->make(true);
    }

    public function editar_transferencia(Request $request,$id)
    {
        $tran =  Transferencias::findOrFail($id);
        //return $tran;
        
        
    }

    public function ver_producto(Request $request)
    {
        $producto = collect(DB::select("select idt.id, replace(pi.nombre_corto,'ñ','N') as nombre_corto, pi.nombre_fiscal, convert(integer, idt.cantidadRecibida), floor(idt.cantidad1) as cantidad1
        from inventario_web_det_transferencias as idt,
        productos_inve as pi,
        where idt.id = :id
        and idt.cod_producto = pi.cod_producto
        and pi.empresa = 1",['id'=>$request->id]));
        $task = '';
        foreach($producto as $pro)
        {
            $task = ['cantidad1'=>$pro->cantidad1,'nombre_corto'=>$pro->nombre_corto.' '.$pro->nombre_fiscal,'cantidad'=>$pro->cantidadRecibida];
        }
       /* return DataTables($task)->make(true);
        return $task;
    }

    public function agregar_producto_transferencia($id)
    {
        $editar = Transferencias::find($id);
        if($editar->id_estado < 20)
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
            iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga, iwet.fechaEntrega
            from inventario_web_encabezado_transferencias as iwet, 
            unidades as u,
            inventario_web_estados as iwe
            where iwet.num_movi = :id
            and iwet.unidad_transf = u.cod_unidad
            and iwet.id_estado = iwe.id
		    and u.empresa = 1',['id'=>$id]);
            return view('sucursales.AgregarProTransferencia',compact('tran','id'));
        }
        else
        {
            return redirect()->route('tran_su')->with('error','¡No es posible agregar más productos!');
        }
    }

    public function productos_faltantes($id)
    {
        $productos = DB::select("select iwt.id, iwt.cod_producto, replace(pi.nombre_corto,'ñ','N') as nombre_corto, ic.nombre,  pi.nombre_fiscal,  
        iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi, iwt.cantidad1
        from inventario_web_det_transferencias as iwt,
        productos_inve as pi, 
        inventario_web_categorias as ic,
        where iwt.num_movi = :id
        and iwt.cod_producto = pi.cod_producto
        and iwt.empresa = 1
        and iwt.incluido is null
        and pi.cod_tipo_prod = ic.cod_tipo_prod
        and pi.empresa = 1
        and ic.empresa = 1",['id'=>$id]);
        return $productos;
    }

    public function actualizar_transferencia(Request $request)
    {
        $nuevo = DetTransferencias::findOrFail($request->id);
        $nuevo->cantidadSolicitada = 0;
        $nuevo->noIncluido = 1;
        $nuevo->incluido = 1;
        $nuevo->updated_at = new Carbon();
        $nuevo->save();
        return $nuevo;
    }*/
}
