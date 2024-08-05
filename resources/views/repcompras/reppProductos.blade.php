@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="{{route('rep_compras')}}"> Atrás</a>
            <a class="nav-link">Reporte por productos</a>
            <form method="get" action="{{url('fdvc')}}">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="span">Desde</span>
                    </div>
                    <input type="date"  class="form-control" placeholder="Inicio" name="inicio">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="span">Hasta</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Fin" name="fin">
                    <div class="input-group-append">
                        <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
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

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Ordenes de compra por productos</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="compras">
            <thead>
                <tr>
                    <th></th>
                    <th>Código</th>
                    <th>Nombre</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script id="details-template" type="text/x-handlebars-template">
@verbatim
    <div class="label label-info">Detalle de orden: </div>
    <table class="table details-table" id="post-{{cod_producto}}">
        <thead>
            <tr>
                <th id="th2">Número</th>
                <th id="th2">Documento</th>
                <th id="th2">Código</th>
                <th id="th2">Proveedor</th>
                <th id="th2">Fecha</th>
                <th id="th2">Motivo</th>
                <th id="th2">Origen</th>
                <th id="th2">Cantidad</th>
                <th id="th2">Precio</th>
                <th id="th2">Pedido</th>
                <th id="th2">Estado</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            <th style="background-color:#abdda4;color:black;"></th>
            </tr>
        </tfoot>
    </table>
@endverbatim
</script>
<script>
var template = Handlebars.compile($("#details-template").html());
var table = $('#compras').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{route('dvrcp_produ')}}",
        dataSrc: "data",
    },
    "order": [[ 1,"asc" ]],
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
        }
    },
    columns: [
        { "className": 'details-control',
            "orderable": false,
            "searchable": false,
            "data": null,
            "defaultContent": '<div class="text-center" style="width:100%; color: #3dc728; cursor:pointer;"><i class="fa fa-plus-circle"></i></div>'},
        { data: 'nombre_corto', name: 'nombre_corto'},
        { data: 'nombre_fiscal', name:'nombre_fiscal'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [0]},
        { targets: 0, searchable: true },
        { targets: [1,2], searchable: true },
        { targets: '_all', searchable: false }
    ]
});

    $('#compras tbody').on('click', 'td.details-control', function(){
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
            pageLength: 50,
            ajax: data.details_url,
            "order": [[ 0,"desc" ]],
            scrollY: '30vh',
            scrollCollapse: true,
            scroller:       true,
            stateSave:      true,
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
            { data: 'numero_orden', name: 'numero_orden' },
            { data: 'Documento', name: 'Documento'},
            { data: 'proveedor', name: 'proveedor'},
            { data: 'nproveedor', name: 'nproveedor'},
            { data: 'fecha_entrega', name: 'fecha_entrega'},
            { data: 'Motivo', name: 'Motivo'},
            { data: 'Origen', name: 'Origeng'},
            { data: 'cantidad', name: 'cantidad', render: $.fn.dataTable.render.number(',','.',2, ' ')},
            { data: 'precio', name: 'precio', render: $.fn.dataTable.render.number(',','.',2, ' ')},
            { data: "'PEDIDO'", name: "'PEDIDO'"},
            { data: 'Estado', name: 'Estado'}
        ],
        "columnDefs": [
            { bSortable: false, targets: [4]},
            { targets: 0, searchable: true },
            { targets: [1,2,3,4], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [4], render:function(data){
                    moment.locale('es');
                    return moment(data).format('LL');
                }
            }
        ],
        "footerCallback": function(row,data,start,end,display){
            var api = this.api(), data;
            var intVal = function(i){
                return typeof i == 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                typeof i == 'number' ?
                    i:0;
            };

            var kilosTotal = api
            .column(7)
            .data()
            .reduce(function (a,b){
                return intVal(a) + intVal(b);
            }, 0);

            var numFormat = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
                numFormat(kilosTotal);
            pageTotal = api.column(7,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            }, 0);
            $(api.column(0).footer() ).html('Totales');
            $(api.column(7).footer()).html(numFormat(kilosTotal));
        }
        })
    }
</script>
@endsection
