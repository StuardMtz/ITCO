@extends('layouts.app2')
@section('content')
<div class="container-fluid">
	<a class="btn btn-sm btn-dark" href="{{route('mimageneral')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
	<div class="card">
		<div class="card-header">
			<h6>Historial de existencia de productos</h6>
		</div>
		<div class="card-body">
			<form class="needs-validation" novalidate method="get" action="{{route('pro_suc')}}">
				<div class="form-row">
					<div class="form-group col-md-6">
						<label><i class="fas fa-calendar-alt"></i> Fecha inicial</label>
						<input type="date" class="form-control" name="mesu" value="{{date('Y-m-d',strtotime($mes))}}" required>
					</div>
					<div class="form-group col-md-6">
						<label><i class="fas fa-calendar-alt"></i> Fecha final</label>
						<input type="date" class="form-control" name="mesd" value="{{date('Y-m-d',strtotime($atras))}}" required>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="nuevo_inventario"><i class="fas fa-store"></i> Sucursal</label>
						<select class="form-control" name="cod_unidad" id="sucursal" required>
							<option value="">Seleccione una sucursal</option>
							@foreach($sucursales as $s)
							<option value="{{$s->cod_unidad}}">{{$s->nombre}}</option>
							@endforeach
						</select>
            		</div>
					<div class="form-group col-md-6">
						<label for="nuevo_inventario"><i class="fas fa-store"></i> <b>Bodega</b></label>
						<select class="form-control" id="bodega" name="bodega" required>
							<option></option>
						</select>
            		</div>
				</div>    
				<b>Dato Opcional</b>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="nuevo_inventario"><i class="fas fa-tasks"></i> Categorias</label>
						<select class="form-control" id="categorias" name="cod_tipo_prod">
							<option value="2">Seleccione una Categoria</option>
							@foreach($cate as $c)
							<option value="{{$c->nombre}}">{{$c->nombre}}</option>
							@endforeach
						</select>
            		</div>
					<div class="form-group col-md-6">
						<label for="nuevo_inventario"><i class="fas fa-cash-register"></i> <b>Marcas</b></label>
						<select class="form-control" id="marcas" name="marca">
							<option value="1">Seleccione una Marca</option>
							@foreach($marcas as $m)
							<option value="{{$m->Descripcion}}">{{$m->Descripcion}}</option>
							@endforeach
						</select>
            		</div>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block"><i class="fas fa-forward"></i> Continuar</button>
					<a class="btn btn-secondary btn-block" href="{{route('ingre_dat')}}"><i class="fas fa-redo-alt"></i> Refrescar</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$("#categorias").click(function(){
		$("#marcas").attr('disabled',true);
	});
	$("#marcas").click(function(){
		$("#categorias").attr('disabled',true);
	});
});
</script>	
<script>
$(function(){
    $('#sucursal').on('change', onSelectSucursalChange);
});
function onSelectSucursalChange(){
    var cod_unidad = $(this).val(); 
    if(! cod_unidad){
        $('#bodega').html('<option value ="">Seleccione una opcion</option>');
        return;
    };        
    $.get('select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].observacion+'</option>';
        $('#bodega').html(html_select);
    });
}
</script>	  
@endsection