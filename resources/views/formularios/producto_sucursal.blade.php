@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet"> 
<div class="container-fluid">
    <h5>Producto @foreach($todos_productos as $t)<b style="color: blue;">{{$t->nombre}}</b>, código <b style="color: blue;">{{$t->nombre_corto}}</b>, existencia actual <b style="color:blue;">{{number_format($t->existencia),0}}</b> unidades </h5>
        {!! Form::open(['method'=>'get','route'=>['pro', $id]]) !!}
    <div style="display:none;">
            <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search" name="n_corto" value="{{$t->categorias}}">
    </div> 
    @endforeach
    <div class="row">
        <div class="col">
            <div class="table-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Teorica</th>
                            <th>Fisica</th>
                            <th>Diferencia</th>
                            <th>Detalles</th>
                            <th>Dañado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead> 
                    <tbody>
                   @foreach($existencia as $ex)
                        <tr>
                            <td>{{number_format($ex->existencia)}}</td>
                            <td>{{number_format($ex->existencia_fisica)}}</td>
                            <td>{{$ex->existencia_fisica - $ex->existencia}}</td>
                            <td>{{$ex->descripcion}}</td>
                            <td>{{$ex->mal_estado}}</td>
                            <td>{{date('d-m-Y H:i', strtotime($ex->created_at))}}</td>
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
    </div>
</div>
@endsection