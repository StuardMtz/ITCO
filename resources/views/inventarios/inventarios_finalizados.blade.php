@extends('layouts.app')
@section('content')
@yield('navbar', View::make('layouts.inventario'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Inventarios finalizados</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="inventario" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Encargado</th>
                    <th>Inventario</th>
                    <th>Fecha inicial</th>
                    <th>Fecha final</th>
                    <th>Realizado en</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Contabilizados</th>
                    <th>Diferencias</th>
                    <th>Porcentaje</th>
                    <th>Dañado</th>
                    <th>Exactitud</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#inventario').DataTable({
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
        url: "{{route('datos_inventarios_finalizados')}}",
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
        { data: 'numero', name:  'numero'},
        { data: 'nombre', name: 'nombre'},
        { data: 'semana', name: 'semana'},
        { data: 'created_at', name: 'created_at' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'diferencia_tiempo', name: 'diferencia_tiempo'},
        { data: 'sucursal', name: 'sucursal'},
        { data: 'bodega', name: 'bodega'},
        { data: 'contado', name: 'contado'},
        { data: 'diferencia', name: 'diferencia'},
        { data: 'porcentaje', name: 'porcentaje',render: $.fn.dataTable.render.number(',', '%', '%', 0, '')},
        { data: 'daniado', name: 'daniado'},
        { data: 'exactitud', name: 'exactitud',render: $.fn.dataTable.render.number(',', '%', '%', 0, '')},
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('lll');
        }}
    ],
    rowCallback:function(row,data){
        if(data['porcentaje'] >= 99 ){
            $($(row).find("td")[10]).html('100%');
        }
    }
});
$('#inventario').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.open(url_global+'/ver/'+row.data().numero, '_blank');
    redirectWindow.location;
});
</script>
@endsection
