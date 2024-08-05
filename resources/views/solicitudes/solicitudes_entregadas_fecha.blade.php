@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script src="{{ asset('js/datatables.min.js') }}"></script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}">Menu principal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('entregas_en_espera')}}">En espera</a>
                <a class="nav-link" href="{{route('solicitudes_en_ruta')}}">Solicitudes en ruta</a>
                <a class="nav-link active" href="#" id="active">Solicitudes entregadas</a>
                <a class="nav-link" href="{{route('vista_clientes')}}">Clientes</a>
                <a class="nav-link" href="{{route('listado_sucursales_entregas')}}">Sucursales</a>
                <a class="nav-link" href="{{route('listado_sucursales_reporte')}}">Reportes</a>
                <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                <li class="nav-item">
                @if (Route::has('register'))
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
            <p class="mb-0">Solicitudes entregadas del {{date('d/m/Y',strtotime($inicio))}} al {{date('d/m/Y',strtotime($fin))}}</p>
        </blockquote>
        <form method="get" action="{{url('solentrefe')}}">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="span">Desde</span>
                        </div>
                        <input type="date"  class="form-control" placeholder="Inicio" name="inicio" value="{{$inicio}}">
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="span">Hasta</span>
                        </div>
                        <input type="date" step="0.01" class="form-control" placeholder="Fin" name="fin" value="{{$fin}}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-dark" type="submit">Buscar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="table table-responsive-sm">
            <table class="table table-sm table-borderless" id="entregas">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Comprobante</th>
                        <th>Cliente</th>
                        <th>Ubicación</th>
                        <th>Fecha de Solicitud</th>
                        <th>Fecha de Entrega</th>
                        <th>Tiempo de entrega</th>
                        <th>Camión</th>
                        <th>Estado</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<script>
var table = $('#entregas').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    scrollY: '60vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{url('dsoentf',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: "data",
    },
    "order": [[ 4,"asc" ]],
    dom: 'Bfrtip',
    lengthMenu: [
        [ 100, 200, 300, -1 ],
        [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {   extend: 'pageLength',
            className: 'btn btn-dark',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {   extend: 'colvis',
            className: 'btn btn-dark',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {   extend: 'excelHtml5',
            className: 'btn btn-dark',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true,
            title: '{{Auth::user()->name}}'
        }
    ],
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
        { data: 'comprobante', name: 'comprobante'},
        { data: 'cliente',name:'cliente'},
        { data: 'aldea', name: 'aldea'},
        { data: 'created_at',name:'inventario_web_entregas.created_at'},
        { data: 'fecha_entregado',name:'fecha_entregado'},
        { data: 'tiempo', name: 'tiempo'},
        { data: 'placa',name:'placa'},
        { data: 'estado', name: 'estado'},
        { data: 'comentarios', name: 'comentarios'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [7]},
        { targets: 1, searchable: true },
        { targets: [1,2,3,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4,5], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ]
});
$('#entregas').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/vcitud/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
