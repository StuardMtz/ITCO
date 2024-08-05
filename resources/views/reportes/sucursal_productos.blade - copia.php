@extends('layouts.app2')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<link href="{{asset('css/estilo2.css')}}" rel="stylesheet">
<div class="container-fluid">
<nav class="navbar navbar-flot">
	<div class="row">
		Fecha Inicial: {{date('d-m-Y', strtotime($fecha_inicial))}}  -  Fecha Final: {{date('d-m-Y', strtotime($fecha_final))}}  -  Sucuarsal: @php $currentNo = '' @endphp
			@foreach($datos as $d)
				@if($d->su_nombre != $currentNo)
						{{$d->su_nombre}} - Bodega: {{$d->bo_nombre}}
				@php $currentNo = $d->su_nombre @endphp
				@endif
			@endforeach
		</div>
</nav>
	<a class="btn btn-warning" href="{{route('ingre_dat')}}">Existencias por Sucursal</a>
	 <a class="btn btn-primary" id="export-btn">Exportar en Excel</a>
		<br>
	</div>
	<div class="container-fluid">
		<table class="table table-sm" id="resultsTable">
			<thead>
				<tr>
					<th class="flotante" style="background-color:black; color:white;" colspan="2">Día</th>
					@foreach($datos as $d)
						@if($d->cod_producto == 1)
						<td class="flotante" style="background-color:black; color:white;">{{date('m-d', strtotime($d->fecha))}}</td>
						@endif
					@endforeach
				</tr>
				@php $currentPro = '' @endphp
				@foreach($datos as $d)
					@if($d->cod_producto != $currentPro)
					<tr title="{{$d->nom_corto}} - {{$d->nom_producto}} - {{number_format($d->min,0)}} - {{number_format($d->max,0)}}">
				<th style="color:white;background:#343434;">{{$d->cod_tipo_prod}}</th>
						<th style="color:white;background:#343434;">{{$d->nom_corto}} - {{$d->nom_producto}}</th>
					@php $currentPro = $d->cod_producto @endphp
					@endif
						@if($d->cod_producto == $currentPro)
							@if($d->existencia < $d->min)
								<td style="background-color:#911818; color:white;">{{number_format($d->existencia,0)}}</td>
							@elseif($d->existencia >= $d->min && $d->existencia <= $d->max)
								<td  style="background-color:#8AD47B; color: black;">{{number_format($d->existencia,0)}}</td>
							@else
								<td  style="background-color:white; color: black;">{{number_format($d->existencia,0)}}</td>
							@endif
						@php $currentPro = $d->cod_producto @endphp
						@else
					</tr>
						@endif
				@endforeach
			</thead>
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
