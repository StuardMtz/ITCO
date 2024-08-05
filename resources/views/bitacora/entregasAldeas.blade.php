@extends('layouts.app2')
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
<script src="{{ asset('js/handlebars.js')}}"></script>
@section('content')
<style type="text/css">
    @media print{
        .oculto-impresion, .oculto-impresion *{
        display: none !important;
        }
    }
    h4,h5{
        text-align: center;
    }
</style>
<div class="container-fluid">
    <a class="btn btn-dark"  href="{{route('entre_muni')}}" style="color:white;"><i class="fas fa-arrow-left"></i> Atrás</a>
    <h5>Entregas por aldea o zona</h5>
    <div class="table-responsive">
        <table class="table table-sm table-hover" id="inventario">
            <thead>
                <tr class="bg-info">
                    <th></th>
                    <th>Aldea o zona</th>
                    <th>Cliente</th>
                    <th>Sucursal</th>
                    <th>No de entregas</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script id="details-template" type="text/x-handlebars-template">
@verbatim
    <div class="label label-info">{{nombre}}</div>
    <table class="table details-table" id="post-{{id_cliente}}">
        <thead>
            <tr class="bg-success">
                <th>Solicitado por</th>
                <th>Camión que entrego</th>
                <th>Fecha de carga</th>
                <th>Fecha de entrega</th>
                <th>Ver</th>
            </tr>
        </thead>
    </table>
@endverbatim
</script>
<script>
    var template = Handlebars.compile($("#details-template").html());
    var table = $('#inventario').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        searching: true,
        ajax:{
            url: "{{route('datAlde',$id)}}",
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
                { "className": 'details-control',
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "defaultContent": '<div class="text-center" style="width:100%; color: #3dc728; cursor:pointer;"><i class="fa fa-plus-circle"></i></div>'},
                { data: 'aldea', name: 'aldea'},
                { data: 'nombre', name: 'nombre'},
                { data: 'name', name: 'name'},
                { data: 'entregas', name:'entregas', render: $.fn.dataTable.render.number(',','.',0 )}
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [1,2,3], searchable: true },
        { targets: '_all', searchable: false },
    ],
    });

    $('#inventario tbody').on('click', 'td.details-control', function(){
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var tableId = 'post-' + row.data().id_cliente;

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
        serverSide: false,
        pageLength: 25,
        ajax: data.details_url,
        "order": [[ 2,"desc" ]],
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
        { data: 'name', name: 'name'},
        { data: 'placa', name: 'placa'},
        { data: 'fecha_carga', name: 'fecha_carga'},
        { data: 'fecha_entregado', name: 'fecha_entregado'},
        { data: null, render: function(data,type,row){
            return "<a href='{{url('veEntre')}}/"+ data.id +"' class='btn btn-sm btn-dark'> Ver entregas</button>"}
        }
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [2,3], render:function(data){
             moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ]
})
}
</script>
@endsection
