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
                @if($t->cod_unidad == Auth::user()->sucursal)
                <a class="nav-link" href="{{route('finalizadas_transf')}}">Atrás</a>
                @else
                <a class="nav-link" href="{{route('trans_ot_sucursales')}}">Atrás</a>
                @endif
                <a class="nav-link active">Transferencia número {{$t->num_movi}}</a>
                <div class="dropdown">
                    <button class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Opciones transferencia
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button type="button" class="dropdown-item" data-toggle="modal" data-target="#staticBackdrop" id="modalb">
                            Verificar transferencia
                        </button>
                        <a class="dropdown-item" href="{{route('regre_sucursal',$id)}}">Permitir cambios a sucursal</a>
                        <a class="dropdown-item" href="{{route('PTran',$id)}}">Imprimir transferencia</a>
                        <a class="dropdown-item" href="{{route('histo_transf',$id)}}">Historial de transferencias</a>
                        <button type="button" class="dropdown-item" data-toggle="modal" data-target="#Transferencia">
                            Modificar encabezado
                        </button>
                    </div>
                </div>
                @if($t->id_estado == 20)
                <a class="nav-link" href="{{route('imag_trans',$id)}}">Ver imágenes</a>
                @else
                @endif
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
    </nav>
<div class="container-fluid" >
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$t->num_movi}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}}</li>
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Observación:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                <li class="list-inline-item" style="background:#f984ef;"><b>Estado:</b> {{$t->estado}} {{number_format($t->porcentaje,2)}}%</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Fecha aprox. entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</li>
                @if($t->fechaUno == '')
                <li class="list-inline-item"><b>Peparando carga:</b></li>
                @else
                <li class="list-inline-item"><b>Peparando carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}</li>
                @endif
                @if($t->fecha_enCarga == '')
                <li class="list-inline-item"><b>Carga preparada:</b></li>
                @else
                <li class="list-inline-item"><b>Carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}</li>
                @endif
                @if($t->fecha_cargado == '')
                <li class="list-inline-item"><b>Fecha de carga:</b></li>
                @else
                <li class="list-inline-item"><b>Fecha de carga: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_cargado))}}</li>
                @endif
                @if($t->fechaSalida == '')
                <li class="list-inline-item"><b>Fecha de salida:</b></li>
                @else
                <li class="list-inline-item"><b>Fecha de salida: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSalida))}}</li>
                @endif
                <li class="list-inline-item"><b>Fecha en sucursal: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_entregado))}}</li>
                <li class="list-inline-item"><b>Observación Supervisor: </b>{{$t->observacionSup}} </li>
                <li class="list-inline-item"><b>Fecha finalizada: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSucursal))}}</li>
                <li class="list-inline-item"><b>Creada por: </b> {{$t->usuario}}</li>
                <li class="list-inline-item"><b>Observación Sucursal: </b>{{$t->observacionSucursal}}</li>
                <li class="list-inline-item"><b>Revisada por: </b> {{$t->usuarioSupervisa}}</li>
                <li class="list-inline-item"><b>Grupo que cargo:</b> {{$t->grupoCarga}}</li>
                <li class="list-inline-item"><b>Validada por: </b> {{$t->name}}</li>
                <li class="list-inline-item"><b>Sale de: </b>{{$t->usale, $t->bsale}}</li>
                <li class="list-inline-item"><b>Integrantes del grupo: </b>@foreach($integra as $integra){{$integra->nombre}} @endforeach</li>
                @if($t->erroresVerificados == 1)
                <li class="list-inline-item"><b>Observaciones de la correccion: </b> {{$t->observacionRevision}}</li>
                @else
                @endif
            </ul>
            @endforeach
        </div>
    </div>

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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless display nowrap" id="existencia" style="width:100%">
            <thead >
                <tr>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad Solicitada</th>
                    <th>Cantidad Enviada</th>
                    <th>Cantidad Recibida</th>
                    <th style="display:none;">NoIncluido</th>
                    <th>Mal estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $pro)
                @if($pro->id == null)
                @else
                <tr>
                    <td>{{$pro->nombre}}</td>
                    <td style="text-transform: uppercase;">{{$pro->nombre_corto}}</td>
                    <td>{{$pro->nombre_fiscal}}</td>
                    <td>{{number_format($pro->cantidadSolicitada,0)}}</td>
                    <td>{{number_format($pro->cantidad1,0)}}</td>
                    <td>{{number_format($pro->cantidad,0)}}</td>
                    <td style="display:none;">{{number_format($pro->noIncluido)}}</td>
                    <td>{{number_format($pro->mal_estado)}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Verificar transferencia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{url('Vtrans',$id)}}">
        {{csrf_field()}}
            <div class="form-group">
              <label for="textareaObservaciones"><b>Observaciones</b></label>
              <textarea class="form-control" name="observaciones" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Confirmar</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Verificar transferencia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{url('Vtrans',$id)}}">
        {{csrf_field()}}
            <div class="form-group">
              <label for="textareaObservaciones"><b>Observaciones</b></label>
              <textarea class="form-control" name="observaciones" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Confirmar</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
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
                <form class="needs-validation" novalidate method="post" action="{{url('edencdes',$id)}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="validationCustom02"><b>Descripción</b></label>
                            @error('descripcion')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <textarea type="text" class="form-control" id="validationCustom02" name="descripcion" required>{{$t->descripcion}}</textarea>
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
                        <div class="form-group col-md-12">
                            <label for="validationCustom01"><b>Observación</b></label>
                            @error('observacionSucursal')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <textarea class="form-control" id="validationCustom01" name="observacionSucursal" placeholder="¡ Favor de llenar este campo !"
                                required>{{$t->observacionSucursal}}</textarea>
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
    scrollX: true,
    scrollY: '60vh',
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
    dom: 'Bfrtip',
    lengthMenu: [
        [ 100, 200, 300, -1 ],
        [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {   extend: 'pageLength',
            className: 'btn btn-dark',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {   extend: 'colvis',
            className: 'btn btn-dark',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {   extend: 'excelHtml5',
            className: 'btn btn-dark',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true
        }
    ],
    "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,3], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if(data[4] == data[5]){

        }
        else{
			$($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#fa8072)");
		    $($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#fa8072)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#fa8072)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#fffafa,#fa8072)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa,#fa8072)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#fffafa,#fa8072)");
        }
    }
});
</script>
@endsection
