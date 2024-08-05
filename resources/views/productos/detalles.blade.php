@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet"> 
<div class="container-fluid">
    <h1>Todos los Productos</h1>
    <div class="table-sm">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Teórico</th>
                    <th>Existencia Fisica</th>
                    <th>Detalles</th>
                </tr>
            </thead> 
            <tbody>
            @foreach($detalles as $ex)
                <tr>
                    <td>{{$ex->nombre}}</td>
                    <td>{{number_format($ex->existencia)}}</td>
                    @if($ex->existencia_teorica >= 0 && $ex->existencia_fisica >= 0)
                    <td >{{number_format($ex->existencia_fisica - $ex->existencia_teorica)}}</td>
					@elseif($ex->existencia_teorica > 0 && $ex->existencia_fisica < 0)
					<td >{{number_format($ex->existencia_teorica + $ex->existencia_fisica)}}</td>
					@elseif($ex->existencia_teorica < 0 && $ex->existencia_fisica < 0)
					<td >{{number_format($ex->existencia_fisica - $ex->existencia_teorica)}}</td>
					@elseif($ex->existencia_teorica < 0 && $ex->existencia_fisica > 0)
					<td >{{number_format($ex->existencia_fisica + $ex->existencia_teorica)}}</td>
					@endif
                    <td>{{$ex->descripcion}}</td>
                </tr>
            @endforeach
                <tr>
                    <td style="background-color:#CCF3CC; color:black;"></td>
                    <td style="background-color:#CCF3CC; color:black;"><b>Diferencia: {{$diferencia}}</b></td>
                    <td style="background-color: #CCF3CC; color:black;"><b>Total Ingresado: {{$suma}}</b></td>
                    <td colspan="2" style="background-color: #CCF3CC; color:black;"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection