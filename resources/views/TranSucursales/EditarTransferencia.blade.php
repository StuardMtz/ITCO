@extends('layouts.app')
@section('content')
<script src="{{asset('js/productosmanualSuc.js')}}" defer></script>

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
            @foreach($tran as $t)
                <a class="nav-link" href="{{route('transferencias_en_espera')}}">Atrás</a>
                <a class="nav-link">Editando transferencia</a>
                <div class="dropdown">
                    <button class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Opciones transferencia
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('VeTranSuc',$id)}}">Ver transferencia</a>
                        <a class="dropdown-item" href="{{route('PTranSu',$id)}}">Imprimir transferencia</a>
                        {{-- <a class="dropdown-item" href="{{route('noti_transf',$id)}}">Notificar atraso de transferencia</a> --}}
                    </div>
                </div>
                <button type="button" class="nav-link" data-toggle="modal" data-target="#Transferencia">
                    Modificar encabezado
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
    <form class="needs-validation" novalidate method="post" action="{{route('agregar_producto_msucursal',$id)}}">
    {{csrf_field()}}
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$id}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}} {{$t->bodega}}</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Referencia: </b>{{$t->observacion}}</li>
                @if($t->id_estado == 13 && $t->id_usuarioRecibe == null)
                <li class="list-inline-item" style="background:#940000;color:white;"><b>Estado: </b> {{$t->estado}}</li>
                @elseif($t->id_estado == 13 && $t->id_usuarioRecibe != null)
                <li class="list-inline-item" style="background:#a8e4a0;"><b>Estado:</b> Autorizada</li>
                @elseif($t->id_estado == 15)
                <li class="list-inline-item" style="background:#dcd0ff;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado == 16 || $t->id_estado == 17)
                <li class="list-inline-item" style="background:#87cefa;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado >= 18 || $t->id_estado <= 19)
                <li class="list-inline-item" style="background:#ffb347;"><b>Estado:</b> {{$t->estado}}</li>
                @elseif($t->id_estado == 20)
                <li class="list-inline-item" style="background:#f984ef;"><b>Estado:</b> {{$t->estado}}</li>
                @endif
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Sale sucursal: </b>{{$t->sale}}</li>
                <li class="list-inline-item"><b>Sale bodega: </b>{{$t->bsale}}</li>
                <li class="list-inline-item"><b>No. factura: </b>{{$t->numeroFactura}} <b>Serie: </b>{{$t->serieFactura}}</li>
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
    <div id="app"><!--La equita id debe ser app, como hemos visto en app.js-->
        <productossucursales-component></productossucursales-component><!--Añadimos nuestro componente vuejs-->
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
                <form class="needs-validation" novalidate method="post" action="{{url('EdETranS',$id)}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="validationCustom02"><b>Descripción</b></label>
                            @error('descripcion')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <textarea type="text" class="form-control" id="validationCustom02" name="observacion"  required>{{$t->observacion}}</textarea>
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
                            <label for="validationCustom02"><b>Estado de transferencia</b></label>
                            @error('estado')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <select class="form-control" name="estado" required>
                                <option value="{{$t->id_estado}}" class="form-control bg-warning"><b><i class="fas fa-exclamation-circle"></i> {{$t->estado}}</b></option>
                                @foreach($estados as $e)
                                @if($t->id_usuarioRecibe != '' && $t->id_estado > $e->id)
                                <option value="{{$e->id}}">{{$e->nombre}}</option>
                                @elseif($t->id_usuarioRecibe != '' && $t->id_estado < $e->id)
                                <option value="{{$e->id}}">{{$e->nombre}}</option>
                                @elseif($t->id_estado > 14)
                                @endif
                                @endforeach
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
                            <textarea type="text" class="form-control" id="validationCustom02" name="descripcion"  required>{{$t->descripcion}}</textarea>
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
                            <label><b>Sale de Sucursal</b></label>
                            <select id="sasucursal" name="saleDe" class="form-control" required>
                                <option value="{{$t->cod_unidad}}" hidden >{{$t->sale}}</option>
                                @foreach($saleDe as $sd)
                                <option value="{{$sd->cod_unidad}}">{{$sd->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label><b>Sale de Bodega</b></label>
                            <select id="bodegasa" name="saleBo" class="form-control" required>
                                <option value="{{$t->cod_bodega}}" hidden >{{$t->bsale}}</option>
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
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
                    <div class="form-row">
                        <div class="form-group col-md-6" id="divserie">
                            <label><b>Serie factura</b></label>
                            <input class="form-control" type="text" value="{{$t->serieFactura}}" id="noserie" name="serie">
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="divfactura">
                            <label><b>Número factura</b></label>
                            <input type="number" min="1" class="form-control" value="{{$t->numeroFactura}}" placeholder="Número factura" name="numero" id="factura">
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
        $('#sasucursal').on('change', onSelectSucursaleChange);
    });
    function onSelectSucursaleChange(){
        var cod_unidad = $(this).val();
        if(! cod_unidad){
            $('#bodegasa').html('<option value ="">Seleccione una opcion</option>');
            return;
        };

    $.get(url_global+'/select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].observacion+'</option>';
        $('#bodegasa').html(html_select);
    });
}
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
