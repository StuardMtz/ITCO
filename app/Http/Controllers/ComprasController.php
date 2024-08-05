<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Auth;
use App\User;
use App\Estado;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
//datos_reporte_compras_fecha
//--------------------------- Funciones para ver la vista principal del reporte de compras --------------------------------------------------------------------
    function inicio()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            return view('repcompras.inicio');
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function datos_inicio()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $ordenes = DB::select("select
            oc.empresa,
            e.abreviatura,
            oc.numero_orden as Orden_de_Compra,
            oc.numero_orden,
            ' orden' || '-' || oc.numero_orden as Orden,
            oc.fechaemision,
            oc.partida_arancelaria,oc.referencia,
            oc.requisicion,
            oc.fecharecepcion as Fecha_Recepcion,
            oc.descripcion_larga,
            oc.descripcion_corta as Descripcion,
            oc.comentario_avance as Avance,
            p.descripcion,
            convert(varchar(192),
            p.proveedor)+' - '+ convert(varchar(192),
            p.descripcion) as proveedor,
            case oc.tipo_ocompra
                when '2' then 'Clientes'
                when '1' then 'Stock'
                else 'Sin Motivo 'end  as Motivo,
            case oc.tipo_orden
                when 'E' then  'Exterior'
                else 'Local' end as 'Origen',
            case oc.estado
                when 'A' then 'Abierta - Creada'
                when 'C' then 'Cerrada - Terminada'
                when 'N' then 'Anulada'
                when 'U' then 'Autorizada - En Transito'
                else '0' end as 'Estado'
            from
                DBA.ordendecompra as oc,
                proveedor as p,
                empresa as e
            where
                oc.proveedor = p.proveedor
                and oc.empresa = e.empresa
            and oc.fecharecepcion >:fecha",['fecha'=>Carbon::now()->subMonths(3)]);
            return DataTables::of($ordenes)->addColumn('details_url', function($ordenes){
                return url('dreCp/'. $ordenes->numero_orden .'/'.$ordenes->empresa);
            })->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function detalles_reporte_compras($orden,$empresa)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $detalles = DB::select("select oc.empresa, oc.numero_orden,
            ' Orden de Compra' || ' - ' || oc.numero_orden as Documento,
            oc.fechaemision, oc.descripcion_larga,
            dc.cod_producto, dc.descripcion,
            dc.cantidad, dc.fecha_entrega,
            dc.precio, dc.precio_venta,
            dc.descuento, dc.orden*100, 'PEDIDO', pi.nombre_fiscal,
            pi.nombre_corto
            from ordendecompra as oc,
            det_compras as dc,
            productos_inve as pi
            where
            dc.empresa = oc.empresa
            and dc.num_compra = oc.numero_orden
            and dc.cod_producto = pi.cod_producto
            and dc.Empresa = pi.empresa
            and oc.Numero_Orden = :numero
            and oc.empresa = :empresa
            union
            select mi.empresa, mi.numero_orden,
            ' Ingreso al Inventario' || ' - ' ||  upper(mi.cod_tipo_movi || '-' || mi.cod_serie_movi || '-' || mi.num_movi),
            mi.fecha, mi.observacion, dmi.cod_producto, dmi.descripcion, dmi.cantidad2*-1, mi.fecha, null, null, null,
            dmi.orden, 'RECIBIDO',
            pr.nombre_fiscal, pr.nombre_corto
            from movi_inve as mi,
            det_movi_inve as dmi,
            productos_inve as pr
            where dmi.empresa = mi.empresa
            and dmi.cod_tipo_movi = mi.cod_tipo_movi
            and dmi.cod_Serie_movi = mi.cod_Serie_movi
            and dmi.num_movi = mi.num_movi
            and mi.cod_tipo_movi = 'P'
            and mi.numero_orden is not null
            and pr.cod_producto = dmi.cod_producto
            and pr.empresa = dmi.empresa
            and mi.numero_orden = :numero2
            and mi.empresa = :empresa2",['numero'=>$orden,'empresa'=>$empresa,'numero2'=>$orden,'empresa2'=>$empresa]);
            return DataTables($detalles)->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para mostrar el listado de ordenes de compras filtradas por fecha -----------------------------------------------------
    function reporte_compras_por_fecha(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('repcompras.comprasFecha',compact('inicio','fin'));
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function datos_reporte_compras_fecha($inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $ordenes = DB::select("select
            oc.empresa,
            e.abreviatura,
            oc.numero_orden as Orden_de_Compra,
            oc.numero_orden,
            ' orden' || '-' || oc.numero_orden as Orden,
            oc.fechaemision,
            oc.partida_arancelaria,oc.referencia,
            oc.requisicion,
            oc.fecharecepcion as Fecha_Recepcion,
            oc.descripcion_larga,
            oc.descripcion_corta as Descripcion,
            oc.comentario_avance as Avance,
            p.descripcion,
            convert(varchar(192),
            p.proveedor)+' - '+ convert(varchar(192),
            p.descripcion) as proveedor,
            case oc.tipo_ocompra
                when '2' then 'Clientes'
                when '1' then 'Stock'
                else 'Sin Motivo 'end  as Motivo,
            case oc.tipo_orden
                when 'E' then  'Exterior'
                else 'Local' end as 'Origen',
            case oc.estado
                when 'A' then 'Abierta - Creada'
                when 'C' then 'Cerrada - Terminada'
                when 'N' then 'Anulada'
                when 'U' then 'Autorizada - En Transito'
                else '0' end as 'Estado'
            from
                DBA.ordendecompra as oc,
                proveedor as p,
                empresa as e
            where
                oc.proveedor = p.proveedor
                and oc.empresa = e.empresa
            and oc.fechaemision between :inicio and :fin",['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables::of($ordenes)->addColumn('details_url', function($ordenes){
                return url('dreCp/'. $ordenes->numero_orden .'/'.$ordenes->empresa);
            })->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver la vista principal del reporte de compras --------------------------------------------------------------------
    function vista_reporte_compras_por_producto()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            return view('repcompras.reppProductos');
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function datos_reporte_compras_por_producto()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $ordenes = DB::select("select dc.cod_producto, pi.nombre_corto, pi.nombre_fiscal
            from DBA.ordendecompra as oc,
            det_compras as dc,
            productos_inve as pi
            where oc.numero_orden = dc.num_compra
            and dc.cod_producto = pi.cod_producto
            and dc.empresa = pi.empresa
            and oc.fecharecepcion > :fecha
            and pi.empresa = 1
            group by dc.cod_producto, pi.nombre_corto, pi.nombre_fiscal",['fecha'=>Carbon::now()->subMonths(3)]);
            return DataTables::of($ordenes)->addColumn('details_url', function($ordenes){
                return url('ddrcpp/'. $ordenes->cod_producto);
            })->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function ddetalles_reporte_compras_por_producto($producto)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $detalles = DB::select("select oc.empresa, oc.numero_orden, ' Orden de Compra' || ' - ' || oc.numero_orden as Documento,
            oc.descripcion_larga, dc.cod_producto, dc.descripcion, dc.cantidad, dc.fecha_entrega,
            dc.precio, dc.orden*100, 'PEDIDO', pr.proveedor, pr.descripcion as nproveedor,
            case oc.tipo_ocompra when '2' then 'Clientes' when '1' then 'Stock' else 'Sin Motivo 'end  as Motivo,
            case oc.tipo_orden when 'E' then  'Exterior' else 'Local' end as 'Origen',
            case oc.estado when 'A' then 'Abierta - Creada' when 'C' then 'Cerrada - Terminada'  when 'N' then
            'Anulada' when 'U' then 'Autorizada - En Transito' else '0' end as 'Estado'
            from ordendecompra as oc,
            det_compras as dc,
            proveedor as pr
            where dc.empresa = oc.empresa
            and dc.num_compra = oc.numero_orden
            and oc.proveedor = pr.proveedor
            and dc.cod_producto = :producto
            and oc.FechaEmision > :fecha
            union
            select mi.empresa, mi.numero_orden,
            ' Ingreso al Inventario' || ' - ' ||  upper(mi.cod_tipo_movi || '-' || mi.cod_serie_movi || '-' || mi.num_movi),
            mi.observacion, dmi.cod_producto, dmi.descripcion, dmi.cantidad2*-1, mi.fecha, null,
            dmi.orden, 'RECIBIDO', pr.proveedor, pr.descripcion as nproveedor, null,null, null
            from movi_inve as mi,
            det_movi_inve as dmi,
            proveedor as pr
            where dmi.empresa = mi.empresa
            and dmi.cod_tipo_movi = mi.cod_tipo_movi
            and dmi.cod_Serie_movi = mi.cod_Serie_movi
            and dmi.num_movi = mi.num_movi
            and mi.cod_tipo_movi = 'P'
            and mi.numero_orden is not null
            and pr.proveedor = mi.proveedor
            and dmi.cod_producto = :producto2
            and mi.fecha > :fecha2
            ",['producto'=>$producto,'producto2'=>$producto,'fecha'=>Carbon::now()->subMonths(3),'fecha2'=>Carbon::now()->subMonths(3)]);
            return DataTables($detalles)->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver la vista principal del reporte de compras --------------------------------------------------------------------
    function vista_compras_por_producto_fecha(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('repcompras.repProductosFecha',compact('inicio','fin'));
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function datos_reporte_compras_por_producto_fecha($inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $ordenes = DB::select("select dc.cod_producto, pi.nombre_corto, pi.nombre_fiscal,
            max(FechaEmision) as maxi, min(FechaEmision) as mini
            from DBA.ordendecompra as oc,
            det_compras as dc,
            productos_inve as pi
            where oc.numero_orden = dc.num_compra
            and dc.cod_producto = pi.cod_producto
            and dc.empresa = pi.empresa
            and oc.FechaEmision between :inicio and :fin
            and pi.empresa = 1
            group by dc.cod_producto, pi.nombre_corto, pi.nombre_fiscal",['inicio'=>$inicio,'fin'=>$fin]);
            return DataTables::of($ordenes)->addColumn('details_url', function($ordenes){
                return url('ddfvc/'. $ordenes->cod_producto.'/'.$ordenes->mini.'/'.$ordenes->maxi);
            })->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    function ddetalles_reporte_compras_por_producto_fecha($producto,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',33)->first();
        if($permiso == true)
        {
            $detalles = DB::select("select oc.empresa, oc.numero_orden, ' Orden de Compra' || ' - ' || oc.numero_orden as Documento,
            oc.descripcion_larga, dc.cod_producto, dc.descripcion, dc.cantidad, dc.fecha_entrega,
            dc.precio, dc.orden*100, 'PEDIDO', pr.proveedor, pr.descripcion as nproveedor,
            case oc.tipo_ocompra when '2' then 'Clientes' when '1' then 'Stock' else 'Sin Motivo 'end  as Motivo,
            case oc.tipo_orden when 'E' then  'Exterior' else 'Local' end as 'Origen',
            case oc.estado when 'A' then 'Abierta - Creada' when 'C' then 'Cerrada - Terminada'  when 'N' then
            'Anulada' when 'U' then 'Autorizada - En Transito' else '0' end as 'Estado'
            from ordendecompra as oc,
            det_compras as dc,
            proveedor as pr
            where dc.empresa = oc.empresa
            and dc.num_compra = oc.numero_orden
            and oc.proveedor = pr.proveedor
            and dc.cod_producto = :producto
            and oc.FechaEmision between :inicio and :fin
            union
            select mi.empresa, mi.numero_orden,
            ' Ingreso al Inventario' || ' - ' ||  upper(mi.cod_tipo_movi || '-' || mi.cod_serie_movi || '-' || mi.num_movi),
            mi.observacion, dmi.cod_producto, dmi.descripcion, dmi.cantidad2*-1, mi.fecha, null,
            dmi.orden, 'RECIBIDO', pr.proveedor, pr.descripcion as nproveedor, null,null, null
            from movi_inve as mi,
            det_movi_inve as dmi,
            proveedor as pr
            where dmi.empresa = mi.empresa
            and dmi.cod_tipo_movi = mi.cod_tipo_movi
            and dmi.cod_Serie_movi = mi.cod_Serie_movi
            and dmi.num_movi = mi.num_movi
            and mi.cod_tipo_movi = 'P'
            and mi.numero_orden is not null
            and pr.proveedor = mi.proveedor
            and dmi.cod_producto = :producto2
            and mi.fecha between :inicio2 and :fin2
            ",['producto'=>$producto,'producto2'=>$producto,'inicio'=>$inicio,'inicio2'=>$inicio,'fin'=>$fin,'fin2'=>$fin]);
            return DataTables($detalles)->make(true);
        }
        else
        {
            return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

}/*select dc.cod_producto, oc.estado as tipo, pi.nombre_corto, pi.nombre_fiscal,
case oc.tipo_orden when 'E' then  'Exterior' else 'Local' end as 'Origen',
case oc.estado when 'A' then 'Abierta - Creada' when 'C' then 'Cerrada - Terminada'  when 'N' then
'Anulada' when 'U' then 'Autorizada - En Transito' else '0' end as 'Estado'
from DBA.ordendecompra as oc,
det_compras as dc,
productos_inve as pi
where oc.numero_orden = dc.num_compra
and dc.cod_producto = pi.cod_producto
and dc.empresa = pi.empresa
and oc.fecharecepcion >'2022-01-01'
group by dc.cod_producto, tipo_orden, estado, tipo, pi.nombre_corto, pi.nombre_fiscal*/
