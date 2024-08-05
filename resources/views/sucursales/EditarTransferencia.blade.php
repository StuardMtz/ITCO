@extends('layouts.app')
<link href="{{asset('css/css2/select2.css')}}" rel="stylesheet">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/productosmanual.js')}}" defer></script>
@section('content')
    @foreach($tran as $t)
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
			<a class="nav-link" href="{{route('VeTran',$id)}}">Atrás</a>
	 	    <a class="nav-link active" id="active">Editando transferencia</a>
            <a class="nav-link" href="{{route('agre_datos_transf',$id)}}">Agregar productos</a>
            <a class="nav-link" href="{{route('imag_trans',$id)}}">Agregar imágenes</a>@guest
            @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
            @endguest
        </div>
    </div>
</nav>

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

	<div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$t->num_movi}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}}</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}</li>
            </ul>
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Observación:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                <li class="list-inline-item"><b>Estado:</b> {{$t->estado}}</li>
                <li class="list-inline-item"><b>Grupo que cargo:</b> {{$t->grupoCarga}}</li>
            </ul>
        </div>
    </div>

<form class="needs-validation" novalidate method="post" action="{{route('agregar_pro_manual',$id)}}">
{{csrf_field()}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <select id="producto" name="producto[]" class="custom-select" required></select>
        <div class="valid-tooltip">
            Excelente!
        </div>
        <div class="invalid-tooltip">
            No puede dejar este campo vacio.
        </div>
        <button class="btn btn-success btn-sm" type="submit">Buscar</button>
    </nav>
</form>
 @endforeach

    <div id="app" class="content"><!--La equita id debe ser app, como hemos visto en app.js-->
        <sucursal-component></sucursal-component><!--Añadimos nuestro componente vuejs-->
    </div>

<div class="modal fade" id="Transferencia" tabindex="-1" aria-labelledby="TransferenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TransferenciaModalLabel">Confirmar transferencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('ed_tran',$id)}}">
                {{csrf_field()}}
                    <div class="row">
                        <div class="col">
                            <label for="validationCustom01"><b>Observación</b></label>
                            <textarea class="form-control" id="validationCustom01" name="observacion" placeholder="¡ Favor de llenar este campo !"
                                required>{{$t->observacionSucursal}}</textarea>
                            <div class="valid-feedback">
                                Excelente!
                            </div>
                            <div class="invalid-feedback">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="validationCustom02"><b>Estado de transferencia</b></label>
                            <select class="form-control" name="estado" required>
                                <option value="{{$t->id_estado}}" class="form-control bg-warning"><b><i class="fas fa-exclamation-circle"></i> --> {{$t->estado}} <-- Estado actual</b></option>
                                @foreach($estados as $e)
                                <option value="{{$e->id}}">{{$e->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">
                                Excelente!
                            </div>
                            <div class="invalid-feedback">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-success btn-block" type="submit"><b><i class="fas fa-save"></i> Guardar cambios</b></button>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
<script type="application/json" name="server-data">
    {{ $id }}
</script>
<script type="application/javascript">
       var json = document.getElementsByName("server-data")[0].innerHTML;
       var server_data = JSON.parse(json);
</script>
<script type="application/javascript">
  window.onload=function(){
    var pos=window.name || 0;
    window.scrollTo(0,pos);
  }
  window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
  }
  </script>
@endsection
