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
    <a class="btn btn-block" id="finalizada" href="{{route('v_final')}}"><i class="fas fa-map-marked"></i> Rutas Finalizadas</a>
  </div>
  <br>
  <h4>Rutas Asignadas</h4>
  <hr>
  <div class="table-condensed">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>Fecha de Entrega</th>
          <th>Editar</th>
          <th>Finalizar</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rutas as $r)
        <tr>
          <td>{{date('d-m-Y','strtotime'($r->fecha_entrega))}}</td>
          <td><a class="btn btn-dark" href="{{route('v_en_ruta',$r->id)}}"><i class='fas fa-edit'></i> Editar</a></td>
          <td><a class="btn btn-danger" href="{{route('f_r_pi',$r->id)}}"><i class="fas fa-clipboard-check"></i> Finalizar</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{$rutas->links()}}
</div>
@endsection
