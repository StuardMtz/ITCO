<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use Auth;
use Image;
use App\User;
use App\Semana;
use App\Estado;
use App\Camion;
use Carbon\Carbon;
use App\Historial;
use App\Inventario;
use App\ProductoSemana;
use App\usuarioPermiso;
use Yajra\DataTables\DataTables;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

//vista_editar_usuario
class SemanaController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

//----------------------------------------------- Funcion para ver la pantalla de inicio del panel administrativo ---------------------------------------------
    public function inicio_administracion()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $semanas = Semana::orderby('id','asc')->get();
            $fechas = new Carbon();
            $fecha = Carbon::parse($fechas)->format('Y-m-d');
            $historial              = new Historial();
            $historial->id_usuario  = Auth::user()->id;
            $historial->actividad   = 'Ingreso al panel administrativo de usuarios';
            $historial->created_at  = Carbon::now();
            $historial->updated_at  = Carbon::now();
            $historial->save();
            return view('administracion.inicio',compact('semanas','fechas','fecha'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para agregar productos a los inventarios semanales --------------------------------------------------
    public function agregar_producto($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $semana = Semana::find($id);
    	    return view('administracion.agregar_producto',compact('semana'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function productos_semana($semana)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $se = Semana::find($semana);
            $productos_agregados = DB::select('select * from inventario_web_productos_semana
            where semana = :semana
            or semana is null',['semana'=>$se->semana]);
            return DataTables::of($productos_agregados)->make(true);
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function semana_producto(Request $request,$semana)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $existe = DB::table('inventario_web_productos_semana')->where('cod_producto',$request->producto)->count();
            if($existe == 1)
            {
                return back()->with('error','¡El producto ya se encuentra agregado!');
            }
            else
            {
                $producto = DB::table('productos_inve')->select(['cod_producto','inventario_web_categorias.nombre','nombre_fiscal',
                'nombre_corto','inventario_web_categorias.cod_tipo_prod'])
                ->join('inventario_web_categorias','productos_inve.cod_tipo_prod','=','inventario_web_categorias.cod_tipo_prod')
                ->where('cod_producto',$request->producto)->where('productos_inve.empresa',Auth::user()->empresa)->first();
                $agregar = new ProductoSemana();
                $agregar->cod_producto  = $producto->cod_producto;
                $agregar->cod_tipo_prod = $producto->nombre;
                $agregar->nombre_fiscal = $producto->nombre_fiscal;
                $agregar->nombre_corto  = $producto->nombre_corto;
                $agregar->created_at    = Carbon::now();
                $agregar->updated_at    = Carbon::now();
                $agregar->save();
                $historial              = new Historial();
                $historial->id_usuario  = Auth::user()->id;
                $historial->actividad   = 'Agrego Producto a Semana';
                $historial->created_at  = Carbon::now();
                $historial->updated_at  = Carbon::now();
                $historial->save();
                return back()->with('success','¡Agreado con exito!');
            }
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function productos(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }
            $tags = DB::table('productos_inve')->where('nombre_fiscal','like','%'. $term .'%')->where('empresa',Auth::user()->empresa)
            ->orwhere('nombre_corto','like','%'. $term .'%')->where('empresa',Auth::user()->empresa)->limit(10)->get();
            $formatted_tags = [];
            foreach ($tags as $tag) {
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

//----------------------------------------------- Funcion para actualizar la fecha del inventario -------------------------------------------------------------
    public function vista_actualizar_semana($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $semana = Semana::find($id);
            return view('administracion.actualizar_semana',compact('semana'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function actualizar_semana($id,Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $semana = Semana::FindOrFail($id);
            $semana->fecha_inicial = $request->fecha_inicial;
            $semana->fecha_final = $request->fecha_final;
            if($semana->save())
            {
                $historial              = new Historial();
                $historial->id_usuario  = Auth::user()->id;
                $historial->actividad   = 'Se cambiaron las fechas de inicio y fin del inventario semanal';
                $historial->created_at  = Carbon::now();
                $historial->updated_at  = Carbon::now();
                $historial->save();
            }
            return redirect()->route('inicio_adm')->with('success','Actualizado con exito');
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para crear una nueva semana para inventarios ------------------------------------------------------
    public function crear_semana()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
    	    return view('administracion.crear_semana');
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function guardar_semana(Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $existe = Semana::where('semana',$request->semana)->first();
            if($existe == true)
            {
                return back()->with('error','¡No se permite duplicar semana existente!');
            }
            else
            {
                $semana                 = new Semana();
                $semana->semana         = $request->semana;
                $semana->fecha_inicial  = $request->fecha_inicial;
                $semana->fecha_final    = $request->fecha_final;
                $semana->created_at     = Carbon::now();
                $semana->updated_at     = Carbon::now();
                $semana->save();
                return redirect()->route('inicio_adm')->with('success','Semana agregada con exito');
            }
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funcion para ver el listado de usuarios en el sistema -------------------------------------------------------
    public function listado_usuarios()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $usuarios = DB::select('select u.id, u.name, pr.nombre, u.email
            from users as u,
            planta_roles as pr
            where u.roles = pr.rol
            and pr.id > 7');
            return view('administracion.listado_usuarios',compact('usuarios'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function agregar_usuario()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $sucursales = DB::table("unidades")->where('empresa',Auth::user()->empresa)->get();//consulta que trae todas las sucursales que pertenecen a la empresa 1
            $roles = DB::table('planta_roles')->where('id','>',7)->get();
            return view('administracion.usuario',compact('sucursales','roles'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function permisos_usuario()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $permisos = DB::table('inventario_web_permisos')->orderby('id','asc')->get();
            $formatted_tags = [];
            foreach ($permisos as $tag)
            {
                $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->nombre];
            }
            return \Response::json($formatted_tags);
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function crear_usuario(UserRequest $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $validator = Validator::make($request->all(),[
                'name'=>'required',
                'email'=>'required',
                'sucursal'=>'required',
                'bodega'=>'required',
                'roles'=>'required',
                'password'=>'required',
                'permisos'=>'required',
            ]);
            if ($validator->fails())
            {
                return back()
                ->withErrors($validator)
                ->withInput();
            }
            else
            {
                $usuario                = new User();
                $usuario->name          = $request->name;
                $usuario->email         = $request->email;
                $usuario->sucursal      = $request->sucursal;
                $usuario->bodega        = $request->bodega;
                $usuario->roles         = $request->roles;
                $usuario->password      = Hash::make($request->password);
                $usuario->created_at    = Carbon::now();
                $usuario->updated_at    = Carbon::now();
                $usuario->empresa       = Auth::user()->empresa;
                $usuario->no_identificacion = $request->no_identificacion;
                $usuario->codigo_vendedor   = $request->codigo_vendedor;
                $usuario->serie_cotizacion  = $request->serie_cotizacion;
                $usuario->codigo_diamante   = $request->codigo_diamante;
                if($request->file('icono_actividad'))
                {
                    $url = Storage::disk('public')->putFile('storage',$request->file('icono_actividad'));
                    $usuario->icono_actividad= $url;
                    $real_path = public_path('/'.$url);
                    Image::make($real_path)
                    ->resize(48, 50, function ($constraint){
                        $constraint->aspectRatio();
                    })
                    ->save($real_path,72);
                }
                if($usuario->save())
                {
                    $id_n = $usuario->created_at;
                    $id = User::where('created_at',$id_n)->first();
                    foreach($request->permisos as $key => $value)
                    {
                        $permiso = new usuarioPermiso();
                        $permiso->id_usuario = $id->id;
                        $permiso->id_permiso = $request->permisos[$key];
                        $permiso->created_at = Carbon::now();
                        $permiso->save();
                    }
                    $historial              = new Historial();
                    $historial->id_usuario  = Auth::user()->id;
                    $historial->actividad   = 'Se agrego nuevo usuario';
                    $historial->created_at  = Carbon::now();
                    $historial->updated_at  = Carbon::now();
                    $historial->save();
                }
                return redirect()->route('lis_us');
            }
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para editar los datos de un usuario ---------------------------------------------------------------
    public function vista_editar_usuario($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $usuario = DB::select('select u.id, u.name, pr.nombre, u.email, un.nombre as sucursal,
            pr.rol, un.cod_unidad, pr.nombre as nomrol, u.password, b.nombre as bod, u.icono_actividad,
            b.cod_bodega, u.no_identificacion, u.codigo_vendedor, u.serie_cotizacion, u.codigo_diamante
            from users as u,
            planta_roles as pr,
            unidades as un,
            bodegas as b
            where u.roles = pr.rol
            and u.sucursal = un.cod_unidad
            and u.bodega = b.cod_bodega
            and u.id = :id
            and un.cod_unidad = b.cod_unidad
            and un.empresa = :empresa
            and b.empresa = un.empresa',['id'=>$id,'empresa'=>Auth::user()->empresa]);
            $permisos = DB::select("select iwp.id, iwp.nombre
            from inventario_web_permisos as iwp,
            inventario_web_permisos_usuario as iwps
            where iwp.id = iwps.id_permiso
            and iwps.id_usuario = :id",['id'=>$id]);
            $sucursales = DB::table("unidades")->where('empresa',Auth::user()->empresa)->get();
            $roles = DB::table('planta_roles')->where('id','>',7)->get();
            return view('administracion.usuario_editar',compact('usuario','sucursales','roles','permisos','id'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function editar_usuario($id, Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $validator = Validator::make($request->all(),[
                'name'=>'required',
                'email'=>'required',
                'sucursal'=>'required',
                'bodega'=>'required',
                'roles'=>'required',
                'permisos'=>'required',
            ]);
            if ($validator->fails())
            {
                return back()
                ->withErrors($validator)
                ->withInput();
            }
            else
            {
                $usuario                = User::FindOrFail($id);
                $usuario->name          = $request->name;
                $usuario->email         = $request->email;
                $usuario->sucursal      = $request->sucursal;
                $usuario->bodega        = $request->bodega;
                $usuario->roles         = $request->roles;
                $usuario->no_identificacion = $request->no_identificacion;
                $usuario->codigo_vendedor   = $request->codigo_vendedor;
                $usuario->serie_cotizacion  = $request->serie_cotizacion;
                $usuario->codigo_diamante   = $request->codigo_diamante;
                if($request->file('icono_actividad'))
                {
                    $url = Storage::disk('public')->putFile('storage',$request->file('icono_actividad'));
                    $usuario->icono_actividad= $url;
                    $real_path = public_path('/'.$url);
                    Image::make($real_path)
                    ->resize(48, 50, function ($constraint){
                        $constraint->aspectRatio();
                    })
                    ->save($real_path,72);
                }
                if($request->password == '')
                {

                }
                else
                {
                    $usuario->password      = Hash::make($request->password);
                }
                $eliminar = usuarioPermiso::where('id_usuario',$id)->delete();
                //$usuario->created_at    = Carbon::now();
                $usuario->updated_at    = Carbon::now();
                if($usuario->update())
                {
                    foreach($request->permisos as $key => $value)
                    {
                        $permiso = new usuarioPermiso();
                        $permiso->id_usuario = $id;
                        $permiso->id_permiso = $request->permisos[$key];
                        $permiso->created_at = Carbon::now();
                        $permiso->save();
                    }
                    $historial = new Historial();
                    $historial->id_usuario = Auth::user()->id;
                    $historial->actividad = 'Se edito información de Usuario';
                    $historial->created_at = Carbon::now();
                    $historial->updated_at = Carbon::now();
                    $historial->save();
                }
                return redirect()->route('lis_us');
            }
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el historial de un usuario ---------------------------------------------------------------
    public function historial($id)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $historial = Historial::where('id_usuario',$id)->orderby('id','desc')->where('created_at','>',Carbon::now()->subMonths(6))->get();
            return view('administracion.historial',compact('historial','id'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para ver el historial de un usuario ---------------------------------------------------------------
    public function historial_por_fecha($id,Request $request)
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $inicio = $request->inicio;
            $fin = $request->fin;
            $historial = Historial::where('id_usuario',$id)->orderby('id','desc')->whereBetween('created_at',[$inicio,$fin])->get();
            return view('administracion.historial_fecha',compact('historial','inicio','fin','id'));
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------- Funciones para eliminar caracteres especiales ---------------------------------------------------------------
    public function vista_procesos()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            return view('administracion.eliminar_caracteres'); //por medio de esta vista se pueden ejecutar los procedimientos anteriores
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function categoria_nombre()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            //Esta funcion es creada porque la aplicación no reconoce los caracteres esperciales de la base de datos. Por esta razón es necesario eliminar dichos caracteres, en la base de datos existe una tabla llamada tipos_prod la cual contiene la categoria de los productos,  dicha tabla no debe ser modificada, para eliminar los caracteres especiales sin afectar la base de datos se creo una copia de dicha tabla con el nombre de inventario_web_categoria, en la que se pueden eliminar los caracteres especiales sin afectar el funcionamiento de la base de datos
            $categoria_nombre = DB::select('call inventario_web_categorias_cam_nombre()');//por medio de este procedimiento almacenado se realiza la copia de los datos de la tabla tipos_prod a la tabla inventario_web_categorias
            return back()->with('success','Realizado con Existo');
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function categoria_modificar()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            $categoria_modificar = DB::select('call inventario_web_categorias_utf()');//este procedimiento almacenado elimina todos los caracteres especiales de la columna nombre de la tabla inventario_web_categorias
            return back();
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function productos_nombre()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            //al igual que con las categorias, fue necesario eliminar los caracteres especiales del nombre de los productos, para no afectar al funcionamiento de otros programas, se realiza una copia del nombre del producto en otra culumna dentro de la misma tabla
            $productos_nombre = DB::select('call inventario_web_nombre_fiscal()');//este procedimiento copia el nombre de los productos a la columna llamada nombre_fiscal
            return redirect()->route('home');
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }

    public function productos_modificar()
    {
        $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first();
        if($permiso == true)
        {
            //esta funcion elimina los caracteres especiales de los nombres de los productos
            $productos_modificar = DB::select('call inventario_web_nombre_fiscal_utf()');//por medio de este procedimiento almacenado se eliminan los caracteres especiales de la columna, nombre_fiscal
            return back();
        }
        else
        {
        return redirect()->route('home')->with('error','No tienes permisos para accesar');
        }
    }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------









//

    public function busqueda_productos_inventario(Request $request)
    {
        $term = trim($request->q);
        if (empty($term))
        {
            return \Response::json([]);
        }
        $tags = DB::select("select tp.nombre as cod_tipo_prod, pi.nombre_corto, pi.nombre_fiscal
        from productos_inve as pi,
        tipos_prod as tp
        where pi.empresa = 1
        and tp.Empresa = 1
        and tp.cod_tipo_prod = pi.cod_tipo_prod
        and pi.cod_tipo_prod = :term2
        or pi.nombre_corto = :term
        and tp.Empresa = 1
        and tp.cod_tipo_prod = pi.cod_tipo_prod
        and pi.empresa = 1",['term'=>$term,'term2'=>$term]);
        $formatted_tags = [];
        foreach ($tags as $tag)
        {
            $formatted_tags[] = ['id' => $tag->cod_tipo_prod, 'text' => $tag->cod_tipo_prod.' - '.$tag->nombre_corto.' '.$tag->nombre_fiscal];
      }
      return \Response::json($formatted_tags);
    }

    public function agregar_prose($semana,$id)
    {
        $agregar = ProductoSemana::where('id',$id)->update(['semana'=>$semana]);
        $historial              = new Historial();
        $historial->id_usuario  = Auth::user()->id;
        $historial->actividad   = 'Agrego Producto a Semana';
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        return back()->with('success','¡Agregado con exito!');
    }

    public function eliminar_producto($semana,$id)
    {
        $agregar = ProductoSemana::where('id',$id)->update(['semana'=>NULL]);
        $historial              = new Historial();
        $historial->id_usuario  = Auth::user()->id;
        $historial->actividad   = 'Elimino un producto de una semana';
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        return back()->with('success','¡Producto eliminado con exito!');
    }

    public function cargar_productos(Request $request)
    {
        $todos_productos = DB::select('call inventario_web_productos_mostrar(?,?)',array($request->sucursal,$request->bodega));
        foreach ($todos_productos as $tp)
        {
            $nuevo                      = new ProductoSemana();
            $nuevo->cod_producto        = $tp->cod_producto;
            $nuevo->nombre_corto        = $tp->nombre_corto;
            $nuevo->nombre_fiscal       = $tp->nombre_fiscal;
            $nuevo->cod_tipo_prod       = $tp->TABLA_APLICABLE;
            $nuevo->existencia_teorica  = $tp->existencia;
            $nuevo->created_at          = Carbon::now();
            $nuevo->updated_at          = Carbon::now();
            $nuevo->save();
        }
        return redirect()->route('home');
    }

    public function vista_carga()
    {
        $sucursales = DB::table("unidades")->where('empresa',1)->get();//consulta que trae todas las sucursales que pertenecen a la empresa 1
        return view('formularios.cargar_productos',compact('sucursales'));
    }

    public function semana_categoria($cat,$semana)
    {
        $productos = ProductoSemana::where('semana','=',NULL)->where('cod_tipo_prod',$cat)->get();
        foreach ($productos as $ag)
        {
            $agre = ProductoSemana::where('id',$ag->id)->update(['semana'=>$semana]);
        }
        $historial              = new Historial();
        $historial->id_usuario  = Auth::user()->id;
        $historial->actividad   = 'Agrego categoria a la semana';
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        return back();
    }

    public function eliminar_categoria($cat,$semana)
    {
        $productos = ProductoSemana::where('semana','=',$semana)->where('cod_tipo_prod',$cat)->get();
        foreach ($productos as $ag)
        {
            $agre = ProductoSemana::where('id',$ag->id)->update(['semana'=>NULL]);
        }
        $historial              = new Historial();
        $historial->id_usuario  = Auth::user()->id;
        $historial->actividad   = 'Elimino categoria de semana';
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        return back();
    }

    public function inventario_sucursales($id)
    {
        $semana = Semana::find($id);
        $sucursales = User::where('roles',3)->where('sucursal','!=',27)->get();
        foreach ($sucursales as $sucursal)
        {
            $inventario                 = new Inventario();
            $inventario->encargado      = $sucursal->name;
            $inventario->estado         = 'En proceso';
            $inventario->sucursal       = $sucursal->sucursal;
            $inventario->bodega         = $sucursal->bodega;
            $inventario->usuario        = $sucursal->id;
            $inventario->creado         = 'No';
            $inventario->semana         = $semana->semana;
            $inventario->fecha_inicial  = $semana->fecha_inicial;
            $inventario->fecha_final    = $semana->fecha_final;
            $inventario->created_at     = Carbon::now();
            $inventario->updated_at     = Carbon::now();
            $inventario->save();
        }
            $historial              = new Historial();
            $historial->id_usuario  = Auth::user()->id;
            $historial->actividad   = 'Se creo Inventario Semanal para todas las sucursales';
            $historial->created_at  = Carbon::now();
            $historial->updated_at  = Carbon::now();
            $historial->save();
        return back()->with('success','Inventario Semanal Creado con Exito');
    }

    public function busqueda(Request $request,$semana)
    {
        $productos = ProductoSemana::where('nombre_corto','LIKE', '%' . $request->busqueda . '%')
        ->where('semana',NULL)->orwhere('nombre_fiscal','LIKE', '%' . $request->busqueda . '%')->where('semana',NULL)
        ->orwhere('cod_tipo_prod','LIKE', '%' . $request->busqueda . '%')->where('semana',NULL)->get();
        return view('panel.busqueda',compact('productos','semana'));
    }

    public function busqueda_agregados(Request $request,$semana)
    {
        $productos = ProductoSemana::where('nombre_corto','LIKE', '%' . $request->busqueda . '%')
        ->where('semana',$semana)->orwhere('nombre_fiscal','LIKE', '%' . $request->busqueda . '%')->where('semana',$semana)
        ->orwhere('cod_tipo_prod','LIKE', '%' . $request->busqueda . '%')->where('semana',$semana)->get();
        return view('panel.busqueda_agregados',compact('productos','semana'));
    }

    public function usuarios()
    {
        return view('panel.usuarios');
    }

    public function datos_usuarios()
    {
        $usuarios = DB::table('users')
        ->select(['name','email','id'])->where('id','!=',1);
        return DataTables::of($usuarios)->make(true);
    }

	public function sucursales()
    {
        return view('panel.sel_sucursal');
    }

    public function listado_de_sucursales()
    {
        $sucursales = DB::table("users")->where('roles',3);
        return DataTables::of($sucursales)->make(true);
    }

    public function inventarios_por_sucursal($sucursal,$bodega)
    {
        $historial                  = new Historial();
        $historial->id_usuario      = Auth::user()->id;
        $historial->actividad       = 'Visualizo inventarios de la sucursal';
        $historial->id_sucursal     = $sucursal;
        $historial->created_at      = Carbon::now();
        $historial->updated_at      = Carbon::now();
        $historial->save();
        return view('panel.inventarios',compact('sucursal','bodega'));
    }

    public function datos_inventarios_sucursales($sucursal,$bodega)
    {
        $inventarios = DB::select('select inventario_web_encabezado.id as numero,inventario_web_encabezado.encargado as encargado,
        inventario_web_encabezado.created_at as created_at, inventario_web_encabezado.updated_at as updated_at,users.name as nombre,
        inventario_web_encabezado.bodega as bodega,inventario_web_encabezado.estado as estado, inventario_web_encabezado.sucursal as sucursal,
        unidades.nombre as uninombre,bodegas.nombre as bonombre,inventario_web_encabezado.creado as creado,inventario_web_encabezado.semana as semana,
        inventario_web_encabezado.fecha_inicial as fecha_inicial,inventario_web_encabezado.fecha_final as fecha_final,
        inventario_web_encabezado.porcentaje as porcentaje
        from inventario_web_encabezado
        join users on users.id = inventario_web_encabezado.usuario
        join unidades on unidades.cod_unidad = inventario_web_encabezado.sucursal and empresa = 1
        join bodegas on bodegas.cod_bodega = inventario_web_encabezado.bodega and unidades.cod_unidad = bodegas.cod_unidad and bodegas.empresa = 1
        where inventario_web_encabezado.sucursal = :sucs
        and inventario_web_encabezado.bodega = :bod
        order by numero desc',['sucs'=>$sucursal,'bod'=>$bodega]);
        return DataTables::of($inventarios)->make(true);
    }

	public function agregar_faltante(Request $request)
    {
        $nuevo                  = new ProductoSemana();
        $nuevo->cod_producto    = $request->cod;
        $nuevo->nombre_corto    = $request->nombre_corto;
        $nuevo->nombre_fiscal   = $request->nombre;
        $nuevo->cod_tipo_prod   = $request->categoria;
        $nuevo->created_at      = Carbon::now();
        $nuevo->updated_at      = Carbon::now();
        $nuevo->save();
        return back()->with('success','Agregado Correctamente');
    }

	 public function buscar_codigo(Request $request)
    {
		$todos_productos = DB::select('call inventario_web_nuevos()');
        foreach ($todos_productos as $tp)
        {
            $nuevo                      = new ProductoSemana();
            $nuevo->cod_producto        = $tp->cod_producto;
            $nuevo->nombre_corto        = $tp->nombre_corto;
            $nuevo->nombre_fiscal       = $tp->nombre_fiscal;
            $nuevo->cod_tipo_prod       = $tp->TABLA_APLICABLE;
            $nuevo->existencia_teorica  = $tp->existencia;
            $nuevo->created_at          = Carbon::now();
            $nuevo->updated_at          = Carbon::now();
            $nuevo->save();
            }
        return back();
        /*$producto = DB::select('call inventario_web_agregar_producto(?)',array($request->codigo));
        return view('panel.producto_faltante',compact('producto'));*/
    }

    public function faltante(Request $request)
     {
        $producto = DB::select('call inventario_web_agregar_producto(?)',array($request->codigo));
        return view('panel.producto_faltante',compact('producto'));
     }

	public function ver_productos_con_diferencias($id)
    {
		if(Auth::user()->roles <> 1)
		{
            $historial              = new Historial();
            $historial->id_usuario  = Auth::user()->id;
            $historial->actividad   = 'Visualizo diferencias en inventario numero'.' '.$id;
            $historial->created_at  = Carbon::now();
            $historial->updated_at  = Carbon::now();
            $historial->save();
			$id = $id;//atrapa el ID del inventario
			$dato = Inventario::find($id);//
			$cod = $dato->sucursal;
			$datos = DB::select('call inventario_web_encabezado_completo(?)',array($id));//se obtienen los datos del encabezadod del inventario
            $ver_inv =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)
            ->groupby('categoria','id','cod_producto','nombre_corto','nombre_fiscal','existencia_teorica','no_encabezado','created_at',
            'updated_at','existencia_fisica','mal_estado','empresa')->orderby('existencia_fisica','des')
            ->get();//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
            $o = DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_teorica','>=',1)->orwhere('no_encabezado',$id)
            ->where('existencia_teorica','<',0)->count('id');//esta consulta permite traer todos los productos que seran mostrados en la vista, para poder realizar el inventario
			$ver =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)->get()->count('id');
			if($o <> 0)
			{
				$suma = ($ver / $o)*100;
			}
			else
			{
				$suma = 0;
			}
			return view('panel.ver_dif',compact('ver_inv','datos','id','suma','cod'));
		}
		else
		{
            $historial              = new Historial();
            $historial->id_usuario  = Auth::user()->id;
            $historial->actividad   = 'Visualizo diferencias en inventario numero'.' '.$id;
            $historial->created_at  = Carbon::now();
            $historial->updated_at  = Carbon::now();
            $historial->save();
			$id = $id;//atrapa el ID del inventario
			$dato = Inventario::find($id);//
			$datos = DB::select('call inventario_web_encabezado_completo(?)',array($id));//se obtienen los datos del encabezadod del inventario
            $o = DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_teorica','>=',1)->orwhere('no_encabezado',$id)
            ->where('existencia_teorica','<',0)
            ->count('id');//esta consulta permite traer todos los productos que seran mostrados en la vista, para poder realizar el inventario
			$ver =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)->get()->count('id');
			if($o <> 0)
			{
				$suma = ($ver / $o)*100;
			}
			else
			{
				$suma = 0;
			}
			return view('productos.ver_dif',compact('datos','id','suma'));
		}
    }

	public function ver_productos_con_diferencias_positivas($id)
    {
        $historial              = new Historial();
        $historial->id_usuario  = Auth::user()->id;
        $historial->actividad   = 'Visualizo diferencias en inventario numero'.' '.$id;
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        $id = $id;//atrapa el ID del inventario
        $dato = Inventario::find($id);//
        $cod = $dato->sucursal;
        $datos = DB::select('call inventario_web_encabezado_completo(?)',array($id));//se obtienen los datos del encabezadod del inventario
        $ver_inv =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)
        ->groupby('categoria','id','cod_producto','nombre_corto','nombre_fiscal','existencia_teorica','no_encabezado',
        'created_at','updated_at','existencia_fisica','mal_estado','empresa')
        ->get();//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
        $o = DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_teorica','>=',1)->orwhere('no_encabezado',$id)
        ->where('existencia_teorica','<',0)
        ->count('id');//esta consulta permite traer todos los productos que seran mostrados en la vista, para poder realizar el inventario
        $ver =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)->get()->count('id');
        if($o <> 0)
        {
            $suma = ($ver / $o)*100;
        }
        else
        {
            $suma = 0;
        }
        if(Auth::user()->roles ==1)
        {
            return view('productos.ver_dif_mas',compact('ver_inv','datos','id','suma','cod'));
        }
        else
        {
            return view('panel.ver_dif_mas',compact('ver_inv','datos','id','suma','cod'));
        }
    }

    public function ver_productos_con_diferencias_negativas($id)
    {
        $historial              = new Historial();
        $historial->id_usuario  = Auth::user()->id;
        $historial->actividad   = 'Visualizo diferencias en inventario numero'.' '.$id;
        $historial->created_at  = Carbon::now();
        $historial->updated_at  = Carbon::now();
        $historial->save();
        $id = $id;//atrapa el ID del inventario
        $dato = Inventario::find($id);//
        $cod = $dato->sucursal;
        $datos = DB::select('call inventario_web_encabezado_completo(?)',array($id));//se obtienen los datos del encabezadod del inventario
        $ver_inv =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)
        ->groupby('categoria','id','cod_producto','nombre_corto','nombre_fiscal','existencia_teorica','no_encabezado','created_at',
        'updated_at','existencia_fisica','mal_estado','empresa')
        ->get();//recupera todos los productos que fueron operados dentro del inventario, los que no tuvieron cambio no son devueltos en la consulta
        $o = DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_teorica','>=',1)
        ->orwhere('no_encabezado',$id)->where('existencia_teorica','<',0)
        ->count('id');//esta consulta permite traer todos los productos que seran mostrados en la vista, para poder realizar el inventario
        $ver =  DB::table('inventario_web')->where('no_encabezado',$id)->where('existencia_fisica','!=',NULL)->get()->count('id');
        if($o <> 0)
        {
            $suma = ($ver / $o)*100;
        }
        else
        {
            $suma = 0;
        }
        if(Auth::user()->roles ==1)
        {
            return view('productos.ver_dif_menos',compact('ver_inv','datos','id','suma','cod'));
        }
        else
        {
            return view('panel.ver_dif_menos',compact('ver_inv','datos','id','suma','cod'));
        }
    }

	public function ingresar_datos_grafica()
	{
		$actual = Carbon::now();
		$pasado = Carbon::now();
		$atras = $actual->subDay($actual->day);
		$menos = $pasado->subMonths(3);
		$mes = $menos->subDay($pasado->day - 1);
        $sucursales = [''=>'Seleccione una Sucursal'] + DB::table("unidades")->where('empresa',1)
        ->pluck("nombre","cod_unidad")->all();//consulta que trae todas las sucursales que pertenecen a la empresa 1
        $cate = DB::table("inventario_web_productos_semana")->groupBy('cod_tipo_prod','id','semana','empresa','cod_producto',
        'nombre_fiscal','created_at','updated_at',
		'nombre_corto','existencia_teorica')->orderBy('cod_tipo_prod','asc')->get();
		$marcas = DB::table("Marcas")->where('Empresa',1)->orderBy('Marca','asc')->get();

		return view('reportes.ingre_grafica',compact('sucursales','atras','mes','cate','marcas'));
	}

	public function grafica(Request $request)
	{
		foreach($request->tag_list as $key => $value)
		{
			$data = array($dato = $request->tag_list[$key]);
			$producto = ProductoSemana::where('id',$dato)->first();
		}
		$pro = $producto->nombre_fiscal;
		$datos = DB::select('call inventario_web_grafica(?,?,?,?,?)',array($request->cod_unidad,$request->bodega,$producto->cod_producto,$request->mesu,$request->mesd));
		$greorden["chart"] = array("type" => "line");
		$min = [];
		$max = [];
		$existencia = [];
		$dias = [];
		$reorden = [];
		$suc = '';
		foreach($datos as $d)
		{
			$min[] =  (floatval($d->min));
			$max[] =  (floatval($d->max));
			$existencia[] =  (floatval($d->existencia));
			$dias[] = [date('m-d-Y',strtotime($d->fecha))];
			$reorden[] = (floatval($d->reorden));
			$suc = [$d->sucursal.' '.$d->bodega];

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
		return view('reportes.grafica', compact('greorden','pro'));
	}

    public function listado_sucursales()
    {

        if(Auth::user()->roles == 4)
        {
            return view('panel.sucursales');
        }
        elseif(Auth::user()->roles == 1)
        {
            return view('productos.sucursales');
        }
    }

    public function categorias(Request $request)
    {
      $term = trim($request->q);
      if (empty($term)) {
          return \Response::json([]);
      }
      $tags = DB::table('productos_inve')->where('nombre_fiscal','like','%'. $term .'%')->where('empresa',1)
      ->orwhere('nombre_corto','like','%'. $term .'%')->where('empresa',1)->limit(10)->get();
      $formatted_tags = [];
      foreach ($tags as $tag) {
          $formatted_tags[] = ['id' => $tag->cod_producto, 'text' => $tag->nombre_corto.' '.$tag->nombre_fiscal];
      }
      return \Response::json($formatted_tags);
    }

//existencia_productos_categoria

    public function guardar_cambio_inventario(Request $request,$id)
    {
        $inventario = Inventario::findOrFail($id);
    }

    public function busqueda_max_min(Request $request)
    {
      $term = trim($request->q);
      if (empty($term)) {
          return \Response::json([]);
      }
      $tags = DB::table('tipos_prod')->where('nombre','like','%'. $term .'%')->where('empresa',1)->limit(10)->get();
      $formatted_tags = [];
      foreach ($tags as $tag) {
          $formatted_tags[] = ['id' => $tag->cod_tipo_prod, 'text' => $tag->nombre];
      }
      return \Response::json($formatted_tags);
    }

//----------------------------------------------- Funciones para editar la información de los camiones ------------------------------------------------------
    public function vista_agregar_camion()
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
        {
            //Muestra el formulario para agregar un nuevo camión dentro de la sucursal
            $pilotos = User::where('roles',5)->get();//Muestra los pilotos que están asinados a la misma sucursal
            $sucursales = User::where('roles',3)->get();
            return view('administracion.agregar_camion',compact('pilotos', 'sucursales'));
        }
        else
        {
            return redirect()->route('edi_cam_adm')->with('error','No tienes permisos para accesar');
        }
    }

    public function guardar_camion(Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
        {
            //Funcion para guardar datos del camion
            if($camiones = Camion::where('placa',$request->placa)->first())//Verifica si el número de placa ya está en uso.
            {
                return back()->with('error','El número de placa ya existe en el sistema');//Si existe redirigue al formulario mostrando el error.
            }
            elseif($fletes = Camion::where('id_piloto',$request->piloto)->first())//Verifica que un piloto solo pueda estar asignado a un camión.
            {
                return back()->with('error','Este usuario ya está asignado a otro camión');
            }
            else
            {
                $nuevo_camion               = new Camion();
                $nuevo_camion->marca        = $request->marca;
                $nuevo_camion->placa        = $request->placa;
                $nuevo_camion->tonelaje     = $request->tonelaje;
                $nuevo_camion->id_estado    = 21;
                $nuevo_camion->id_sucursal  = $request->sucursal;
                $nuevo_camion->tipo_camion  = $request->tipo;
                $nuevo_camion->espacio      = $request->volumen;
                $nuevo_camion->id_piloto    = $request->piloto;
                $nuevo_camion->created_at   = new Carbon();
                $nuevo_camion->updated_at   = new Carbon();
                if($nuevo_camion->save())
                {
                    $historial              = new Historial();
                    $historial->id_usuario  = Auth::id();
                    $historial->actividad   = 'Agrego un camión con placas número '. $request->placa;
                    $historial->created_at  = Carbon::now();
                    $historial->updated_at  = Carbon::now();
                    $historial->save();
                }
                return redirect()->route('edi_cam_adm')->with('success','El nuevo camión se agrego correctamente');
            }
        }
        else
        {
            return redirect()->route('edi_cam_adm')->with('error','No tienes permisos para accesar');
        }
    }
    public function editar_camion_administracion()
    {
        $edit_camiones = DB::select('select c.marca, c.placa, c.tonelaje, c.tipo_camion, e.nombre, c.id, u.name
        from inventario_web_camiones as c,
        inventario_web_estados as e,
        users as u
        where c.id_estado = e.id
        and c.id_sucursal = u.id
        and c.id_estado in (21,22)');
        return view('administracion.camion' ,compact('edit_camiones'));
    }
    public function vista_editar_camion($id)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',27)->first())
        {
            $camion = DB::select("select iwc.id, iwc.marca, iwc.placa, iwc.tonelaje, iwc.id_sucursal, iwc.tipo_camion,
            iwe.nombre, iwe.id as id_estado, iwc.espacio, u.name
            from inventario_web_camiones as iwc,
            inventario_web_estados as iwe,
            users as u
            where iwc.id = :id
            and iwc.id_estado = iwe.id
            and iwc.id_sucursal = u.id",['id'=>$id]);
            foreach($camion as $c)
            {
                $estado = $c->id_estado;
                $usuario = $c->id_sucursal;
            }
            $sucursales = User::where('roles',3)->where('id','!=',$usuario)->get();
            $estados = Estado::where('id','!=',$estado)->whereIn('id',[21,22])->get();//Cambia el estado de un camión
            return view('administracion.editar_camion',compact('camion','estados','id','sucursales'));
        }
        else
        {
            return redirect()->route('edi_cam_adm')->with('error','No tienes permisos para accesar');
        }
    }

    public function editar_camion($id,Request $request)
    {
        if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',6)->first())
        {
            $editar=camion::findOrfail($id);
            $editar->marca        = $request->marca;
            $editar->placa        = $request->placa;
            $editar->tonelaje     = $request->tonelaje;
            $editar->id_estado    = $request->estado;
            $editar->tipo_camion  = $request->tipo;
            $editar->espacio      = $request->volumen;
            $editar->id_sucursal  = $request->sucursal;
            if($editar->save())
            {
                $historial              = new Historial();
                $historial->id_usuario  = Auth::id();
                $historial->actividad   = 'Agrego un camión con placas número '. $request->placa;
                $historial->created_at  = Carbon::now();
                $historial->updated_at  = Carbon::now();
                $historial->save();
            }
            return redirect()->route('edi_cam_adm')->with('success','Cambios guardados correctamente');
        }
        else
        {
            return redirect()->route('edi_cam_adm')->with('error','No tienes permisos para accesar');
        }
    }
}

