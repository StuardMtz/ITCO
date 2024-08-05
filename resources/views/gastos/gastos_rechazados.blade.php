@extends('layouts.app')
<script src="{{ asset('js/proveedores.js') }}" defer></script>
@section('content')
@yield('content', View::make('layouts.gastos'))

<div class="container-fluid">
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
                    <th>Serie documento</th>
                    <th>No. documento</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Retención</th>
                    <th>No retencion</th>
                    <th>Fecha solicitud</th>
                    <th>Fecha autorizado</th>
                    <th>Rechazado por</th>
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
        url: "{{route('dagast_rech')}}",
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
        { data: 'serie_documento', name: 'serie_documento'},
        { data: 'no_documento', name: 'no_documento'},
        { data: 'descripcion', name: 'descripcion'},
        { data: 'monto', name: 'monto', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'iva', name: 'iva', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'no_retencion', name: 'no_retencion'},
        { data: 'fecha_registrado', name: 'fecha_registrado'},
        { data: 'fecha_autorizacion', name: 'fecha_autorizacion'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [8,9], render:function(data){
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
