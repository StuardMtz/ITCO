<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventario;
use Auth;
use DB;
use App\Productos;
use App\Http\Requests\InventarioRequest;
use App\InventarioGeneral;
use App\ProductoSemana;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\InventarioFinalizado;
use App\Historial;

class Crud_InventarioController extends Controller
{
  //nuevo_inventario
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->user = Auth::user();
      return $next($request);
    });
  }

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
  public function actualizar($id)
  {
    /*Agrega */
    $datos = Inventario::find($id);
    $todos_productos = DB::select('call inventario_web_productos_mostrar(?,?)',array($datos->sucursal,$datos->bodega));
    foreach ($todos_productos as $actu) 
    {
      $su = InventarioGeneral::where('no_encabezado',$id)->where('existencia_fisica',NULL)->where('cod_producto',$actu->cod_producto)
      ->where('existencia_teorica',NULL)->update(['existencia_teorica'=>$actu->existencia]);
      /*foreach ($su as $su) {  
      $todos_productos = DB::select('call inventario_web_producto_existencia(?,?,?)',array($datos->sucursal,$datos->bodega,$su->cod_producto));
      $su->existencia_teorica = $actu->existencia;
      $su->update();
      }*/
    }
    return back();
  }

  public function conv_utf()
  {
   	return back();
  }

  public function agregar_faltante(Request $request,$id)
  {
    $producto = DB::select('select cod_producto, pi.empresa, nombre_corto, nombre_fiscal, iwc.nombre 
    from productos_inve as pi,
    inventario_web_categorias as iwc
    where cod_producto = :cod
    and pi.cod_tipo_prod = iwc.cod_tipo_prod
    and pi.empresa = 1
    and iwc.empresa = 1',['cod'=>$request->producto]);
    $existe = DB::select('select count(*) as total
    from inventario_web
    where cod_producto = :co
    and no_encabezado = :id',['co'=>$request->producto,'id'=>$id]);
    foreach($existe as $ex)
    {
      $x = $ex->total;
    }
    if($x == 0)
    {
      foreach($producto as $pr)
      {
        $nuevin                     = new InventarioGeneral();
        $nuevin->cod_producto       = $pr->cod_producto;
        $nuevin->nombre_corto       = $pr->nombre_corto;
        $nuevin->nombre_fiscal      = $pr->nombre_fiscal;
        $nuevin->existencia_teorica = 0;
        $nuevin->categoria          = $pr->nombre;
        $nuevin->no_encabezado      = $id;
        $nuevin->created_at         = Carbon::now();
        $nuevin->updated_at         = Carbon::now();
        $nuevin->empresa            = Auth::user()->empresa;
        $nuevin->save();
      }
      return back()->with('success','¡Producto agregado con exito!');
    }
    else
    {
      return back()->with('error','¡El producto ya está agregado en este inventario');
    }
  }
}
