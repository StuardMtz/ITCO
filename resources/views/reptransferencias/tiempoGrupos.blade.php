@extends('layouts.app')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
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
                <a class="nav-link" href="{{route('rep_verf_transf')}}">Reporte eficacia</a>
                <a class="nav-link active" href="#" active>Reporte tiempos</a>
                <a class="nav-link" href="{{route('rstrans')}}">Sucursales</a>
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
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Eficacia por verificador</p>
    </blockquote>
    <form method="get" action="{{url('rgtrans')}}">
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Desde</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Inicio" name="inicio" value="{{$inicio}}">
                </div>
            </div>
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Hasta</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Fin" name="fin" value="{{$fin}}">
                    <div class="input-group-append">
                        <button class="btn btn-warning" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    <div class="row" style="height: 250px;">
        <div class="col" id="graficaBarras">
        </div>
    </div>
    <hr>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="sucursales">
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th>Fecha inicio</th>
                    <th>Fecha final</th>
                    <th>En cola</th>
                    <th>Preparando carga</th>
                    <th>Cargando</th>
                    <th>Verificación</th>
                    <th>En camino</th>
                    <th>Transferencias</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
var enCola =  <?php echo json_encode($enCola) ?>;
var preCarga =   <?php echo json_encode($preCarga); ?>;
var Cargando =   <?php echo json_encode($Cargando); ?>;
var verificado =   <?php echo json_encode($verificado); ?>;
var viaje =   <?php echo json_encode($viaje); ?>;
var nombre = <?php echo json_encode($nombre); ?>;
var data = [];
Highcharts.chart('graficaBarras', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Tiempo por estado'
    },
    xAxis: {
        type: 'text',
        categories: nombre
    },
    yAxis: {
        title: {
            text: 'Promedio en horas'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
        '<td style="padding:0"><b>{point.y:.2f} horas</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
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
        name: 'En cola',
        data: enCola
    }, {
            name: 'Preparando carga',
            data: preCarga
        }, {
            name: 'Cargando',
            data: Cargando,
        }, {
            name: 'Verificación',
            data: verificado,
    }],
    responsive: {
        rules: [{
            condition: {
                maxWidth: 300
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }
});
</script>
<script>
var table = $('#sucursales').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    ajax:{
        url: "{{route('drep_tiem_group_transf',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: "data",
    },
    "order": [[ 0,"asc" ]],
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
    columns: [
        { data: 'grupoCarga', name: 'grupoCarga'},
        { data: 'inicio', name:'inicio'},
        { data: 'fin', name: 'fin'},
        { data: 'enCola', name: 'enCola'},
        { data: 'preCarga', name: 'preCarga'},
        { data: 'Cargando', name: 'Cargando'},
        { data: 'verificado', name: 'viaje'},
        { data: 'viaje', name: 'viaje'},
        { data: 'total', name: 'total'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [1,2], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/rpgtrans/'+row.data().grupoCarga +'/'+row.data().inicio+'/'+row.data().fin);
    redirectWindow.location;
});
</script>
@endsection
