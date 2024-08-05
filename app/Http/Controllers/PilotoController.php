<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Estado;
use App\SolicitudEnvio;
use App\Ruta;
use Auth;
use DB;
use Carbon\Carbon;
use App\Bitacora;

class PilotoController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
      $this->user = Auth::user();
      return $next($request);
    });
  }
  //------------------------------------------------------ Actualmente se reeplazo por la aplicación para moviles ---------------------------------------------
  //----------------------------- Vista Inicio Pilotos --------------------------------------------------------------------------------------------------------
  public function inicio()
  {
    //Muestra las rutas pendientes por realizar que tiene el piloto
    $camion = DB::table('inventario_web_camiones')->where('id_piloto',Auth::id())->first();//Verifica que el usuario tenga asignado un camión
    $rutas  = Ruta::where('id_camion',$camion->id)->where('id_estado',1)->paginate(15);//Devuelve las rutas asignadas al camión
    return view('pilotos.inicio',compact('rutas'));
  }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////Vista Rutas Finalizadas /////////////////////////////////////////////
  public function vista_rutas_finalizadas()
  {
    $camion = DB::table('inventario_web_camiones')->where('id_piloto',Auth::id())->first();//Verifica que el usuario tenga asignado un camión
    $rutas  = Ruta::where('id_camion',$camion->id)->where('id_estado',8)->paginate(15);//Devuelve las rutas marcadas como finalizadas por el piloto
    return view('pilotos.finalizadas',compact('rutas'));
  }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  /////////////////////////////////////////////////////////////////////////////////Vista editar entregas //////////////////////////////////////////////
  public function vista_editar_entregas($id)
  {
    //Muestra las entregas contenidas dentro de una ruta
    $entregas = SolicitudEnvio::where('id_ruta',$id)->where('id_estado','!=',8)->get();
    return view('pilotos.editar_ruta',compact('entregas'));
  }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////Vista Editar Entrega dentro de Ruta /////////////////////////////////
  public function vista_editar_entrega($id,Request $request)
  {
    //Muestra el formulario para modificar una entrega
    $entrega = SolicitudEnvio::find($id);
    $estados = $entrega->id_ruta;
    return view('pilotos.editar_entrega',compact('entrega','estados'));
  }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////Editar datos de entrega y crear bitacora de entrega /////////////////
  public function editar_entrega($id, Request $request)
  {
    if($no = SolicitudEnvio::where('id',$id)->where('id_estado',8)->first())//Verifica que la entrega no haya sido finalizada
    {
      //Si la entrega fue marcada como finalizada, ya no se permiten las modificaciones
      return redirect()->route('v_ed_entrega',['id'=>$id])->with('error','No se permite realizar cambios');
    }
    else
    {
      $fecha = new Carbon();
      $editar = SolicitudEnvio::FindOrFail($id);
      if($request->estado == 2)//Si el estado enviado es 2, la entrega no sufre mayor cambio
      {
        $editar->id_estado  = $request->estado;
        $editar->updated_at = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $editar->id_estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        $bitacora->id_sucursal= Auth::user()->sucursal;
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 3)
      {
        //Si el estado enviado es 3, se actualiza la fecha de carga entro de la entrega
        $editar->fecha_carga  = $fecha;
        $editar->id_estado    = $request->estado;
        $editar->updated_at   = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 4)
      {
        //Si el estado enviado es 4, se acualiza la fecha de carga dentro de la entrega
        $editar->fecha_parqueo  = $fecha;
        $editar->id_estado      = $request->estado;
        $editar->updated_at     = $updated_at;
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 5)
      {
        //Si el estado enviado es 5, se actualiza la fecha de ruta entro de la entrega
        $editar->fecha_ruta = $fecha;
        $editar->id_estado  = $request->estado;
        $editar->updated_at = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 6)
      {
        //Si el estado enviado es 6, se acutaliza la fecha de destino dentro de la entrega
        $editar->fecha_destino  = $fecha;
        $editar->id_estado      = $request->estado;
        $editar->updated_at     = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 7)
      {
        //Si el estado enviado es 7, se actualiza la fecha de descarga dentro de la entrega
        $editar->fecha_descarga = $fecha;
        $editar->id_estado      = $request->estado;
        $editar->updated_at     = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 8)
      {
        //Si el estado enviado es 8, se actualiza la fecha de entrega dentro de la entrega :v
        $editar->fecha_entregado  = $fecha;
        $editar->id_estado        = $request->estado;
        $editar->longitud         = $request->longitud;
        $editar->latitud          = $request->latitud;
        $editar->updated_at       = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
      elseif($request->estado == 9)
      {
        //El estado nueve es para cancelar un envío
        $editar->id_estado  = $request->estado;
        $editar->updated_at = Carbon::now();
        $editar->save();
        $bitacora             = new Bitacora();
        $bitacora->id_entrega = $id;
        $bitacora->id_estado  = $request->estado;
        $bitacora->comentario = $request->comentario;
        $bitacora->longitud   = $request->longitud;
        $bitacora->latitud    = $request->latitud;
        $bitacora->id_sucursal= Auth::user()->sucursal;
        $bitacora->created_at = Carbon::now();
        $bitacora->updated_at = Carbon::now();
        if($request->file('foto'))
        {
          $url = Storage::disk('public')->putFile('storage/bitacora',$request->file('foto'));
          $bitacora->fill(['foto'=>$url])->save();
        }
        $bitacora->save();
        return redirect()->route('v_en_ruta',['id'=>$editar->id_ruta])->with('success','Guardado con Exito');
      }
    }
  }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/

  ////////////////////////////////////////////////////////////////////////////////Finalizar una ruta //////////////////////////////////////////////////
    public function finalizar_ruta($id)
    {
      //Permite finalizar una ruta luego de realizar las entregas que fueron asignadas
      $fecha              = new Carbon();
      $f_ruta             = Ruta::FindOrFail($id);
      $f_ruta->id_estado  = 8;
      $f_ruta->fecha_fin  = $fecha;
      $f_ruta->updated_at = Carbon::now();
      $f_ruta->save();
      return redirect()->route('p_inicio')->with('success','Ruta marcada como Finalizada');
      }
  //-------------------------------------------------------------------------------------------------------------------------------------------------*/
}
