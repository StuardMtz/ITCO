@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
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
  <div class="row">
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link"  href="{{route('sel_suc')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('entre_muni')}}"><i class="fas fa-clipboard"></i> Por municipios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('pClient')}}"><i class="fas fa-users"></i> Clientes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" id="active"><i class="fas fa-truck"></i> Camiones</a>
      </li>
    </ul>
  </div>
  <h5>Listado de Camiones</h5>
  <table class="table table-sm">
    <thead>        
      <tr>
        <th>#</th>
        <th>Marca</th>
        <th>Placa</th>
        <th>Tonelaje</th>
        <th>Tipo</th>          
        <th>Capacidad</th>
        <th>Ver</th>
      </tr>
    </thead>
    <tbody>
      @foreach($camiones as $c)
      <tr>
        <td>{{$c->id}}</td>
        <td>{{$c->marca}}</td>
        <td>{{$c->placa}}</td>
        <td>{{$c->tonelaje}}</td>
        <td>{{$c->tipo_camion}}</td>
        <td>{{$c->espacio}}</td>
        <td><a class="btn btn-warning btn-sm" href="{{route('vEnCami',$c->id)}}"><i class="fas fa-eye"></i> Ver Entregas</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
