@extends('layouts.app2')
@section('content')
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<link href="{{asset('css/solicitud_form.css')}}" rel="stylesheet">
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
        <a class="btn btn-dark" href="{{ route('s_en_ruta')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
    </div>
    <hr>
    <div class="row">
        <div class="col">
            {!! Form::model($entrega, ['method'=>'post','route'=>['g_cancelar_solicitud', $entrega->id]]) !!}
            <form>
                <div class="row">
                    <div class="col">
                        <fieldset class="form-group">
                            <legend class="col-form-label"><h4>Estados</h4></legend>
                            <div class="col-sm-10">
                                <select class="form-control" onclick="getLocation()" name="estado">
                                    <option>Seleccione una opción</option>
                                    @foreach($estados as $es)
                                    @if($es->id > $entrega->id_estado)
                                    <option value="{{$es->id}}">{{$es->nombre}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col"> 
                        <legend class="col-form-label"><h4>Comentario</h4></legend>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fas fa-comment-alt"></i></div>
                            </div>
                            <textarea type="text" class="form-control" placeholder="Agregar Comentario" name="comentario"></textarea>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-6">
                        <b>Latidud</b><li id="lat"></li>
                    </div>
                    <div class="col-6">
                        <b>Longitud </b><li id="lng"></li>
                    </div>
                </div>
                <input class="form-control" type="text" class="" id="latitude" value="" name="latitud" style="display:none;" required>
                <input class="form-control" type="text" id="longitude" value="" name="longitud" style="display:none;" required>
                
                <div class="row justify-content-md-center">
                    <button type="submit" id="continuar" class="btn btn-dark btn-block"><b class="far fa-save" disabled></b>Guardar</button>
                </div>
            </form>
            {!! Form::close() !!}
        <hr>
    </div>
    <div class="col">
        <div class="card">
            <ul class="list-group list-group-flush">
                @if($entrega->id_departamento != '')
                <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$entrega->cliente->nombre}}</li>
                <li class="list-group-item"><i class="fas fa-map-signs"></i><b> Aldea/Caserio/Otros:</b> {{$entrega->otros->nombre}}</li>
                <li class="list-group-item"><i class="fas fa-map-pin"></i><b> Dirección:</b> {{$entrega->direccion}}</li>
                @else
                <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$entrega->sucur->name}}</li>
                @endif
                <li class="list-group-item"><i class="fas fa-file-alt"></i><b> Comprobante:</b> {{$entrega->comprobante}}</li>
                <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Entrega:</b> {{$entrega->detalle_entrega}}</li>
                <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Dirección:</b> {{$entrega->detalle_direccion}}</li>
                <li class="list-group-item"><i class="fas fa-exclamation-circle"></i><b> Estado:</b> {{$entrega->estado->nombre}}</li>
                <li class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{number_format($entrega->estado->porcentaje)}}%"></div>
                </li>
            </ul>
        </div>
    </div>
</div>
</div>
<script>
    var a = document.getElementById("lat");
    var b = document.getElementById("lng");
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }
    function showPosition(position) {
        var x = document.getElementById("latitude").value = position.coords.latitude;
        var y = document.getElementById("longitude").value = position.coords.longitude;
        a.innerHTML = position.coords.latitude;
        b.innerHTML = position.coords.longitude;
    }
</script>
<script>
    window.onload=function(){
        var pos=window.name || 0;
        window.scrollTo(0,pos);
    }
    window.onunload=function(){
        window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
    }
</script>
<script>
    $(document).ready(function(){
        $("#ubicacion").click(function(){
            $("#continuar").attr('disabled',false);
        });
        $("#marcas").click(function(){
            $("#categorias").attr('disabled',true);
        });
    });
</script>
@endsection
