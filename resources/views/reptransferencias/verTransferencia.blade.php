@extends('layouts.app2')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/timeline.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script>
    var url_global='{{url("/")}}';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ asset('js/placas.js') }}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
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
@foreach($tran as $t)
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link"  onclick="window.close();">Atrás</a>
	 		    <a class="nav-link active">Transferencia número {{$t->num_movi}}</a>
                <button type="button" class="nav-link" data-toggle="modal" data-target="#exampleModal">
                    Linea de tiempo
                </button>
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
<div class="container-fluid">
	<div class="card">
        <div class="row">
            <div class="col">
                <b>Número de transferencia:</b>  {{$t->num_movi}}
            </div>
            <div class="col">
                <b>Sucursal:</b> {{$t->nombre}}
            </div>
            <div class="col">
                <b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}
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
        </div>
        <div class="row">
            <div class="col" style="background:#f984ef;">
                <b>Estado:</b> {{$t->estado}} {{number_format($t->porcentaje,2)}}%
            </div>
            <div class="col">
                <b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}
            </div>
            <div class="col">
                <b>Fecha aprox. entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}
            </div>
        </div>
        <div class="row">
            <div class="col">
                @if($t->fechaUno == '')
                <b>Peparando carga:</b>
                @else
                <b>Peparando carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}
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
                @if($t->fecha_cargado == '')
                <b>Fecha de carga:</b>
                @else
                <b>Fecha de carga: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_cargado))}}
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
                <b>Fecha en sucursal: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_entregado))}}
            </div>
            <div class="col">
                <b>Observación Supervisor: </b>{{$t->observacionSup}}
            </div>
        </div>
        <div class="row">
            <div class="col">
                <b>Fecha finalizada: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSucursal))}}
            </div>
            <div class="col">
                <b>Creada por: </b> {{$t->usuario}}
            </div>
            <div class="col-6">
                <b>Observación Sucursal: </b>{{$t->observacionSucursal}}
            </div>
        </div>
        <div class="row">
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
        @if($t->erroresVerificados == 1)
        <div class="row">
            <div class="col">
                <b>Observaciones de la correccion: </b> {{$t->observacionRevision}}
            </div>
        </div>
        @else
        @endif
    </div>
    @endforeach
    <br>
    <div class="table-responsive-sm">
	    <table class="table table-sm table-borderless" id="existencia">
		    <thead >
			    <tr>
				    <th>Categoria</th>
			        <th>Código</th>
			        <th>Producto</th>
                    <th>Cantidad Solicitada</th>
                    <th>Cantidad Enviada</th>
                    <th>Cantidad Recibida</th>
                    <th style="display:none;">NoIncluido</th>
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
                    <td>{{number_format($pro->cantidadSolicitada,0)}}</td>
                    <td>{{number_format($pro->cantidad1,0)}}</td>
                    <td>{{number_format($pro->cantidad,0)}}</td>
                    <td style="display:none;">{{number_format($pro->noIncluido)}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
	    </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transferencia #{{$id}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" style="height: 250px;">
                    <div class="col" id="graficaBarras">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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

});
</script>
<script type="text/javascript">
var enCola =  <?php echo json_encode($enCola) ?>;
var preCarga =   <?php echo json_encode($preCarga); ?>;
var Cargando =   <?php echo json_encode($Cargando); ?>;
var verificado =   <?php echo json_encode($verificado); ?>;
var viaje =   <?php echo json_encode($viaje); ?>;
var data = [];

   Highcharts.chart('graficaBarras', {
    chart: {
        type: 'timeline'
    },
    accessibility: {
        screenReaderSection: {
            beforeChartFormat: '<h5>{chartTitle}</h5>' +
                '<div>{typeDescription}</div>' +
                '<div>{chartSubtitle}</div>' +
                '<div>{chartLongdesc}</div>' +
                '<div>{viewTableButton}</div>'
        },
        point: {
            valueDescriptionFormat: '{index}. {"Tiempo en cola"}. {point.description}.'
        }
    },
    xAxis: {
        visible: false
    },
    yAxis: {
        visible: false
    },
    title: {
        text: 'Linea de tiempo de la transferencia'
    },
    colors: [
        '#a8e4a0',
        '#dcd0ff',
        '#7cb9e8',
        '#ffb347',
        '#ff5349',
        '#363C46'
    ],
    series: [{
        data: [{
            name: 'En cola',
            label: enCola
        }, {
            name: 'Preparando carga',
            label: preCarga
        }, {
            name: 'Cargando',
            label: Cargando
        }, {
            name: 'Verificando carga',
            label: verificado
        }, {
            name: 'En camino',
            label: viaje
        }]
    }]
});
</script>
@endsection
