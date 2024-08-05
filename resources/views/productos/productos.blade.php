@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet"> 
<div class="container-fluid">
    <h1>Listado de productos</h1>
    <div class="flotante">{!! Form::open(['method'=>'get','route'=>['cat', $id]]) !!}
                        {!! Form::submit('Atrás',['class'=>'btn btn-danger']) !!}
        {!! Form::close() !!}</div>
    <div class="table-condensed">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Existencia</th>
                    <th></th>
                </tr>
            </thead> 
            <tbody>
                @foreach($todos_productos as $tproducto)
                <tr>
                    <td>{{$tproducto->nombre_fiscal}}</td>
                    <td>{{number_format($tproducto->existencia1),0}}</td>
                    <td><a type="button" class="btn btn-dark btn-sm" href="{{ route('finven',array('cod'=>$tproducto->cod_producto,'id'=>$id,'categoria'=>$categoria))}}">Agregar</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection