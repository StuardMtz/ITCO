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
    <a class="btn btn-dark" href="{{ route('r_inicio')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
  </div>
  <h4>Rutas Finalizadas</h4>
  {!! Form::open(array('route'=>array('busqueda',$id),'method'=>'get')) !!}
<div class="row">
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
  <hr>
  <div class="table-condensed">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Placa Camion</th>
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
