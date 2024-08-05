@extends('layouts.app2')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
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

	<nav class="navbar navbar-expand-lg">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" 
        aria-expanded="false" aria-label="Toggle navigation">
	   	<img src="storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
				<a class="nav-link" href="{{route('ingre_datos')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
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
			<h6><b>Fecha inicial:</b> {{date('d-m-Y', strtotime($fecha_inicial))}}  -  <b>Fecha final:</b> {{date('d-m-Y', strtotime($fecha_final))}}  -  
			<b>Producto:</b> {{$producto->nombre_corto}} - {{$producto->nombre_fiscal}}</h6>
		</div>
	</nav>
<div class="container-fluid">
	<table class="table table-sm table-borderless" id="resultsTable">
		<thead>
			<tr>
				<th class="flota" style="color:white; background:black;border:solid 1px white;">Día</th>
				@foreach($datos as $d)
				@if($d->cod_unidad == 27 && $d->cod_bodega == 1)
				<td class="flota" style="background-color:black; color:white;">{{date('m-d', strtotime($d->fecha))}}</td>
				@endif
				@endforeach
			</tr>
				@php $currentSuc = '' @endphp
				@php $currentBod = '' @endphp
				@php $currentNom = '' @endphp
				@foreach($datos as $d)
				@if($d->bodega != $currentNom)
				@if($d->cod_unidad == '27')
			<tr title="{{$d->sucursal}} - {{$d->bodega}} - {{number_format($d->min,0)}} - {{number_format($d->max,0)}}">
				<th style="color:black; background:#E3B26978;">{{$d->sucursal}} - {{$d->bodega}}</th>
				@endif
				@php $currentSuc = $d->cod_unidad @endphp
				@php $currentBod = $d->cod_bodega @endphp
				@php $currentNom = $d->bodega @endphp
				@endif
				@if($d->cod_unidad == $currentSuc && $d->cod_bodega == $currentBod)
				@if($d->cod_unidad == '27')
				@if($d->existencia < $d->min)
				<td style="background-color:#EEEEEE; color:#FF0000;">{{number_format($d->existencia,0)}}</td>
				@elseif($d->existencia >= $d->min && $d->existencia <= $d->max)
				<td  style="background-color:#EEEEEE; color: #048600;"><b>{{number_format($d->existencia,0)}}</b></td>
				@else
				<td  style="background-color:#EEEEEE; color: black;">{{number_format($d->existencia,0)}}</td>
				@endif
				@endif
				@php $currentSuc = $d->cod_unidad @endphp
				@php $currentBod = $d->cod_bodega @endphp
				@else
			</tr>
				@endif
				@endforeach
				@php $currentSuc = '' @endphp
				@php $currentBod = '' @endphp
				@php $currentNom = '' @endphp
				@foreach($datos as $d)
				@if($d->bodega != $currentNom)
				@if($d->Cod_Cliente == '6815' || $d->Cod_Cliente == '6816')
			<tr title="{{$d->sucursal}} - {{$d->bodega}} - {{number_format($d->min,0)}} - {{number_format($d->max,0)}}">
				<th style="color:black; background:#DB7B7B;">{{$d->sucursal}} - {{$d->bodega}}</th>
				@endif
				@php $currentSuc = $d->cod_unidad @endphp
				@php $currentBod = $d->cod_bodega @endphp
				@php $currentNom = $d->bodega @endphp
				@endif
				@if($d->cod_unidad == $currentSuc && $d->cod_bodega == $currentBod)
				@if($d->Cod_Cliente == '6815' || $d->Cod_Cliente == '6816')
				@if($d->existencia < $d->min)
				<td style="background-color:#CEC5F782; color:red;">{{number_format($d->existencia,0)}}</td>
				@elseif($d->existencia >= $d->min && $d->existencia <= $d->max)
				<td  style="background-color:#CEC5F782; color:orange;">{{number_format($d->existencia,0)}}</td>
				@else
				<td  style="background-color:#CEC5F782; color:black;">{{number_format($d->existencia,0)}}</td>
				@endif
				@endif
				@php $currentSuc = $d->cod_unidad @endphp
				@php $currentBod = $d->cod_bodega @endphp
				@else
			</tr>
				@endif
				@endforeach
				@php $currentSuc = '' @endphp
				@php $currentBod = '' @endphp
				@php $currentNom = '' @endphp
				@foreach($datos as $d)
				@if($d->bodega != $currentNom)
				@if($d->cod_unidad == $sucursal && $d->cod_bodega == $bodega)
			<tr title="{{$d->sucursal}} - {{$d->bodega}} - {{number_format($d->min,0)}} - {{number_format($d->max,0)}}">
				<th style="color:white;background:#343434;">{{$d->sucursal}} - {{$d->bodega}}</th>
				@endif
				@php $currentSuc = $d->cod_unidad @endphp
				@php $currentBod = $d->cod_bodega @endphp
				@php $currentNom = $d->bodega @endphp
				@endif
				@if($d->cod_unidad == $currentSuc && $d->cod_bodega == $currentBod)
				@if($d->cod_unidad == $sucursal && $d->cod_bodega == $bodega)
				@if($d->existencia < $d->min)
				<td style="background-color:#D50B0BB0; color:white;">{{number_format($d->existencia,0)}}</td>
				@elseif($d->existencia >= $d->min && $d->existencia <= $d->max)
				<td  style="background-color:#73D118AB; color: black;">{{number_format($d->existencia,0)}}</td>
				@else
				<td  style="background-color:white; color: black;">{{number_format($d->existencia,0)}}</td>
				@endif
				@endif
				@php $currentSuc = $d->cod_unidad @endphp
				@php $currentBod = $d->cod_bodega @endphp
				@else
			</tr>
				@endif
				@endforeach
		</thead>
	</table>
</div>
<script>
$(document).ready(function() {
    $('#export-btn').on('click', function(e){
    	e.preventDefault();
    	ResultsToTable();
    });
    function ResultsToTable(){
        $("#resultsTable").table2excel({
           filename: "Reporte_Producto.xls"
        });
    }
});
</script>
@endsection