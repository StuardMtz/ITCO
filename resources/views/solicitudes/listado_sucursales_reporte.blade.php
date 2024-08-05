@extends('layouts.app')
@section('content')
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}">Menu principal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link"  href="{{route('entregas_en_espera')}}">Atrás</a>
                <a class="nav-link" href="#">Sucursales</a>
                <a class="nav-link" href="{{route('entregas_por_municipio')}}"> Entregas por municipio</a>
                <a class="nav-link" href="{{route('listado_camiones')}}">Entregas por camión</a>
                <a class="nav-link" href="{{route('marcadores_mapa')}}"> Mapa entregas</a>
                <form method="get" action="{{url('repgen')}}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="span">Desde</span>
                        </div>
                        <input type="date"  class="form-control" placeholder="Inicio" name="inicio">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="span">Hasta</span>
                        </div>
                        <input type="date" step="0.01" class="form-control" placeholder="Fin" name="fin">
                        <div class="input-group-append">
                            <button class="btn btn-warning" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
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
            <p class="mb-0">Reporete de entregas por sucursales</p>
        </blockquote>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="sucursales" style="width:100%">
                <thead>
                    <tr>
                        <th>Sucursales</th>
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
    scrollX: true,
    scrollY: '65vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{route('datos_listado_sucursales_entregas')}}",
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
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
    ],
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/repentresuc/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
