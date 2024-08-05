@extends('layouts.app')
@section('content')

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="{{route('mimageneral')}}">Minimax general</a>
            <a class="nav-link" href="{{route('rep_suc_lis')}}">Minimax sucursal</a>
            <a class="nav-link" href="#">Registro existencias</a>
            <a class="nav-link" href="{{route('grafClasPSuc',1)}}">Clase A</a>
            <a class="nav-link" href="{{route('grafClasPSuc',2)}}">Clase B</a>
            <a class="nav-link" href="{{route('grafClasPSuc',3)}}">Clase C</a>
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
    @foreach($detalle as $det)
    <blockquote class="blockquote text-center">
        <p class="mb-0">{{$det->sucursal}} {{$det->bodega}}</p>
    </blockquote>
    @endforeach
    <hr>
    <div class="row justify-content-center">
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucProRa',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>1])}}">Ver productos clase A</a>
        </div>
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucProRa',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>2])}}">Ver productos clase B</a>
        </div>
        <div class="col">
            <a class="btn btn-dark btn-sm" href="{{route('grafClasSucProRa',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>3])}}">Ver productos clase C</a>
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
                        [0, '#FFF'],
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
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 3,
                outerRadius: '107%'
            },
            {
                // default background
            },
            {
                backgroundColor: '#DDD',
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
                        [0, '#FFF'],
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
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 3,
                outerRadius: '107%'
            },
            {
            // default background
            },
            {
                backgroundColor: '#DDD',
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
                        [0, '#FFF'],
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
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 3,
                outerRadius: '107%'
            },
            {
                // default background
            },
            {
                backgroundColor: '#DDD',
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
<script>
  //Declaramos la función que recibe el tiempo
  function refrescar(tiempo){
    //Cuando pase el tiempo elegido la página se refrescará
    setTimeout("location.reload(true);", tiempo);
  }
  //Podemos ejecutar la función de este modo
  //La página se actualizará dentro de 10 segundos
  refrescar(50000);
</script>
@endsection
