@extends('layouts.app')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script>
    var url_global='{{url("/")}}';
</script>

    <nav class="navbar navbar-expand-lg">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('rep_verf_transf')}}">Atrás</a>
                <a class="nav-link" href="{{route('rep_verf_transf')}}">Reporte eficacia</a>
                <a class="nav-link" href="{{route('rep_tiem_group_transf')}}">Reporte tiempos</a>
                <a class="nav-link" href="#" id="active">Sucursales</a>
            </div>
        </div>
    </nav>
<div class="container-fluid">
    <h5>Tiempo en sucursales</h5>
    <form method="get" action="{{url('rstrans')}}">
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
                        <button class="btn btn-success" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row" style="height: 700px;">
        <div class="col" id="graficaBarras">
        </div>
    </div>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="sucursales">
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Tiempo de confirmación</th>
                    <th>Transferencias finalizados</th>
                    <th>Transferencias sin finalizar</th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div>
<script type="text/javascript">
var tiempo =  <?php echo json_encode($tiempo) ?>;
var horas =  <?php echo json_encode($horas) ?>;
var nombre =   <?php echo json_encode($nombre); ?>;
var data = [];
$.each(tiempo, function(index, value){
    var color;
    if (value >= 24) color = '#FE010170';
    else if (value >= 12 & value < 24) color = '#F7A70469';
    else color = '#39B41DCF';
    data.push({y:value, color: color});
    });
Highcharts.chart('graficaBarras', {
    chart: {
        type: 'bar',
        options3d: {
            enabled: true,
            alpha: 3,
            beta: 1,
            depth: 100,
            viewDistance: 15
        }
    },
    title: {
        text: 'Tiempo de confirmación'
    },
    xAxis: {
        type: 'text',
        categories: nombre
    },
    yAxis: {
        title: {
            text: 'Tiempo'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.nombre}: </td>' +
        '<td style="padding:0"><b>{point.y:.2f} horas</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        bar: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },

    series: [{
        name: 'Tiempo',
        data: data
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
        url: "{{route('dat_suc_rep',['inicio'=>$inicio,'fin'=>$fin])}}",
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
        { data: 'name', name: 'name'},
        { data: 'bodega', name: 'bodega'},
        { data: 'tiempo', name: 'tiempo'},
        { data: 'total', name: 'total'},
        { data: 'sin', name: 'sin'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if(data['tiempoh'] >= 24){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#FE010114,#FE010136,#FE010170)");
		    $($(row).find("td")[1]).css("background-image","linear-gradient(#FE010114,#FE010136,#FE010170)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#FE010114,#FE010136,#FE010170)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#FE010114,#FE010136,#FE010170)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#FE010114,#FE010136,#FE010170)");
        }
        if(data['tiempoh'] > 12 && data['tiempoh'] < 24){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#F7A70414,#F7A70436,#F7A70469)");
		    $($(row).find("td")[1]).css("background-image","linear-gradient(#F7A70414,#F7A70436,#F7A70469)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#F7A70414,#F7A70436,#F7A70469)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#F7A70414,#F7A70436,#F7A70469)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#F7A70414,#F7A70436,#F7A70469)");
        }
    }
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/transps/'+row.data().cod_unidad+'/'+ row.data().cod_bodega);
    redirectWindow.location;
});
</script>
@endsection
