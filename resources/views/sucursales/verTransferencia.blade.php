@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
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
    <ul class="nav nav-tabs">
        <li class="nav-item">
			<a class="nav-link bg-light" href="{{route('tran_su')}}"><b><i class="fas fa-arrow-left"></i> Atrás</b></a>
        </li>
        <li class="nav-item">
	 		<a class="nav-link" id="active"><i class="fas fa-eye"></i> Transferencia número {{$t->num_movi}}</a>
        </li>
        @if($t->id_estado == 18)
        <li class="nav-item">
            <button type="button" class="nav-link btn-dark" data-toggle="modal" data-target="#Transferencia">
                <i class="fas fa-check-circle"></i> Modificar encabezado
            </button>
        </li>
        @elseif($t->id_estado == 20)
        <li class="nav-item">
            <a class="nav-link bg-light" href="{{route('PTran',$id)}}"><b><i class="fas fa-print"></i> Imprimir</b></a>
        </li>
        @else
        @endif
	</ul>
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
                    <b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}
                </div>
                <div class="col">
                    <b>Vehículo:</b> {{$t->placa_vehiculo}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Descripción:</b> {{$t->descripcion}}
                </div>
                <div class="col">
                    <b>Observación:</b> {{$t->observacion}}
                </div>
                <div class="col">
                    <b>Comentario:</b> {{$t->comentario}}
                </div>
                @if($t->id_estado == 18)
                <div class="col" style="background:#87cefa;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 19)
                <div class="col" style="background:#ffb347;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 20)
                <div class="col" style="background:#f984ef;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col">
                    <b>Fecha aprox. entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}
                </div>
                <div class="col">
                    @if($t->fechaSalida == '')
                    <b>Fecha de salida:</b>
                    @else
                    <b>Fecha de salida: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSalida))}}
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Creada por: </b> {{$t->usuario}}
                </div>
                <div class="col">
                    <b>Revisada por: </b> {{$t->usuarioSupervisa}}
                </div>
                <div class="col">
                    <b>Grupo que cargo:</b> {{$t->grupoCarga}}
                </div>
                <div class="col">
                    <b>Integrantes del grupo: </b>
                </div>
            </div>
            <div class="row">
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
	<table class="table table-sm" id="existencia">
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
<div class="modal fade" id="Transferencia" tabindex="-1" aria-labelledby="TransferenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TransferenciaModalLabel">Confirmar transferencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('ed_tran',$id)}}">
                {{csrf_field()}}
                    <div class="row">
                        <div class="col">
                            <label for="validationCustom01"><b>Observación</b></label>
                            <textarea class="form-control" id="validationCustom01" name="observacion" placeholder="¡ Favor de llenar este campo !"
                                required>{{$t->observacionSucursal}}</textarea>
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
                                <option value="{{$e->id}}">{{$e->nombre}}</option>
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
                    <br>
                    <button class="btn btn-success btn-block" type="submit"><b><i class="fas fa-save"></i> Guardar cambios</b></button>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (){
   var table = $('#existencia').DataTable({
    pageLength: 100,
    serverSide: false,
    searching: true,
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
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
    ],

   });
});
</script>
@endsection
