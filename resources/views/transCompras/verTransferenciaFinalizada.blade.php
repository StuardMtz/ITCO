@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script src="{{ asset('js/placas.js') }}" defer></script>

    @foreach($tran as $t)
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="javascript: history.go(-1)">Atrás</a>
                <a class="nav-link active" id="active">Transferencia número {{$t->num_movi}}</a>
                <div class="dropdown">
                    <a class="nav-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Opciones transferencia
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <!--<a class="dropdown-item" href="{{route('transc_ver',$id)}}">Validar transferencia</a>-->
                        <a class="dropdown-item" href="{{route('transc_impriPDF',$id)}}">Imprimir transferencia</a>
                        <a class="dropdown-item" href="{{route('histo_transf',$id)}}">Historial de transferencias</a>
                        <!--<button type="button" class="dropdown-item" data-toggle="modal" data-target="#Transferencia">
                            Modificar encabezado
                        </button> -->
                    </div>
                </div>
                @if($t->id_estado == 18 || $t->id_estado == 19)
                <a class="nav-link" id="modalb" data-toggle="modal" data-target="#Transferencia">
                    Modificar encabezado
                </a>
                @endif
                <a class="nav-link" href="{{route('anultransfcom',$id)}}" onclick="return confirm('Está seguro que desea anular la orden?')">Anular transferencia</a>
                @if($t->id_estado == 20)
                <a class="nav-link" href="{{route('marcar_correccion',$id)}}">Marcar corrección</a>
                <a class="nav-link" href="{{route('imag_trans',$id)}}">Ver imágenes</a>
                @endif
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
<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$t->num_movi}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}}</li>
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Observación:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                @if($t->erroresVerificados == 1)
                <li class="list-inline-item" style="background-color:#07A6059E;"><b>Estado:</b> {{$t->estado}}</li>
                @else
                <li class="list-inline-item"><b>Estado:</b> {{$t->estado}}</li>
                @endif
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Fecha aprox. entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</li>
                <li class="list-inline-item"><b>Fecha en sucursal: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_entregado))}}</li>
                <li class="list-inline-item"><b>Observación Supervisor: </b>{{$t->observacionSup}}</li>
                <li class="list-inline-item"><b>Creada por: </b> {{$t->usuario}}</li>
                <li class="list-inline-item"<b>Observación Sucursal: </b>{{$t->observacionSucursal}}</li>
                <li class="list-inline-item"<b>Revisada por: </b> {{$t->usuarioSupervisa}}</li>
                <li class="list-inline-item"<b>Sale de: </b>{{$t->usale, $t->bsale}}</li>
            </ul>
            @endforeach
        </div>
    </div>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="existencia">
            <thead >
                <tr>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad solicitada</th>
                    <th>Cantidad enviada</th>
                    <th>Cantidad recibida</th>
                    <th>Mal estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $pro)
                @if($pro->id == null)
                @else
                <tr>
                    <td>{{$pro->nombre}}</td>
                    <td>{{$pro->nombre_corto}}</td>
                    <td>{{$pro->nombre_fiscal}}</td>
                    @if($pro->cantidad1 > $pro->cantidad)
                    <td style="background-color:#D11010B0">{{number_format($pro->cantidadSolicitada,0)}}</td>
                    <td style="background-color:#D11010B0">{{number_format($pro->cantidad1,0)}}</td>
                    <td style="background-color:#D11010B0">{{number_format($pro->cantidad,0)}}</td>
                    <td style="background-color:#D11010B0">{{number_format($pro->mal_estado,0)}}</td>
                    @else
                    <td>{{number_format($pro->cantidadSolicitada,0)}}</td>
                    <td>{{number_format($pro->cantidad1,0)}}</td>
                    <td>{{number_format($pro->cantidad,0)}}</td>
                    <td>{{number_format($pro->mal_estado,0)}}</td>
                    @endif
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
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
                    <form class="needs-validation" novalidate method="post" action="{{url('trc_mod_enca',$id)}}">
                    {{csrf_field()}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="validationCustom02"><b>Descripción</b></label>
                                @error('descripcion')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <textarea type="text" class="form-control" id="validationCustom02" name="descripcion" required>{{$t->observacion}}</textarea>
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
                            <div class="form-group col-md-6">
                                <label for="validationCustom03"><b>Placas</b></label>
                                @error('placa')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <select id="placa" name="placa" class="form-control" required style="width: 100%">
                                <option value="{{$t->placa_vehiculo}}">{{$t->placa_vehiculo}}</option>
                                </select>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="validationCustom02"><b>Estado de transferencia</b></label>
                                @error('estado')
                                    <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                                @enderror
                                <select class="form-control" name="estado" required>
                                    <option value="{{$t->id_estado}}" class="form-control bg-warning"><b><i class="fas fa-exclamation-circle"></i> --> {{$t->estado}} <-- Estado actual</b></option>
                                    @foreach($estados as $e)
                                    @if($t->id_estado > $e->id)
                                    <option value="{{$e->id}}">{{$e->nombre}}  <-- Anterior</option>
                                    @elseif($t->id_estado < $e->id)
                                    <option value="{{$e->id}}">{{$e->nombre}}  Siguiente --> </option>
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
                                <textarea type="text" class="form-control" id="validationCustom02" name="referencia" required>{{$t->referencia}}</textarea>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
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
<script>
var table = $('#existencia').DataTable({
    pageLength: 100,
    serverSide: false,
    "order": [[ 0,"asc"]],
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
