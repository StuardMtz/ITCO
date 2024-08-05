@extends('layouts.app')
@section('content')
<script src="{{ asset('js/propietario.js') }}" defer></script>
<script src="{{asset('js/productosmanual.js')}}" defer></script>

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    @foreach($tran as $t)
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="{{route('transc_inicio')}}">Atrás</a>
            <a class="nav-link" id="active">Editando transferencia</a>
            <div class="dropdown">
                <a class="nav-link"  id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Opciones transferencia
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{route('transc_ver',$id)}}">Verificar transferencia</a>
                    <a class="dropdown-item" href="{{route('transc_impriPDF',$id)}}">Imprimir transferencia</a>
                    <a class="dropdown-item" href="{{route('histo_transf',$id)}}">Historial de cambios</a>
                </div>
            </div>
            <a  class="nav-link" id="modalb" data-toggle="modal" data-target="#Transferencia">
                Modificar encabezado
            </a>
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
                <li class="list-inline-item"><b>Estado:</b> {{$t->estado}}</li>
                <li class="list-inline-item"><b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}</li>
                @if($t->fechaEntrega != '')
                <li class="list-inline-item"><b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</li>
                @else
                <li class="list-inline-item"><b>Fecha de entrega:</b></li>
                @endif
                <li class="list-inline-item"><b>Observación:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                <li class="list-inline-item"><b>Sale de: </b> {{$t->usale}}, {{$t->bsale}}</li>
            </ul>
        </div>
    </div>
        @endforeach
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
                <button class="btn btn-success btn-sm my-2 my-sm-0" type="submit">Agregar</button>
            </nav>
        </form>
        <br>
    </div>
    <div id="app"><!--La equita id debe ser app, como hemos visto en app.js-->
        <editarcomprastransferencia-component></editarcomprastransferencia-component><!--Añadimos nuestro componente vuejs-->
    </div>
    <div class="modal fade" id="Transferencia" aria-labelledby="transferenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modificar transferencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate method="post" action="{{url('trc_edec',$id)}}">
                    {{csrf_field()}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="validationCustom02"><b>Detalles</b></label>
                                @error('descripcion')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <textarea type="text" class="form-control" id="validationCustom02" name="observacion" required>{{$t->descripcion}}</textarea>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="validationCustom01"><b>Comentario</b></label>
                                @error('comentario')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <textarea type="text" class="form-control" id="validationCustom01" name="comentario" required>{{$t->comentario}}</textarea>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="validationCustom03"><b>Placas</b></label>
                                @error('placa')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <select id="placa" name="placa" class="form-control" required style="width: 100%">
                                <option value="{{$t->cod_placa}}">{{$t->placa_vehiculo}}</option>
                                </select>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="validationCustom02"><b>Referencia</b></label>
                                @error('referencia')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <textarea type="text" class="form-control" id="validationCustom02" name="referencia" required>{{$t->referencia}}</textarea>
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
                                <label for="validationCustom01"><b>Fecha para carga:</b></label>
                                @error('fechaCarga')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <input type="datetime-local" class="form-control" id="validationCustom01" name="fechaCarga"
                                value="{{date('Y-m-d\TH:i',strtotime($t->fecha_paraCarga))}}" required>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputFechaEntrega"><b>Fecha de entrega:</b></label>
                                @error('fechaEntrega')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <input type="date" class="form-control" id="inputFechaEntrega" name="fechaEntrega"
                                value="{{date('Y-m-d',strtotime($t->fechaEntrega))}}" required>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-row" id="sucursalbodega">
                            <div class="form-group col-md-6" id="divsucursal">
                                <label><b>Entra a sucursal</b></label>
                                <select name="entraSu" class="form-control" required id="sucursal">
                                    <option value="{{$t->unidad_transf}}" hidden>{{$t->nombre}}</option>
                                    @foreach($entraSu as $su)
                                    <option value="{{$su->cod_unidad}}">{{$su->nombre}}</option>
                                    @endforeach
                                </select>
                                <div class="valid-tooltip">
                                    Muy bien.
                                </div>
                                <div class="invalid-tooltip">
                                    No debes dejar este campo en blanco.
                                </div>
                            </div>
                            <div class="form-group col-md-6" id="divbodega">
                                <label for="nuevo_inventario"><b>Entra a Bodega</b></label>
                                <select class="form-control" id="bodega" name="entraBo" required>
                                    <option value="{{$t->bodega_Transf}}" hidden >{{$t->bodega}}</option>
                                </select>
                                <div class="valid-tooltip">
                                    Muy bien.
                                </div>
                                <div class="invalid-tooltip">
                                    No debes dejar este campo en blanco.
                                </div>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-dark btn-block" type="submit">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="Historial" tabindex="-1" aria-labelledby="Historial" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Historial fecha de entrega</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

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
<script>
    $(function(){
        $('#sucursal').on('change', onSelectSucursalChange);
    });
    function onSelectSucursalChange(){
        var cod_unidad = $(this).val();
        if(! cod_unidad){
            $('#bodega').html('<option value ="">Seleccione una opcion</option>');
            return;
        };

    $.get(url_global+'/select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].observacion+'</option>';
        $('#bodega').html(html_select);
    });
}
</script>
@endsection
