@extends('layouts.app2')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<link href="{{asset('css/estilo2.css')}}" rel="stylesheet">

	<nav class="navbar navbar-expand-lg">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" 
        aria-expanded="false" aria-label="Toggle navigation">
	   	<img src="storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
				<a class="nav-link" href="{{route('ingre_dat')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
				<a class="nav-link" id="export-btn">Exportar a excel</a>
				@guest
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </div>
        </div>
    </nav>

	<nav class="navbar navbar-flot">
		<div class="row">
			<b>Fecha inicial:</b> {{date('d-m-Y', strtotime($fecha_inicial))}}  -  <b>Fecha final:</b> {{date('d-m-Y', strtotime($fecha_final))}}  -  
			<b>Sucuarsal:</b> @php $currentNo = '' @endphp
			@foreach($datos as $d)
				@if($d->su_nombre != $currentNo)
					{{$d->su_nombre}} - <b>Bodega:</b> {{$d->bo_nombre}}
				@php $currentNo = $d->su_nombre 
				@endphp
				@endif
			@endforeach
			{{$categorias}}
		</div>
	</nav>
<div class="container-fluid">
	<div class="table-responsive">
		<table class="table table-sm table-borderless" id="resultsTable">
			<thead>
				<tr class="flotante">
					<th style="background-color:black; color:white;" colspan="2">Día</th>
					@foreach($datos as $d)
					@if($d->cod_producto == 1)
					<td style="background-color:black; color:white;">{{date('m-d', strtotime($d->fecha))}}</td>
					@endif
					@endforeach
				</tr>
					@php $currentPro = '' @endphp
					@foreach($datos as $d)
					@if($d->cod_tipo_prod == $categorias)
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
					@endif
					@endforeach
			</thead>
		</table>
	</div>
</div>
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
@endsection