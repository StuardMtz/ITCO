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
    <a class="btn btn-dark btn-block" href="{{ route('p_inicio')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
  </div>
  <h4>Rutas Finalizadas</h4>
  <hr>
  <div class="table-condensed">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Estado</th>
          <th>Fecha de Entrega</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rutas as $r)
        <tr>
          <td>{{$r->id}}</td>
          <td>{{$r->estado->nombre}}</td>
          <td>{{date('d-m-Y','strtotime'($r->fecha_entrega))}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{$rutas->links()}}
</div>
@endsection
