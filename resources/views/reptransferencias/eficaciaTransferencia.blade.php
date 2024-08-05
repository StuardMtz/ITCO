@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('inicio_transferencias')}}">Atrás</a>
                <a class="nav-link active" href="#">Reporte eficacia</a>
                <a class="nav-link" href="{{route('rep_tiem_group_transf')}}">Reporte tiempos</a>
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
    <form method="get" action="{{url('rtrans')}}">
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
    <div class="row" style="height: auto; width: 100%;" id="graficaBarras">
    </div>
    <hr>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="sucursales">
            <thead>
                <tr>
                    <th>Verificador</th>
                    <th>Fecha inicio</th>
                    <th>Fecha final</th>
                    <th>Eficacia</th>
                    <th>Total de transferencias</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
var punteo =  <?php echo json_encode($punteo) ?>;
var nombre =   <?php echo json_encode($nombre); ?>;
var contador = 0;
var contador2 = 0;
var arreglo = [];
data = [];
$.each(punteo, function(index, value){
    var color;
    if (value >= 0 & value <= 8) color = '#870A06';
    else if (value > 8 & value <= 16) color = '#B41E1A';
    else if (value > 16 & value <= 24) color = '#ED0D07';
    else if (value > 24 & value <= 32) color = '#CF4F1C';
    else if (value > 32 & value <= 40) color = '#EE4C0B';
    else if (value > 40 & value <= 48) color = '#D9A802';
    else if (value > 48 & value <= 56) color = '#E8B402';
    else if (value > 56 & value <= 64) color = '#E3D60D';
    else if (value > 64 & value <= 72) color = '#ECEC03';
    else if (value > 72 & value <= 80) color = '#A4F207';
    else if (value > 80 & value <= 88) color = '#5DF207';
    else if (value > 88 & value <= 100) color = '#006400';
    else color = '#C9231E ';
    data.push({y:value, color: color});
});
Highcharts.chart('graficaBarras', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Porcentaje de eficacia'
    },
    xAxis: {
        type: 'text',
        categories: nombre
    },
    yAxis: {
        title: {
            text: 'Porcentaje'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        floating: true
    },
    tooltip: {
        backgroundColor: '#FFFFFF',
        pointFormat: 'Porcentaje: <b>{point.y:.2f} %</b>'
    },
    plotOptions: {
        bar: {
            pointPadding: 0,
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                color: 'black',
                format: '{point.y:.2f}%'
            }
        }
    },
    series: [{
        name: 'Porcentaje',
        data: data
    }],
    responsive: {
        rules: [{
            condition: {
                maxWidth: 100
            },
            chartOptions: {
                legend: {
                    enabled: true,
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
        url: "{{route('drep_verf_transf',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: "data",
    },
    "order": [[ 3,"asc" ]],
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
        { data: 'usuarioSupervisa', name: 'usuarioSupervisa'},
        { data: 'inicio', name:'inicio'},
        { data: 'final', name: 'final'},
        { data: 'eficacia', name: 'eficacia', render: $.fn.dataTable.render.number(',','.',2,'','%')},
        { data: 'total', name: 'totla'}
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
    window.location.href =(url_global+'/rvtrans/'+row.data().usuarioSupervisa +'/'+row.data().inicio+'/'+row.data().final);
    redirectWindow.location;
});
</script>
@endsection
