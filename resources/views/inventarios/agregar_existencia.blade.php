@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<div class="container-fluid">
   <a class="btn btn-dark btn-sm"  onclick="window.close();" style="color:white;"><i class="fas fa-arrow-left"></i> Atrás</a>
   <div class="card">
      @foreach($existencia as $existencia)
      <div class="card-header">
         <h6>{{$existencia->nombre_corto}} - {{$existencia->nombre}}</h6>
      </div>
      <div class="card-body">
         <form id="form" method="post" action="{{url('gu_expro')}}">
         {{csrf_field()}}
            <div class="form-row">
               <div class="form-group col-md-6">
                  <label for="ExistenciaTeorica"><b>Existencia en kardex</b></label>
                  <input type="number" class="form-control" step="1" value="{{$existencia->existencia}}" disabled>
                  <input type="number" class="form-control" step="0" value="{{$existencia->existencia}}" style="display:none;" name="existencia">
                  <input type="number" class="form-control" step="0" value="{{$id}}" style="display:none;" name="id">
               </div>
               <div class="form-group col-md-6">
                  <label for="inputExistenciaFisica"><b>Existencia fisica</b></label>
                  <input type="number" class="form-control" step="1" required name="fisica">
               </div>
            </div>
            <div class="form-row">
               <div class="form-group col-md-6">
                  <label for="inputDescripcion"><b>Observaciones</b></label>
                  <textarea  class="form-control" name="observaciones"></textarea>
               </div>
               <div class="form-group col-md-6">
                  <label for="inputMalEstado"><b>Producto dañado</b></label>
                  <select class="form-control" name="mal_estado">
                     <option value="1">No</option>
                     <option value="2">Sí</option>
                  </select>
               </div>
            </div>
            <div class="form-row">
               <button type="submit" class="btn btn-dark btn-block btn-sm">Guardar</button>
            </div>
            @endforeach
         </form>
      </div>
   </div>
   <hr>
   <div class="table-responsive-sm">
      <table class="table table-sm">
         <thead>
            <tr>
               <th>Categoria</th>
               <th>Código producto</th>
               <th>Nombre</th>
               <th>Teorico</th>
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
               <td>{{number_format($detalle->existencia_fisica)}}</td>
               <td>{{number_format($detalle->diferencias)}}</td>
               <td>{{number_format($detalle->mal_estado)}}</td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
   <button class="btn btn-warning btn-sm" type="button" data-toggle="collapse" data-target="#HistorialProducto" aria-expanded="false" aria-controls="collapseExample">
      Ver historial del conteo del producto
   </button>
   <div class="collapse" id="HistorialProducto">
      <div class="card card-body">
         <div class="table-responsive-sm">
            <table class="table table-sm">
               <thead>
                  <tr>
                     <th>Código</th>
                     <th>Nombre</th>
                     <th>Teorico</th>
                     <th>Fisico</th>
                     <th>Diferencia</th>
                     <th>Detalle</th>
                     <th>Mal estado</th>
                     <th>Fecha</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($historial as $ht)
                  <tr>
                     <td>{{$ht->nombre_corto}}</td>
                     <td>{{$ht->nombre_fiscal}}</td>
                     <td>{{number_format($ht->existencia,0)}}</td>
                     <td>{{number_format($ht->existencia_fisica)}}</td>
                     <td>{{number_format($ht->diferencia)}}</td>
                     <td>{{$ht->descripcion}}</td>
                     <td>{{$ht->mal_estado}}</td>
                     <td>{{date('d/m/Y H:i:s',strtotime($ht->created_at))}}</td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@endsection