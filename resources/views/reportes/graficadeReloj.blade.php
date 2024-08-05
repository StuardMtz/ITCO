@extends('layouts.app')
@section('content')

    @foreach($detalle as $det)
    <blockquote class="blockquote text-center">
        <p class="mb-0">{{$det->sucursal}} {{$det->bodega}}</p>
    </blockquote>
    @endforeach
<div class="container-fluid">
            <a class="btn btn-dark btn-sm" href="{{route('repminimax',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>1])}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <hr>
    <div class="row justify-content-center">
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucPro',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>1])}}">Ver productos clase A</a>
        </div>
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucPro',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>2])}}">Ver productos clase B</a>
        </div>
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucPro',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>3])}}">Ver productos clase C</a>
        </div>
    </div>
    <div class="row">
        <div class="col" id="containerA">
        </div>
        <div class="col" id="containerB">
        </div>
        <div class="col" id="containerC">
        </div>
    </div>
    <div class="row">
        <div class="col" id="containerL">
        </div>
    </div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js";></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
Highcharts.chart('containerA', {
    chart: {
        type: 'gauge',
        plotBackgroundColor: null,
        plotBackgroundImage: null,
        plotBorderWidth: 0,
        plotShadow: false
    },
    title: {
        text: 'Productos Clase A'
    },
    pane: {
        startAngle: -150,
        endAngle: 150,
        background: [
            {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 3 },
                    stops: [
                        [0, '#EBF6F600'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, 
            {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 3 },
                    stops: [
                        [0, '#EBF6F600'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 3,
                outerRadius: '107%'
            }, 
            {
                backgroundColor: '#EBF6F600',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }
        ]
    },
    // the value axis
    yAxis: {
        min: 0,
        max: 100,
        minorTickInterval: 'auto',
        minorTickWidth: 1,
        minorTickLength: 12,
        minorTickPosition: 'inside',
        minorTickColor: '#666',
        tickPixelInterval: 30,
        tickWidth: 2,
        tickPosition: 'inside',
        tickLength: 12,
        tickColor: '#666',
        labels: {
            step: 2,
            rotation: 'auto'
        },
        title: {
            text: 'Clase A'
        },
        plotBands: [
            {
                from: 0,
                to: 8.5,
                color: '#B20F0A' 
            },
            {
                from: 8.6,
                to: 16.5,
                color: '#D4140E' 
            },
            {
                from: 16.6,
                to: 24.5,
                color: '#F03630' 
            },
            {
                from: 24.6,
                to: 32.5,
                color: '#D35E08' 
            },
            {
                from: 32.6,
                to: 40.5,
                color: '#EE7923' 
            },
            {
                from: 40.6,
                to: 48.5,
                color: '#EEA723' 
            },
            {
                from: 48.6,
                to: 56.5,
                color: '#EEC623' 
            },
            {
                from: 56.6,
                to: 64.5,
                color: '#EEE723' 
            },
            {
                from: 64.6,
                to: 72.5,
                color: '#DEEE23' 
            },
            {
                from: 72.6,
                to: 80.5,
                color: '#B0EE23' 
            },
            {
                from: 80.6,
                to: 88.5,
                color: '#7FEE23' 
            },
            {
                from: 88.6,
                to: 100,
                color: '#60C809' 
            },
        ]
    },
    series: [
        {
            name: 'Punteo',
            data: [{{$existenciaA}}],
            tooltip: {
                valueSuffix: '%'
            }
        }
    ]
},
function (chart) {     
});
</script>
<script type="text/javascript">
Highcharts.chart('containerB', {
    chart: {
        type: 'gauge',
        plotBackgroundColor: null,
        plotBackgroundImage: null,
        plotBorderWidth: 0,
        plotShadow: false
    },
    title: {
        text: 'Productos Clase B'
    },
    pane: {
        startAngle: -150,
        endAngle: 150,
        background: [
            {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 3 },
                    stops: [
                        [0, '#EBF6F600'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, 
            {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 3 },
                    stops: [
                        [0, '#EBF6F600'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 3,
                outerRadius: '107%'
            }, 
            {
                backgroundColor: '#EBF6F600',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }
        ]
    },
    // the value axis
    yAxis: {
        min: 0,
        max: 100,
        minorTickInterval: 'auto',
        minorTickWidth: 1,
        minorTickLength: 12,
        minorTickPosition: 'inside',
        minorTickColor: '#666',
        tickPixelInterval: 30,
        tickWidth: 2,
        tickPosition: 'inside',
        tickLength: 12,
        tickColor: '#666',
        labels: {
            step: 2,
            rotation: 'auto'
        },
        title: {
            text: 'Clase B'
        },
        plotBands: [
            {
                from: 0,
                to: 8,
            color: '#B20F0A' 
            },
            {
                from: 8,
                to: 16,
                color: '#D4140E' 
            },
            {
                from: 16,
                to: 24,
                color: '#F03630' 
            },
            {
                from: 24,
                to: 32,
                color: '#D35E08' 
            },
            {
                from: 32,
                to: 40,
                color: '#EE7923' 
            },
            {
                from: 40,
                to: 48,
                color: '#EEA723' 
            },
            {
                from: 48,
                to: 56,
                color: '#EEC623' 
            },
            {
                from: 56,
                to: 64,
                color: '#EEE723' 
            },
            {
                from: 64,
                to: 72,
                color: '#DEEE23' 
            },
            {
                from: 72,
                to: 80,
                color: '#B0EE23' 
            },
            {
                from: 80,
                to: 88,
                color: '#7FEE23' 
            },
            {
                from: 88,
                to: 100,
                color: '#60C809' 
            },
        ]
    },
    series: [
        {
            name: 'Punteo',
            data: [{{$existenciaB}}],
            tooltip: {
                valueSuffix: '%'
            }
        }
    ]
},
function (chart) {   
});
</script>
<script type="text/javascript">
Highcharts.chart('containerC', {
    chart: {
        type: 'gauge',
        plotBackgroundColor: null,
        plotBackgroundImage: null,
        plotBorderWidth: 0,
        plotShadow: false
    },
    title: {
        text: 'Productos Clase C'
    },
    pane: {
        startAngle: -150,
        endAngle: 150,
        background: [
            {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 3 },
                    stops: [
                        [0, '#EBF6F600'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, 
            {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 3 },
                    stops: [
                        [0, '#EBF6F600'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 3,
                outerRadius: '107%'
            }, 
            {
                backgroundColor: '#EBF6F600',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }
        ]
    },
    // the value axis
    yAxis: {
        min: 0,
        max: 100,
        minorTickInterval: 'auto',
        minorTickWidth: 1,
        minorTickLength: 12,
        minorTickPosition: 'inside',
        minorTickColor: '#666',
        tickPixelInterval: 30,
        tickWidth: 2,
        tickPosition: 'inside',
        tickLength: 12,
        tickColor: '#666',
        labels: {
            step: 2,
            rotation: 'auto'
        },
        title: {
            text: 'Clase C'
        },
        plotBands: [
            {
                from: 0,
                to: 8,
                color: '#B20F0A' 
            },
            {
                from: 8,
                to: 16,
                color: '#D4140E' 
            },
            {
                from: 16,
                to: 24,
                color: '#F03630' 
            },
            {
                from: 24,
                to: 32,
                color: '#D35E08' 
            },
            {
                from: 32,
                to: 40,
                color: '#EE7923' 
            },
            {
                from: 40,
                to: 48,
                color: '#EEA723' 
            },
            {
                from: 48,
                to: 56,
                color: '#EEC623' 
            },
            {
                from: 56,
                to: 64,
                color: '#EEE723' 
            },
            {
                from: 64,
                to: 72,
                color: '#DEEE23' 
            },
            {
                from: 72,
                to: 80,
                color: '#B0EE23' 
            },
            {
                from: 80,
                to: 88,
                color: '#7FEE23' 
            },
            {
                from: 88,
                to: 100,
                color: '#60C809' 
            },
        ]
    },
    series: [
        {
            name: 'Punteo',
            data: [{{$existenciaC}}],
            tooltip: {
                valueSuffix: '%'
            }
        }
    ]
},
function (chart) {     
});
</script>
<script type="text/javascript">
$(function () {
    $('#containerL').highcharts(
        {!! json_encode($graficaLineal) !!}
    );
})
</script>
@endsection