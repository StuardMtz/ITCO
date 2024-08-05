@extends('layouts.app')
@section('content')
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link btn-dark" href="javascript:history.back()">Atrás</a>
            <form method="get" action="{{url('trarepf')}}" class="form-inline">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">De</span>
                    </div>
                    <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" required>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Hasta</span>
                    </div>
                    <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" required>
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
        </div>
    </div>
</nav>
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Reporte general</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha creada</th>
                    <th>Finalizado</th>
                    <th>Creado por</th>
                    <th>Descripción</th>
                    <th>No. factura</th>
                    <th>Serie factura</th>
                    <th>Estado</th>
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
    scrollY: '75vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('datos_tra_reporte')}}",
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
        { data: 'num_movi', name: 'num_movi'},
        { data: 'nombre', name:'nombre'},
        { data: 'bodega', name: 'bodega'},
        { data: 'created_at', name: 'created_at'},
        { data: 'fechaSucursal', name: 'fechaSucursal'},
        { data: 'usuario', name: 'usuario'},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'numeroFactura', name: 'numeroFactura'},
        { data: 'serieFactura', name: 'serieFactura'},
        { data: 'estado', name: 'estado'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,5,6,7,8], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('lll');
        }},
    ],
    rowCallback:function(row,data){
        if(data['id_estado'] == 20 ){
            $($(row).find("td")[0]).css("background-color","#238C1173");
            $($(row).find("td")[1]).css("background-color","#238C1173");
			$($(row).find("td")[2]).css("background-color","#238C1173");
            $($(row).find("td")[3]).css("background-color","#238C1173");
            $($(row).find("td")[4]).css("background-color","#238C1173");
            $($(row).find("td")[5]).css("background-color","#238C1173");
            $($(row).find("td")[6]).css("background-color","#238C1173");
            $($(row).find("td")[7]).css("background-color","#238C1173");
            $($(row).find("td")[8]).css("background-color","#238C1173");
            $($(row).find("td")[9]).css("background-color","#238C1173");
        }
        else if(data['id_estado'] == 13 ){
            $($(row).find("td")[0]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[1]).css("background-color","#F0EB4A7D");
			$($(row).find("td")[2]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[3]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[4]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[5]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[6]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[7]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[8]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[9]).css("background-color","#F0EB4A7D");
        }
        if(data['fechaSucursal'] == null){
            $($(row).find("td")[4]).html('');
        }
    }
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTranS/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
