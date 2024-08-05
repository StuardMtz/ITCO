@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.transferenciasCompras'))

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Reporte de producto dañado</p>
    </blockquote>

    <form method="get" action="{{url('ferepprodmes')}}" class="form-inline">
    {{csrf_field()}}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Inicio</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="inicio">
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Fin</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="fin">
            <div class="input-group-append">
                <button type="submit" class="btn btn-warning btn-sm" type="button">Buscar</button>
            </div>
        </div>
    </form>
    <div class="table-responsive-md">
        <table class="table table-sm table-borderless" id="transferencias">
            <thead>
                <tr>
                    <th></th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad dañado</th>
                    <th>Ver</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script id="details-template" type="text/x-handlebars-template">
@verbatim
    <div class="label label-info">Historial de ingresos del producto</div>
    <div class="table-responsive-sm">
        <table class="table details-table" id="post-{{cod_producto}}">
            <thead>
                <tr class="bg-success">
                    <th>No. transferencia</th>
                    <th>Observación</th>
                    <th>Código</th>
                    <th>Nombre fiscal</th>
                    <th>Cantidad dañado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
        </table>
    </div>
@endverbatim
</script>
<script>
var template = Handlebars.compile($("#details-template").html());
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    ajax:{
        url: "{{route('drep_pro_m_est')}}",
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
        {   "className": 'details-control',
            "orderable": false,
            "searchable": false,
            "data": null,
            "defaultContent": '<div class="text-center" style="width:100%; color: #3dc728; cursor:pointer;"><i class="fa fa-plus-circle"></i></div>'},
        { data: 'nombre_corto', name:'nombre_corto'},
        { data: 'nombre_fiscal', name: 'nombre_fiscal'},
        { data: 'mal_estado', name: 'mal_estado'},
        { data: null, render: function(data,type,row){
            return "<a href='{{url('drepromeim')}}/"+data.cod_producto+"'class= 'btn btn-dark btn-sm'>Ver</button>"}
        }
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [1,2,3], searchable: true },
        { targets: '_all', searchable: false }
    ],
});
$('#transferencias tbody').on('click', 'td.details-control', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var tableId = 'post-' + row.data().cod_producto;
    if(row.child.isShown()){
        row.child.hide();
        tr.removeClass('shown');
    }else {
        row.child(template(row.data())).show();
        initTable(tableId, row.data());
        tr.next().find('td').addClass('no-padding bg-gray');
    }
});
function initTable(tableId, data) {
    $('#' + tableId).DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: data.details_url,
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
            { data: 'observacion', name: 'observacion'},
            { data: 'nombre_corto', name: 'nombre_corto'},
            { data: 'nombre_fiscal', name: 'nombre_fiscal'},
            { data: 'mal_estado', name: 'mal_estado'},
            { data: 'fechaSucursal', name: 'fechaSucursal'}
        ],
        "columnDefs": [
            { targets: 0, searchable: true },
            { targets: [0,1,2,3], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [5], render:function(data){
                moment.locale('es');
                return moment(data).format('LL');}
            }
        ]
    })
}
</script>
@endsection
