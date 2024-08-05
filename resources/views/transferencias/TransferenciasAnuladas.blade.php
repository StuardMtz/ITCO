@extends('layouts.app')
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
                <a class="nav-link" href="{{route('inicio_transferencias')}}">Creadas</a>
                <a class="nav-link" href="{{route('trans_bodega')}}">En bodega</a>
                <a class="nav-link" href="{{route('despacho_transf')}}">Despachadas</a>
                <a class="nav-link active" href="#">Anuladas</a>
                <a class="nav-link" href="{{route('finalizadas_transf')}}">Finalizadas</a>
                <a class="nav-link" href="{{route('trans_ot_sucursales')}}">Otras unidades</a>
                <a class="nav-link" href="{{route('list_us_transf')}}">Integrantes de grupo</a>
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
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias anuladas</p>
    </blockquote>
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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="4"><input class="form-control" type="text" id="column2_search" placeholder="Bodega salida"></th>
                    <th colspan="3"><input class="form-control" type="text" id="column4_search" placeholder="Fecha salida"></th>
                    <th colspan="3"><input class="form-control" type="text" id="column7_search" placeholder="Placa"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha de creación</th>
                    <th>Fecha de salida</th>
                    <th>Fecha de finalización</th>
                    <th>Descripción</th>
                    <th>Placa</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
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
    scrollX: true,
    scrollY: '60vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
    ajax:{
        url: "{{route('dtranAnulad')}}",
        dataSrc: "data",
    },
    "order": [[ 5,"desc" ]],
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
        { data: 'num_movi', name: 'num_movi'},
        { data: 'nombre', name:'nombre'},
        { data: 'bodega', name: 'bodega'},
        { data: 'created_at', name: 'created_at'},
        { data: 'fechaSalida', name: 'fechaSalida'},
        { data: 'fecha_entregado', name: 'fecha_entregado', render: function(data,type,row){
            if(type === "sort" || type === "type"){
                return data;
            }
            return moment(data).format('DD/MM/YYYY');
        }},
        { data: 'observacion', name: 'observacion'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
            return row['propietario'] +' '+  row['placa_vehiculo']}
        },
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [8]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6,7,8,9], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4,5], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ]
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column4_search').on('keyup', function(){
    table.columns(4).search(this.value).draw();
});
$('#column7_search').on('keyup', function(){
    table.columns(7).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTran/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection
