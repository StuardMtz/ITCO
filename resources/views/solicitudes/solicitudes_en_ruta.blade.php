@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.solicitudes'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Solicitudes en ruta</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless display nowrap" id="en_ruta" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Fecha de solicitud</th>
                    <th>Camión</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Dirección</th>
                    <th>Editar</th>
                    <th>Ver</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
$('#en_ruta').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: 'datsolrut',
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
        { data: 'id', name: 'id'},
        { data: 'comprobante', name: 'comprobante'},
        { data: 'cliente',name:'cliente'},
        { data: 'created_at',name:'created_at'},
        { data: 'placa', name: 'placa'},
        { data: 'estado', name: 'estado'},
        { data: 'aldea',name:'aldea'},
        { data: 'direccion', name: 'direccion'},
        { data: null, render: function(data,type,row){
            return "<a href='{{url('can_sol/')}}/"+data.id+"'  class= 'btn btn-warning btn-sm'>Editar</a>"}
        },
        { data: null, render: function(data,type,row){
            return "<a href='{{url('vcitud/')}}/"+data.id+"'  class= 'btn btn-dark btn-sm'>Ver</a>"}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [8,9]},
        { targets: 1, searchable: true },
        { targets: [0,1,3,4,5], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ]
});
</script>
@endsection
