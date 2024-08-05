@extends('layouts.app')
@section('content')
<link href="{{asset('css/css2/select2.css')}}" rel="stylesheet">
<script src="{{ asset('js/placas.js') }}" defer></script>

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
            @foreach($tran as $t)
			    <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
	 		    <a class="nav-link active">Editando transferencia</a>
                <a class="nav-link" href="{{route('imag_trans',$id)}}">Ver imágenes</a>
                <button type="button" class="nav-link" data-toggle="modal" data-target="#staticBackdrop">
                    Finalizar inspección
                </button>
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
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}} {{$t->bodega}}</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Observación:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                @if($t->id_estado == 15)
                <li class="list-inline-item" style="background:#87cefa;"><b>Estado:</b> {{$t->estado}}</li>
                @else
                <li class="list-inline-item" style="background:#ffb347;"><b>Estado:</b> {{$t->estado}}</li>
                @endif
                <li class="list-inline-item"><b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}</li>
                <li class="list-inline-item"><b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</li>
                @if($t->fechaUno == '')
                <li class="list-inline-item"><b>Preparando carga:</b></li>
                @else
                <li class="list-inline-item"><b>Fecha inicio de carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}</li>
                @endif
                @if($t->fecha_enCarga == '')
                <li class="list-inline-item"><b>Carga preparada:</b></li>
                @else
                <li class="list-inline-item"><b>Carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}</li>
                @endif
                <li class="list-inline-item"><b>Sale de:</b> {{$t->usale}}, {{$t->bsale}}</li>
            </ul>
        </div>
    </div>
    @endforeach
</div>
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" aria-labelledby="transferenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Despachar transferencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('GRTran',$id)}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="validationCustom03"><b>Número de placa</b></label>
                            <select id="placa" name="placa" class="form-control" required style="width: 100%" required>
                                <option></option>
                            </select>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="validationCustom01"><b>Observación</b></label>
                            <input class="form-control" id="validationCustom01" name="piloto" placeholder="¡ Favor de llenar este campo !"
                            required></input>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="SeleccioneGrupo"><b>Seleccione grupo de carga</b></label>
                            <select name="grupo" class="form-control" required>
                                <option value="">Seleccione grupo de carga</option>
                                @foreach($grupos as $gp)
                                <option value="{{$gp->id}}">{{$gp->name}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                        <div class="form-row col-md-6">
                            <label for="grupoCargo"><b>Observaciones</b></label>
                            <textarea class="form-control" name="observaciones" required placeholder="¡No deje este campo vacio!"></textarea>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-dark btn-sm btn-block"><i class="fas fa-save"></i> Guardar datos</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div id="app" class="content"><!--La equita id debe ser app, como hemos visto en app.js-->
        <verificar-component></verificar-component><!--Añadimos nuestro componente vuejs-->
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
