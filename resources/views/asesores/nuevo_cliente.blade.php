@extends('layouts.app')
@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"
  integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
  crossorigin="anonymous"></script>
<link href="{{asset('css/asesores_form.css')}}" rel="stylesheet">
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
        <a class="nav-link bg-light" href="javascript:history.go(-1)"><i class="fas fa-arrow-left"></i> Atrás</a>
    </div>
</div>
<hr>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Nueva Solicitud de Entrega</h4>
        </div>
        {!! Form::open(array('route'=>'guardar_nuevo_cliente','before'=>'csrf','method'=>'post')) !!}
        <form>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud"><b>Cliente</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-user"></b></div>
                        </div>
                        <input type="text" class="form-control" id="cliente" placeholder="Cliente" name="cliente" required>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud"><b>NIT</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                        </div>
                        <input type="number" class="form-control" id="nit" placeholder="NIT" maxlength="8" name="nit">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud"><b>Correo</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-at"></i></div>
                        </div>
                        <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com" name="correo">
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud"><b>Teléfono</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-phone"></i></div>
                        </div>
                        <input type="number" class="form-control" id="telefono" placeholder="2230-0000" name="telefono">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud"><b>Departamento</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="departamento" name="departamento" required>
                            <option value="0">Seleccione un Departamento</option>
                            @foreach($departamentos as $d)
                            <option value="{{$d->id}}">{{$d->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud"><b>Municipio</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-marker-alt"></b></div>
                        </div>
                        <select class="form-control" id="municipio" name="municipio" required>
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud"><b>Aldea/Caserio/Otros</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-signs"></b></div>
                        </div>
                        <select class="form-control" id="otros" name="otros" required>
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud"><b>Dirección</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-pin"></b></div>
                        </div>
                        <input type="text" class="form-control" id="direccion" placeholder="Dirección" name="direccion" required>
                    </div>
                </div>
            </div>
            <br>
            <div class="row justify-content-md-center">
                <button type="submit" class="btn btn-success btn-block" id="boton"><b class="fas fa-angle-double-right"> Continuar</b></button>
            </div>
        </form>
        {!! Form::close() !!}
    </div>
</div>
<script>
$(function(){
  $('#departamento').on('change', onSelectDepartamentoChange);
});
function onSelectDepartamentoChange(){
  var id = $(this).val();
  if(! id){
    $('#municipio').html('<option value ="">Seleccione una opcion</option>');
    return;
  };
  $.get('muni/'+id,function(data){
    var html_select = '<option value ="">Seleccione una opcion</option>';
    for (var i=0; i<data.length; ++i)
    html_select += '<option value="'+data[i].id+'">'+data[i].nombre+'</option>';
    $('#municipio').html(html_select);
  });
}
$(function(){
  $('#municipio').on('change', onSelectMunicipioChange);
});
function onSelectMunicipioChange(){
  var id = $(this).val();
  if(! id){
    $('#otros').html('<option value ="">Seleccione una opcion</option>');
    return;
  };
  $.get('otros/'+id,function(data){
    var html_select = '<option value ="">Seleccione una opcion</option>';
    for (var i=0; i<data.length; ++i)
    html_select += '<option value="'+data[i].id+'">'+data[i].nombre+'</option>';
    $('#otros').html(html_select);
  });
}
</script>
@endsection