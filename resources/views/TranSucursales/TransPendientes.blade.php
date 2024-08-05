@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.TransSucursales'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias pendientes de autorizar</p>
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
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha</th>
                    <th>Solicita autorización</th>
                    <th>Descripción</th>
                    <th>No. factura</th>
                    <th>Serie factura</th>
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
        url: "{{route('dtran_pendientes_suc')}}",
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
        { data: 'nombre', name:'nombre'},
        { data: 'bodega', name: 'bodega'},
        { data: 'created_at', name: 'created_at'},
        { data: 'usuario', name: 'usuario'},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'numeroFactura', name: 'numeroFactura'},
        { data: 'serieFactura', name: 'serieFactura'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,4,5,6,7], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3], render:function(data){
            moment.locale('es');
            return moment(data).format('lll');
        }},
    ]
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTranS/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
