@extends('layouts.app')
@section('content')
<nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#transCompras" aria-controls="navbarNav"
    aria-expanded="false" aria-label="Toggle navigation">
        <img src="{{url('/')}}/storage/opciones.png" width="25">
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
                <a class="nav-link" href="{{route('map_acti_fech',['inicio'=>$inicio,'fin'=>$fin])}}">Mapa visitas</a>
            </li>
            <form method="get" action="{{route('lis_td_ac_fe')}}" class="form-inline">
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
        </ul>
    </div>
</nav>

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

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Listado de actividades por cargo</p>
    </blockquote>
    <div class="table-responsive-md">
        <table class="table table-sm borderless" id="transferencias">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Observaciones</th>
                    <th>Comentario</th>
                    <th>Fecha</th>
                    <th>Fecha final</th>
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
    ajax:{
        url: "{{route('litdafe',['inicio'=>$inicio,'fin'=>$fin])}}",
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
        { data: 'Observaciones', name: 'Observaciones'},
        { data: 'Comentario', name: 'Comentario'},
        { data: 'Fecha', name: 'Fecha'},
        { data: 'Fecha_Final', name: 'Fecha_Final'},
        { data: 'nombre', name: 'nombre'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
    rowCallback:function(row,data){
        if(data['Fecha_Final'] == null){
            $($(row).find("td")[4]).html('')
        }
    },
    rowCallback:function(row,data){
        $($(row).find("td")[0]).css("text-transform","capitalize");
    },
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/deRepUs/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
