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
            <a class="nav-link">Reporte ordenes de compras</a>
            <a class="nav-link" href="{{route('vrcp_produ')}}"> Reporte por productos</a>
            <form method="get" action="{{url('rdCoF')}}">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="span">Desde</span>
                    </div>
                    <input type="date"  class="form-control" placeholder="Inicio" name="inicio" required>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="span">Hasta</span>
                    </div>
                    <input type="date" step="0.01" class="form-control" placeholder="Fin" name="fin" required>
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

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Ordenes de compra</p>
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
        <table class="table table-sm table-borderless" id="compras">
            <thead>
                <tr>
                    <th></th>
                    <th>Empresa</th>
                    <th>Número</th>
                    <th>Fecha emisión</th>
                    {{-- <th>Partida arancelaria</th>
                    <th>Referencia</th>
                    <th>Requesición</th> --}}
                    <th>Fecha entrega</th>
                    {{-- <th>Descripción larga</th>
                    <th>Descripción</th>
                    <th>Avance</th> --}}
                    <th>Proveedor</th>
                    {{-- <th>Motivo</th>
                    <th>Tipo</th> --}}
                    <th>Estado</th>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="1"><input type="text" id="column1_search" class="form-control"></th>
                    <th colspan="1"><input type="text" id="column2_search" class="form-control"></th>
                    <th colspan="1"><input type="text" id="column3_search" class="form-control"></th>
                    <th colspan="1"><input type="text" id="column4_search" class="form-control"></th>
                    <th colspan="1"><input type="text" id="column5_search" class="form-control"></th>
                    <th colspan="1"><input type="text" id="column6_search" class="form-control"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script id="details-template" type="text/x-handlebars-template">
@verbatim
    <div class="label label-info">Detalle de orden: </div>
    <table class="table details-table" id="post-{{numero_orden}}">
        <thead>
            <tr>
                <th id="th2">Número</th>
                <th id="th2">Documento</th>
                <!-- <th id="th2">Descripción</th> -->
                <th id="th2">Código</th>
                <th id="th2">Producto</th>
                <th id="th2">Cantidad</th>
                <!-- <th id="th2">Precio</th>
                <th id="th2">Estado</th> -->
            </tr>
        </thead>
        <tfoot>
            <tr>
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
    pageLength: 50,
    searching: true,
    responsive: false,
    stateSave:  false,
    ajax:{
        url: "{{route('drep_compras')}}",
        dataSrc: "data",
    },
    dom: 'Bfrtip',
    lengthMenu: [
        [ 100, 200, 300, -1 ],
        [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
    ],
    buttons: [
        {
            className: 'nav-link',
            extend: 'pageLength',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {
            className: 'nav-link',
            extend: 'colvis',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {
            className: 'nav-link',
            extend: 'excelHtml5',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true,
            title: 'Reporte Rendimiento'
        }
    ],
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
        { "className": 'details-control',
            "orderable": false,
            "searchable": false,
            "data": null,
            "defaultContent": '<div class="text-center" style="width:100%; color: #3dc728; cursor:pointer;"><i class="fa fa-plus-circle"></i></div>'},
        { data: 'abreviatura', name: 'abreviatura'},
        { data: 'numero_orden', name: 'numero_orden'},
        { data: 'fechaemision', name:'fechaemision'},
        //{ data: 'partida_arancelaria', name: 'partida_arancelaria'},
        //{ data: 'referencia', name: 'referencia'},
        //{ data: 'requisicion', name: 'requisicion'},
        { data: 'Fecha_Recepcion', name: 'Fecha_Recepcion'},
        //{ data: 'descripcion_larga', name: 'descripcion_larga'},
        //{ data: 'Descripcion', name: 'Descripcion'},
        //{ data: 'Avance', name: 'Avance'},
        { data: 'proveedor', name: 'proveedor'},
        //{ data: 'Motivo', name: 'Motivo'},
        //{ data: 'Origen', name: 'Origen'},
        { data: 'Estado', name: 'Estado'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [0]},
        { targets: 0, searchable: true },
        { targets: [1,3,4,5], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }},
        {targets: [4], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ]
});
$('#column1_search').on('keyup', function(){
    table.columns(1).search(this.value).draw();
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column3_search').on('keyup', function(){
    table.columns(3).search(this.value).draw();
});
$('#column4_search').on('keyup', function(){
    table.columns(4).search(this.value).draw();
});
$('#column5_search').on('keyup', function(){
    table.columns(5).search(this.value).draw();
});
$('#column6_search').on('keyup', function(){
    table.columns(6).search(this.value).draw();
});

    $('#compras tbody').on('click', 'td.details-control', function(){
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var tableId = 'post-' + row.data().numero_orden;

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
            "order": [[ 2,"asc" ]],
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
            //{ data: 'descripcion_larga', name: 'descripcion_larga'},
            { data: 'nombre_corto', name: 'nombre_corto'},
            { data: 'nombre_fiscal', name: 'nombre_fiscal'},
            { data: 'cantidad', name: 'cantidad', render: $.fn.dataTable.render.number(',','.',2, ' ')}
            //{ data: 'precio', name: 'precio', render: $.fn.dataTable.render.number(',','.',2, ' ')},
            //{ data: "'PEDIDO'", name: "'PEDIDO'"}
        ],
        "columnDefs": [
            { bSortable: false, targets: [3]},
            { targets: 0, searchable: true },
            { targets: [1,2,3,4], searchable: true },
            { targets: '_all', searchable: false },
        ],
           "footerCallback": function(row,data,start,end,display){
            var api = this.api(), data;
            var intVal = function(i){
                return typeof i == 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                typeof i == 'number' ?
                    i:0;
            };

           /* var kilosTotal = api
            .column(5)
            .data()
            .reduce(function (a,b){
                return intVal(a) + intVal(b);
            }, 0);

            var numFormat = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
                numFormat(kilosTotal);
            pageTotal = api.column(5,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            }, 0);
            $(api.column(0).footer() ).html('Totales');
            $(api.column(5).footer()).html(numFormat(kilosTotal));*/
        }
        })
    }
</script>
@endsection
