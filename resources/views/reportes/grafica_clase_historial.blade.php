@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucPro',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>$clase])}}"><i class="fas fa-arrow-left"></i> Atrás</a>
        </div>
        <div class="col">
            
        </div>
    </div>
    <div class="row">
        <div class="col" id="graficaBarras">
        </div>
    </div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js";></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script type="text/javascript">

var punteo =  <?php echo json_encode($punteo) ?>;
var nombre =   <?php echo json_encode($nombre); ?>;
var contador = 0;
var contador2 = 0;
var arreglo = [];
data = [];
$.each(punteo, function(index, value){
    var color;
    if (value >= 85) color = '#4CAF42';
    else if (value >= 70) color = '#DDE103 ';
    else color = '#C9231E ';
    data.push({y:value, color: color});
});
Highcharts.chart('graficaBarras', {
    chart: {
        type: 'column',
        options3d: {
            enabled: true,
            alpha: 3,
            beta: 1,
            depth: 100,
            viewDistance: 15
        }
    },
    title: {
        text: {!! json_encode($clas) !!}
    },
    subtitle: {
        text: 'Historial de existencia por producto'
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
        verticalAlign: 'middle'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                color: 'black',
                format: '{point.y:.1f}%' 
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
                maxWidth: 500
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
@endsection