<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Auth;
use App\User;
use App\Estado;
use Carbon\Carbon;
use App\Historial;
use App\Transferencias;
use App\DetTransferencias;
use Yajra\DataTables\DataTables;
use App\Mail\Transferencia;
use App\Mail\NotificacionBodega;
use Illuminate\Support\Facades\Mail;

class BodegaTransferenciaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    
    

    public function transferencias_finalizadas()
    {
        if(Auth::user()->roles == 17)
        {
            return view('bodega.transferenciasFinalizadas');
        }
        else 
        {
            return redirect()->route('home');
        }
    }

    public function datos_transferencias_finalizadas()
    {
        $inventario = DB::select('select num_movi, fecha, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado
        from inventario_web_encabezado_transferencias as iwet,
        unidades as u,
        inventario_web_estados as iwe
        where iwet.id_estado = 18
        and iwet.unidad_transf = u.cod_unidad
        and iwet.id_estado = iwe.id
        and u.empresa = iwet.empresa
        and iwet.cod_unidad = :user',['user'=>Auth::user()->sucursal]);
        return DataTables($inventario)->make(true);
    }

    public function ver_transferencia($id)
    {
        if(Auth::user()->roles == 17)
        {
            $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
            iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
            iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
            iwet.usuario, iwet.fechaSalida, iwet.usuarioSupervisa, iwet.fechaEntrega, iwet.fechaUno
            from inventario_web_encabezado_transferencias as iwet, 
            unidades as u,
            inventario_web_estados as iwe
            where iwet.num_movi = :id
            and iwet.unidad_transf = u.cod_unidad
            and iwet.id_estado = iwe.id
            and u.empresa = iwet.empresa',['id'=>$id]);

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
                inv.existencia1, inves.existencia1 as cd, inv.minimo, inv.piso_sugerido, inv.maximo, iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,
                ((inv.existencia1/inv.maximo)*100) as porcentaje, ((pi.peso*0.453592) * cantidad) as peso, (pi.peso_o_volumen * cantidad) as volumen
                from inventario_web_det_transferencias as iwt,
                productos_inve as pi, 
                inventario_web_categorias as ic,
                inventarios as inv,
                inventarios as inves,
                where iwt.num_movi = :id
                and iwt.cod_producto = pi.cod_producto
                and iwt.incluido = 1
                and iwt.bodega_Transf = inv.cod_bodega
                and iwt.cod_producto = inv.cod_producto
                and iwt.unidad_transf = inv.cod_unidad
                and inves.cod_producto = iwt.cod_producto
                and inves.cod_unidad = 27
                and inves.cod_bodega = 1
                and pi.cod_tipo_prod = ic.cod_tipo_prod
                and pi.empresa = iwt.empresa
                and ic.empresa = iwt.empresa
                and inv.empresa = iwt.empresa
                and inves.empresa = iwt.empresa
                order by porcentaje asc',['id'=>$id]);
                return view('bodega.verTransferencia',compact('tran','productos','id','integra'));
            }
            elseif($ver >= 18 && $ver <= 20)
            {   
                $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
                iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,
                ((pi.peso*0.453592) * cantidad) as peso, (pi.peso_o_volumen * cantidad) as volumen
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
                return view('bodega.verTransferencia',compact('tran','productos','id','integra'));
            }
        }
        else 
        {
            return redirect()->route('home');
        }   
    }
}
