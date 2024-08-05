@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    @foreach($tran as $t)
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
			    <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
                <a class="nav-link">Transferencia número {{$t->num_movi}}</a>
                <div class="dropdown">
                    <button class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Opciones transferencia
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('PTranSu',$id)}}">Imprimir transferencia</a>
                        <!--<button type="button" class="dropdown-item" data-toggle="modal" data-target="#Transferencia">
                            Modificar encabezado
                        </button> -->
                    </div>
                </div>
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
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Referencias:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}</li>
                <li class="list-inline-item" style="background:#f984ef;"><b>Estado:</b> {{$t->estado}}</li>
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Fecha finalizada: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSucursal))}}</li>
                <li class="list-inline-item"><b>Creada por: </b> {{$t->usuario}}</li>
                <li class="list-inline-item"><b>No. Factura:</b> {{$t->numeroFactura}}</li>
                <li class="list-inline-item"><b>Serie factura:</b> {{$t->serieFactura}}</li>
                <li class="list-inline-item"><b>Cliente:</b> {{$t->cliente}}</li>
                <li class="list-inline-item"><b>Sale sucursal: </b>{{$t->sale}}</li>
                <li class="list-inline-item"><b>Sale bodega: </b>{{$t->bsale}}</li>
            </ul>
        </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive-sm">
                <table class="table table-sm table-borderless" id="existenciaI">
                    <thead>
                        <tr>
                            <th colspan="4">Sale de {{$t->bsale}}</th>
                        </tr>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Sale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productosE as $pro)
                        <tr>
                            <td>{{$pro->nombre_corto}}</td>
                            <td>{{$pro->nombre_fiscal}}</td>
                            <td>{{number_format($pro->cantidad1,0)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive-sm">
                <table class="table table-sm table-borderless" id="existencia">
                    <thead>
                        <tr>
                            <th colspan="4">Ingresa en {{$t->nombre}}</th>
                        </tr>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Ingresa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productosI as $pro)
                        <tr>
                            <td>{{$pro->nombre_corto}}</td>
                            <td>{{$pro->nombre_fiscal}}</td>
                            <td>{{number_format($pro->cantidad1,0)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
    scrollY: '75vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
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
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if(data[4] == data[5]){

        }
        else{
			$($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#fa8072)");
		    $($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#fa8072)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#fa8072)");
        }
    }
});
</script>
<script>
var table = $('#existenciaI').DataTable({
    pageLength: 100,
    "order": [[ 0,"asc"]],
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
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if(data[4] == data[5]){

        }
        else{
			$($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#fa8072)");
		    $($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#fa8072)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#fa8072)");
        }
    }
});
</script>
@endsection
