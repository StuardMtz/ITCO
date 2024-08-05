@extends('layouts.app')
@section('content')
<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#transCompras" aria-controls="navbarNav"
        aria-expanded="false" aria-label="Toggle navigation">
    <img src="storage/opciones.png" width="25">
    </button>
    <div class="collapse navbar-collapse" id="transCompras">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{route('lis_td_ac')}}">Atrás</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" id="active" disabled>Todas las actividades {{date('d/m/Y',strtotime($inicio))}} al {{date('d/m/Y',strtotime($fin))}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('map_acti_fech_re',['inicio'=>$inicio,'fin'=>$fin,'rol'=>$rol])}}">Mapa visitas</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Listado de actividades por cargo</p>
    </blockquote>
    <form method="get" action="{{route('lis_td_ac_re_fe')}}" class="form-inline" style="margin: 5px">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Área</span>
            </div>
            <select class="form-control" aria-label="Area" name="area" required>
                <option value="0">Todos</option>
                @foreach($roles as $rl)
                <option value="{{$rl->rol}}">{{$rl->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Inicio</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" value="{{$inicio}}" required>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Fin</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" value="{{$fin}}" required>
            <div class="input-group-append">
                <button type="submit" class="btn btn-warning" type="button">Buscar</button>
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
    <div class="table-responsive-md">
        <table class="table table-sm table-borderless" id="transferencias">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Visita</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Área</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: false,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
    ajax:{
        url: "{{route('litdarefe',['inicio'=>$inicio,'fin'=>$fin,'rol'=>$rol])}}",
        dataSrc: "data",
    },
    buttons: [
        {
            className: 'nav-link',
            extend: 'pageLength',
            className: 'btn btn-dark',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {
            className: 'nav-link',
            extend: 'colvis',
            className: 'btn btn-dark',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {
            className: 'nav-link',
            extend: 'excelHtml5',
            className: 'btn btn-dark',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true,
            title: '{{Auth::user()->name}}'
        }
    ],
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
        { data: 'orden', name: 'orden'},
        { data: 'name', name: 'name'},
        { data: 'Descripcion', name: 'Descripcion'},
        { data: 'nombre', name: 'nombre'},
        { data: 'Fecha', name: 'Fecha'},
        { data: 'Hora', name: 'Hora'},
        { data: 'rol', name: 'rol'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
    rowCallback:function(row,data){
        $($(row).find("td")[1]).css("text-transform","capitalize");
    }
});
</script>
@endsection
