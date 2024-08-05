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
                @if($t->id_estado < 18)
                <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
                @elseif($t->id_estado == 18)
                <a class="nav-link" href="{{route('despacho_transf')}}">Atrás</a>
                @elseif($t->id_estado > 18)
                <a class="nav-link" href="{{route('finalizadas_transf')}}">Atrás</a>
                @endif
                <a class="nav-link">Transferencia número {{$t->num_movi}}</a>
                <a class="nav-link" href="{{route('PTran',$id)}}">Imprimir transferencia</a>
                @if($t->id_estado == 19 && $t->id_estado)
                <div class="dropdown">
                    <button class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Opciones transferencia
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('agre_datos_transf',$id)}}">Agregar productos</a>
                        <a class="dropdown-item" href="{{route('validad_tranf',$id)}}">Validar transferencia</a>
                        <a class="dropdown-item" href="{{route('PTran',$id)}}">Imprimir transferencia</a>
                        <button type="button" class="dropdown-item btn-warning" id="modalhis" data-toggle="modal" data-target="#Historial">
                            Historial de cambios
                        </button>
                    </div>
                </div>
            @endif
            @if($t->id_estado >= 18)
                <button type="button" class="nav-link" data-toggle="modal" data-target="#Transferencia">
                    Modificar encabezado
                </button>
                <a type="button" class="nav-link" href="{{route('histo_transf',$id)}}">Historial de transferencias</a>
                <a class="nav-link" href="{{route('anultransf',$id)}}" onclick="return confirm('Está seguro que desea anular la orden?')">Anular transferencia</a>
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
    </nav>
<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia: </b>{{$t->num_movi}}</li>
                <li class="list-inline-item"><b>Sucursal:</b> {{$t->nombre}} {{$t->bodega}}</li>
                <li class="list-inline-item"><b>Descripción:</b> {{$t->observacion}}</li>
                <li class="list-inline-item"><b>Observación:</b> {{$t->descripcion}}</li>
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}
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
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</li>
                <li class="list-inline-item"><b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}</li>
                <li class="list-inline-item"><b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</li>
                @if($t->fechaUno == '')
                <li class="list-inline-item"><b>Fecha preparando carga:</b></li>
                @else
                <li class="list-inline-item"><b>Fecha preparando carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}</li>
                @endif
                @if($t->fecha_enCarga == '')
                <li class="list-inline-item"><b>Fecha carga preparada:</b></li>
                @else
                <li class="list-inline-item"><b>Fecha carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}</li>
                @endif
                @if($t->fechaSalida == '')
                <li class="list-inline-item"><b>Fecha de salida:</b></li>
                @else
                <li class="list-inline-item"><b>Fecha de salida: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSalida))}}</li>
                @endif
                <li class="list-inline-item"><b>Creada por: </b> {{$t->usuario}}</li>
                <li class="list-inline-item"><b>Revisada por: </b> {{$t->usuarioSupervisa}}</li>
                <li class="list-inline-item"><b>Grupo que cargo:</b> {{$t->grupoCarga}}</li>
                <li class="list-inline-item"><b>Sale de: </b> {{$t->usale}}, {{$t->bsale}}</li>
                <li class="list-inline-item"><b>Integrantes del grupo: </b>@foreach($integra as $integra){{$integra->nombre}} @endforeach</li>
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
    <div class="table-responsive-sm">
	    <table class="table table-sm table-borderless display nowrap" id="existencia" style="width:100%">
		    <thead >
			    <tr>
				    <th>Categoria</th>
			        <th>Código</th>
			        <th>Producto</th>
                    <th>Cantidad a enviar</td>
                    <th>Bultos</th>
                    <th>Peso Kg.</th>
                    <th>Volumen</th>
			    </tr>
            </thead>
            <tbody>
                @foreach($productos as $pro)
                @if($pro->id == null)
                @else
                <tr>
                    <td>{{$pro->nombre}}</td>
                    <td style="text-transform: uppercase;">{{utf8_encode($pro->nombre_corto)}}</td>
                    <td>{{$pro->nombre_fiscal}}</td>
                    <td>{{($pro->cantidad)}}</td>
                    <td>{{$pro->costo}}</td>
                    <td>{{number_format($pro->peso,2)}}</td>
                    <td>{{number_format($pro->volumen,2)}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
	    </table>
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
    scrollX: true,
    scrollY: '58vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
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
    ],
    "footerCallback": function(row,data,start,end,display){
        var api = this.api(), data;
        var intVal = function(i){
            return typeof i == 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i == 'number' ?
                i:0;
            };
            var kilosTotal = api
            .column(5)
            .data()
            .reduce(function (a,b){
                return intVal(a) + intVal(b);
            }, 0);
            var volumenTotal = api
            .column(6)
            .data()
            .reduce(function (a,b){
                return intVal(a) + intVal(b);
            }, 0);

            pageTotal = api.column(6,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            }, 0);
            $(api.column(0).footer() ).html('Totales');
            $(api.column(5).footer()).html(parseFloat(kilosTotal/ 1000).toFixed(3)+' Toneladas');
            $(api.column(6).footer()).html(parseFloat(volumenTotal).toFixed(3)+' Volumen');
        },
    });

</script>
<script>
function anular() {
  let text;
  if (confirm("Desea anular esta orden?") == true) {
    text = "La orden se está anulando...!";
  } else {

    text = "La orden no se anulo!";
  }
  document.getElementById("texto").innerHTML = text;
}
</script>
@endsection
