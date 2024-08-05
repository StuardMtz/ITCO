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
            <a class="nav-link" href="{{route('liquidaciones')}}">Atrás</a>
            <a class="nav-link" href="#">Editar liquidación</a>
            <button type="button" class="btn btn-dark" id="modalb" data-toggle="modal" data-target="#nuevaLiquidacion">
                Editar encabezado liquidación
            </button>
            <button type="button" class="nav-link" data-toggle="modal" data-target="#liquidacionModal">
                Finalizar liquidación
            </button>
            @guest
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
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>¡{{ $message}}!</strong>
    </div>
    @endif
    @if($message= Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>¡{{ $message}}!</strong>
    </div>
    @endif
</div>


<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Agregue o elimine los gastos para la liquidación</p>
    </blockquote>
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Responsable: </b>{{$encabezado->responsable}}</li>
                <li class="list-inline-item"><b>Del</b> {{date('d/m/Y', strtotime($encabezado->fecha_inicial))}} <b>al</b> {{date('d/m/Y', strtotime($encabezado->fecha_final))}}</li>
                <li class="list-inline-item"><b>Fecha de creación</b> {{date('d/m/Y', strtotime($encabezado->fecha_creacion))}}</li>
                <li class="list-inline-item"><b>Observaciones: </b> {{$encabezado->observaciones}}</li>
            </ul>
        </div>
    </div>

<div id="app"><!--La equita id debe ser app, como hemos visto en app.js-->
    <editarliquidacion-component></editarliquidacion-component><!--Añadimos nuestro componente vuejs-->
</div>

<!-- Modal -->
<div class="modal fade" id="liquidacionModal" tabindex="-1" aria-labelledby="liquidacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="liquidacionModalLabel">Finalizar liquidación</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <li>Una vez finalice la liquidación no podra agregar o eliminar gastos a la misma.</li>
                <li>Las liquidaciones sin finalizar no pueden ser aprovadas, favor de finalizar las liquidaciones en espera</li>
                <a class="btn btn-sm btn-warning btn-block" href="{{route('cam_estado',$id)}}">Finalizar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nuevaLiquidacion" aria-labelledby="nuevaLiquidacionLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar encabezado de liquidación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nit-tab" data-toggle="tab" data-target="#nit" type="button" role="tab"
                        aria-controls="nit" aria-selected="true">Liquidación</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="nit" role="tabpanel" aria-labelledby="nit-tab">
                        <form class="needs-validation" novalidate method="post" action="{{url('edi_enca_li',$id)}}">
                        {{csrf_field()}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><b>Nombre responsable</b></label>
                                    <input type="text" class="form-control" name="responsable" placeholder="Nombre del responsable" aria-label="responsable"
                                    value="{{$encabezado->responsable}}" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Fecha inicial</b></label>
                                    <input type="date" class="form-control" name="fecha_inicial" aria-label="fecha_inicial" value="{{$encabezado->fecha_inicial}}" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Fecha final</b></label>
                                    <input type="date" class="form-control" name="fecha_final" aria-label="fecha_final" value="{{$encabezado->fecha_final}}" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><b>Obersavaciones</b></label>
                                    <textarea type="text" class="form-control" name="observaciones" aria-label="observaciones"
                                    value="{{$encabezado->observaciones}}" required>{{$encabezado->observaciones}}</textarea>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-info btn-block" id="guardar">Guardar cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
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
@endsection
