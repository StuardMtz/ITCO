@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.graficas'))
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script src="{{ asset('js/datatables.js')}}"></script>
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Máximo y Mínimo general</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="ordenes">
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Nombre corto</th>
                    <th>Nombre fiscal</th>
                    <th>Mínimo</th>
                    <th>Existencia</th>
                    <th>Máximo</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
var table = $('#ordenes').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    ajax:{
        url: 'dmimag',
        dataSrc: 'data',
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
        },
    },
    columns: [
        { data: 'name', name:  'name'},
        { data: 'nombre_corto', name: 'nombre_corto'},
        { data: 'nombre_fiscal', name: 'nombre_fiscal'},
        { data: 'minimo', name: 'minimo', render: $.fn.dataTable.render.number(',', '.', 0, '')},
        { data: 'existencia1', name: 'existencia1', render: $.fn.dataTable.render.number(',', '.', 0, '')},
        { data: 'maximo', name: 'maximo', render: $.fn.dataTable.render.number(',', '.', 0, '')},
        { data: 'porcentaje', name: 'porcentaje', render: $.fn.dataTable.render.number(',', '.', 0, '')},
    ],
    "columnDefs": [
        { bSortable: false, targets: [0,1,2,6]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6], searchable: true },
        { targets: '_all', searchable: false }
    ],
    rowCallback:function(row,data){
        if((data['porcentaje'] >= 75) & (data['porcentaje'] <= 100)){
            $($(row).find("td")[3]).css("background-color","#A5E4A2");
            $($(row).find("td")[4]).css("background-color","#B0DBDC");
            $($(row).find("td")[5]).css("background-color","#A5E4A2");
            $($(row).find("td")[6]).css("background-color","#A5E4A2");
        }
        if((data['porcentaje'] >= 50) & (data['porcentaje'] < 75)){
            $($(row).find("td")[3]).css("background-color","#C9E9B8");
            $($(row).find("td")[4]).css("background-color","#B0DBDC");
            $($(row).find("td")[5]).css("background-color","#C9E9B8");
            $($(row).find("td")[6]).css("background-color","#C9E9B8");
        }
        if((data['porcentaje'] >= 30) & (data['porcentaje'] < 50)){
            $($(row).find("td")[3]).css("background-color","#F5EB98");
            $($(row).find("td")[4]).css("background-color","#B0DBDC");
            $($(row).find("td")[5]).css("background-color","#F5EB98");
            $($(row).find("td")[6]).css("background-color","#F5EB98");
        }
        if(data['porcentaje'] < 30){
            $($(row).find("td")[3]).css("background-color","#F78181");
            $($(row).find("td")[4]).css("background-color","#B0DBDC");
            $($(row).find("td")[5]).css("background-color","#F78181");
            $($(row).find("td")[6]).css("background-color","#F78181");
        }
        else
        {
            $($(row).find("td")[4]).css("background-color","#B0DBDC");
        }
    },
    "footerCallback": function(row,data,start,end,display){
        var api = this.api(), data;
        var intVal = function(i){
            return typeof i == 'string' ?
                i.replace(/[\$,]/g, '')*1 :
            typeof i == 'number' ?
                i:0;
        };
        minimo = api
        .column(3)
        .data()
        .reduce(function (a,b){
            return intVal(a) + intVal(b);
        }, 0);


        existencia = api.column(4)
        .data()
        .reduce( function (a,b){
            return intVal(a) + intVal(b);
        }, 0);

        maximo = api.column(5)
        .data()
        .reduce( function (a,b){
            return intVal(a) + intVal(b);
        }, 0);

        var Format = $.fn.dataTable.render.number( '\,', '.', 0 ).display;
        Format(minimo);
        minimo = api.column(3,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            },
        0);
        existencia = api.column(4,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            },
        0);
        maximo = api.column(5,{page: 'current'})
            .data()
            .reduce( function (a,b){
                return intVal(a) + intVal(b);
            },
        0);
        $(api.column(2).footer() ).html('Totales');
        $(api.column(3).footer()).html(Format(minimo)+' Mínimo');
        $(api.column(4).footer()).html(Format(existencia)+' Existencia');
        $(api.column(5).footer()).html(Format(maximo)+' Máximo');
    }
});
$(document).ready(function() {
// Setup - add a text input to each footer cell
    $('#ordenes thead tr').clone(true).appendTo( '#ordenes thead' );
    $('#ordenes thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" />' );
        $( 'input', this ).on( 'keyup change', function () {
            var val = $.fn.dataTable.util.escapeRegex(
                $(this).val()
            );
            if ( table.column(i).search() !== this.value ) {
                table
                .column(i)
                .search( val ? '^'+val+'$' : '', true, false )
                .draw();
            }
        });
    });
});
</script>
@endsection
