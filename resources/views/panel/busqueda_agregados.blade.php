@extends('layouts.app')

@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<div class="container-fluid">
    <a class="btn btn-dark" href="{{route('agr_pro',array('semana'=>$semana))}}"><i class="fas fa-undo-alt"> Atrás</i></a>
    <div class="flotante">
        {!! Form::open(['method'=>'get','route'=>['bu',$semana]]) !!}
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search" name="busqueda">
            <button class="btn btn-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i> Buscar</button>
  {!! Form::close() !!} 
</nav>
    </div>
    <div class="table-condensed">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Código Producto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $tp)
                <tr>
                    <td><a class="fas fa-times-circle" href="{{route('ecpro',array('cat'=>$tp->cod_tipo_prod,'semana'=>$semana))}}"></a>{{$tp->cod_tipo_prod}}</td>
                    <td><a class="fas fa-times-circle" href="{{route('epro',array('id'=>$tp->id,'semana'=>$semana))}}"></a> {{$tp->nombre_corto}} {{$tp->nombre_fiscal}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection