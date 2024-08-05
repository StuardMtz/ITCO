@extends('layouts.app')
@section('content')
@yield('navbar', View::make('layouts.inventario'))
<div class="container-fluid">
    <form method="get" action="{{url('invefech')}}" class="d-flex">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="desde">Desde</span>
            </div>
            <input type="date"  class="form-control" placeholder="Inicio" name="inicio">
            <div class="input-group-prepend">
                <span class="input-group-text" id="hasta">Hasta</span>
            </div>
            <input type="date" class="form-control" placeholder="Fin" name="fin">
            <div class="input-group-append">
                <button class="btn btn-warning btn-sm" type="submit">Buscar</button>
            </div>
        </div>
    </form>
    <blockquote class="blockquote text-center">
        <p class="mb-0 titulos">Panel de inventarios</p>
    </blockquote>
    <div class="table-responsive-sm">
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
        serverSide: true,
        pageLength: 100,
        searching: true,
        responsive: false,
        scrollX: true,
        scrollY: '63vh',
        scrollCollapse: true,
        paging: true,
        stateSave: true,
        ajax:{
            url: "{{route('datos_reporte_inventarios')}}",
            dataSrc: "data",
        },

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
        { targets: '_all', searchable: true },
    ],
    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      window.location.href =(url_global+'/invreal/'+row.data().id);
      redirectWindow.location;
    });
</script>
@endsection
