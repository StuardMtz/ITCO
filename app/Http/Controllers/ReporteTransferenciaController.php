<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class ReporteTransferenciaController extends Controller
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
    public function reporte_verificadores(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            if($request->inicio == '')
            {
                $fin = new Carbon();
                $ini = carbon::now();
                $inicio = $ini->subDays(30);
                $reporte = DB::select('select usuarioSupervisa, sum(porcentaje) as por, count(id) as total, (por/total) as eficacia,
                min(fechaSalida) as inicio, max(fechaSalida) as final
                from inventario_web_encabezado_transferencias
                where id_estado = 20
                and porcentaje is not null
                and usuarioSupervisa is not null
                and fechaSalida between :inicio and :fin
                group by  usuarioSupervisa
                order by eficacia asc',['inicio'=>$inicio,'fin'=>$fin]);
                $punteo[] = '';
                $nombre[] = '';
                foreach($reporte as $r)
                {
                    $punteo[] = (floatval($r->eficacia));
                    $nombre[] = ($r->usuarioSupervisa);
                }
                return view('repTransferencias.eficaciaTransferencia',compact('inicio','fin','punteo','nombre'));
            }
            else 
            {
                $inicio = $request->inicio;
                $fin = $request->fin;
                $reporte = DB::select('select usuarioSupervisa, sum(porcentaje) as por, count(id) as total, (por/total) as eficacia,
                min(fechaSalida) as inicio, max(fechaSalida) as final
                from inventario_web_encabezado_transferencias
                where id_estado = 20
                and porcentaje is not null
                and usuarioSupervisa is not null
                and fechaSalida between :inicio and :fin
                group by  usuarioSupervisa
                order by eficacia asc',['inicio'=>$inicio,'fin'=>$fin]); 
                foreach($reporte as $r)
                {
                    $punteo[] = (floatval($r->eficacia));
                    $nombre[] = ($r->usuarioSupervisa);
                }
                return view('repTransferencias.eficaciaTransferencia',compact('inicio','fin','punteo','nombre'));
            }
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_reporte_verificadores($inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $reporte = DB::select('select usuarioSupervisa, sum(porcentaje) as por, count(id) as total, (por/total) as eficacia,
            min(fechaSalida) as inicio, max(fechaSalida) as final
            from inventario_web_encabezado_transferencias
            where id_estado = 20
            and porcentaje is not null
            and usuarioSupervisa is not null
            and fechaSalida between :inicio and :fin
            group by  usuarioSupervisa',['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables::of($reporte)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function reportes_por_verificador($veri,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $reporte = DB::select("select datediff(hh,fecha_cargado, fechaSalida) as difer, id, usuarioSupervisa, porcentaje, 
            fechaSalida as inicio, mod(datediff(mi,fecha_cargado,fechaSalida),60) as minutos,
            convert(varchar(20),difer)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia
            from inventario_web_encabezado_transferencias
            where id_estado = 20
            and porcentaje is not null
            and usuarioSupervisa is not null
            and fechaSalida between :inicio and :fin
            and usuarioSupervisa = :us
            order by id asc",['inicio'=>$inicio,'fin'=>$fin,'us'=>$veri]);
            foreach($reporte as $r)
            {
                $punteo[] = (floatval($r->porcentaje));
                $nombre[] = (date('d/m/Y',strtotime($r->inicio)));
                $datos[] = $r->id.' '.$r->diferencia;
            }
            return view('repTransferencias.eficaciaVerificador',compact('inicio','fin','punteo','nombre','veri','datos'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_reportes_por_verificadores($veri,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $reporte = DB::select("select datediff(hh,fecha_cargado, fechaSalida) as difer, id, usuarioSupervisa, porcentaje, 
            fechaSalida as inicio, mod(datediff(mi,fecha_cargado,fechaSalida),60) as minutos,
            convert(varchar(20),difer)+' horas '+convert(varchar(20),minutos)+' minutos' as diferencia
            from inventario_web_encabezado_transferencias
            where id_estado = 20
            and porcentaje is not null
            and usuarioSupervisa is not null
            and fechaSalida between :inicio and :fin
            and usuarioSupervisa = :us
            order by id asc",['inicio'=>$inicio,'fin'=>$fin,'us'=>$veri]);
            return DataTables::of($reporte)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function reporte_tiempos_grupos(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $enCola[] = '';
            $preCarga[] = '';
            $Cargando[] = '';
            $verificado[] = '';
            $viaje[] = '';
            $nombre[] = '';
            if($request->inicio == '')
            {
                $fin = new Carbon();
                $ini = new Carbon();
                $inicio = $ini->subDays(30);
                $reporte = DB::select("select count(id) as total,grupoCarga, (sum(datediff(hh,fecha_enCola,fechaUno)) /total) as enC, (sum(mod(datediff(mi,fecha_enCola,fechaUno),60))/total) as ecMi, 
                (sum(datediff(hh,fechaUno,fecha_enCarga))/total) as ecH,(sum(mod(datediff(mi,fechaUno,fecha_enCarga),60))/total) as minutos,
                (sum(datediff(hh,fecha_enCarga,fecha_cargado))/total) as cH, (sum(mod(datediff(mi,fecha_enCarga,fecha_cargado),60))/total) as cMi, 
                (sum(datediff(hh,fechaUno,fecha_cargado))/total) as rH, (sum(mod(datediff(mi,fechaUno,fecha_cargado),60))/total) as rMi,
                (sum(datediff(hh,fechaSalida,fechaSucursal))/total) as vH, (sum(mod(datediff(mi,fechaSalida,fechaSucursal),60))/total) as vMi,
                convert(varchar(3),enC)+'.'+ convert(varchar(3),left(replicate(0,2),2 -len(ecMi))+left(ecMi,2)) as enCola,
                convert(varchar(3),ecH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(minutos))+left(minutos,2)) as preCarga,
                convert(varchar(3),cH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(cMi))+left(cMi,2)) as Cargando,
                convert(varchar(3),rH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(rMi))+left(rMi,2)) as verificado,
                convert(varchar(3),vH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(vMi))+left(vMi,2)) as viaje,
                min(fecha_cargado) as inicio, max(fecha_cargado) as fin
                from inventario_web_encabezado_transferencias
                where fecha_cargado between :inicio and :fin
                and id_estado = 20
                and grupoCarga is not null
                group by grupoCarga
                order by grupoCarga asc",['inicio'=>$inicio,'fin'=>$fin]);
                foreach($reporte as $r)
                {
                    $enCola[] = (floatval($r->enCola));
                    $preCarga[] = (floatval($r->preCarga));
                    $Cargando[] = (floatval($r->Cargando));
                    $verificado[] = (floatval($r->verificado));
                    $viaje[] = (floatval($r->viaje));
                    $nombre[] = $r->grupoCarga;
                }
                return view('repTransferencias.tiempoGrupos',compact('inicio','fin','enCola','preCarga','Cargando','verificado','viaje','nombre'));
            }
            else 
            {
                $inicio = $request->inicio;
                $fin = $request->fin;
                $reporte = DB::select("select count(id) as total,grupoCarga, (sum(datediff(hh,fecha_enCola,fechaUno)) /total) as enC, (sum(mod(datediff(mi,fecha_enCola,fechaUno),60))/total) as ecMi, 
                (sum(datediff(hh,fechaUno,fecha_enCarga))/total) as ecH,(sum(mod(datediff(mi,fechaUno,fecha_enCarga),60))/total) as minutos,
                (sum(datediff(hh,fecha_enCarga,fecha_cargado))/total) as cH, (sum(mod(datediff(mi,fecha_enCarga,fecha_cargado),60))/total) as cMi, 
                (sum(datediff(hh,fechaUno,fecha_cargado))/total) as rH, (sum(mod(datediff(mi,fechaUno,fecha_cargado),60))/total) as rMi,
                (sum(datediff(hh,fechaSalida,fechaSucursal))/total) as vH, (sum(mod(datediff(mi,fechaSalida,fechaSucursal),60))/total) as vMi,
                convert(varchar(3),enC)+'.'+ convert(varchar(3),left(replicate(0,2),2 -len(ecMi))+left(ecMi,2)) as enCola,
                convert(varchar(3),ecH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(minutos))+left(minutos,2)) as preCarga,
                convert(varchar(3),cH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(cMi))+left(cMi,2)) as Cargando,
                convert(varchar(3),rH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(rMi))+left(rMi,2)) as verificado,
                convert(varchar(3),vH)+'.'+convert(varchar(3),left(replicate(0,2),2 -len(vMi))+left(vMi,2)) as viaje,
                min(fecha_cargado) as inicio, max(fecha_cargado) as fin
                from inventario_web_encabezado_transferencias
                where fecha_cargado between :inicio and :fin
                and id_estado = 20
                and grupoCarga is not null
                group by grupoCarga
                order by grupoCarga asc",['inicio'=>$inicio,'fin'=>$fin]);
                foreach($reporte as $r)
                {
                    $enCola[] = (floatval($r->enCola));
                    $preCarga[] = (floatval($r->preCarga));
                    $Cargando[] = (floatval($r->Cargando));
                    $verificado[] = (floatval($r->verificado));
                    $viaje[] = (floatval($r->viaje));
                    $nombre[] = $r->grupoCarga;
                }
                return view('repTransferencias.tiempoGrupos',compact('inicio','fin','enCola','preCarga','Cargando','verificado','viaje','nombre'));
            }
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_reporte_tiempos_grupos($inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $reporte = DB::select("select count(id) as total,grupoCarga, (sum(datediff(hh,fecha_enCola,fechaUno)) /total) as enC, (sum(mod(datediff(mi,fecha_enCola,fechaUno),60))/total) as ecMi, 
            (sum(datediff(hh,fechaUno,fecha_enCarga))/total) as ecH,(sum(mod(datediff(mi,fechaUno,fecha_enCarga),60))/total) as minutos,
            (sum(datediff(hh,fecha_enCarga,fecha_cargado))/total) as cH, (sum(mod(datediff(mi,fecha_enCarga,fecha_cargado),60))/total) as cMi, 
            (sum(datediff(hh,fechaUno,fecha_cargado))/total) as rH, (sum(mod(datediff(mi,fechaUno,fecha_cargado),60))/total) as rMi,
            (sum(datediff(hh,fechaSalida,fechaSucursal))/total) as vH, (sum(mod(datediff(mi,fechaSalida,fechaSucursal),60))/total) as vMi,
            convert(varchar(3),enC)+' horas '+ convert(varchar(3),ecMi)+' minutos' as enCola,
            convert(varchar(3),ecH)+' horas '+convert(varchar(3),minutos)+' minutos' as preCarga,
            convert(varchar(3),cH)+' horas '+convert(varchar(3),cMi)+' minutos' as Cargando,
            convert(varchar(3),rH)+' horas '+convert(varchar(3),rMi)+' minutos' as verificado,
            convert(varchar(3),vH)+' horas '+convert(varchar(3),vMi)+' minutos' as viaje,
            min(fecha_cargado) as inicio, max(fecha_cargado) as fin
            from inventario_web_encabezado_transferencias
            where fecha_cargado between :inicio and :fin
            and id_estado = 20
            and grupoCarga is not null
            group by grupoCarga",['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables::of($reporte)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function reporte_por_grupo_tiempos($gru,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            return view('repTransferencias.tiempoGrupo',compact('gru','inicio','fin'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_reporte_por_grupo_tiempos($gru,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $reporte = DB::select("select id, grupoCarga, datediff(hh,fecha_enCola,fechaUno) as enC, mod(datediff(mi,fecha_enCola,fechaUno),60) as ecMi, 
            datediff(hh,fechaUno,fecha_enCarga) as ecH,mod(datediff(mi,fechaUno,fecha_enCarga),60) as minutos,
            datediff(hh,fecha_enCarga,fecha_cargado) as cH, mod(datediff(mi,fecha_enCarga,fecha_cargado),60)as cMi, 
            datediff(hh,fechaUno,fecha_cargado) as rH, mod(datediff(mi,fechaUno,fecha_cargado),60) as rMi,
            datediff(hh,fechaSalida,fechaSucursal) as vH, mod(datediff(mi,fechaSalida,fechaSucursal),60) as vMi,
            convert(varchar(20),enC)+' horas '+ convert(varchar(20),ecMi)+' minutos' as enCola,
            convert(varchar(20),ecH)+' horas '+convert(varchar(20),minutos)+' minutos' as preCarga,
            convert(varchar(20),cH)+' horas '+convert(varchar(20),cMi)+' minutos' as Cargando,
            convert(varchar(20),rH)+' horas '+convert(varchar(20),rMi)+' minutos' as verificado,
            convert(varchar(20),vH)+' horas '+convert(varchar(20),vMi)+' minutos' as viaje
            from inventario_web_encabezado_transferencias
            where fecha_cargado between :inicio and :fin
            and id_estado = 20
            and grupoCarga = :gr",['gr'=>$gru,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables::of($reporte)->make(true);
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
    public function reporte_sucursales_transferencias(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $tiempo[] = '';
            $nombre[] = '';
            $horas[] = '';
            if($request->inicio == '')
            {
                $inicio = 0;
                $fin = 0; 
                $sucursales = DB::select("select count(iwet.id) as total, u.nombre, b.nombre as bodega, u.cod_unidad, b.cod_bodega,
                (sum(datediff(hh,iwet.fecha_entregado,iwet.fechaSucursal))/total) as tiempoh,
                (sum(mod(datediff(mi,iwet.fecha_entregado,iwet.fechaSucursal),60))/total) as tiempom, 
                convert(varchar(3),tiempoh)+'.'+ convert(varchar(3),left(replicate(0,2),2 -len(tiempom))+left(tiempom,2)) as tiempo
                from inventario_web_encabezado_transferencias as iwet
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on b.cod_unidad = u.cod_unidad
                full join (select iwt.unidad_transf, count(iwt.id) as sin, 
                iwt.bodega_Transf
                from inventario_web_encabezado_transferencias as iwt
                where id_estado = 19
                group by iwt.unidad_transf, iwt.bodega_Transf) as d on u.cod_unidad = d.unidad_transf 
                where id_estado = 20 
                and iwet.bodega_Transf = b.cod_bodega  
                and fechaSalida between :fin and :inicio 
                and u.empresa = iwet.empresa
                and u.empresa = b.empresa
                group by iwet.unidad_transf, u.nombre, iwet.bodega_Transf, d.unidad_transf, d.sin, bodega,u.cod_unidad,
                 b.cod_bodega",['inicio'=>Carbon::now(),'fin'=>Carbon::now()->subdays(35)]);
                foreach($sucursales as $r)
                {
                    $tiempo[] = (floatval($r->tiempo));
                    $nombre[] = $r->nombre.' '.$r->bodega;
                    $horas[] = (floatval($r->tiempoh));
                }
                return view('reptransferencias.sucursales',compact('inicio','fin','tiempo','nombre','horas'));
            }
            else 
            {
                $inicio = $request->inicio;
                $fin = $request->fin;
                $sucursales = DB::select("select count(iwet.id) as total, u.nombre, b.nombre as bodega, u.cod_unidad, b.cod_bodega,
                (sum(datediff(hh,iwet.fecha_entregado,iwet.fechaSucursal))/total) as tiempoh,
                (sum(mod(datediff(mi,iwet.fecha_entregado,iwet.fechaSucursal),60))/total) as tiempom, 
                convert(varchar(3),tiempoh)+'.'+ convert(varchar(3),left(replicate(0,2),2 -len(tiempom))+left(tiempom,2)) as tiempo
                from inventario_web_encabezado_transferencias as iwet
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join bodegas as b on b.cod_unidad = u.cod_unidad
                full join (select iwt.unidad_transf, count(iwt.id) as sin, 
                iwt.bodega_Transf
                from inventario_web_encabezado_transferencias as iwt
                where id_estado = 19
                group by iwt.unidad_transf, iwt.bodega_Transf) as d on u.cod_unidad = d.unidad_transf 
                where id_estado = 20 
                and iwet.bodega_Transf = b.cod_bodega  
                and fechaSalida between :inicio and :fin 
                and u.empresa = iwet.empresa
                and u.empresa = b.empresa
                group by iwet.unidad_transf, u.nombre, iwet.bodega_Transf, d.unidad_transf, d.sin, bodega,u.cod_unidad,
                 b.cod_bodega",['inicio'=>$request->inicio,'fin'=>$request->fin]);
                foreach($sucursales as $r)
                {
                    $tiempo[] = (floatval($r->tiempo));
                    $nombre[] = $r->nombre.' '.$r->bodega;
                    $horas[] = (floatval($r->tiempoh));
                }
                return view('reptransferencias.sucursales',compact('inicio','fin','sucursales','tiempo','nombre','horas'));
            }
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
    public function datos_sucursales_reporte_transferencias($inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            if($inicio == 0)
            {
                $inicio = Carbon::now();
                $fin = Carbon::now()->subdays(35);
                $sucursales = DB::select("select count(iwet.id) as total, u.nombre as name, b.nombre as bodega, u.cod_unidad, b.cod_bodega,
                (sum(datediff(hh,iwet.fecha_entregado,iwet.fechaSucursal))/total) as tiempoh,
               (sum(mod(datediff(mi,iwet.fecha_entregado,iwet.fechaSucursal),60))/total) as tiempom, 
               convert(varchar(3),tiempoh)+' horas'+convert(varchar(3),tiempom)+' minutos' as tiempo, d.sin
               from inventario_web_encabezado_transferencias as iwet
               join unidades as u on iwet.unidad_transf = u.cod_unidad
               join bodegas as b on b.cod_unidad = u.cod_unidad
               full join (select iwt.unidad_transf, count(iwt.id) as sin, 
               iwt.bodega_Transf
               from inventario_web_encabezado_transferencias as iwt
               where id_estado = 19
               group by iwt.unidad_transf, iwt.bodega_Transf) as d on u.cod_unidad = d.unidad_transf 
               where id_estado = 20 
               and iwet.bodega_Transf = b.cod_bodega  
               and fechaSalida between :fin and :inicio 
               and u.empresa = iwet.empresa
               and u.empresa = b.empresa
               group by iwet.unidad_transf, name, iwet.bodega_Transf, d.unidad_transf, d.sin, bodega,u.cod_unidad, b.cod_bodega",['inicio'=>$inicio,'fin'=>$fin]);
                return DataTables($sucursales)->make(true);
            }
            else 
            {
                $sucursales = DB::select("select count(iwet.id) as total, u.nombre as name, b.nombre as bodega, u.cod_unidad, b.cod_bodega,
                (sum(datediff(hh,iwet.fecha_entregado,iwet.fechaSucursal))/total) as tiempoh,
               (sum(mod(datediff(mi,iwet.fecha_entregado,iwet.fechaSucursal),60))/total) as tiempom, 
               convert(varchar(3),tiempoh)+' horas'+convert(varchar(3),tiempom)+' minutos' as tiempo, d.sin
               from inventario_web_encabezado_transferencias as iwet
               join unidades as u on iwet.unidad_transf = u.cod_unidad
               join bodegas as b on b.cod_unidad = u.cod_unidad
               full join (select iwt.unidad_transf, count(iwt.id) as sin, 
               iwt.bodega_Transf
               from inventario_web_encabezado_transferencias as iwt
               where id_estado = 19
               group by iwt.unidad_transf, iwt.bodega_Transf) as d on u.cod_unidad = d.unidad_transf 
               where id_estado = 20 
               and iwet.bodega_Transf = b.cod_bodega  
               and fechaSalida between :inicio and :fin 
               and u.empresa = iwet.empresa
               and u.empresa = b.empresa
               group by iwet.unidad_transf, name, iwet.bodega_Transf, d.unidad_transf, d.sin, bodega,u.cod_unidad, b.cod_bodega",['inicio'=>$inicio,'fin'=>$fin]);
                return DataTables($sucursales)->make(true);
            }
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function transferencias_por_sucursal(Request $request,$id,$bodega)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            if($request->inicio == '')
            {
                $inicio = Carbon::now();
                $fin = Carbon::now();
                return view('reptransferencias.transferenciasSucursal',compact('id','inicio','fin','bodega'));
            }
            else 
            {
                $inicio = $request->inicio;
                $fin = $request->fin;
                return view('reptransferencias.transferenciasSucursal',compact('id','inicio','fin','bodega'));
            }
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function datos_transferencias_por_sucursal($id,$bodega,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            if($inicio == $fin)
            {
                $transferencias = DB::select("select num_movi, fecha, u.nombre, placa_vehiculo, iwe.nombre as estado, 
                iwet.fecha_enCola, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
                iwet.porcentaje, iwet.opcionalDos, iwet.usuarioSupervisa, iwet.usuario
                from inventario_web_encabezado_transferencias as iwet
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
                where iwet.id_estado = 20
                and u.empresa = iwet.empresa
                and iwet.unidad_transf = :id
                and iwet.bodega_transF = :bodega",['id'=>$id,'bodega'=>$bodega]);
                return DataTables::of($transferencias)->make(true);
            }
            else 
            {
                $transferencias = DB::select("select num_movi, fecha, u.nombre, placa_vehiculo, iwe.nombre as estado, 
                iwet.fecha_enCola, iwet.fechaSalida, iwet.fecha_entregado, tv.propietario, iwet.erroresVerificados,
                iwet.porcentaje, iwet.opcionalDos, iwet.usuarioSupervisa, iwet.usuario
                from inventario_web_encabezado_transferencias as iwet
                join unidades as u on iwet.unidad_transf = u.cod_unidad
                join inventario_web_estados as iwe on iwet.id_estado = iwe.id
                full join T_Vehiculos as tv on tv.Placa = iwet.placa_vehiculo
                where iwet.id_estado = 20
                and u.empresa = iwet.empresa
                and iwet.unidad_transf = :id
                and iwet.fecha_enCola between :inicio and :fin",['id'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
                return DataTables::of($transferencias)->make(true);
            }
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }

    public function ver_transferencia($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',25)->first();
        if($permiso == true)
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacionSucursal, u.nombre, placa_vehiculo, iwe.nombre as estado,
            iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fechaEntrega, iwet.fechaUno,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
            iwet.usuario, iwet.fechaSalida, iwet.usuarioSupervisa, iwet.fecha_paraCarga, iwet.observacion,
            iwet.fechaSucursal, iwet.fecha_entregado, iwet.observacionSup, iwet.observacionSucursal, 
            tv.propietario, iwet.erroresVerificados, iwet.observacionRevision, iwet.porcentaje, iwet.erroresVerificados
            from inventario_web_encabezado_transferencias as iwet 
            join unidades as u on iwet.unidad_transf = u.cod_unidad
            join inventario_web_estados as iwe on iwet.id_estado = iwe.id
            left join T_Vehiculos as tv on iwet.placa_vehiculo = tv.Placa
            where iwet.num_movi = :id
            and u.empresa = iwet.empresa',['id'=>$id]);

            $integra = DB::select('select nombre 
            from inventario_web_bitacora_transferencias
            where num_movi = :id',['id'=>$id]);

            $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidadRecibida) as cantidad, 
            iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,  iwt.noIncluido,
            floor(iwt.cantidad1) as cantidad1, iwt.cantidadSolicitada
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

            $grafica = DB::select("select id, grupoCarga, datediff(hh,fecha_enCola,fechaUno) as enC, mod(datediff(mi,fecha_enCola,fechaUno),60) as ecMi, 
            datediff(hh,fechaUno,fecha_enCarga) as ecH,mod(datediff(mi,fechaUno,fecha_enCarga),60) as minutos,
            datediff(hh,fecha_enCarga,fecha_cargado) as cH, mod(datediff(mi,fecha_enCarga,fecha_cargado),60)as cMi, 
            datediff(hh,fechaUno,fecha_cargado) as rH, mod(datediff(mi,fechaUno,fecha_cargado),60) as rMi,
            datediff(hh,fechaSalida,fechaSucursal) as vH, mod(datediff(mi,fechaSalida,fechaSucursal),60) as vMi,
            convert(varchar(20),enC)+' horas '+ convert(varchar(20),ecMi)+' minutos' as enCola,
            convert(varchar(20),ecH)+' horas '+convert(varchar(20),minutos)+' minutos' as preCarga,
            convert(varchar(20),cH)+' horas '+convert(varchar(20),cMi)+' minutos' as Cargando,
            convert(varchar(20),rH)+' horas '+convert(varchar(20),rMi)+' minutos' as verificado,
            convert(varchar(20),vH)+' horas '+convert(varchar(20),vMi)+' minutos' as viaje
            from inventario_web_encabezado_transferencias
            where id_estado = 20
            and num_movi = :id",['id'=>$id]);
            foreach($grafica as $r)
            {
                $enCola[] = $r->enCola;
                $preCarga[] = $r->preCarga;
                $Cargando[] = $r->Cargando;
                $verificado[] = $r->verificado;
                $viaje[] = $r->viaje;
            }
            return view('reptransferencias.verTransferencia',compact('tran','productos','id','integra','grafica','enCola','preCarga','Cargando','verificado','viaje'));
        }
        return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}
