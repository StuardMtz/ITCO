@extends('layouts.app')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
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

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('rep_verf_transf')}}">Atrás</a>
                <a class="nav-link active" href="#">Reporte eficacia {{$veri}}</a>
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
    <h5>Eficacia por verificador</h5>
    <div class="row">
        <div class="col">
        <!--<form method="get" action="{{url('rtrans')}}">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Desde</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Inicio" name="inicio" value="{{$inicio}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Hasta</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Fin" name="fin" value="{{$fin}}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </form> -->
        </div>
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
                    <th>Transferencia</th>
                    <th>Verificador</th>
                    <th>Fecha</th>
                    <th>Tiempo de verificación</th>
                    <th>Eficacia</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
var punteo =  <?php echo json_encode($punteo) ?>;
var nombre =   <?php echo json_encode($nombre); ?>;
var datos = <?php echo json_encode($datos); ?>;
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
        else if (value > 88 & value <= 96) color = '#19CD24';
        else if (value > 88 & value <= 100) color = '#006400';
        else color = '#C9231E ';
        data.push({y:value, color: color});
    });

   Highcharts.chart('graficaBarras', {
       chart: {
           type: 'line'
       },
       title: {
           text: 'Porcentaje de eficacia'
       },
        xAxis: {
            type: 'text',
            categories: nombre,
            crosshair: true,
       },
       yAxis: {
           title: {
               text: 'Porcentaje',
               categories: datos,
               crosshair: true,
           }
       },
       legend: {
           layout: 'vertical',
           align: 'right',
           verticalAlign: 'middle'
       },

       plotOptions: {
        bar: {
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
            url: "{{route('drvtrans',['inicio'=>$inicio,'fin'=>$fin,'veri'=>$veri])}}",
            dataSrc: "data",
        },
        "order": [[ 0,"desc" ]],
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
                { data: 'id', name: 'id'},
                { data: 'usuarioSupervisa', name:'usuarioSupervisa'},
                { data: 'inicio', name: 'inicio'},
                { data: 'diferencia', name: 'diferencia'},
                { data: 'porcentaje', name: 'porcentaje', render: $.fn.dataTable.render.number(',','.',2,'','%')}
                ],
        "columnDefs": [
            { targets: 0, searchable: true },
            { targets: [0], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [2], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
            }}
        ],

        rowCallback:function(row,data){
        if(data['porcentaje'] == 0 && data['porcentaje'] <= 8 ){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#870A06 90%)");
        }
        else if(data['porcentaje'] > 8 && data['porcentaje'] <= 16){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#B41E1A 90%)");
        }
        else if(data['porcentaje'] > 16 && data['porcentaje'] <= 24){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#ED0D07 90%)");
        }
        else if(data['porcentaje'] > 24 && data['porcentaje'] <= 32){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#CF4F1C 90%)");
        }
        else if(data['porcentaje'] > 32 && data['porcentaje'] <= 40){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#EE4C0B 90%)");
        }
        else if(data['porcentaje'] > 40 && data['porcentaje'] <= 48){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#D9A802 90%)");
        }
        else if(data['porcentaje'] > 48 && data['porcentaje'] <= 56){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#E8B402 90%)");
        }
        else if(data['porcentaje'] > 56 && data['porcentaje'] <= 64){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#E3D60D 90%)");
        }
        else if(data['porcentaje'] > 64 && data['porcentaje'] <= 72){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#ECEC03 90%)");
        }
        else if(data['porcentaje'] > 72 && data['porcentaje'] <= 80){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#A4F207 90%)");
        }
        else if(data['porcentaje'] > 80 && data['porcentaje'] <= 88){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#5DF207 90%)");
        }
        else if(data['porcentaje'] > 88 && data['porcentaje'] <= 96){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#19CD24 90%)");
        }
        else if(data['porcentaje'] > 96 && data['porcentaje'] <= 100){
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa 70%,#006400 90%)");
        }
    },
    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      var redirectWindow = window.open(url_global+'/vrtrans/'+ row.data().id, '_blank');
      redirectWindow.location;
    });
</script>
@endsection
