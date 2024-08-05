@extends('layouts.app')
<script src="{{ asset('js/proveedores.js') }}" defer></script>
@section('content')
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('inicio_gastos_espera')}}">Gastos pendientes</a>
                <a class="nav-link" href="{{route('lis_suc_gas')}}">Gastos autorizados</a>
                <a class="nav-link" href="#">Gastos rechazados</a>
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

    <blockquote class="blockquote text-center">
        <p class="mb-0">Gastos rechazados</p>
    </blockquote>


    <div class="container">
        @if($message= Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>¡{{ $message}}!</strong>
        </div>
        @endif
        @if($message= Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>¡{{ $message}}!</strong>
        </div>
        @endif
    </div>

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="gastos" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>No. documento</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha solicitud</th>
                    <th>Fecha autorizado</th>
                    <th>Solicitado por</th>
                    <th>Autorizo</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#gastos').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('dgast_recha')}}",
        dataSrc: 'data',
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
        { data: 'id', name:  'id'},
        { data: 'proveedor', name: 'proveedor'},
        { data: 'no_documento', name: 'no_documento'},
        { data: 'descripcion', name: 'descripcion'},
        { data: 'monto', name: 'monto'},
        { data: 'fecha_registrado', name: 'fecha_registrado'},
        { data: 'fecha_autorizacion', name: 'fecha_autorizacion'},
        { data: 'usuario', name: 'usuario'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [5,6], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ]
});
$('#gastos').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/vdgasop/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
