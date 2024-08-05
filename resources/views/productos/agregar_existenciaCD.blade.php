@extends('layouts.app2')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
@section('content')
<div class="container">
    @if($message= Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>¡{{ $message}}!</strong>
    </div>
    @endif
    @if($message= Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <p>¡{{ $message}}!</p>
    </div>
    @endif
</div>
<div class="container-fluid">
   <a class="btn btn-dark"  onclick="window.close();" style="color:white;"><i class="fas fa-arrow-left"></i> Atrás</a>
   <h5>{{$nombre}}</h5>
   <form id="form" method="post" action="{{url('agp')}}">
   {{csrf_field()}}
      @foreach($existencia as $existencia)
      <div class="form-row">
         <div class="form-group col">
            <label for="ExistenciaTeorica"><b>Existencia en kardex</b></label>
            <input type="number" class="form-control" step="1" value="{{$existencia->existencia}}" disabled>
            <input type="number" class="form-control" step="0" value="{{$existencia->existencia}}" style="display:none;" name="existencia">
            <input type="number" class="form-control" step="0" value="{{$suma}}" style="display:none;" name="suma">
            <input type="number" class="form-control" step="0" value="{{$id}}" style="display:none;" name="id">
         </div>
         <div class="form-group col">
            <label for="inputExistenciaFisica"><b>Existencia fisica</b></label>
            <input type="number" class="form-control" step="1" required name="fisica">
         </div>
      </div>
      <div class="form-row">
         <div class="form-group col">
            <label for="inputDescripcion"><b>Observaciones</b></label>
            <textarea  class="form-control" name="observaciones"></textarea>
         </div>
         <div class="form-group col">
            <label for="inputMalEstado"><b>Producto dañado</b></label>
            <select class="form-control" name="mal_estado">
               <option value="1">No</option>
               <option value="2">Sí</option>
            </select>
         </div>
      </div>
      <div class="form-row">
         <button type="submit" class="btn btn-dark btn-block">Guardar</button>
      </div>
      @endforeach
   </form>
   <br>
   <table class="table table-sm">
      <thead>
         <tr>
            <th>Categoria</th>
            <th>Código producto</th>
            <th>Nombre</th>
            <th>Teorico</th>
            <th>Orden de carga</th>
            <th>Fisico</th>
            <th>Diferencia</th>
            <th>Dañado</th>
         </tr>
      </thead>
      <tbody>
         @foreach($detalles as $detalle)
         <tr>
            <td>{{$detalle->categoria}}</td>
            <td>{{$detalle->nombre_corto}}</td>
            <td>{{$detalle->nombre_fiscal}}</td>
            <td>{{number_format($detalle->existencia_teorica)}}</td>
            <td>{{number_format($detalle->cantidad)}}</td>
            <td>{{number_format($detalle->existencia_fisica)}}</td>
            <td>{{number_format($detalle->diferencias)}}</td>
            <td>{{number_format($detalle->mal_estado)}}</td>
         </tr>
         @endforeach
      </tbody>
   </table>
</div>
@endsection