@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ asset('js/placas.js') }}" defer></script>
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
<div class="container-fluid" id="boton">
@foreach($tran as $t)
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="nav-item">
			    <a class="nav-link bg-light" href="javascript:history.go(-1)"><i class="fas fa-arrow-left"></i> Atrás</a>
            </li>
            <li class="nav-item">
	 		    <a class="nav-link" id="active"><i class="fas fa-eye"></i> Transferencia número {{$t->num_movi}}</a>
            </li>
            @if(Auth::id() == 44 || Auth::id() == 6)
            <li class="nav-item">
                <a class="nav-link bg-light" id="export-btn" href="{{route('EdTran',$id)}}"><i class="fas fa-edit"></i> Editar transferencia</a>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link btn-dark" data-toggle="modal" data-target="#Transferencia" id="modelg">
                    <i class="fas fa-pencil-alt"></i> Modificar encabezado
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link btn-warning" id="modalhis" data-toggle="modal" data-target="#Historial">
                    <i class="fas fa-history"></i> Historial de cambios
                </button>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link bg-light" href="{{route('PTran',$id)}}"><i class="fas fa-print"></i> Imprimir</a>
            </li>
	    </ul>
    </div>
	<div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <b>Número de transferencia:</b>  {{$t->num_movi}}
                </div>
                <div class="col">
                    <b>Sucursal:</b> {{$t->nombre}}
                </div>
                <div class="col">
                    <b>Descripción:</b> {{$t->descripcion}}
                </div>
                <div class="col">
                    <b>Observación:</b> {{$t->observacion}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}
                </div>
                <div class="col">
                    <b>Comentario:</b> {{$t->comentario}}
                </div>
                @if($t->id_estado == 13)
                <div class="col" style="background:#940000;color:white;">
                    Estado: {{$t->estado}}
                </div>
                @elseif($t->id_estado == 14)
                <div class="col" style="background:#a8e4a0;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 15)
                <div class="col" style="background:#dcd0ff;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 16 || $t->id_estado == 17)
                <div class="col" style="background:#87cefa;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado >= 18 || $t->id_estado <= 19)
                <div class="col" style="background:#ffb347;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 20)
                <div class="col" style="background:#f984ef;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @endif
                <div class="col">
                    <b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}
                </div>
                <div class="col">
                    <b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}
                </div>
                <div class="col">
                    @if($t->fechaUno == '')
                    <b>Fecha preparando carga:</b>
                    @else
                    <b>Fecha preparando carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}
                    @endif
                </div>
                <div class="col">
                    @if($t->fecha_enCarga == '')
                    <b>Fecha carga preparada:</b>
                    @else
                    <b>Fecha carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @if($t->fechaSalida == '')
                    <b>Fecha de salida:</b>
                    @else
                    <b>Fecha de salida: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSalida))}}
                    @endif
                </div>
                <div class="col">
                    <b>Creada por: </b> {{$t->usuario}}
                </div>
                <div class="col">
                    <b>Revisada por: </b> {{$t->usuarioSupervisa}}
                </div>
                <div class="col">
                    <b>Grupo que cargo:</b> {{$t->grupoCarga}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Integrantes del grupo: </b>
                </div>
                @foreach($integra as $in)
                <div class="col">
                    {{$in->nombre}}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <br>
    @endforeach
    <div class="table-responsive-sm">
	    <table class="table table-sm" id="existencia">
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
                    <td>{{utf8_encode($pro->nombre_corto)}}</td>
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
<div class="modal fade" id="Transferencia" aria-labelledby="transferenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modificar transferencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" novalidate method="post" action="{{url('adminEd',$id)}}">
                        {{csrf_field()}}
                        <!--<div class="row">
                            <div class="col">
                                <label for="validationCustom01"><b>Observación</b></label>
                                <textarea class="form-control" id="validationCustom01" name="observacion" placeholder="¡ Favor de llenar este campo !"
                                required>{{$t->observacion}}</textarea>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col">
                                <label for="validationCustom02"><b>Descripción</b></label>
                                <textarea type="text" class="form-control" id="validationCustom02" name="descripcion" required>{{$t->descripcion}}</textarea>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="validationCustom03"><b>Placas</b></label>
                                <select id="placa" name="placa" class="form-control" required style="width: 100%">
                                <option value="{{$t->placa_vehiculo}}">{{$t->placa_vehiculo}} {{$t->propietario}}</option>
                                </select>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="validationCustom01"><b>Comentario</b></label>
                                <textarea type="text" class="form-control" id="validationCustom01" name="comentario" required>{{$t->comentario}}</textarea>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="validationCustom02"><b>Referencia</b></label>
                                <textarea type="text" class="form-control" id="validationCustom02" name="referencia" required>{{$t->referencia}}</textarea>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="validationCustom02"><b>Estado de transferencia</b></label>
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
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="validationCustom01"><b>Fecha para carga:</b></label>
                                 <input type="datetime-local" class="form-control" id="validationCustom01" name="fechaCarga"
                                 value="{{date('Y-m-d\TH:i',strtotime($t->fecha_paraCarga))}}" required>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="inputFechaEntrega"><b>Fecha de entrega:</b></label>
                                <input type="date" class="form-control" id="inputFechaEntrega" name="fechaEntrega"
                                value="{{date('Y-m-d',strtotime($t->fechaEntrega))}}" required>
                                <div class="valid-feedback">
                                    Excelente!
                                </div>
                                <div class="invalid-feedback">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-success btn-block" type="submit"><b><i class="fas fa-save"></i> Guardar cambios</b></button>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cancelar</button>
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
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Fecha entrega</th>
                    <th>Fecha modificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historial as $h)
                <tr>
                    <td>{{$h->actividad}}</td>
                    <td>{{date('d/m/Y',strtotime($h->created_at))}}</td>
                    <td>{{date('d/m/Y H:i:s',strtotime($h->updated_at))}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function (){
   var table = $('#existencia').DataTable({
      pageLength: 50,
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
        { targets: [0,1,2,3,4,5], searchable: true },
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

            var numFormat = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
               numFormat(kilosTotal);
            var Format = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
               Format(volumenTotal);
            pageTotal = api.column(6,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            }, 0);
            $(api.column(0).footer() ).html('Totales');
               $(api.column(5).footer()).html(Format(kilosTotal / 1000)+' Toneladas');
               $(api.column(6).footer()).html(numFormat(volumenTotal)+' Volumen');
        },


   });
   // Handle form submission event
   $('#frm-example2').on('submit', function(e){
      // Prevent actual form submission
      e.preventDefault();

      // Serialize form data
      var data = table.$('input,input,input').serializeArray();

      // Include extra data if necessary
      // data.push({'name': 'extra_param', 'value': 'extra_value'});

      // Submit form data via Ajax
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{url('actuTran')}}",
            type: 'post',
            data: data,
            success: function(data){
            console.log('Server response', data);
         }
      });
   });
});
</script>
@endsection
