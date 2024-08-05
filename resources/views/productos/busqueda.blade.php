@extends('layouts.app')

@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<div class="container-fluid">
  <h4>Resultado Busqueda</h4>
  <hr>
  <h5>Inventario numero {{$id}}</h5>
  <div class="flotante">
    {!! Form::open(['method'=>'get','route'=>['bus', $id]]) !!}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search" name="n_corto">
      <button class="btn btn-success my-2 my-sm-0" type="submit">Buscar</button>
	<a class="btn btn-primary" href="{{ route('cat', $id) }}"> Sin Contar</a>
    {!! Form::close() !!}
    </nav>
  </div>
  <div class="table-condensed">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>Categoria</th>
          <th>Código Producto</th>
          <th>Teorico</th>
          <th>Fisico</th>
          <th>Diferencia</th>
          <th>Dañado</th>
        </tr>
      </thead>
      <tbody>
      @foreach($op as $item)
	@if(Auth::id() == 3)
      		@if($item->existencia_fisica - $item->existencia_teorica != 0)
        	<tr onclick="" class="item{{$item->id}}" id="edit-modal" data-cod="{{$item->cod_producto}}" data-name="{{$item->nombre_corto}} {{$item->nombre_fiscal}}">
          		<td>{{$item->categoria}}</td>
          		<td>{{$item->nombre_corto}} - {{$item->nombre_fiscal}}</td>
          		<td>{{number_format($item->existencia_teorica,0)}}</td>
          		<td>{{$item->existencia_fisica}}</td>
          		@if($item->existencia_teorica >= 0 && $item->existencia_fisica >= 0)
          			<td>{{number_format($item->existencia_fisica - $item->existencia_teorica)}}</td>
			@elseif($item->existencia_teorica > 0 && $item->existencia_fisica < 0)
				<td>{{number_format($item->existencia_teorica + $item->existencia_fisica)}}</td>
			@elseif($item->existencia_teorica < 0 && $item->existencia_fisica < 0)
				<td>{{number_format($item->existencia_fisica - $item->existencia_teorica)}}</td>
			@elseif($item->existencia_teorica < 0 && $item->existencia_fisica >= 0)
				<td>{{number_format($item->existencia_fisica + $item->existencia_teorica)}}</td>
			@endif
          		<td>{{$item->mal_estado}}</td>
        	</tr>
		@else
		@endif
	@else
		<tr onclick="" class="item{{$item->id}}" id="edit-modal" data-cod="{{$item->cod_producto}}" data-name="{{$item->nombre_corto}} {{$item->nombre_fiscal}}">
          		<td>{{$item->categoria}}</td>
          		<td>{{$item->nombre_corto}} - {{$item->nombre_fiscal}}</td>
          		<td>{{number_format($item->existencia_teorica,0)}}</td>
          		<td>{{$item->existencia_fisica}}</td>
          		@if($item->existencia_teorica >= 0 && $item->existencia_fisica >= 0)
          			<td>{{number_format($item->existencia_fisica - $item->existencia_teorica)}}</td>
			@elseif($item->existencia_teorica > 0 && $item->existencia_fisica < 0)
				<td>{{number_format($item->existencia_teorica + $item->existencia_fisica)}}</td>
			@elseif($item->existencia_teorica < 0 && $item->existencia_fisica < 0)
				<td>{{number_format($item->existencia_fisica - $item->existencia_teorica)}}</td>
			@elseif($item->existencia_teorica < 0 && $item->existencia_fisica >= 0)
				<td>{{number_format($item->existencia_fisica + $item->existencia_teorica)}}</td>
			@endif
          		<td>{{$item->mal_estado}}</td>
        	</tr>
			@endif
      @endforeach
      </tbody>
    </table>
  </div>
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
  			</div>
  			<div class="modal-body">
          {!! Form::open(array('route'=>'agp','before'=>'csrf','method'=>'post')) !!}
          <form>
            <div class="col my-1">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text"><i class="far fa-clipboard"></i></div>
                </div>
                <input type="text" class="form-control" id="n" placeholder="Existencia Fisica" name="name" disabled>
              </div>
            </div>
            <hr>
            <div class="col my-1">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text"><i class="far fa-clipboard"></i></div>
                </div>
                <input type="number" class="form-control" id="ExistenciaFisaca" placeholder="Existencia Fisica" name="existencia_fisica">
              </div>
            </div>
            <hr>
            <div class="col my-1">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text"><i class="fas fa-edit"></i></div>
                </div>
                <input type="text" class="form-control" id="Descripcion" placeholder="Descripción" name="descripcion">
              </div>
            </div>
            <hr>
            <div class="col-auto">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="autoSizingCheck" name="dañado" value="Si">
                <label class="form-check-label" for="autoSizingCheck">Dañado</label>
              </div>
            </div>
            <div class="form-group" style="display:none;">
              <label>No. de Inventario</label>
              <select class="form-control" id="exampleFormControlSelect1" name="id">
                <option value="{{$id}}" name="id">{{$id}}</option>
              </select>
              <hr>
              <label>Código del Producto</label>
              <select class="form-control" id="exampleFormControlSelect1" name="cod">
                <option value="fid" name="cod" id="fid">6</option>
              </select>
            </div>
            <hr>
            <button type="submit" class="btn btn-dark"><i class="fas fa-check"></i> Agregar</button>
          </form>
          {!! Form::close() !!}
          <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
  $(document).ready(function() {
    $(document).on('click', '#edit-modal', function() {
      $('.deleteContent').hide();
      $('.form-horizontal').show();
      $('#fid').val($(this).data('cod'));
      $('#n').val($(this).data('name'));
      $('#suma').val($(this).data('suma'));
      $('#myModal').modal('show');
    });
  });
  </script>
  <script>
  window.onload=function(){
    var pos=window.name || 0;
    window.scrollTo(0,pos);
  }
  window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
  }
  </script>
@endsection