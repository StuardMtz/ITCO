@extends('layouts.app2')

@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<style>
	.flota{
			overflow: hidden;
            position: sticky;
            top: 3rem;
			}
        .flotant {
            overflow: hidden;
            position: sticky;
            top: 0;
			}
    </style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<div class="container-fluid">
	@if(Auth::user()->roles == 3)
	@else
	<a class="btn btn-dark" href="{{route('exist',['sucu'=>$sucu,'bod'=>$bod,'todo'=>'1'])}}"><i class="fas fa-chart-bar"></i> Atrás</a>
	@endif
	 <a class="btn btn-primary" id="export-btn">Exportar en Excel</a>
	 <a class="btn btn-danger" href="{{route('exist',['sucu'=>$sucu,'bod'=>$bod,'todo'=>'2'])}}"><i class="fas fa-arrow-circle-down"></i> Abajo del Mínimo</a>
	 <a class="btn btn-warning" href="{{route('exist',['sucu'=>$sucu,'bod'=>$bod,'todo'=>'3'])}}"><i class="fas fa-arrow-circle-down"></i> Abajo del Reorden</a>
		<br>
	</div>
	<h4>{{$sucursal->nombre}} {{$bodega->nombre}}</h4>
	<div>
    {!! Form::open(['method'=>'get','route'=>['busq_exi']]) !!}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    	<input value="{{$sucu}}" name="sucu" style="display: none;">
    	<input value="{{$bod}}" name="bod" style="display: none">
      <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search" name="cod">
      <button class="btn btn-success my-2 my-sm-0" type="submit">Buscar</button>
    {!! Form::close() !!}
    </nav>
	<div class="container-fluid">
		<table class="table table-sm" id="resultsTable">
			<thead >
				<tr class="flotant">
					<th class="flotant">Categoria</th>
					<th class="flotant">Código</th>
					<th class="flotant">Producto</th>
					<th class="flotant" style="text-align: right;">Abajo del Máximo</th>
					<th class="flotant" style="text-align: right;">Abajo del Reorden</th>
					<th class="flotant" style="text-align: right;">Existencia</th>
					<th class="flotant" style="text-align: right;">Mínimo</th>
					<th class="flotant" style="text-align: right;">Reorden</th>
					<th class="flotant" style="text-align: right;">Máximo</th>
				</tr>		
			</thead>
			<tbody>
				@foreach($datos as $d)
				<tr>
					@if($d->existencia <= $d->min)
					<td>{{$d->cod_tipo_prod}}</td>
					<td>{{$d->nom_corto}}</td>
					<td ><a href="{{route('graficado',['sucu'=>$sucu,'bod'=>$bod,'dato'=>$d->cod_producto])}}" target="_blank">{{$d->nom_producto}}</a></td>
					<td style="background-color: #DF483F; text-align: right;"><b>{{number_format($d->existencia - $d->max)}} </b></td>
					<td style="background-color: #DF483F; text-align: right;"><b>{{number_format($d->reorden - $d->existencia)}} </b></td>
					<td style="background-color: #D8EAFF; text-align: right;"><b>{{number_format($d->existencia)}}</b> <i class="fas fa-times"></i></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->min)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->reorden)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->max)}}</b></td>
					@elseif($d->existencia >= $d->min && $d->existencia <= $d->reorden)
					<td>{{$d->cod_tipo_prod}}</td>
					<td>{{$d->nom_corto}}</td>
					<td ><a href="{{route('graficado',['sucu'=>$sucu,'bod'=>$bod,'dato'=>$d->cod_producto])}}" target="blank">{{$d->nom_producto}}</a></td>
					<td style="background-color:#E0B17C; text-align: right;"><b>{{number_format($d->existencia - $d->max)}}</b></td>
					<td style="background-color:#E0B17C; text-align: right;"><b>{{number_format($d->existencia - $d->reorden)}}</b></td>
					<td style="background-color: #D8EAFF; text-align: right;"><b>{{number_format($d->existencia)}}</b> <i class="fas fa-exclamation-triangle"></i></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->min)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->reorden)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->max)}}</b></td>
					@elseif($d->existencia >= $d->reorden  && $d->existencia <= (($d->reorden + $d->max)/2.1))
					<td>{{$d->cod_tipo_prod}}</td>
					<td>{{$d->nom_corto}}</td>
					<td ><a href="{{route('graficado',['sucu'=>$sucu,'bod'=>$bod,'dato'=>$d->cod_producto])}}" target="blank">{{$d->nom_producto}}</a></td>
					<td style="background-color: #FFFA88; text-align: right;"><b>{{number_format($d->max -$d->existencia)}}</b></td>
					<td style="background-color: #FFFA88; text-align: right;"><b>{{number_format($d->existencia - $d->reorden )}}</b></td>
					<td style="background-color: #D8EAFF; text-align: right;"><b>{{number_format($d->existencia)}}</b> <i class="fas fa-arrow-circle-down"></i></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->min)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->reorden)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->max)}}</b></td>
					@elseif($d->existencia >= (($d->reorden + $d->max)/2.1)  && $d->existencia <= $d->max)
					<td>{{$d->cod_tipo_prod}}</td>
					<td>{{$d->nom_corto}}</td>
					<td ><a href="{{route('graficado',['sucu'=>$sucu,'bod'=>$bod,'dato'=>$d->cod_producto])}}" target="blank">{{$d->nom_producto}}</a></td>
					<td style="background-color:#94DE98; text-align: right;"><b>{{number_format($d->existencia - $d->max)}}</b></td>
					<td style="background-color:#94DE98; text-align: right;"><b>{{number_format($d->existencia - $d->reorden)}}</b></td>
					<td style="background-color:#D8EAFF; text-align: right;"><b>{{number_format($d->existencia)}}</b> <i class="fas fa-check-circle"></i></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->min)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->reorden)}}</b></td>
					<td style="text-align: right;"><b style="font-size: 13px;">{{number_format($d->max)}}</b></td>
					@elseif($d->existencia >= $d->max)
					<td>{{$d->cod_tipo_prod}}</td>
					<td>{{$d->nom_corto}}</td>
					<td ><a href="{{route('graficado',['sucu'=>$sucu,'bod'=>$bod,'dato'=>$d->cod_producto])}}" target="blank">{{$d->nom_producto}}</a></td>
					<td style="text-align: right;"><b>+{{number_format($d->existencia - $d->max)}} </b></td>
					<td style="text-align: right;"><b>+{{number_format($d->existencia - $d->reorden)}}</b></td>
					<td style="background-color:#D8EAFF; text-align:right;"><b>{{number_format($d->existencia)}}</b> <i class="fas fa-arrow-circle-up"></i></td>
					<td style="text-align: right;"><b>{{number_format($d->min)}}</b></td>
					<td style="text-align: right;"><b>{{number_format($d->reorden)}}</b></td>
					<td style="text-align: right;"><b>{{number_format($d->max)}}</b></td>
					@endif
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

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
                html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
                $('#bodega').html(html_select);
        });
        }
	</script>
	<script>
		$(document).ready(function() {
			$('#export-btn').on('click', function(e){
			e.preventDefault();
			ResultsToTable();
		});
		function ResultsToTable(){
        $("#resultsTable").table2excel({
           filename: "Reporte_Sucursal.xls"
			});
			}
		});
	</script>
	<script>
		$(document).ready(function(){
			$("#ver").click(function(){
				$("#datos,#ocultar").css('display','block');
				$("#ver").css('display','none');
					});
			$("#ocultar").click(function(){
				$("#datos,#ocultar").css('display','none');
				$("#ver").css('display','block');
					});
			});
	</script>
@endsection
