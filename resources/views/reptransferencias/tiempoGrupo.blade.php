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
                <a class="nav-link" href="{{route('rep_tiem_group_transf')}}">Atrás</a>
                <a class="nav-link active" href="#" >Reporte tiempos {{$gru}}</a>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
    </nav>
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias del {{date('d/m/Y','strtotime'($inicio))}} al {{date('d/m/Y','strtotime'($fin))}}</p>
    </blockquote>
        <!--<div class="col">
        <form method="get" action="{{url('rgtrans')}}">
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
        </form>
        </div>-->
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="sucursales">
            <thead>
                <tr>
                    <th>Transferencia</th>
                    <th>En cola</th>
                    <th>Preparando carga</th>
                    <th>Cargando</th>
                    <th>Verificación</th>
                    <th>En camino</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#sucursales').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    ajax:{
        url: "{{route('drpgtrans',['gru'=>$gru,'inicio'=>$inicio,'fin'=>$fin])}}",
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
        { data: 'enCola', name: 'enCola'},
        { data: 'preCarga', name: 'preCarga'},
        { data: 'Cargando', name: 'Cargando'},
        { data: 'verificado', name: 'viaje'},
        { data: 'viaje', name: 'viaje'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false }
    ],
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var redirectWindow = window.open(url_global+'/vrtrans/'+ row.data().id, '_blank');
    redirectWindow.location;
});
</script>
@endsection
