@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ url('/') }}/storage/sistegualogo.png"
                width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ url('/') }}/storage/opciones.png" width="25">
        </button>
        <div class="navbar-nav">
            <a class="nav-link vista" href="javascript:history.back()">Atrás</a>
            <a class="nav-link vista" href="{{ route('inicio_gastos_espera') }}">Solicitudes</a>
            <a class="nav-link vista" href="{{ route('rep_liquida') }}">Reporte de liquidaciones</a>
            <a class="nav-link vista" href="{{ route('resumen_total_de_gastos') }}">Resumen total</a>
            @guest
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </div>
    </nav>
    <div class="container-fluid">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Resumen de gastos</p>
        </blockquote>
        <div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <strong>¡{{ $message }}!</strong>
                </div>
            @endif
            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <strong>¡{{ $message }}!</strong>
                </div>
            @endif
        </div>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="gastos" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="2"><input type="text" id="column1_search" placeholder="Unidad"
                                class="form-control"></th>
                        <th colspan="3"><input type="text" id="column2_search" placeholder="Sucursal"
                                class="form-control"></th>
                        <th colspan="1"><input type="text" id="column3_search" placeholder="Mes" class="form-control">
                        </th>
                        <th colspan="1"><input type="text" id="column4_search" placeholder="Año" class="form-control">
                        </th>
                        <th colspan="3"><input type="text" id="column5_search" placeholder="Tipo"
                                class="form-control"></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Unidad</th>
                        <th>Sucursal</th>
                        <th>Primero registrado</th>
                        <th>Ultimo registrado</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Tipo</th>
                        <th>Registrados</th>
                        <th>Monto total</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <div class="label label-info" style="text-align: center; background-color:#a4b4dd">Detalle de gasto: </div>
            <table class="table details-table" id="post-{{keym}}">
                <thead>
                    <tr>
                        <th id="th2">Serie</th>
                        <th id="th2">Numero</th>
                        <th id="th2">Descripción</th>
                        <th id="th2">Proveedor</th>
                        <th id="th2">Registrado</th>
                        <th id="th2">Autorizado</th>
                        <th id="th2">Autorizo</th>
                        <th id="th2">No. Retencion</th>
                        <th id="th2">IVA</th>
                        <th id="th2">Monto</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                        <th style="background-color:#a4b4dd;color:rgb(201, 181, 0);"></th>
                    </tr>
                </tfoot>
            </table>
        @endverbatim
        </script>
    <script>
    var template = Handlebars.compile($("#details-template").html());
    var table = $('#gastos').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        responsive: false,
        scrollX: true,
        scrollY: '70vh',
        scrollCollapse: true,
        scroller: true,
        stateSave: false,
        ajax: {
            url: "{{ route('datos_resumen_de_gastos') }}",
            dataSrc: 'data',
        },
        "order": [
            [0, "asc"]
        ],
        dom: 'Bfrtip',
        lengthMenu: [
            [100, 200, 300, -1],
            ['100 filas', '200 filas', '300 filas', 'Mostrar Todo']
        ],
        buttons: [
            {
                extend: 'pageLength',
                className: 'btn btn-dark',
                text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
            },
            {
                extend: 'colvis',
                className: 'btn btn-dark',
                text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
            },
            {
                extend: 'excelHtml5',
                className: 'btn btn-dark',
                text: '<i class="far fa-file-excel"></i>  Exportar a excel',
                autoFilter: true,
                title: '{{ Auth::user()->name }}'
            }
        ],
        "language": {
            "lengthMenu": "<span class='text-paginate'>Mostrar _MENU_ registros</span>",
            "zeroRecords": "No se encontraron resultados",
            "EmptyTable": "Ningún dato disponible en esta tabla =(",
            "info": "<span class='text-paginate'>Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros</span>",
            "infoEmty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "InfoPostFix": "",
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
            {
                data: 'cod_unidad',
                name: 'cod_unidad'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'Primero_registrado',
                name: 'Primero_registrado'
            },
            {
                data: 'Ultimo_registrado',
                name: 'Ultimo_registrado'
            },
            {
                data: 'Mes',
                name: 'Mes'
            },
            {
                data: 'Year',
                name: 'Year'
            },
            {
                data: 'Tipo',
                name: 'Tipo'
            },
            {
                data: 'Cantidad_total',
                name: 'Cantidad_total'
            },
            {
                data: 'Monto_total',
                name: 'Monto_total'
            },
        ],
        "columnDefs": [{
            targets: 0,
            searchable: true
        },
        {
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
            searchable: true
        },
        {
            targets: '_all',
            searchable: false
        },
    ],
    rowCallback: function(row, data) {
        $($(row).find("td")[1]).css("text-transform", "capitalize");
    },
});
$('#column1_search').on('keyup', function() {
    table.columns(1).search(this.value).draw();
});
$('#column2_search').on('keyup', function() {
    table.columns(2).search(this.value).draw();
});
$('#column3_search').on('keyup', function() {
    table.columns(5).search(this.value).draw();
});
$('#column4_search').on('keyup', function() {
    table.columns(6).search(this.value).draw();
});
$('#column5_search').on('keyup', function() {
    table.columns(7).search(this.value).draw();
});
$('#gastos tbody').on('click', 'td.details-control', function(){
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var tableId = 'post-' + row.data().keym;

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
            pageLength: 10,
            ajax: data.details_url,
            "order": [[ 4,"asc" ]],
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
            { data: 'serie_documento', name: 'serie_documento' },
            { data: 'no_documento', name: 'no_documento'},
            { data: 'descripcion', name: 'descripcion'},
            { data: 'proveedor', name: 'proveedor'},
            { data: 'Registrado', name: 'Registrado'},
            { data: 'Autorizado', name: 'Autorizado'},
            { data: 'name', name: 'name'},
            { data: 'no_retencion', name: 'no_retencion'},
            { data: 'iva', name: 'iva', render: $.fn.dataTable.render.number(',','.',2, ' ')},
            { data: 'monto', name: 'monto', render: $.fn.dataTable.render.number(',','.',2, ' ')},
        ],
        "columnDefs": [
            { targets: 0, searchable: true },
            { targets: [0,1,2,3,4,5,6,7], searchable: true },
            { targets: '_all', searchable: false },
        ],
        rowCallback: function(row, data) {
            $($(row).find("td")[6]).css("text-transform", "capitalize");
        },
           "footerCallback": function(row,data,start,end,display){
            var api = this.api(), data;
            var intVal = function(i){
                return typeof i == 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                typeof i == 'number' ?
                    i:0;
            };
        }
        })
    }
    </script>
@endsection
