@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.solicitudes'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Ver las solicitudes en otras sucursales</p>
    </blockquote>
    <div class="table-responsive">
        <table class="table table-sm table-borderless" id="sucursales" style="width:100%">
            <thead>
                <tr>
                    <th>Sucursales</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#sucursales').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{route('datos_listado_sucursales_entregas')}}",
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
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
    ],
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/entrdsuc/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
