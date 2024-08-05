@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
    aria-expanded="false" aria-label="Toggle navigation">
        <img src="{{url('/')}}/storage/opciones.png" width="25">
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{route('listado_vendedores')}}">Atrás</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" id="active" disabled>Listado días actividades</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('lis_det_acti_us',$id)}}">Todas las actividades</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('mapa_visitas',$id)}}">Mapa visitas</a>
            </li>
            <form method="get" action="{{url('repUsF',$id)}}" class="form-inline">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Inicio</span>
                    </div>
                    <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio">
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Fin</span>
                    </div>
                    <input type="date" aria-label="Fecha inicial" class="form-control" name="fin">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-warning" type="button">Buscar</button>
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
    <h6>Listado días de actividades por usuario</h6>
    <div class="table-responsive-md">
        <table class="table table-sm table-borderless" id="transferencias">
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
    serverSide: true,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: false,
    scrollY: '67vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('datos_rep_usuarios',$id)}}",
        dataSrc: "data",
    },
    "order": [[ 3,"desc" ]],
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
