<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Auth;
use App\User;
use App\DetMovi;
use App\MoviInve;
use Carbon\Carbon;
use App\Historial;
use App\GrupoUsuario;
use App\Transferencias;
use App\DetTransferencias;
use App\Mail\Transferencia;
use App\BitacoraTransferencia;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Mail;

class STransferenciaController extends Controller
{
    //guardar_revision_transferencia
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function transferencias_pendientes()
    {
        $inventario = DB::select('select num_movi, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.observacion, 
        u.nombre, tv.descripcion as placa_vehiculo, iwe.nombre as estado, tv.nombre as propietario
        from inventario_web_encabezado_transferencias as iwet
        join unidades as u on iwet.unidad_transf = u.cod_unidad
        join inventario_web_estados as iwe on iwet.id_estado = iwe.id
        full join T_Flotas as tv on iwet.placa_vehiculo = tv.Codigo
        where iwet.id_estado >= 16
        and iwet.id_estado < 18        
        and u.empresa = iwet.empresa
        and iwet.usuarioSupervisa = :v',['v'=>Auth::user()->name]);
        return DataTables($inventario)->make(true);
    }

    

    public function transferencias_finalizadas()
    {
        if(Auth::user()->roles == 16)
        {
            return view('suptransferencia.transferenciasFinalizadas');
        }
        return redirect()->route('home');
    }

    public function transferencias_finalizadas_datos()
    {
        $inventario = DB::select('select num_movi, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.observacion, 
        u.nombre, placa_vehiculo, iwe.nombre as estado, iwet.fechaSalida, tv.propietario
        from inventario_web_encabezado_transferencias as iwet,
        unidades as u,
        inventario_web_estados as iwe,
        T_Vehiculos as tv
        where iwet.id_estado = 18
        and iwet.unidad_transf = u.cod_unidad
        and iwet.id_estado = iwe.id
        and iwet.placa_vehiculo = tv.Placa
        and u.empresa = iwet.empresa
        and iwet.cod_unidad = :usu',['usu'=>Auth::user()->sucursal]);
        return DataTables($inventario)->make(true);
    }

    public function ver_transferencia_finalizada($id)
    {
        $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
        iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
        iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
        iwet.usuario, iwet.fechaSalida, iwet.usuarioSupervisa, tv.propietario, iwet.fechaUno
        from inventario_web_encabezado_transferencias as iwet, 
        unidades as u,
        inventario_web_estados as iwe,
        T_Vehiculos as tv
        where iwet.num_movi = :id
        and iwet.unidad_transf = u.cod_unidad
        and iwet.id_estado = iwe.id
        and iwet.placa_vehiculo = tv.Placa
        and u.empresa = iwet.empresa',['id'=>$id]);

        $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, ic.nombre,  pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
        iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,
        ((pi.peso*0.453592) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
        from inventario_web_det_transferencias as iwt,
        productos_inve as pi, 
        inventario_web_categorias as ic,
        where iwt.num_movi = :id
        and iwt.cod_producto = pi.cod_producto
        and iwt.empresa = 1
        and iwt.incluido = 1
        and pi.cod_tipo_prod = ic.cod_tipo_prod
        and pi.empresa = iwt.empresa
        and ic.empresa = iwt.empresa
        order by ic.nombre asc',['id'=>$id]);

        $integra = DB::select('select nombre 
        from inventario_web_bitacora_transferencias
        where num_movi = :id',['id'=>$id]);
        return view('suptransferencia.verTransferencia',compact('tran','productos','id','integra'));
    }

    public function imprimir_transferencia($id)
    {
        $tran = DB::select('select num_movi, iwet.created_at, iwet.observacion, u.nombre, placa_vehiculo, iwe.nombre as estado,
        iwet.descripcion, iwet.comentario, iwet.referencia, iwet.id_estado, iwet.fecha_paraCarga,
        iwet.usuario, iwet.fecha_enCola, iwet.fecha_enCarga, iwet.fecha_cargado, iwet.grupoCarga,
        iwet.usuarioSupervisa, iwet.fechaSalida, tv.propietario, iwet.fechaUno
        from inventario_web_encabezado_transferencias as iwet, 
        unidades as u,
        inventario_web_estados as iwe,
        T_Vehiculos as tv
        where iwet.num_movi = :id
        and iwet.unidad_transf = u.cod_unidad
        and iwet.id_estado = iwe.id
        and iwet.placa_vehiculo = tv.Placa
        and u.empresa = iwet.empresa',['id'=>$id]);

        $per = Transferencias::find($id);

        $productos = DB::select('select iwt.id, iwt.cod_producto, pi.nombre_corto, pi.nombre_fiscal, floor(iwt.cantidad1) as cantidad, 
        iwt.created_at, iwt.updated_at, iwt.incluido, iwt.num_movi,
        ((pi.peso*0.453592) * cantidad) as peso, (pi.factor_a_unidad_basica * cantidad) as volumen
        from inventario_web_det_transferencias as iwt,
        productos_inve as pi, 
        where iwt.num_movi = :id
        and iwt.cod_producto = pi.cod_producto
        and iwt.incluido = 1
        and iwt.cantidad1 > 0
        and pi.empresa = iwt.empresa
        order by pi.nombre_corto asc',['id'=>$id]);
        if($per->id_estado >= 18 && $per->id_estado <= 19)
        {
            $usuario = Auth::user()->name;
            $pdf = PDF::loadView('suptransferencia.imprimir',compact('tran','productos','usuario'));
            return $pdf->download('transferencia.pdf');
        }
        return back()->with('error','Cambie el estado a ¡en cola! para poder imprimir');
    }
}
