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
        <a class="btn btn-dark" href="{{route('vista_clientes',$suc)}}"><i class="fas fa-undo-alt"></i> Atrás</a>
    </div>
</div>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Editar Cliente</h4>
        </div>
        {!! Form::model($cliente, ['method'=>'post','route'=>['guardar_edit_cliente', $cliente->id]]) !!}
        <form>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Cliente</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-user"></b></div>
                        </div>
                        <input type="text" class="form-control" id="cliente" placeholder="Cliente" name="cliente" value="{{$cliente->nombre}}" required>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud">NIT</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                        </div>
                        <input type="number" class="form-control" id="nit" placeholder="NIT" name="nit" value="{{$cliente->nit}}">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Correo</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-at"></i></div>
                        </div>
                        <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com" name="correo" value="{{$cliente->correo}}">
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud">Teléfono</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-phone"></i></div>
                        </div>
                        <input type="number" class="form-control" id="telefono" placeholder="2230-0000" name="telefono" required value="{{$cliente->telefono}}">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud">Departamento</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="departamento" name="departamento" required>
                            <option value="{{$cliente->id_departamento}}">{{$cliente->departamento->nombre}}</option>
                            @foreach($departamentos as $d)
                            <option value="{{$d->id}}">{{$d->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud">Municipio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-marker-alt"></b></div>
                        </div>
                        <select class="form-control" id="municipio" name="municipio" required>
                            <option value="{{$cliente->id_municipio}}">{{$cliente->municipio->nombre}}</option>
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud">Aldea/Caserio/Otros</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-signs"></b></div>
                        </div>
                        <select class="form-control" id="otros" name="otros" required>
                            <option value="{{$cliente->id_otros}}">{{$cliente->otros->nombre}}</option>
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud">Dirección</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-pin"></b></div>
                        </div>
                        <input type="text" class="form-control" id="direccion" placeholder="Dirección" name="direccion" value="{{$cliente->direccion}}" required>
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
