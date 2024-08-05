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
    <ul class="nav nav-tabs">
        @if($t->id_estado < 17)
        <li class="nav-item">
			<a class="nav-link bg-light" href="{{route('home')}}"><b><i class="fas fa-arrow-left"></i> Atrás</b></a>
        </li>
        @elseif($t->id_estado >= 18 && $t->id_estado <= 20)
        <li class="nav-item">
			<a class="nav-link bg-light" href="{{route('FBTran',$id)}}"><b><i class="fas fa-arrow-left"></i> Atrás</b></a>
        </li>
        @else
		<li class="nav-item">
			<a class="nav-link bg-light" href="{{route('EdTran',$id)}}"><b><i class="fas fa-arrow-left"></i> Atrás</b></a>
        </li>
        @endif
        <li class="nav-item">
	 		<a class="nav-link active bg-info" id="export-btn"><b><i class="fas fa-eye"></i> Transferencia número {{$t->num_movi}}</b></a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-light" href="{{route('PTran',$id)}}"><b><i class="fas fa-print"></i> Imprimir</b></a>
        </li>
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
                <div class="col">
                    <b>Estado:</b> {{$t->estado}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}
                </div>
                <div class="col">
                    @if($t->fechaUno == '')
                    <b>Preparando carga:</b>
                    @else
                    <b>Preparnado carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}
                    @endif
                </div>
                <div class="col">
                    @if($t->fecha_enCarga == '')
                    <b>Carga preparada:</b>
                    @else
                    <b>Carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}
                    @endif
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
                <th>Cantidad a enviar</td>
                <th>Peso</th>
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
            </tr>
        </tfoot>
	</table>
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
            .column(4)
            .data()
            .reduce(function (a,b){
                return intVal(a) + intVal(b);
            }, 0);

            var volumenTotal = api
            .column(5)
            .data()
            .reduce(function (a,b){
                return intVal(a) + intVal(b);
            }, 0);

            var numFormat = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
               numFormat(kilosTotal);
            var Format = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
               Format(volumenTotal);
            pageTotal = api.column(5,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            }, 0);
            $(api.column(0).footer() ).html('Totales');
               $(api.column(4).footer()).html(Format(kilosTotal / 1000)+' Toneladas');
               $(api.column(5).footer()).html(numFormat(volumenTotal)+' Volumen');
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
