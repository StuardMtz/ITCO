@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
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
	 		    <a class="nav-link"> Transferencia número {{$id}}</a>
                @if($t->cod_unidad == Auth::user()->sucursal)
                <button type="button" class="nav-link" data-toggle="modal" data-target="#autorizarModal">
                    Autorizar transferencia
                </button>
                @else
                @endif
				<a class="nav-link" href="{{route('PTranSu',$id)}}">Imprimir transferencia</a>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
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
                </ul>
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

<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$t->id}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}} {{$t->bodega}}</li>
                @if($t->id_estado == 13 && $t->id_usuarioRecibe == null)
                <li class="list-inline-item" style="background:#940000;color:white;"><b>Estado: </b>{{$t->estado}}</li>
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
                <li class="list-inline-item"><b>Referencia: </b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Solicitada por: </b> {{$t->usuario}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>No. factura:</b> {{$t->numeroFactura}}</li>
                <li class="list-inline-item"><b>Serie:</b> {{$t->serieFactura}}</li>
            </ul>
        </div>
    </div>

    @endforeach
    <div class="table-responsive-sm">
	    <table class="table table-sm table-borderless display nowrap" id="existencia" style="width:100%">
		    <thead >
			    <tr>
				    <th>Categoria</th>
			        <th>Código</th>
			        <th>Producto</th>
                    <th>Cantidad</td>
			    </tr>
            </thead>
            <tbody>
                @foreach($productos as $pro)
                @if($pro->id == null)
                @else
                <tr>
                    <td>{{$pro->nombre}}</td>
                    <td>{{utf8_encode($pro->nombre_corto)}}</td>
                    <td>{{$pro->nombre_fiscal}}</td>
                    <td>{{($pro->cantidad)}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
	    </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="autorizarModal" tabindex="-1" aria-labelledby="autorizarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="autorizarModalLabel">Autorizar transferencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Autoriza realizar la presente transferencia de productos hacia su bodega?</p>
                <form class="needs-validation" novalidate method="post" action="{{url('EdETranS',$id)}}">
                {{csrf_field()}}
                    <div class="form-group col-md-12">
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
                    <div class="form-group col-md-12">
                            <label for="validationCustom02"><b>Estado de transferencia</b></label>
                            @error('estado')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <select class="form-control" name="estado" required>
                                <option value="20" class="form-control bg-warning"><b><i class="fas fa-exclamation-circle"></i> Finalizada</b></option>
                            </select>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-dark">Permitir transferencia</button>
                    </div>
                </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>
<script>
var table = $('#existencia').DataTable({
    pageLength: 100,
    serverSide: false,
    "order": [[ 0,"asc"]],
    scrollX: true,
    scrollY: '75vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "language": {
        "lengthMenu": "<span class='text-paginate'>Mostrar _MENU_ registros</span>",
        "zeroRecords": "No se encontraron resultados",
        "EmptyTable":     "Ningún dato disponible en esta tabla =(",
        "info": "<span class='text-paginate'>Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros</span>",
        "infoEmty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "InfoPostFix":    "",
        "search": "<span class='text-paginate'>Buscar</span>",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "paginate": {
            "First": "Primero",
            "Last": "Último",
            "next": "Siguiente",
            "previous": "Anterior",
        },
    },
    "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,3], searchable: true },
        { targets: '_all', searchable: false },
    ]
});

</script>
@endsection
