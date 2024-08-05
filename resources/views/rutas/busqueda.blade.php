@extends('layouts.app')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
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
    <a class="btn btn-dark" href="{{ route('r_final',$id)}}"><i class="fas fa-undo-alt"></i> Atrás</a>
    <a class="btn btn-success" href="{{route('v_camiones')}}"><i class="fas fa-map-marked-alt"></i> Crear Ruta</a>
    <a class="btn btn-secondary" href="{{route('r_final')}}"><i class="fas fa-map-marked"></i> Rutas Finalizadas</a>
  </div>
  <h4>Busqueda por Fecha</h4>
  <hr>
  {!! Form::open(array('route'=>array('busqueda',$id),'method'=>'get')) !!}
  <div class="row justify-content-md-center">
    <div class="col">
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">Fecha Inicial</div>
        </div>
        <input type="date" class="form-control" name="fecha_inicial">
      </div>
    </div>
    <div class="col">
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">Fecha Final</div>
        </div>
        <input type="date" class="form-control" name="fecha_final">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
      </div>
    </div>
    {!! Form::close() !!}
  </div>
  <div class="table-condensed">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Placa Camion</th>
          <th>Estado</th>
          <th>Fecha Creación</th>
          <th>Fecha Fin</th>
          <th>Ver</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rutas as $r)
        <tr>
          <td>{{$r->id}}</td>
          <td>{{$r->camion->placa}}</td>
          <td>{{$r->estado->nombre}}</td>
          <td>{{date('d-m-Y','strtotime'($r->created_at))}}</td>
          @if($r->fecha_fin!='')
          <td>{{date('d-m-Y H:i','strtotime'($r->fecha_fin))}}</td>
          @else
          <td></td>
          @endif
          <td><a class="btn" id="ver" href="{{route('v_ruta',$r->id)}}"><i class="fas fa-eye"></i> Ver</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
