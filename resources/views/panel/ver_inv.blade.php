@extends('layouts.app2')
@section('content')
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
<script src="{{ asset('js/handlebars.js')}}"></script>
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
    <a class="btn btn-dark"  onclick="window.close();" style="color:white;"><i class="fas fa-arrow-left"></i> Atrás</a>
    <a class="btn btn-danger" style="color:white;" href="{{route('edit_inve',$id)}}"><i class="far fa-edit"></i> Editar</a>
    <h5>Inventario número {{$id}}</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <tbody>
                @foreach($datos as $d)
                <tr>
                    <th scope="row">Nombre del Encargado</th>
                    <td>{{$d->encargado}} {{$d->apellidos}}</td>
                    <th scope="row">DPI</th>
                    <td>{{$d->identificacion}}</td>
                    <th scope="row">Realizado por</th>
                    <td>{{$d->nombre}}</td>

                    <th scope="row" colspan="2">Sucursal</th>
                    <td>{{$d->uninombre}}</td>
                    <th scope="row">Bodega</th>
                    <td>{{$d->bonombre}}</td>
                </tr>
                <tr>
                    @if($d->fecha_inicial == '')
                    <th scope="row">Fecha inicio</th>
                    <td>{{date('d/m/Y', strtotime($d->created_at))}}</td>
                    <th scope="row">Fecha fin</th>
                    <td>{{date('d/m/Y', strtotime($d->updated_at))}}</td>
                    @else
                    <th scope="row">Fecha inicio</th>
                    <td>{{date('d/m/Y', strtotime($d->fecha_inicial))}}</td>
                    <th scope="row">Fecha fin</th>
                    <td>{{date('d/m/Y', strtotime($d->fecha_final))}}</td>
                    @endif
                    <th scope="row">Estado</th>
                    <td>{{$d->estado}}</td>
                    <th scope="row">Dirección</th>
                    <td>{{$d->direccion}}</td>
                    @if($suma > 100)
                    <th scope="row">Realizado en</th>
                    <td>100%</td>
                    @else
                    <th scope="row">Realizado en </th>
                    <td>{{number_format($suma)}}%</td>
                    @endif
                    @if($d->estado == 'En proceso')
                    <td colspan="2"><a class="btn btn-secondary" href="{{route('fin',array('id'=>$id,'suma'=>$suma))}}">Finalizar</a></td>
                    @else
                    <td colspan="2"> <a class="btn btn-primary" href="{{ route('pdf',array('id'=>$id))}}"><i class="fas fa-print"></i> PDF</a></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            @if($d->estado == 'En proceso' && $suma >= 100)
            <tbody>
                <tr><div class="alert alert-danger" role="alert">Inventario completo, favor de finalizarlo.</div>
                </tr>
            </tbody>
            @else
            @endif
        </table>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover" id="inventario">
            <thead>
                <tr class="bg-info">
                    <th></th>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Teorico</th>
                    <th>Fisico</th>
                    <th>Diferencia</th>
                    <th>Dañado</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script id="details-template" type="text/x-handlebars-template">
@verbatim
    <div class="label label-info">Historial de ingresos del producto</div>
    <table class="table details-table" id="post-{{cod_producto}}">
        <thead>
            <tr class="bg-success">
                <th>Existencia fisica</th>
                <th>Descripción</th>
                <th>Fecha de operación</th>
                <th>Usuario</th>
            </tr>
        </thead>
    </table>
@endverbatim
</script>
<script>
    var template = Handlebars.compile($("#details-template").html());
    var table = $('#inventario').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        ajax:{
            url: "{{route('datos_inve',$id)}}",
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
            },
        },
        columns: [
                { "className": 'details-control',
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "defaultContent": '<div class="text-center" style="width:100%; color: #3dc728; cursor:pointer;"><i class="fa fa-plus-circle"></i></div>'},
                { data: 'categoria', name: 'categoria'},
                { data: 'nombre_corto', name: 'nombre_corto'},
                { data: 'nombre_fiscal', name:'nombre_fiscal'},
                { data: 'existencia_teorica', name:'existencia_teorica', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'existencia_fisica',name:'existencia_fisica', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'diferencias', name: 'diferencias', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'mal_estado', name: 'mal_estado'},
                { data: null, render: function(data,type,row){
                return "<a href='{{url('agre_ex')}}/"+data.id+ '/{{$suma}}'+"'class= 'btn btn-sm btn-dark' target='_blank'>Editar</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,3,4], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
            if(data['diferencias'] < 0){
                $($(row).find("td")[6]).css("background-color","#F26969");
            }
            else if(data['diferencias'] > 0){
                $($(row).find("td")[6]).css("background-color","#B2E7A8");
            }
        },
    });

    $('#inventario tbody').on('click', 'td.details-control', function(){
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
        { data: 'existencia_fisica', name: 'existencia_fisica', render: $.fn.dataTable.render.number(',', '.', 0, '')},
        { data: 'descripcion', name: 'descripcion'},
        { data: 'created_at', name: 'created_at'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,3], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [2], render:function(data){
             moment.locale('es');
            return moment(data).format('LLLL');}
        }
    ]
})
}
</script>
@endsection
