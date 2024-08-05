@extends('layouts.app')

@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<div class="container">
        @if($message= Session::get('success'))
 		<div class="alert alert-success">
 			<p>{{ $message}}</p>
 		</div>
        @endif
        @if($message= Session::get('error'))
 		<div class="alert alert-danger">
 			<p>{{ $message}}</p>
 		</div>
        @endif
    </div>
<div class="container-fluid">
    <h1>Productos sin Contar</h1>
    <hr>
    <div class="table-condensed">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Código Producto Nombre</th>
                    <th>Agregar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($producto as $item)
                <tr>
                    <td>{{$item->categoria}}</td>
                    <td>{{$item->nombre_corto}} - {{$item->nombre_fiscal}}</td>
                    <td><button class="edit-modal btn btn-dark" data-cod="{{$item->cod_producto}}" data-no_corto="{{$item->nombre_corto}}" data-name="{{$item->nombre_fiscal}}"
                                data-categoria="{{$item->categoria}}">Agregar</button> </td>
                </tr>
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
  					<h4 class="modal-title"></h4>
  				</div>
  				<div class="modal-body">
                    {!! Form::open(array('route'=>'agf','before'=>'csrf','method'=>'post')) !!}
                    <form>
                        <div class="col my-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="far fa-clipboard"></i></div>
                                </div>
                                <input type="text" class="form-control" id="cat" placeholder="Categoria" name="categoria">
                            </div>
                        </div>
                        <hr>
                        <div class="col my-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="far fa-clipboard"></i></div>
                                </div>
                                <input type="text" class="form-control" id="n" placeholder="nombre" name="nombre">
                            </div>
                        </div>
                        <hr>
                        <div class="col my-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="far fa-clipboard"></i></div>
                                </div>
                                <input type="text" class="form-control" id="nc" placeholder="nombre_corto" name="nombre_corto">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group" style="display:none;">
                            <label>Código del Producto</label>
                            <select class="form-control" id="exampleFormControlSelect1" name="cod">
                                <option value="fid" name="cod" id="fid"></option>
                            </select>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-dark"><i class="fas fa-check"></i> Agregar</button>
                    </form>
                    {!! Form::close() !!}
  					<div class="modal-footer">
  						<button type="button" class="btn btn-warning" data-dismiss="modal">
  							<span class='glyphicon glyphicon-remove'></span> Close
  						</button>
  					</div>

  				</div>
  			</div>
		  </div>
</div>
<script>
    $(document).ready(function() {
  $(document).on('click', '.edit-modal', function() {
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#fid').val($(this).data('cod'));
        $('#cat').val($(this).data('categoria'));
        $('#nc').val($(this).data('no_corto'));
        $('#n').val($(this).data('name'));
        $('#myModal').modal('show');
    });
});
</script>
@endsection
