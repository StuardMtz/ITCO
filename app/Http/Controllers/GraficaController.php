<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\User;
use App\Semana;
use Carbon\Carbon;
use App\Historial;
use App\Inventario;
use App\ProductoSemana;
use Yajra\DataTables\DataTables;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class GraficaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

//----------------------------------------------- Funciones para utilizar el minimax general de sucursales ----------------------------------------------------
    public function minimo_maximo_general()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            return view('reportes.minimaxGeneral');
        }
        else 
        {
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
            if($permiso == true)
            {
                return redirect()->route('rep_suc_lis');
            }
            else 
            {
                return redirect()->route('home')->with('error','No tienes permisos para accesar');
            }
        }
    }

    public function datos_minimo_maximo_general()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $existencia = DB::select("select u.name, pi.cod_producto, iwc.cod_tipo_prod,  replace(pi.nombre_corto, 'CAÑ','CAN') as nombre_corto, 
            pi.nombre_fiscal, i.minimo, i.existencia1,
            i.maximo, (i.existencia1 / (i.maximo+0.00001))*100 as porcentaje
            from productos_inve as pi,
            inventarios as i,
            users as u,
            inventario_web_categorias as iwc
            where pi.empresa = u.empresa
            and i.empresa = u.empresa
            and iwc.empresa = u.empresa
            and i.minimo > 0
            and pi.descontinuado = 'N'
            and pi.cod_producto = i.cod_producto
            and u.sucursal = i.cod_unidad
            and u.roles = 3
            and i.cod_unidad != 14
            and iwc.cod_tipo_prod = pi.cod_tipo_prod
            and u.bodega = 1
			and i.cod_bodega != 2
            order by pi.cod_producto asc");
            return DataTables::of($existencia)->make(true);
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ver la existencia en la bodega de terminado planta ---------------------------------------------
    function existencia_terminado()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',35)->first();
        if($permiso == true)
        {
            $existencia = DB::select('select p.nombre_corto, p.nombre_fiscal, i.existencia1,
            p.cod_tipo_prod 
            from inventarios as i,
            productos_inve as p
            where i.cod_unidad = 11 
            and i.cod_bodega = 1
            and i.existencia1 > 0
            and i.empresa = 1
            and p.empresa = i.empresa
            and p.cod_producto = i.cod_producto 
            and p.cod_tipo_prod != :ser01
            and p.nombre_corto != :nca',['ser01'=>'SER01','nca'=>'NCA']);
            return view('reportes.bodegaTerminado',compact('existencia'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para utilizar el minimax por sucursal -------------------------------------------------------------
    function listado_de_sucursales()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first())
        {
            return view('reportes.sucursales');
        }
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
    }

    public function datos_listado_sucursales()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
        {
            $sucursales = user::where('roles',3)->where('empresa',Auth::user()->empresa)->get();
            return DataTables($sucursales)->make(true);
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function existencia_productos($sucursal,$bodega,$todo)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
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
                return view('reportes.sucursal_existencia',compact('sucu','bod','sucursal','bodega'));
            }
            elseif($todo == '2')
            {
                return view('reportes.sucursal_existencia_minimo',compact('sucu','bod','sucursal','bodega'));
            }
            else
            {
                return view('reportes.sucursal_existencia_reorden',compact('sucu','bod','sucursal','bodega'));
            }
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function datos_existencia($sucursal,$bodega)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
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
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function datos_existencia_minimos($sucursal,$bodega)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
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
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function datos_existencia_maximos($sucursal,$bodega)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
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
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function graficado($sucursal,$bodega,$producto)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
        {
            $mesd = Carbon::now();
            $fecha_b = Carbon::now();
            $mesu = $fecha_b->subMonths(6);
            $prod = DB::table('Productos_inve')->where('cod_producto',$producto)->first();
            $pro = $prod->nombre_fiscal;
            $datos = DB::select('select unidades.nombre as sucursal,unidades.cod_unidad as cod_unidad,bodegas.observacion as bodega,bodegas.cod_bodega as cod_bodega,
            bodegas.Cod_Cliente as Cod_Cliente,inventarios_diarios.cod_producto,inventarios_diarios.cantidad as existencia,inventarios_diarios.day as dia,
            inventarios_diarios.minimo as min,inventarios_diarios.maximo as max,inventarios_diarios.fecha as fecha,inventarios_diarios.reorden as reorden,
            pi.nombre_fiscal as nombre_fiscal
            from inventarios_diarios
            join unidades on unidades.cod_unidad = inventarios_diarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios_diarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            join productos_inve as pi on inventarios_diarios.cod_producto = pi.cod_producto
            where inventarios_diarios.cod_producto = :prod
            and inventarios_diarios.cod_unidad = :suc
            and inventarios_diarios.cod_bodega = :bod
            and unidades.empresa = :empresa
            and bodegas.empresa = unidades.empresa
            and fecha between :mesu and :mesd
            and pi.empresa = unidades.empresa
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
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function existencia_productos_categoria($sucursal,$bodega,Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
        {
            $sucu = DB::table('unidades')->where('cod_unidad',$sucursal)->where('empresa',Auth::user()->empresa)->first();
            $bod = DB::table('bodegas')->where('empresa',Auth::user()->empresa)->where('cod_unidad',$sucursal)->where('cod_bodega',$bodega)->first();
            foreach($request->producto as $key =>$value)
            {
                $cate = Crypt::encryptString($request->producto[$key]);
            }
            return view('reportes.sucursal_categoriaExistencia',compact('sucu','bod','sucursal','bodega','cate'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function datos_categoria($sucursal,$bodega,$cate)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
        if($permiso == true)
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
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para mostrar grafica por clase de productos -------------------------------------------------------
    public function grafica_sucursal_clase($sucursal,$bodega)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first())
        {
            $hoy = Carbon::now();
            $fecha = $hoy->subDays(35); 
            $existenciaA = 0;
            $existenciaB = 0;
            $existenciaC = 0;
            $datos = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and inventarios.Ubicacion_X = 1
            and fecha > :fecha 
            group by inventarios_diarios.cod_unidad',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            foreach($datos as $d)
            { 
                $existenciaA =  (floatval($d->punteo));
            }
            $datosB = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and inventarios.Ubicacion_X = 2
            and fecha > :fecha 
            group by inventarios_diarios.cod_unidad',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            foreach($datosB as $dB)
            {
                $existenciaB =  (floatval($dB->punteo));
            }
            $datosC = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and inventarios.Ubicacion_X = 3
            and fecha > :fecha 
            group by inventarios_diarios.cod_unidad',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            foreach($datosC as $dC)
            {
                $existenciaC =  (floatval($dC->punteo));
            }
            $detalle = DB::select('select bodegas.nombre as bodega, unidades. nombre as sucursal
            from bodegas 
            join unidades on bodegas.cod_unidad = unidades.cod_unidad
            where unidades.cod_unidad = :un
            and unidades.empresa = :empresa
            and bodegas.cod_bodega = :bo
            and bodegas.empresa = unidades.empresa',['un'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $todas = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/10)*100),2) as punteo,
            inventarios.Ubicacion_X, fecha
            from inventarios,
            inventarios_diarios
            where inventarios.cod_producto = inventarios_diarios.cod_producto
            and inventarios.cod_unidad = :su
            and inventarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.Ubicacion_X is not null 
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and fecha > :fecha
            group by inventarios_diarios.cod_unidad, inventarios.Ubicacion_X, inventarios_diarios.day,inventarios.Ubicacion_X, fecha
            order by  fecha asc',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $graficaLineal["chart"] = array("type" => "line");
            $clase = [];
            $punteo = [];
            $punteoA = [];
            $punteoB = [];
            $punteoC = [];
            $fecha = [];
            foreach($todas as $gra)
            {
                if($gra->Ubicacion_X == 1)
                {
                    $clas = 'Productos clase A';
                    $punteoA[] =  (floatval($gra->punteo.''.'%'));
                    $fecha[] = (date('d/m/Y',strtotime($gra->fecha)));
                }
                if($gra->Ubicacion_X == 2)
                {
                    $clas = 'Productos clase B';
                    $punteoB[] =  (floatval($gra->punteo.''.'%'));
                }
                if($gra->Ubicacion_X == 3)
                {
                    $clas = 'Productos clase C';
                    $punteoC[] =  (floatval($gra->punteo.''.'%'));
                }
                $clase[] =  ($clas);
            }
            $graficaLineal["title"] = array("text" =>'Historial de existencias');
            $graficaLineal["xAxis"] = array("categories" => $fecha);
            $graficaLineal["yAxis"] = array("title" => array("text" => "Porcentaje de existencias"));
            $graficaLineal["series"] = [
                array("name" => "Clase A", "data" => $punteoA,"color"=>"#B43C12"),
                array("name" => "Clase B", "data" => $punteoB,"color"=>"#59A90E"),
                array("name" => "Clase C", "data" => $punteoC,"color"=>"#0E26A9")
            ];
            return view('reportes.graficadeReloj', compact('existenciaA','existenciaB','existenciaC','detalle','sucursal','bodega','graficaLineal'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver los detalles de una clase de producto ----------------------------------------------------
    public function grafica_sucursal_clase_producto($sucursal,$bodega,$clase)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $hoy = Carbon::now();
            $fecha = $hoy->subDays(35);
            $grafica = DB::select('select inventarios_diarios.cod_unidad,productos_inve.nombre_corto, productos_inve.nombre_fiscal,  
            Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios_diarios
            join inventarios on inventarios_diarios.cod_producto = inventarios.cod_producto
            join productos_inve on inventarios_diarios.cod_producto = productos_inve.cod_producto
            where inventarios.cod_unidad = :su
            and inventarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.Ubicacion_X = :cl
            and inventarios.empresa = :empresa
            and fecha > :fecha 
            and inventarios_diarios.empresa = inventarios.empresa
            and productos_inve.empresa = inventarios.empresa
            group by inventarios_diarios.cod_unidad, productos_inve.nombre_corto, productos_inve.nombre_fiscal, 
            inventarios_diarios.cod_producto
            order by punteo asc',
            ['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'cl'=>$clase,'empresa'=>Auth::user()->empresa]);
            $nombre = [];
            $punteo = [];
            if($clase == 1)
            {
                $clas = 'Productos clase A';
            }
            if($clase == 2)
            {
                $clas = 'Productos clase B';
            }
            if($clase == 3)
            {
                $clas = 'Productos clase C';
            }
            if($clase == 4)
            {
                $clas = 'Productos clase D';
            }
            foreach($grafica as $gra)
            {
                $nombre[] =  ($gra->nombre_corto.' - '.str_replace("'",'',$gra->nombre_fiscal));
                $punteo[] =  (floatval($gra->punteo));
            }
            return view('reportes.grafica_clase_producto',compact('nombre','sucursal','bodega','clas','punteo','clase'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ver el historial de una clase por podructos ----------------------------------------------------
    public function grafica_sucursal_clase_historial($sucursal,$bodega,$clase)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $hoy = Carbon::now();
            $fecha = Carbon::now()->subDays(105);
            $grafica = DB::select('select inventarios_diarios.cod_unidad, month, max(fecha) as mes, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios.cod_unidad = :su
            and inventarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.Ubicacion_X = :cl
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and fecha between :fecha and :fechados
            group by inventarios_diarios.cod_unidad, month
            order by month asc',
            ['fecha'=>$fecha,'fechados'=>$hoy,'su'=>$sucursal,'bo'=>$bodega,'cl'=>$clase,'empresa'=>Auth::user()->empresa]);
            if($clase == 1)
            {
                $clas = 'Productos clase A';
            }
            if($clase == 2)
            {
                $clas = 'Productos clase B';
            }
            if($clase == 3)
            {
                $clas = 'Productos clase C';
            }
            if($clase == 4)
            {
                $clas = 'Productos clase D';
            }
            $nombre = [];
            $punteo = [];
            foreach($grafica as $gra)
            {
                $nombre[] =  (date('M',strtotime($gra->mes)));
                $punteo[] =  (floatval($gra->punteo));
            }
            return view('reportes.grafica_clase_historial',compact('nombre','sucursal','bodega','clas','punteo','clase'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver las graficas de rejol en una sucursal al azar --------------------------------------------
    public function grafica_sucursal_clase_random()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $hoy = Carbon::now();
            $fecha = $hoy->subDays(35); 
            $random = DB::table('unidades')
            ->select(['cod_unidad'])->where('empresa',Auth::user()->empresa)
            ->where('cod_unidad','!=',[2,14])->inRandomOrder(1)
            ->first();
            $bodega = 1;
            $sucursal = $random->cod_unidad;
            $datos = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and inventarios.Ubicacion_X = 1
            and fecha > :fecha 
            group by inventarios_diarios.cod_unidad',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $existenciaA[] = '';
            foreach($datos as $d)
            { 
                $existenciaA =  (floatval($d->punteo));
            }
            $datosB = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and inventarios.Ubicacion_X = 2
            and fecha > :fecha 
            group by inventarios_diarios.cod_unidad',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $existenciaB[] = '';
            foreach($datosB as $dB)
            {
                $existenciaB =  (floatval($dB->punteo));
            }
            $datosC = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and inventarios.Ubicacion_X = 3
            and fecha > :fecha 
            group by inventarios_diarios.cod_unidad',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $existenciaC[] = '';
            foreach($datosC as $dC)
            {
                $existenciaC =  (floatval($dC->punteo));
            }
            //return response()->json($yourFirstChart);
            $detalle = DB::select('select bodegas.nombre as bodega, unidades. nombre as sucursal
            from bodegas 
            join unidades on bodegas.cod_unidad = unidades.cod_unidad
            where unidades.cod_unidad = :un
            and unidades.empresa = :empresa
            and bodegas.cod_bodega = :bo
            and bodegas.empresa = unidades.empresa',['un'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $todas = DB::select('select inventarios_diarios.cod_unidad, Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/10)*100),2) as punteo,
            inventarios.Ubicacion_X, fecha
            from inventarios,
            inventarios_diarios
            where inventarios_diarios.cod_producto = inventarios.cod_producto
            and inventarios_diarios.cod_unidad = :su
            and inventarios_diarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.Ubicacion_X is not null 
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and fecha > :fecha
            group by inventarios_diarios.cod_unidad, inventarios.Ubicacion_X, inventarios_diarios.day,inventarios.Ubicacion_X, fecha
            order by  fecha asc',['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'empresa'=>Auth::user()->empresa]);
            $graficaLineal["chart"] = array("type" => "line");
            $clase = [];
            $punteoA = [];
            $punteoB = [];
            $punteoC = [];
            $fecha = [];
            foreach($todas as $gra)
            {
                if($gra->Ubicacion_X == 1)
                {
                    $clas = 'Productos clase A';
                    $punteoA[] =  (floatval($gra->punteo.''.'%'));
                    $fecha[] = (date('d/m/Y',strtotime($gra->fecha)));
                }
                if($gra->Ubicacion_X == 2)
                {
                    $clas = 'Productos clase B';
                    $punteoB[] =  (floatval($gra->punteo.''.'%'));
                }
                if($gra->Ubicacion_X == 3)
                {
                    $clas = 'Productos clase C';
                    $punteoC[] =  (floatval($gra->punteo.''.'%'));
                }
                $clase[] =  ($clas);   
            }
            $graficaLineal["title"] = array("text" =>'Historial de existencias');
            $graficaLineal["xAxis"] = array("categories" => $fecha);
            $graficaLineal["yAxis"] = array("title" => array("text" => "Porcentaje de existencias"));
            $graficaLineal["series"] = [
                array("name" => "Clase A", "data" => $punteoA,"color"=>"#B43C12"),
                array("name" => "Clase B", "data" => $punteoB,"color"=>"#59A90E"),
                array("name" => "Clase C", "data" => $punteoC,"color"=>"#0E26A9")
            ];
            if($existenciaA == [''] || $existenciaB == [''] || $existenciaC == [''])
            {
                return back();
            }
            else 
            {
                return view('reportes.graficadeRelojRandom', compact('existenciaA','existenciaB','existenciaC','detalle','sucursal','bodega','graficaLineal'));
            }
        }
        else 
        {
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',28)->first();
            if($permiso == true)
            {
                return redirect()->route('rep_suc_lis');
            }
            else 
            {
                return redirect()->route('home')->with('error','No tienes permisos para accesar');
            }
        }
    }

    public function grafica_sucursal_clase_producto_random($sucursal,$bodega,$clase)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $hoy = Carbon::now();
            $fecha = $hoy->subDays(35);
            $grafica = DB::select('select inventarios_diarios.cod_unidad,productos_inve.nombre_corto, productos_inve.nombre_fiscal,  
            Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo
            from inventarios_diarios
            join inventarios on inventarios_diarios.cod_producto = inventarios.cod_producto
            join productos_inve on inventarios_diarios.cod_producto = productos_inve.cod_producto
            where inventarios.cod_unidad = :su
            and inventarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.Ubicacion_X = :cl
            and inventarios.empresa = :empresa
            and fecha > :fecha 
            and inventarios_diarios.empresa = inventarios.empresa
            and productos_inve.empresa = inventarios.empresa
            group by inventarios_diarios.cod_unidad, productos_inve.nombre_corto, productos_inve.nombre_fiscal, inventarios_diarios.cod_producto
            order by punteo asc',
            ['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'cl'=>$clase,'empresa'=>Auth::user()->empresa]);
            $nombre = [];
            $punteo = [];
            if($clase == 1)
            {
                $clas = 'Productos clase A';
            }
            if($clase == 2)
            {
                $clas = 'Productos clase B';
            }
            if($clase == 3)
            {
                $clas = 'Productos clase C';
            }
            if($clase == 4)
            {
                $clas = 'Productos clase D';
            }
            foreach($grafica as $gra)
            {
                $nombre[] =  ($gra->nombre_corto.' - '.str_replace("'",'',$gra->nombre_fiscal));
                $punteo[] =  (floatval($gra->punteo));
            }
            return view('reportes.grafica_clase_producto_random',compact('nombre','sucursal','bodega','clas','punteo','clase'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver la existencia de una clase en todas las sucursales ---------------------------------------
    public function Grafica_clases_todas_sucursales($clase)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $hoy = Carbon::now();
            $fecha = $hoy->subDays(35);
            $grafica = DB::select('select inventarios_diarios.cod_unidad, unidades.nombre, 
            Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo, inventarios_diarios.cod_unidad
            from inventarios_diarios
            join inventarios on inventarios_diarios.cod_producto = inventarios.cod_producto
            join unidades on inventarios_diarios.cod_unidad = unidades.cod_unidad
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and fecha > :fecha
            and inventarios.Ubicacion_X = :cl
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and unidades.empresa = inventarios.empresa
            and unidades.cod_unidad not in (2,6,11,14,15,32,37)
            group by inventarios_diarios.cod_unidad, unidades.nombre
            order by punteo asc',
            ['fecha'=>$fecha,'cl'=>$clase,'empresa'=>Auth::user()->empresa]);
            $nombre = [];
            $punteo = [];
            if($clase == 1)
            {
                $clas = 'Productos clase A';
            }
            if($clase == 2)
            {
                $clas = 'Productos clase B';
            }
            if($clase == 3)
            {
                $clas = 'Productos clase C';
            }
            if($clase == 4)
            {
                $clas = 'Productos clase D';
            }
            foreach($grafica as $gra)
            {
                $nombre[] =  ($gra->nombre);
                $punteo[] =  (floatval($gra->punteo));
            }
            return view('reportes.grafica_clase_sucursales',compact('punteo','nombre','clas'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function sucursal_clase_producto($sucursal,$bodega,$clase)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $hoy = Carbon::now();
            $fecha = $hoy->subDays(35);
            $productos = DB::select('select inventarios_diarios.cod_unidad,productos_inve.nombre_corto, productos_inve.nombre_fiscal,  
            Round(((sum( punteo)/ count(distinct inventarios.cod_producto)/300)*100),2) as punteo,
            inventarios.maximo, inventarios.minimo, inventarios.existencia1, inventarios.piso_sugerido, inventarios.cod_producto,
            inventarios.cod_unidad, inventarios.cod_bodega   
            from inventarios_diarios
            join inventarios on inventarios_diarios.cod_producto = inventarios.cod_producto
            join productos_inve on inventarios_diarios.cod_producto = productos_inve.cod_producto
            where inventarios.cod_unidad = :su
            and inventarios.cod_bodega = :bo
            and inventarios.cod_unidad = inventarios_diarios.cod_unidad
            and inventarios.cod_bodega = inventarios_diarios.cod_bodega
            and inventarios.empresa = :empresa
            and inventarios_diarios.empresa = inventarios.empresa
            and fecha > :fecha
            and inventarios.Ubicacion_X = :cl
            and productos_inve.empresa = inventarios.empresa
            group by inventarios_diarios.cod_unidad, productos_inve.nombre_corto, productos_inve.nombre_fiscal, inventarios_diarios.cod_producto,
            inventarios.maximo, inventarios.minimo, inventarios.existencia1, inventarios.piso_sugerido, inventarios.cod_producto,
            inventarios.cod_unidad, inventarios.cod_bodega
            order by punteo asc',
            ['fecha'=>$fecha,'su'=>$sucursal,'bo'=>$bodega,'cl'=>$clase,'empresa'=>Auth::user()->empresa]);
            return DataTables($productos)->make(true);
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para el formulario de tabla de existencias de un producto dentro de una sucursal --------------------
    public function ingresar_datos()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $actual = Carbon::now();
            $pasado = Carbon::now();
            $atras = $actual->subDay($actual->day);
            $menos = $pasado->subMonths(3);
            $mes = $menos->subDay($pasado->day - 1);
            $sucursales = DB::table("unidades")->where('empresa',Auth::user()->empresa)->get();//consulta que trae todas las sucursales que pertenecen a la empresa 1
            return view('reportes.ingre_datos',compact('sucursales','atras','mes'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function historial_producto(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $producto = DB::table('productos_inve')->where('cod_producto',$request->cod_producto)->where('empresa',Auth::user()->empresa)->first();
            $datos = DB::select("select unidades.nombre as sucursal,unidades.cod_unidad as cod_unidad,bodegas.observacion as bodega,
            bodegas.cod_bodega as cod_bodega,bodegas.Cod_Cliente as Cod_Cliente,inventarios_diarios.cod_producto,inventarios_diarios.cantidad as existencia,
            inventarios_diarios.day as dia,inventarios_diarios.minimo as min,inventarios_diarios.maximo as max,inventarios_diarios.fecha as fecha
            from inventarios_diarios
            join unidades on unidades.cod_unidad = inventarios_diarios.cod_unidad 
            and unidades.empresa = :empresa
            join bodegas on bodegas.cod_bodega = inventarios_diarios.cod_bodega and unidades.cod_unidad = bodegas.cod_unidad 
            and bodegas.empresa = unidades.empresa
            where cod_producto = :prod
            and fecha between :mesu and :mesd
            and inventarios_diarios.cod_unidad <> '2'
            group by inventarios_diarios.day,inventarios_diarios.month,unidades.nombre,bodegas.observacion,inventarios_diarios.cod_producto,inventarios_diarios.cantidad,min,max,fecha,
            cod_unidad,cod_bodega,Cod_Cliente
            order by cod_unidad asc,cod_bodega asc,fecha asc",['prod'=>$request->cod_producto,'mesu'=>$request->mesu,'mesd'=>$request->mesd,
            'empresa'=>Auth::user()->empresa]);
            $fecha_inicial = $request->mesu;
            $fecha_final = $request->mesd;
            $sucursal = $request->cod_unidad;
            $bodega = $request->bodega;
            if($request->cod_unidad == '')
            {
                return view('reportes.historial_sucursales',compact('datos','fecha_inicial','fecha_final','producto'));
            }
            elseif($request->cod_unidad > 0 && $request->bodega <> '')
            {
                return view('reportes.historial_sucursal',compact('datos','fecha_inicial','fecha_final','producto','sucursal','bodega'));
            }
            else
            {
                return back()->with('error','Seleccione una Bodega');
            }
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function producto(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $term = trim($request->q);
            if (empty($term)) 
            {
                return \Response::json([]);
            }
            $tags = DB::table('productos_inve')->where('empresa',Auth::user()->empresa)->where('nombre_fiscal','like','%'. $term .'%')
            ->orwhere('nombre_corto',$term)->where('empresa',Auth::user()->empresa)->limit(10)->get();
            $formatted_tags = [];
            foreach ($tags as $tag) 
            {
                $formatted_tags[] = ['id' => $tag->cod_producto, 'text' => $tag->nombre_corto.' '.$tag->nombre_fiscal];
            }
            return \Response::json($formatted_tags);
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }   
//------------------------------------------------------------------------------------------------------------------------------------------------------------- 

//----------------------------------------------- Funcion para ver el historial de la existencia de todos los productos en una sucursal -----------------------
    public function ingresar_datos_sucursal()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $actual = Carbon::now();
            $pasado = Carbon::now();
            $atras = $actual->subDay($actual->day);
            $menos = $pasado->subMonths(3);
            $mes = $menos->subDay($pasado->day - 1);
            $sucursales = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();
            $cate = DB::table('inventario_web_categorias')->where('empresa',Auth::user()->empresa)->orderby('nombre','asc')->get();
            $marcas = DB::table("Marcas")->where('Empresa',Auth::user()->empresa)->orderBy('Marca','asc')->get();
            return view('reportes.ingre_datos_sucursal',compact('sucursales','atras','mes','cate','marcas'));
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function productos_sucursal(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',26)->first();
        if($permiso == true)
        {
            $categorias = $request->cod_tipo_prod;
            $marca = $request->marca;
            $sucursales = DB::table('unidades')->where('empresa',Auth::user()->empresa)->get();//consulta que trae todas las sucursales que pertenecen a la empresa 1
            $fecha_inicial = $request->mesu;
            $fecha_final = $request->mesd;
            $datos = DB::select('select inventarios_diarios.cod_producto as cod_producto,inventarios_diarios.cantidad as existencia,inventarios_diarios.day as dia,
            productos_inve.nombre_fiscal as nom_producto,productos_inve.nombre_corto as nom_corto,
            inventarios_diarios.fecha as fecha,unidades.nombre as su_nombre,bodegas.nombre as bo_nombre,inventarios_diarios.minimo as min,
            inventarios_diarios.maximo as max,inventario_web_categorias.nombre as cod_tipo_prod,productos_inve.Marca as marca,
            Marcas.Descripcion as marca_nombre
            from inventarios_diarios
            join unidades on unidades.cod_unidad = inventarios_diarios.cod_unidad 
            join bodegas on bodegas.cod_bodega = inventarios_diarios.cod_bodega 
            join productos_inve on productos_inve.cod_producto = inventarios_diarios.cod_producto 
            join Marcas on Marcas.Marca = productos_inve.Marca
            join inventario_web_categorias on productos_inve.cod_tipo_prod = inventario_web_categorias.cod_tipo_prod
            where inventarios_diarios.cod_unidad = :suc
            and inventarios_diarios.cod_bodega = :bod
            and fecha between :mesu and :mesd
            and unidades.empresa = :empresa
            and unidades.cod_unidad = bodegas.cod_unidad
            and bodegas.empresa = unidades.empresa 
            and productos_inve.empresa = unidades.empresa
            and inventario_web_categorias.empresa = productos_inve.empresa
            group by inventarios_diarios.day,inventarios_diarios.month,inventarios_diarios.cod_producto,inventarios_diarios.cantidad,nom_producto,nom_corto,fecha,su_nombre,
            cod_tipo_prod,bo_nombre,min,max,marca,marca_nombre
            order by cod_tipo_prod asc,cod_producto asc,month asc',
            ['suc'=>$request->cod_unidad,'bod'=>$request->bodega,'mesu'=>$request->mesu,'mesd'=>$request->mesd,'empresa'=>Auth::user()->empresa]);
            if($categorias == 2 && $marca == 1)
            {
                return view('reportes.sucursal_productos',compact('datos','sucursales','fecha_inicial','fecha_final'));
            }
            elseif($categorias != 2 && $marca == '')
            {
                return view('reportes.sucursal_categorias',compact('datos','sucursales','fecha_inicial','fecha_final','categorias'));
            }
            elseif($categorias == '' && $marca != 1)
            {
                return view('reportes.sucursal_marcas',compact('datos','sucursales','fecha_inicial','fecha_final','marca'));
            }
        }
        else 
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
}
