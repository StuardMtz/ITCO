@extends('layouts.app2')
@section('content')
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
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
<div class="container">
  <hr>
  <form enctype="multipart/form-data" method="post" action="{{url('importar')}}">
    {{csrf_field()}}
    <div class="row">
      <div class="col">
        <legend class="col-form-label"><h4>Importar Aldeas</h4></legend>
        <div class="input-group">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-camera"></i></div>
          </div>
          <input type="file" class="form-control" name="excel">
          <button type="submit" id="continuar" class="btn btn-success"><b class="far fa-save" disabled> Importar</b></button>
        </div>
      </div>
    </div>
</form>
{!! Form::close() !!}
</div>

<hr>
<div class="container">
  <form enctype="multipart/form-data" method="post" action="{{url('importar_m')}}">
    {{csrf_field()}}
    <div class="row">
      <div class="col">
        <legend class="col-form-label"><h4>Importar Municipios</h4></legend>
        <div class="input-group">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-camera"></i></div>
          </div>
          <input type="file" class="form-control" name="excel">
          <button type="submit" id="continuar" class="btn btn-primary"><b class="far fa-save" disabled> Importar</b></button>
        </div>
      </div>
    </div>
</form>
{!! Form::close() !!}
</div>


<hr>
<div class="container">
  <form enctype="multipart/form-data" method="post" action="{{url('importar_rs')}}">
    {{csrf_field()}}
    <div class="row">
      <div class="col">
        <legend class="col-form-label"><h4>Importar Municipios</h4></legend>
        <div class="input-group">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-camera"></i></div>
          </div>
          <input type="file" class="form-control" name="excel">
          <button type="submit" id="continuar" class="btn btn-danger"><b class="far fa-save" disabled> Importar</b></button>
        </div>
      </div>
    </div>
</form>
{!! Form::close() !!}
</div>

@endsection
