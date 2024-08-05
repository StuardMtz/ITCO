<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\User;
use Carbon\Carbon;
use App\Historial;
use App\Mail\RevisionActividades;
use Yajra\DataTables\DataTables;
use App\Exports\ActividadesExport;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ReporteActividades extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

//--------------------------- Funciones para mostrar el listado de usuarios marcados para el reporte ----------------------------------------------------------
    function inicio()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first())
        {
            $roles = DB::table('planta_roles')->select(['nombre','rol'])
            ->whereIn('rol',[1,6,20])->get();
            return view('actividades.inicio',compact('roles'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_inicio()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first();
        if($permiso == true)
        {
            $usuarios = DB::select('select u.icono_actividad, u.name, pr.nombre, u.codigo_vendedor
            from users as u,
            planta_roles as pr
            where codigo_vendedor is not null
            and icono_actividad is not null
            and u.roles = pr.rol
            order by pr.rol');
            return DataTables($usuarios)->make(true);
        }
        $usuarios = DB::select('select u.icono_actividad, u.name, pr.nombre, u.codigo_vendedor
        from users as u,
        planta_roles as pr
        where codigo_vendedor is not null
        and icono_actividad is not null
        and u.roles = pr.rol
        and codigo_vendedor = :usuario
        order by pr.rol',['usuario'=>Auth::user()->codigo_vendedor]);
        return DataTables($usuarios)->make(true);
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para mostrar el listado actividades realizadas por usuario ------------------------------------------------------------
    function reportes_por_usuario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first())
        {
            return view('actividades.reportes_usuarios',compact('id'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_reportes_de_usuarios($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $reportes = DB::select('select u.name, rv.Fecha, rv.Fecha_Final, rv.Observaciones, rv.Comentario, rv.id, pr.nombre
            from Reporte_de_Vendedor as rv,
            users as u,
            planta_roles as pr
            where rv.Vendedor = u.codigo_vendedor
            and rv.Fecha > :fecha
            and u.codigo_vendedor = :usuario
            and u.roles = pr.rol',['usuario'=>$id,'fecha'=>Carbon::now()->subMonths(3)]);
            return DataTables($reportes)->make(true);
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para visualizar el mapa de visitas realizadas por un usuario en especifico ----------------------------------------------
    function mapa_con_visitas_usuario($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = :vendedor
            and u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            //and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and drv.Fecha > :fecha
            order by drv.Fecha, drv.Hora",['vendedor'=>$id,'fecha'=>Carbon::now()->subMonths(2)]);
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'icono'=>$c->icono_actividad,'orden'=>$c->orden);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = :vendedor
            and u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            //and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.Nombre = 'HOSPEDAJE'
            and drv.Fecha > :fecha
            order by drv.Fecha, drv.Hora",['vendedor'=>$id,'fecha'=>Carbon::now()->subMonths(2)]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>utf8_encode($cs->Descripcion),"prospecto"=>utf8_encode($cs->nombre),"vendedor"=>$cs->name );
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = :vendedor
            and p.ID = drv.ID_Prospecto
            //and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.Nombre = 'ALIMENTACION'
            and drv.Fecha > :fecha
            order by drv.Fecha, drv.Hora",['vendedor'=>$id,'fecha'=>Carbon::now()->subMonths(2)]);
            $comlat[]='';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>utf8_encode($com->Descripcion),"prospecto"=>utf8_encode($com->nombre),"vendedor"=>$com->name );
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado de visitas realizados por usuario filtrado por fecha ----------------------------------------------
    function reportes_por_usuario_fecha(Request $request,$id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('actividades.reportes_fecha_usuario',compact('id','inicio','fin'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_reportes_po_usuario_fecha($id,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $reportes = DB::select('select u.name, rv.Fecha, rv.Fecha_Final, rv.Observaciones, rv.Comentario, rv.id, pr.nombre
            from Reporte_de_Vendedor as rv,
            users as u,
            planta_roles as pr
            where rv.Vendedor = u.codigo_vendedor
            and rv.Vendedor = :usuario
            and rv.Fecha between :inicio and :fin
            and u.roles = pr.rol',['usuario'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($reportes)->make(true);
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para visualizar el mapa de visitas realizadas por un usuario en especifico ----------------------------------------------
    function mapa_visitas_fecha($id,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(p.Nombre_Corto+' '+p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = :vendedor
            and u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'vendedor'=>$id]);
            $lat[]= '';
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'orden'=>$c->orden,'icono'=>$c->icono_actividad);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = :vendedor
            and u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'L2tsXZlQ0jlr'
            and Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'vendedor'=>$id]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>$cs->Descripcion,"prospecto"=>$cs->nombre,"vendedor"=>$cs->name );
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = :vendedor
            and u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'K5HrXEvrqBEF'
            and Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'vendedor'=>$id]);
            $comlat[] = '';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>$com->Descripcion,"prospecto"=>$com->nombre,"vendedor"=>$com->name );
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver los detalles de un reporte realizado por usuario -------------------------------------------------------------
    function detalles_reporte_usuario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first())
        {
            if($vendedor = DB::table('DetalleReporteVendedor')->where('id_reporte',$id)->first())
            {
                return view('actividades.detalles_reporte',compact('id','vendedor'));
            }
            return back()->with('error','¡No existen registros para este reporte!');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_detalles_reporte_usuario($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $detalles = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and drv.id_reporte = :id
            //and u.roles = 1
            and u.sucursal != 11
            order by drv.Hora",['id'=>$id]);
            return $detalles;
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function detalles_reporte_verificado($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first())
        {
            $verificado = DB::table('DetalleReporteVendedor')->where('id_reporte',$id)->update(['verificado'=>'Sí']);
            $correo = DB::table('DetalleReporteVendedor')->where('id_reporte',$id)->first();
            $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',10)->first();
            if($permiso == true)
            {
                $user           = User::where('codigo_vendedor',$correo->Vendedor)->pluck('email');
                $usuario        = Auth::user()->name;
                $fecha          = $correo->Fecha;
                Mail::to($user)->send(new RevisionActividades($usuario,$fecha,));
                return $verificado;
            }
            return $verificado;
        }
        return 'No tienes permiso para modificar';
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el mapa con las ubicaciones recoridas por reporte de usuario ---------------------------------------------------
    function mapa_visitas_por_reporte($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            //and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and drv.id_reporte = :id
            order by drv.Fecha, drv.Hora",['id'=>$id]);
            $lat[]= '';
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'orden'=>$c->orden,"icono"=>$c->icono_actividad);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'L2tsXZlQ0jlr'
            and drv.id_reporte = :id
            order by drv.Fecha, drv.Hora",['id'=>$id]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>utf8_encode($cs->Descripcion),"prospecto"=>utf8_encode($cs->nombre),
                "vendedor"=>$cs->name );
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'K5HrXEvrqBEF'
            and drv.id_reporte = :id
            order by drv.Fecha, drv.Hora",['id'=>$id]);
            $comlat[] = '';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>$com->Descripcion,"prospecto"=>$com->nombre,"vendedor"=>$com->name );
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el seguimiento de actividades de reporte de usuario ------------------------------------------------------------
    function detalle_seguimiento_usuario($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $detalle = DB::table('DetalleReporteVendedor')->where('ID',$id)->first();
            $seguimiento = DB::select("select srv.Fecha, srv.Hora, srv.Descripcion, srv.ACCION, (p.Fax+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as Nombre_Seguimiento
            from SeguimientoReporteVendedores as srv,
            DetalleReporteVendedor as drv,
            Prospectos as p
            where ID_ClienteProspecto = :idp
            and drv.ID = srv.ID_ClienteProspecto
            and p.ID = drv.ID_Prospecto
            and srv.Empresa = p.Empresa
            and srv.Empresa = drv.Empresa
            and usuario = :vendedor",['idp'=>$detalle->ID,'vendedor'=>$detalle->Vendedor]);
            return $seguimiento;
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de todas las actividades realizadas por todos los usuarios ------------------------------------------
    function listado_todas_las_actividades()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first();
        if($permiso == true)
        {
            $roles = DB::table('planta_roles')->select(['nombre','rol'])
            ->whereIn('rol',[1,6,20])->get();
            return view('actividades.todas_las_actividades',compact('roles'));
        }
        return redirect()->route('listado_vendedores')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_listado_todas_las_actividades()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first();
        if($permiso == true)
        {
            $reportes = DB::select('select u.name, rv.Fecha, rv.Fecha_Final, rv.Observaciones, rv.Comentario, rv.id, pr.nombre
            from Reporte_de_Vendedor as rv,
            users as u,
            planta_roles as pr
            where rv.Vendedor = u.codigo_vendedor
            and rv.Fecha > :fecha
            and u.sucursal != 11
            and u.roles = pr.rol',['fecha'=>Carbon::now()->subMonths(2)]);
            return DataTables($reportes)->make(true);
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el mapa con las ubicaciones de todas las acitivdades de todos los usuarios -------------------------------------
    function mapa_todas_visitas_usuarios()
    {
       $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
       if($permiso == true)
       {
           $historial              = new Historial();
           $historial->id_usuario  = Auth::id();
           $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
           $historial->created_at  = new Carbon();
           $historial->updated_at  = new Carbon();
           $historial->save();
           $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
           drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
           drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
           from DetalleReporteVendedor as drv,
           users as u,
           Prospectos as p
           where u.codigo_vendedor = drv.Vendedor
           and p.ID = drv.ID_Prospecto
           and u.roles = 1
           and u.sucursal != 11
           and u.empresa = p.Empresa
           and p.ID != 'L2tsXZlQ0jlr'
           and drv.ID_Prospecto != 'K5HrXEvrqBEF'
           order by drv.Fecha, drv.Hora");
           $lat[]= '';
           foreach ($co as $c) {
               $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
               'orden'=>$c->orden,"icono"=>$c->icono_actividad);
           }
           $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
           drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
           drv.verificado, drv.latitud, drv.longitud
           from DetalleReporteVendedor as drv,
           users as u,
           Prospectos as p
           where u.codigo_vendedor = drv.Vendedor
           and p.ID = drv.ID_Prospecto
           and u.roles = 1
           and u.sucursal != 11
           and u.empresa = p.Empresa
           and p.ID = 'L2tsXZlQ0jlr'
           order by drv.Fecha, drv.Hora");
           $lats[]= '';
           foreach ($hotel as $cs) {
               $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>utf8_encode($cs->Descripcion),"prospecto"=>utf8_encode($cs->nombre),"vendedor"=>$cs->name);
           }
           $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
           drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
           drv.verificado, drv.latitud, drv.longitud
           from DetalleReporteVendedor as drv,
           users as u,
           Prospectos as p
           where u.codigo_vendedor = drv.Vendedor
           and p.ID = drv.ID_Prospecto
           and u.roles = 1
           and u.sucursal != 11
           and u.empresa = p.Empresa
           and p.ID = 'K5HrXEvrqBEF'
           order by drv.Fecha, drv.Hora");
           $comlat[] = '';
           foreach ($comida as $com) {
               $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>utf8_encode($com->Descripcion),"prospecto"=>utf8_encode($com->nombre),
               "vendedor"=>$com->name);
           }
           return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
       }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
   }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de todas las actividades realizadas por todos los usuarios ------------------------------------------
    function listado_de_todas_las_actividades_por_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            return view('actividades.todas_las_actividades_fecha',compact('inicio','fin'));
        }
        return redirect()->route('listado_vendedores')->with('error','¡No tienes permiso para ingresar!');
    }

function datos_listado_actividades_por_fecha($inicio,$fin)
{
    $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first();
    if($permiso == true)
    {
        $reportes = DB::select('select u.name, rv.Fecha, rv.Fecha_Final, rv.Observaciones, rv.Comentario, rv.id, pr.nombre
        from Reporte_de_Vendedor as rv,
        users as u,
        planta_roles as pr
        where rv.Vendedor = u.codigo_vendedor
        and rv.Fecha between :inicio and :fin
        and u.sucursal != 11
        and u.roles = pr.rol',['inicio'=>$inicio,'fin'=>$fin]);
        return DataTables($reportes)->make(true);
    }
    return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el mapa con las ubicaciones de todas las acitivdades de todos los usuarios -------------------------------------
    function mapa_todas_las_actividades_fecha($inicio,$fin)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first())
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
            $lat[]= '';
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'orden'=>$c->orden,"icono"=>$c->icono_actividad);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'L2tsXZlQ0jlr'
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>utf8_encode($cs->Descripcion),"prospecto"=>utf8_encode($cs->nombre),"vendedor"=>$cs->name);
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'K5HrXEvrqBEF'
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
            $comlat[] = '';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>utf8_encode($com->Descripcion),"prospecto"=>utf8_encode($com->nombre),
                "vendedor"=>$com->name);
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el listado de todas las actividades realizadas por todos los usuarios ------------------------------------------
    function listado_de_todas_las_actividades_registradas_por_fecha(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first())
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            $rol = $request->area;
            $roles = DB::table('planta_roles')->select(['nombre','rol'])
                ->whereIn('rol',[1,6,20])->get();
            return view('actividades.actividades_por_roles',compact('inicio','fin','rol','roles'));
        }
        return redirect()->route('listado_vendedores')->with('error','¡No tienes permiso para ingresar!');
    }

function datos_listado_actividades_registradas_por_fecha($inicio,$fin,$rol)
{
    $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',37)->first();
    if($permiso == true)
    {
        if($rol != 0)
        {
            $reportes = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad, pr.nombre as rol
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p,
            Planta_roles as pr
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = :rol
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and drv.Fecha between :inicio and :fin
            and u.roles = pr.rol
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'rol'=>$rol]);
            return DataTables($reportes)->make(true);
        }
        $reportes = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
        drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
        drv.verificado, drv.latitud, drv.longitud, u.icono_actividad, pr.nombre as rol
        from DetalleReporteVendedor as drv,
        users as u,
        Prospectos as p,
        Planta_roles as pr
        where u.codigo_vendedor = drv.Vendedor
        and p.ID = drv.ID_Prospecto
        and u.sucursal != 11
        and u.empresa = p.Empresa
        and p.ID != 'L2tsXZlQ0jlr'
        and drv.ID_Prospecto != 'K5HrXEvrqBEF'
        and drv.Fecha between :inicio and :fin
        and u.roles = pr.rol
        order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
        return DataTables($reportes)->make(true);
    }
    return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el mapa con las ubicaciones de todas las acitivdades de todos los usuarios -------------------------------------
    function mapa_todas_las_actividades_registradas($inicio,$fin,$rol)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            if($rol != 0)
            {
                $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
                drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
                drv.verificado, drv.latitud, drv.longitud, u.icono_actividad, pr.nombre
                from DetalleReporteVendedor as drv,
                users as u,
                Prospectos as p,
                Planta_roles as pr
                where u.codigo_vendedor = drv.Vendedor
                and p.ID = drv.ID_Prospecto
                and u.roles = :rol
                and u.sucursal != 11
                and u.empresa = p.Empresa
                and p.ID != 'L2tsXZlQ0jlr'
                and drv.ID_Prospecto != 'K5HrXEvrqBEF'
                and drv.Fecha between :inicio and :fin
                order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'rol'=>$rol]);
                $lat[]= '';
                foreach ($co as $c) {
                    $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                    'orden'=>$c->orden,"icono"=>$c->icono_actividad);
                }
                $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
                drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
                drv.verificado, drv.latitud, drv.longitud
                from DetalleReporteVendedor as drv,
                users as u,
                Prospectos as p
                where u.codigo_vendedor = drv.Vendedor
                and p.ID = drv.ID_Prospecto
                and u.roles = :rol
                and u.sucursal != 11
                and u.empresa = p.Empresa
                and p.ID = 'L2tsXZlQ0jlr'
                and drv.Fecha between :inicio and :fin
                order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'rol'=>$rol]);
                $lats[]= '';
                foreach ($hotel as $cs) {
                    $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>$cs->Descripcion,"prospecto"=>$cs->nombre,"vendedor"=>$cs->name );
                }
                $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
                drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
                drv.verificado, drv.latitud, drv.longitud
                from DetalleReporteVendedor as drv,
                users as u,
                Prospectos as p
                where u.codigo_vendedor = drv.Vendedor
                and p.ID = drv.ID_Prospecto
                and u.roles = :rol
                and u.sucursal != 11
                and u.empresa = p.Empresa
                and p.ID = 'K5HrXEvrqBEF'
                and drv.Fecha between :inicio and :fin
                order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin,'rol'=>$rol]);
                $comlat[] = '';
                foreach ($comida as $com) {
                    $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>$com->Descripcion,"prospecto"=>$com->nombre,"vendedor"=>$com->name );
                }
                return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
            }
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad, pr.nombre
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p,
            Planta_roles as pr
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
            $lat[]= '';
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'orden'=>$c->orden,"icono"=>$c->icono_actividad);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'L2tsXZlQ0jlr'
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>$cs->Descripcion,"prospecto"=>$cs->nombre,"vendedor"=>$cs->name );
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'K5HrXEvrqBEF'
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha, drv.Hora",['inicio'=>$inicio,'fin'=>$fin]);
            $comlat[] = '';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>$com->Descripcion,"prospecto"=>$com->nombre,"vendedor"=>$com->name );
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver los detalles de todas las actividades realizado por usuario -------------------------------------------------------------
    function listado_detallado_actividades_por_usuario($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first())
        {
            if($vendedor = DB::table('DetalleReporteVendedor')->where('Vendedor',$id)->first())
            {
                return view('actividades.reporte_detallado',compact('id','vendedor'));
            }
            return back()->with('error','¡No existen registros para este reporte!');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_listado_detallado_actividades_por_usuario($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $detalles = DB::select("select ROW_NUMBER() OVER (order by drv.Fecha asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, pr.nombre
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p,
            planta_roles as pr
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.codigo_vendedor = :id
            and u.roles = pr.rol
            and u.sucursal != 11
            and drv.Fecha > :fecha
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id,'fecha'=>Carbon::now()->subMonth(2)]);
            return $detalles;
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el mapa con las visitas realizadas por un solo usuario ---------------------------------------------------------
    function mapa_detalle_actividad_usuario($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and u.codigo_vendedor = :id
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id]);
            $lat[]= '';
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'orden'=>$c->orden,"icono"=>$c->icono_actividad);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'L2tsXZlQ0jlr'
            and u.codigo_vendedor = :id
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>utf8_encode($cs->Descripcion),"prospecto"=>utf8_encode($cs->nombre),"vendedor"=>$cs->name);
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'K5HrXEvrqBEF'
            and u.codigo_vendedor = :id
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id]);
            $comlat[] = '';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>utf8_encode($com->Descripcion),"prospecto"=>utf8_encode($com->nombre),
                "vendedor"=>$com->name);
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver los detalles de todas las actividades realizado por usuario -------------------------------------------------------------
    function listado_detallado_actividades_por_usuario_fecha(Request $request,$id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $vendedor = DB::table('DetalleReporteVendedor')->where('Vendedor',$id)->first();
            if($vendedor == true)
            {
                $inicio = $request->inicio;
                $fin    = $request->fin;
                return view('actividades.reporte_detallado_fecha',compact('id','inicio','fin'));
            }
            return back()->with('error','¡No existen registros para este reporte!');
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }

    function datos_listado_detallado_actividades_por_usuario_fecha($id,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[36,37])->first();
        if($permiso == true)
        {
            $detalles = DB::select("select ROW_NUMBER() OVER (order by drv.Fecha asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, pr.nombre as rol
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p,
            planta_roles as pr
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.codigo_vendedor = :id
            and u.roles = pr.rol
            and u.sucursal != 11
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
            return DataTables($detalles)->make(true);
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para ver el mapa con las visitas realizadas por un solo usuario ---------------------------------------------------------
    function mapa_detalle_actividad_usuario_fecha($id,$inicio,$fin)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',36)->first();
        if($permiso == true)
        {
            $historial              = new Historial();
            $historial->id_usuario  = Auth::id();
            $historial->actividad   = 'Visualizo el panel con el mapa que muestra los puntos de las diferentes visitas de un usuario';
            $historial->created_at  = new Carbon();
            $historial->updated_at  = new Carbon();
            $historial->save();
            $co = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud, u.icono_actividad
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID != 'L2tsXZlQ0jlr'
            and drv.ID_Prospecto != 'K5HrXEvrqBEF'
            and u.codigo_vendedor = :id
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
            $lat[]= '';
            foreach ($co as $c) {
                $lat[]  = array("lat"=>$c->latitud,"lng"=> $c->longitud,"dire"=>utf8_encode($c->Descripcion),"prospecto"=>utf8_encode($c->nombre),"vendedor"=>$c->name,
                'orden'=>$c->orden,"icono"=>$c->icono_actividad);
            }
            $hotel = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'L2tsXZlQ0jlr'
            and u.codigo_vendedor = :id
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
            $lats[]= '';
            foreach ($hotel as $cs) {
                $lats[]  = array("lat"=>$cs->latitud,"lng"=> $cs->longitud,"dire"=>utf8_encode($cs->Descripcion),"prospecto"=>utf8_encode($cs->nombre),"vendedor"=>$cs->name);
            }
            $comida = DB::select("select ROW_NUMBER() OVER (ORDER BY Hora asc) as orden, u.name,
            drv.Fecha, drv.Descripcion,(P.Nombre_Corto+ ' ' +p.empresa_trabajo + ' ' + p.nombre) as nombre, drv.Hora, drv.ID as id,
            drv.verificado, drv.latitud, drv.longitud
            from DetalleReporteVendedor as drv,
            users as u,
            Prospectos as p
            where u.codigo_vendedor = drv.Vendedor
            and p.ID = drv.ID_Prospecto
            and u.roles = 1
            and u.sucursal != 11
            and u.empresa = p.Empresa
            and p.ID = 'K5HrXEvrqBEF'
            and u.codigo_vendedor = :id
            and drv.Fecha between :inicio and :fin
            order by drv.Fecha desc, drv.Hora desc",['id'=>$id,'inicio'=>$inicio,'fin'=>$fin]);
            $comlat[] = '';
            foreach ($comida as $com) {
                $comlat[]  = array("lat"=>$com->latitud,"lng"=> $com->longitud,"dire"=>utf8_encode($com->Descripcion),"prospecto"=>utf8_encode($com->nombre),
                "vendedor"=>$com->name);
            }
            return view('actividades.marcador_visitas',compact('lat','lats','comlat'));
        }
        return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}
