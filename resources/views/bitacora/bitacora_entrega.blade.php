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
  <div class="card">
    <div class="card-header">
      <h6>Ver historial de la entrega número {{$id}}</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive-sm">
        <table class="table table-sm table-borderless">
          <thead>
            <tr>
              <th>Estado</th>
              <th>Comentario</th>
              <th>Fecha de operación</th>
              <th>Foto</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bit as $b)
            <tr>
              <td>{{$b->estado->nombre}}</td>
              <td>{{$b->comentario}}</td>
              <td>{{date('d-m-Y H:i',strtotime($b->created_at))}}</td>
              <td><img  src="{{$b->foto}}" style="width:50%;" alt="categoria imagen"></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection