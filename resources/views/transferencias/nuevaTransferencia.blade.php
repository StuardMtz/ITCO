@extends('layouts.app')
@section('content')
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            @foreach($tran as $t)
            @if($t->id_estado < 18)
		    <a class="nav-link" href="{{url('EdTran',$id)}}">Atrás</a>
            @else
		    <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
            @endif
	 		<a class="nav-link">Agregando productos</a>
            @guest
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            <li class="nav-item">
                @if (Route::has('register'))
                <!--<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> -->
                @endif
            </li>
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
<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$t->num_movi}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}}  {{$t->bodega}}</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Observación:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                @if($t->id_estado == 13)
                <li class="list-inline-item" style="background:#940000;color:white;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado == 14)
                <li class="list-inline-item" style="background:#a8e4a0;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado == 15)
                <li class="list-inline-item" style="background:#dcd0ff;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado == 16 || $t->id_estado == 17)
                <li class="list-inline-item" style="background:#87cefa;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado >= 18 || $t->id_estado <= 19)
                <li class="list-inline-item" style="background:#ffb347;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado == 20)
                <li class="list-inline-item" style="background:#f984ef;"><b>Estado:</b> {{$t->estado}}</li>
                @endif
                <li class="list-inline-item"><b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}</li>
                <li class="list-inline-item"><b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</li>
            </ul>
        </div>
    </div>
    @endforeach
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
</div>
<div class="container-fluid">
    <div id="app" class="content"><!--La equita id debe ser app, como hemos visto en app.js-->
        <agregar-component></agregar-component><!--Añadimos nuestro componente vuejs-->
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
@endsection
