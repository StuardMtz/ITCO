@extends('layouts.app')
@section('content')
<div class="container">
  @if($message= Session::get('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{ $message}}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if($message= Session::get('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>{{ $message}}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
</div>
<div class="container-fluid">
  <div class="row">
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link" href="{{route('entregas_en_espera')}}"><i class="far fa-pause-circle"></i> Solicitudes en espera</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('solicitudes_en_ruta')}}"><i class="fas fa-shipping-fast"></i> Solicitudes en ruta</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('solicitudes_entregadas')}}"><i class="fas fa-clipboard-check"></i> Solicitudes entregadas</a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="#" id="active"><i class="fas fa-map-marked-alt"></i> Asignar rutas</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('v_a_camion')}}"><i class="fas fa-truck-monster"></i> Nuevo Camión</a>
      </li>
    </ul>
  </div>
  
</div>
@endsection
