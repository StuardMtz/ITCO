@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
    aria-expanded="false" aria-label="Toggle navigation">
        <img src="{{url('/')}}/storage/opciones.png" width="25">
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link"disabled>Listado de usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('lis_td_ac')}}">Todos los días registrados</a>
            </li>

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
        <p class="mb-0">Listado de usuarios</p>
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
            <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" required>
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Fin</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" required>
            <div class="input-group-append">
                <button type="submit" class="btn btn-warning" type="button">Buscar</button>
            </div>
        </div>
    </form>
    <div class="table-responsive-md">
        <table class="table table-sm table-borderless" id="transferencias">
            <thead>
                <tr>
                    <th>Icono</th>
                    <th>Nombre</th>
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
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: false,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('dat_vendedores')}}",
        dataSrc: "data",
    },
    "order": [[ 2,"asc" ]],
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
        { data:'icono_actividad', name:'icono_actividad',render: function (remember_token){
            return '<img src="'+url_global+'/'+remember_token+'">';
        }},
        { data: 'name', name: 'name'},
        { data: 'nombre', name: 'nombre'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        $($(row).find("td")[1]).css("text-transform","capitalize");
    },
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/repUs/'+row.data().codigo_vendedor);
    redirectWindow.location;
});
</script>
@endsection

