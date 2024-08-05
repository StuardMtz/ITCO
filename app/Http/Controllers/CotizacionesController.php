<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use DB;
use PDF;
use Auth;
use App\User;
use App\Cotizacion;
use App\Historial;
use App\DetCotizacion;
use App\MoviInve;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class CotizacionesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->user = Auth::user();
      return $next($request);
    });
  }
//imprimir_cotizacion_pdf
//--------------------------- Funciones para ver las cotizaciones creadas por un usuario ------------------------------------------------------------
  function inicio()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[40,41])->first())
    {
      $sucursales = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad','!=',15)->get();
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Ingreso al modulo de cotizaciones';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      return view('cotizaciones.inicio',compact('sucursales'));
    }
    return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
  }

  function datos_inicio()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',41)->first())
    {
      $cotizaciones = DB::select("select cot.num_movi, cot.fecha, cot.Nombre_cliente+' '+'('+convert(varchar(1000),cot.cod_cliente)+')' as Nombre_cliente, cot.cod_cliente,
      cot.referencia, sum(dcot.total) as total, sum(dcot.skd) as sku , c.nit, us.name
      from cotizaciones as cot
      join clientes as c on cot.cod_cliente = c.cod_cliente
      join users as us on us.id = cot.id_usuario
      left join (select dc.num_movi, (dc.cantidad * dc.precio)-((dc.cantidad * dc.precio) * (coalesce(floor(dc.descuento),0)/100)) as total,
      count(*) as skd
      from detalle_Cotizaciones as dc
      where dc.empresa = :dempresa
      and dc.cod_Serie_movi = :dserie
      and dc.cod_tipo_movi = 'O'
      group by dc.cantidad, dc.precio, dc.descuento, dc.num_movi) as dcot on cot.num_movi = dcot.num_movi
      where cot.empresa = :empresa
      and cot.cod_Serie_movi = :serie
      and cot.fecha > :fecha
      and c.empresa = cot.empresa
      and cot.cod_tipo_movi = 'O'
      group by cot.num_movi, cot.fecha, cot.Nombre_cliente, cot.cod_cliente, cot.referencia, c.nit, us.name",['empresa'=>Auth::user()->empresa,
      'serie'=>Auth::user()->serie_cotizacion,'fecha'=>Carbon::now()->subMonths(3),'dempresa'=>Auth::user()->empresa,'dserie'=>Auth::user()->serie_cotizacion]);
      return DataTables($cotizaciones)->make(true);
    }
    elseif($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $cotizaciones = DB::select("select cot.num_movi, cot.fecha, cot.Nombre_cliente+' '+'('+convert(varchar(1000),cot.cod_cliente)+')' as Nombre_cliente, cot.cod_cliente,
      cot.referencia, sum(dcot.total) as total, sum(dcot.skd) as sku , c.nit, us.name
      from cotizaciones as cot
      join clientes as c on cot.cod_cliente = c.cod_cliente
      join users as us on us.id = cot.id_usuario
      left join (select dc.num_movi, (dc.cantidad * dc.precio)-((dc.cantidad * dc.precio) * (coalesce(floor(dc.descuento),0)/100)) as total,
      count(*) as skd, dc.cod_Serie_movi, dc.cod_tipo_movi
      from detalle_Cotizaciones as dc
      where dc.empresa = :dempresa
      and dc.cod_Serie_movi = :dserie
      and dc.cod_tipo_movi = 'O'
      group by dc.cantidad, dc.precio, dc.descuento, dc.num_movi, dc.cod_Serie_movi, dc.cod_tipo_movi) as dcot on cot.num_movi = dcot.num_movi
      where cot.empresa = :empresa
      and cot.id_usuario = :codigo
      and cot.cod_Serie_movi = dcot.cod_Serie_movi
      and cot.fecha > :fecha
      and c.empresa = cot.empresa
      and cot.cod_tipo_movi = dcot.cod_tipo_movi
      group by cot.num_movi, cot.fecha, cot.Nombre_cliente, cot.cod_cliente, cot.referencia, c.nit, us.name",['empresa'=>Auth::user()->empresa,'codigo'=>Auth::id(),
      'fecha'=>Carbon::now()->subMonths(3),'dempresa'=>Auth::user()->empresa,'dserie'=>Auth::user()->serie_cotizacion]);
      return DataTables($cotizaciones)->make(true);
    }
  }

  function codigo_cliente(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $term = trim($request->q);
      if (empty($term)) {
        return \Response::json([]);
      }
      $tags = DB::table('clientes')->where('cod_cliente','like','%'. $term .'%')->orwhere('nombre','like','%'. $term .'%')
      ->orwhere('nit','like','%'. $term.'%')->limit(10)->get();
      $formatted_tags = [];
      foreach ($tags as $tag) {
        $formatted_tags[] = ['id' => $tag->cod_cliente, 'text' => $tag->cod_cliente.' - '.utf8_encode($tag->nombre).' - '.$tag->nit];
      }
      return \Response::json($formatted_tags);
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para crear una nueva cotización para clientes ---------------------------------------------------------------------------
  function crear_nueva_cotizacion(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $ultima = Cotizacion::where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','O')
      ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
      ->select('num_movi')
      ->orderby('num_movi','desc')->first();
      $cambio = DB::table('cambiodeldia')->where('empresa',Auth::user()->empresa)->orderby('dia','desc')->first();
      $cliente = DB::table('clientes')->where('empresa',Auth::user()->empresa)->where('cod_cliente',$request->cod_cliente)->first();
      $validator = Validator::make($request->all(),[
        'cod_cliente'=>'required|numeric',
        'sucursal'=>'required',
      ]);
      if ($validator->fails()) {
        return back()
        ->withErrors($validator)
        ->withInput();
      }
      $cotizacion                   = new Cotizacion();
      $cotizacion->empresa          = Auth::user()->empresa;
      $cotizacion->cod_tipo_movi    = 'O';
      $cotizacion->cod_Serie_movi   = Auth::user()->serie_cotizacion;
      if($ultima == '')
      {
        $cotizacion->num_movi = 1;
      }
      else
      {
        $cotizacion->num_movi         = $ultima->num_movi + 1;
      }
      $cotizacion->id_usuario       = Auth::user()->id;
      $cotizacion->cod_cliente      = $request->cod_cliente;
      $cotizacion->cod_vendedor     = Auth::user()->codigo_vendedor;
      $cotizacion->Nombre_cliente   = utf8_encode($cliente->nombre);
      $cotizacion->fecha            = Carbon::now();
      $cotizacion->vigencia         = 15;
      $cotizacion->unidad_vigencia  = 'D';
      $cotizacion->quetzales_por_dolar = $cambio->quetzales_x_dolar;
      $cotizacion->moneda           = 'L';
      $cotizacion->cod_unidad       = $request->sucursal;
      $cotizacion->save();
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Creo una nueva cotización en el modulo de cotizaciones';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      if($ultima == '')
      {
        return redirect()->route('editar_cotizacion',['id'=>1])->with('success','¡Se a generado la cotización correctamente');
      }
      return redirect()->route('editar_cotizacion',['id'=>$ultima->num_movi+1])->with('success','¡Se a generado la cotización correctamente');
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funcion para editar una cotización a un cliente ---------------------------------------------------------------------------------
  function editar_cotizacion($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $sucursales = DB::table('unidades')->where('empresa',Auth::user()->empresa)->where('cod_unidad','!=',15)->get();
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Ingreso a la vista de edición de cotizaciones en el modulo cotizaciones';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      return view('cotizaciones.editar_cotizacion',compact('id','sucursales'));
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function listado_de_productos() 
  {
    $producto = DB::select("select cod_producto, nombre_corto, nombre_fiscal
    from productos_inve 
    where empresa = :empresa 
    and precio <> 0
    and descontinuado = 'N'
    and cod_tipo_prod != 'SUMINISTROS'
    and cod_tipo_prod != 'SER01'
    and cod_tipo_prod != 'FLETE'
    and cod_tipo_prod != 'Lamina Cintas'
    and Marca is not null
    order by nombre_corto asc",['empresa'=>Auth::user()->empresa]);
    foreach ($producto as $tag) {
      $productos[] = ['id' => $tag->cod_producto, 'nombre_corto' => utf8_encode($tag->nombre_corto), 'nombre_fiscal' => utf8_encode($tag->nombre_fiscal)];
    }
    return $productos;
  }

  function encabezado_cotizacion_edicion($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $encabezado = DB::select("select co.descripcion1, co.quetzales_por_dolar, co.cod_cliente, co.Nombre_cliente, co.num_movi, co.descripcion2,
      co.fecha, co.referencia, coalesce(cli.saldo,0) as saldo, co.cod_Serie_movi, co.cod_tipo_movi, u.nombre as sucursal
      from cotizaciones as co,
      clientes as cli,
      unidades as u
      where co.empresa = :empresa
      and co.cod_Serie_movi = :serie
      and co.num_movi = :num_movi
      and cli.cod_cliente = co.cod_cliente
      and cli.empresa = co.empresa
      and u.empresa = co.empresa
      and u.cod_unidad = co.cod_unidad",['empresa'=>Auth::user()->empresa,'serie'=>Auth::user()->serie_cotizacion,'num_movi'=>$id]);
      return $encabezado;
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function form_encabezado_cotizacion(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $encabezado = DB::select("select co.descripcion1, co.quetzales_por_dolar, co.cod_cliente, co.Nombre_cliente, co.num_movi, co.descripcion2,
      co.fecha, co.referencia, coalesce(cli.saldo,0) as saldo, co.cod_Serie_movi, co.cod_tipo_movi, u.nombre as sucursal, u.cod_unidad
      from cotizaciones as co,
      clientes as cli,
      unidades as u
      where co.empresa = :empresa
      and co.cod_Serie_movi = :serie
      and co.num_movi = :num_movi
      and cli.cod_cliente = co.cod_cliente
      and cli.empresa = co.empresa
      and u.empresa = co.empresa
      and u.cod_unidad = co.cod_unidad",['empresa'=>Auth::user()->empresa,'serie'=>Auth::user()->serie_cotizacion,
      'num_movi'=>$request->num_movi]);
      $datos_enca[]= '';
      foreach($encabezado as $ec)
      {
        $datos_enca = ['nombre_cliente'=>$ec->Nombre_cliente,'descripcion'=>$ec->descripcion1,'descripcion2'=>$ec->descripcion2,
        'referencia'=>$ec->referencia,'sucursal'=>$ec->sucursal,'cod_unidad'=>$ec->cod_unidad];
      }
      return $datos_enca;
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function sucursales_form()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $sucursales = DB::select("select cod_unidad, nombre
      from unidades
      where empresa = :empresa
      and cod_unidad <> 2",['empresa'=>Auth::user()->empresa]);
      return $sucursales;
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function guardar_cambios_encabezado(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      if(is_numeric($request->sucursal))
      {
        $guardar = DB::table('cotizaciones')->where('empresa',Auth::user()->empresa)->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
        ->where('num_movi',$request->num_movi)->update(['Nombre_cliente'=>$request->nombre_cliente,'descripcion1'=>$request->descripcion,
        'descripcion2'=>$request->observacion,'referencia'=>$request->referencia,'cod_unidad'=>$request->sucursal]);
        $historial              = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Modifico el encabezado de una cotización en el modulo de cotizaciones';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        return $guardar;
      }
      else
      {
        $guardar = DB::table('cotizaciones')->where('empresa',Auth::user()->empresa)->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
        ->where('num_movi',$request->num_movi)->update(['Nombre_cliente'=>$request->nombre_cliente,'descripcion1'=>$request->descripcion,
        'descripcion2'=>$request->observacion,'referencia'=>$request->referencia]);
        $historial              = new Historial();
        $historial->id_usuario  = Auth::id();
        $historial->actividad   = 'Modifico el encabezado de una cotización en el modulo de cotizaciones';
        $historial->created_at  = new Carbon();
        $historial->updated_at  = new Carbon();
        $historial->save();
        return $guardar;
      }
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function agregar_producto_cotizacion(Request $request,$id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $orden = DB::table('detalle_Cotizaciones')->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','O')
      ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)->where('num_movi',$id)->orderby('orden','desc')->first();
      $existe = DB::table('detalle_Cotizaciones')->where('empresa',Auth::user()->empresa)->where('cod_tipo_movi','O')
      ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)->where('num_movi',$id)->where('cod_producto',$request->producto)
      ->orderby('orden','desc')->first();
      $siguiente = 0;
      if($orden != '')
      {
        $siguiente = $orden->orden + 1;
      }
      else
      {
        $siguiente = 1;
      }
      $listas = DB::select('select cod_lista
      from cotizaciones as co
      join clientes as cl on co.cod_cliente = cl.cod_cliente
      where co.empresa = :empresa
      and co.cod_tipo_movi = :tipo
      and co.cod_Serie_movi = :serie
      and co.num_movi = :num_movi
      and co.empresa = cl.empresa',['empresa'=>Auth::user()->empresa,'tipo'=>'O','serie'=>Auth::user()->serie_cotizacion,'num_movi'=>$id]);
      $lista = 0;
      foreach($listas as $li)
      {
        $lista = $li->cod_lista;
      }
      $producto = DB::table('productos_inve')->where('empresa',Auth::user()->empresa)->where('cod_producto',$request->producto)->first();
      if($existe == true)
      {
        return back()->with('error','No es posible duplicar productos en cotización');
      }
      else
      {
        if($lista == '')
        {
          $agregar                    = new DetCotizacion();
          $agregar->empresa           = Auth::user()->empresa;
          $agregar->cod_tipo_movi     = 'O';
          $agregar->cod_Serie_movi    = Auth::user()->serie_cotizacion;
          $agregar->num_movi          = $id;
          $agregar->Moneda            = $producto->moneda;
          $agregar->precio            = $producto->precio;
          $agregar->cod_producto      = $request->producto;
          $agregar->orden             = $siguiente;
          $agregar->cantidad          = $request->cantidad_enviar;
          $agregar->descuento         = 0;
          $agregar->save();
          return $agregar;
        }
        else
        {
          $descuento = DB::table('Det_lista_precios')->where('Cod_producto',$request->producto)->where('Cod_lista',$lista)
          ->where('empresa',Auth::user()->empresa)->first();
          $precio_insertar = 0;
          if($descuento == '')
          {
            $agregar                    = new DetCotizacion();
            $agregar->empresa           = Auth::user()->empresa;
            $agregar->cod_tipo_movi     = 'O';
            $agregar->cod_Serie_movi    = Auth::user()->serie_cotizacion;
            $agregar->num_movi          = $id;
            $agregar->Moneda            = $producto->moneda;
            $agregar->precio            = $producto->precio;
            $agregar->cod_producto      = $request->producto;
            $agregar->orden             = $siguiente;
            $agregar->cantidad          = $request->cantidad_enviar;
            $agregar->descuento         = 0;
            $agregar->save();
            return $agregar;
          }
          else
          {
            $agregar                    = new DetCotizacion();
            $agregar->empresa           = Auth::user()->empresa;
            $agregar->cod_tipo_movi     = 'O';
            $agregar->cod_Serie_movi    = Auth::user()->serie_cotizacion;
            $agregar->num_movi          = $id;
            $agregar->Moneda            = $producto->moneda;
            $agregar->precio            = $descuento->Precio;
            $agregar->cod_producto      = $request->producto;
            $agregar->orden             = $siguiente;
            $agregar->cantidad          = $request->cantidad_enviar;
            $agregar->descuento         = 0;
            $agregar->save();
            return agregar;
          }
        }
      }
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function productos_en_cotizacion($id)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $encabezado = Cotizacion::where('num_movi',$id)->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
      ->where('cod_tipo_movi','O')->where('empresa',Auth::user()->empresa)->first();
      $productos = DB::select('select ROW_NUMBER() OVER (ORDER BY orden asc) as orden, dc.Moneda, coalesce(dc.cantidad,0) as cantidades, dc.precio, (i.tasa / 100) as iva,
      coalesce(floor(dc.descuento),0) as descuento, dc.cod_producto, coalesce(dc.descuento,0) as tdescuento, pi.nombre_corto, pi.nombre_fiscal,
      (cantidad * dc.precio)-((cantidad * dc.precio) * (tdescuento/100)) as subtotal, str(subtotal,20,2) as total, floor(cantidades) as cantidad,
      dc.num_movi, co.quetzales_por_dolar as cambio, ((descuento/100) * dc.precio) as monto_descuento, pi.precio as precio_normal, coalesce(lista.Precio,0) as lis_precio
      from detalle_Cotizaciones as dc
      join productos_inve as pi on dc.cod_producto = pi.cod_producto
      join cotizaciones as co on dc.num_movi = co.num_movi
      join iva as i on i.cod_iva = 1
      left join (select dlp.Precio, dlp.Cod_producto
      from clientes as cli,
      lista_precios as lp,
      Det_lista_precios as dlp
      where cli.cod_cliente = :cliente
      and lp.Cod_Lista = dlp.Cod_lista
      and lp.empresa = :empresa
      and lp.empresa = dlp.empresa
      and lp.empresa = cli.empresa
      and cli.cod_lista = lp.Cod_Lista) as lista on dc.cod_producto = lista.cod_producto
      where dc.num_movi = :num_movi
      and dc.cod_Serie_movi = :serie
      and dc.cod_tipo_movi = :tipo
      and pi.empresa = co.empresa
      and pi.empresa = dc.empresa
      and dc.cod_Serie_movi = co.cod_Serie_movi
      and dc.empresa = co.empresa
      order by orden asc',['num_movi'=>$id,'serie'=>$encabezado->cod_Serie_movi,'tipo'=>$encabezado->cod_tipo_movi,
      'empresa'=>Auth::user()->empresa,'cliente'=>$encabezado->cod_cliente]);
      return $productos;
    }
      return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function datos_producto_editar(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $encabezado = Cotizacion::where('num_movi',$request->num_movi)->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
      ->where('cod_tipo_movi','O')->where('empresa',Auth::user()->empresa)->first();
      $producto = DB::select('select dc.Moneda, coalesce(dc.cantidad,0) as cantidades, str(dc.precio,20,2) as precio, coalesce(floor(dc.descuento),0) as descuento,
      dc.cod_producto, dc.iva, coalesce(dc.descuento,1) as tdescuento, pi.nombre_corto, pi.nombre_fiscal, dc.orden, floor(pi.precio_minimo) as minimo,
      (dc.precio * cantidad) as subtotal, floor(subtotal) as total, floor(cantidades) as cantidad, floor(i.existencia1) as existencia,
      ((descuento/100) * dc.precio) as monto_descuento
      from detalle_Cotizaciones as dc
      join productos_inve as pi on dc.cod_producto = pi.cod_producto
      left join inventarios as i on pi.cod_producto = i.cod_producto
      where dc.num_movi = :num_movi
      and dc.cod_Serie_movi = :serie
      and dc.cod_tipo_movi = :tipo
      and pi.empresa = :empresa
      and pi.empresa = dc.empresa
      and pi.empresa = i.empresa
      and i.cod_unidad = :unidad
      and pi.cod_producto = :cod_producto
      and i.cod_bodega = 1',['num_movi'=>$request->num_movi,'serie'=>$encabezado->cod_Serie_movi,'tipo'=>$encabezado->cod_tipo_movi,
      'empresa'=>Auth::user()->empresa,'cod_producto'=>$request->cod_producto,'unidad'=>$encabezado->cod_unidad]);
      $datos_prod[]='';
      foreach($producto as $pr)
      {
        $datos_prod = ['nombre_corto'=>$pr->nombre_corto,'nombre_fiscal'=>$pr->nombre_fiscal,'cantidad'=>$pr->cantidad,
        'precio'=>floatval($pr->precio),'descuento'=>$pr->descuento,'existencia'=>$pr->existencia,'precio_minimo'=>$pr->minimo,
        'monto_descuento'=>number_format($pr->monto_descuento,2),'moneda'=>$pr->Moneda];
      }
      return $datos_prod;
    }
      return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function guardar_datos_producto_cotizacion(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      if($request->descuento == null)
      {
        $guardar = DB::table('detalle_Cotizaciones')->where('empresa',Auth::user()->empresa)
        ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
        ->where('num_movi',$request->num_movi)->where('cod_producto',$request->cod_producto)
        ->update(['cantidad'=>$request->cantidad,'precio'=>$request->precio,'descuento'=>0]);
        return $guardar;
      }
      if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[46,49])->first()){
        $guardar = DB::table('detalle_Cotizaciones')->where('empresa',Auth::user()->empresa)
      ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
      ->where('num_movi',$request->num_movi)->where('cod_producto',$request->cod_producto)
      ->update(['cantidad'=>$request->cantidad,'precio'=>$request->precio,'descuento'=>$request->descuento]);
      return $guardar;
      }
      $guardar = DB::table('detalle_Cotizaciones')->where('empresa',Auth::user()->empresa)
      ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
      ->where('num_movi',$request->num_movi)->where('cod_producto',$request->cod_producto)
      ->update(['cantidad'=>$request->cantidad]);
      return $guardar;
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function eliminar_producto_cotizacion($cod_producto,$num_movi)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $eliminar = DB::table('detalle_Cotizaciones')->where('empresa',Auth::user()->empresa)
      ->where('cod_Serie_movi',Auth::user()->serie_cotizacion)
      ->where('num_movi',$num_movi)->where('cod_producto',$cod_producto)->delete();
      return $eliminar;
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }

  function buscar_producto_manual(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $term = trim($request->q);
      if (empty($term)) {
        return \Response::json([]);
      }
      $tags = DB::table('productos_inve')->where('empresa',Auth::user()->empresa)
      ->where('descontinuado','N')->orderby('nombre_corto','asc')->get();
      $formatted_tags = [];
      foreach ($tags as $tag) {
        $formatted_tags[] = ['id' => $tag->cod_producto, 'text' => utf8_encode($tag->nombre_corto).' '.utf8_encode($tag->nombre_fiscal)];
      }
      return \Response::json($formatted_tags);
    }
    return redirect()->route('inicio_transferencias')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Función para imprimir en PDF una cotización para clientes -----------------------------------------------------------------------
  function imprimir_cotizacion_pdf($id,$tipo_im)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',40)->first())
    {
      $iva = DB::table('iva')->where('cod_iva',1)->first();
      $encabezado = DB::select("select co.descripcion1, co.quetzales_por_dolar, co.cod_cliente, co.Nombre_cliente, co.num_movi, co.descripcion2,
      co.fecha, co.referencia, cli.saldo, co.cod_Serie_movi, co.cod_tipo_movi, co.empresa, u.telefono, u.direccion, v.nombre, v.direccion as correo, u.nombre as sucursal
      from cotizaciones as co,
      clientes as cli,
      unidades as u,
      vendedores as v
      where co.empresa = :empresa
      and co.cod_cliente = cli.cod_cliente
      and co.cod_unidad = u.cod_unidad
      and co.cod_vendedor = v.cod_vendedor
      and co.cod_Serie_movi = :serie
      and co.num_movi = :num_movi
      and cli.cod_cliente = co.cod_cliente
      and u.empresa = co.empresa
      and v.empresa = co.empresa
      and cli.empresa = co.empresa",['empresa'=>Auth::user()->empresa,'serie'=>Auth::user()->serie_cotizacion,'num_movi'=>$id]);
      foreach ($encabezado as $enca)
      {
        $serie = $enca->cod_Serie_movi;
        $tipo = $enca->cod_tipo_movi;
        $dolar = $enca->quetzales_por_dolar;
        $numero = $enca->num_movi;
        $cliente = $enca->cod_cliente;
      }
      $productos = DB::select('select ROW_NUMBER() OVER (ORDER BY orden asc) as orden, dc.Moneda, coalesce(dc.cantidad,0) as cantidades, dc.precio,
      (monto_descuento * dc.cantidad) as sub_total, coalesce(floor(dc.descuento),0) as descuento, dc.cod_producto, dc.iva, coalesce(dc.descuento,0) as tdescuento,
      pi.nombre_corto, pi.nombre_fiscal, (cantidad * dc.precio) as subtotal, subtotal as total, floor(cantidades) as cantidad,
      dc.num_movi, co.quetzales_por_dolar as cambio, ((descuento/100) * dc.precio) as monto_descuento, pi.precio as precio_normal, coalesce(lista.Precio,0) as lis_precio
      from detalle_Cotizaciones as dc
      join productos_inve as pi on dc.cod_producto = pi.cod_producto
      join cotizaciones as co on dc.num_movi = co.num_movi
      left join (select dlp.Precio, dlp.Cod_producto
      from clientes as cli,
      lista_precios as lp,
      Det_lista_precios as dlp
      where cli.cod_cliente = :cliente
      and lp.Cod_Lista = dlp.Cod_lista
      and lp.empresa = :empresa
      and lp.empresa = dlp.empresa
      and lp.empresa = cli.empresa
      and cli.cod_lista = lp.Cod_Lista) as lista on dc.cod_producto = lista.cod_producto
      where dc.num_movi = :num_movi
      and dc.cod_Serie_movi = :serie
      and dc.cod_tipo_movi = :tipo
      and pi.empresa = co.empresa
      and pi.empresa = dc.empresa
      and dc.cod_Serie_movi = co.cod_Serie_movi
      and dc.empresa = co.empresa
      order by orden asc',['num_movi'=>$id,'serie'=>$serie,'tipo'=>$tipo,'empresa'=>Auth::user()->empresa,'cliente'=>$cliente]);
      $permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->whereIn('id_permiso',[46,47,48,49])->get();
      $edicion = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',46)->get();
      //return $permiso;
      if($tipo_im == 1)
      {
        $pdf = PDF::loadView('cotizaciones.imprimirCotizacion',compact('encabezado','productos','dolar','iva', 'permiso','edicion'));
      }
      else
      {
        $pdf = PDF::loadView('cotizaciones.imprimirCotizacionDolar',compact('encabezado','productos','dolar','iva'));
      }
      $historial              = new Historial();
      $historial->id_usuario  = Auth::id();
      $historial->actividad   = 'Imprimio una cotización en el modulo de cotizaciones';
      $historial->created_at  = new Carbon();
      $historial->updated_at  = new Carbon();
      $historial->save();
      return $pdf->download($numero.'-'.$serie.'-.pdf');
    }
    return redirect()->route('home')->with('error','No tienes permisos para accesar');
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para ver el listado general de cotizaciones generado por todos los usuarios -------------------------------------------
  function reporte_cotizacion()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',41)->first())
    {
      return view('cotizaciones.reporte');
    }
    return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
  }

  function datos_reporte_cotizacion()
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',41)->first())
    {
      $cotizaciones = DB::select('SELECT * FROM DBA.inventario_web_cotizaciones_reporte');
      return DataTables($cotizaciones)->make(true);
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------- Funciones para visualizar el listado general de cotizaciones filtrado por un rango de fechas ------------------------------------
  function reporte_cotizacion_fecha(Request $request)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',41)->first())
    {
      $inicio = $request -> inicio;
      $fin    = $request -> fin;
      return view('cotizaciones.reporte_fecha', compact('inicio', 'fin'));
    }
    return redirect()->route('home')->with('error','¡No tienes permiso para ingresar!');
  }

  function datos_reporte_cotizacion_fecha($inicio, $fin)
  {
    if($permiso = DB::table('inventario_web_permisos_usuario')->where('id_usuario',Auth::id())->where('id_permiso',41)->first())
    {
      $cotizaciones = DB::select('SELECT * FROM DBA.inventario_web_cotizaciones_reporte where fecha between :inicio and :fin',['inicio'=>$inicio,'fin'=>$fin]);
      return DataTables($cotizaciones)->make(true);
    }
  }
//-------------------------------------------------------------------------------------------------------------------------------------------------------------
}
