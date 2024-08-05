@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            @foreach($datos as $d)
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Nombre del Encargado:</b> {{$d->encargado}} {{$d->apellidos}}</li>
                <li class="list-inline-item"> <b>DPI: </b>{{$d->no_identificacion}} </li>
                <li class="list-inline-item"> <b>Realizado por: </b>{{$d->nombre}} </li>
                <li class="list-inline-item"> <b>Sucursal: </b>{{$d->sucursal}} </li>
                <li class="list-inline-item"> <b>Bodega: </b>{{$d->bodega}} </li>
                @if($d->fecha_inicial == '')
                <li class="list-inline-item"> <b>Fecha inicio: </b>{{date('d/m/Y', strtotime($d->created_at))}} </li>
                <li class="list-inline-item"> <b>Fecha fin: </b>{{date('d/m/Y H:i:s', strtotime($d->updated_at))}} </li>
                @else
                <li class="list-inline-item"> <b>Fecha inicio: </b>{{date('d/m/Y', strtotime($d->fecha_inicial))}} </li>
                <li class="list-inline-item"> <b>Fecha fin: </b>{{date('d/m/Y H:i:s', strtotime($d->updated_at))}} </li>
                @endif
                <li class="list-inline-item"> <b>Estado: </b>{{$d->estado}} </li>
                <li class="list-inline-item"> <b>Dirección: </b>{{$d->direccion}} </li>
                <li class="list-inline-item"> <b>Realizado en: </b>{{number_format($d->porcentaje)}}% </li>
                <li class="list-inline-item"><b>Total de productos inventariados: </b>{{$ver}} </li>
                <li class="list-inline-item"><b>Inventario realizado en: </b>{{$d->diferencia}} </li>
                @if($inicio == '')
                <li class="list-inline-item"><b>Fecha del primer ingreso: </b> </li>
                @else
                <li class="list-inline-item"><b>Fecha del primer ingreso: </b>{{date('d/m/Y H:i:s', strtotime($inicio))}} </li>
                @endif
                @if($final == '')
                <li class="list-inline-item"><b>Fecha del último ingreso: </b> </li>
                @else
                <li class="list-inline-item"><b>Fecha del último ingreso: </b>{{date('d/m/Y H:i:s', strtotime($final))}} </li>
                @endif
                <li class="list-inline-item"><b>Total de productos dañados: </b>{{$mal_estado}} </li>
            </ul>
            @endforeach
            <div class="row">
                <div class="btn-group" role="group" aria-label="Opciones inventario">
                    <a class="btn btn-dark btn-sm"  onclick="window.close();" style="color:white;"><i class="fas fa-times-circle"></i> Cerrar</a>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editarInventario">
                        <i class="far fa-edit"></i> Editar inventario
                    </button>
                    @if($d->estado == 'En proceso')
                    <a class="btn btn-danger btn-sm" href="{{route('finalizar_inventario',['id'=>$id,'suma'=>$d->porcentaje])}}">Finalizar</a>
                    @else
                    @endif
                </div>
            </div>
            @if($d->estado == 'En proceso' && $d->porcentaje >= 100)
            @elseif($d->estado == 'Finalizado' )
            <div class="btn-group" role="group" aria-label="Botons para PDF">
                <a class="btn btn-sm btn-info" href="{{route('pdf',array('id'=>$id))}}"><i class="fas fa-file-pdf"></i> Inventario en PDF</a>
                <a class="btn btn-sm btn-info" href="{{route('pdf_dif_pos',$id)}}"><i class="fas fa-file-pdf"></i> Diferencias positivas PDF</a>
                <a class="btn btn-sm btn-info" href="{{route('pdf_dif_neg',$id)}}"><i class="fas fa-file-pdf"></i> Diferencias negativas PDF</a>
            </div>
            @endif
        </div>
    </div>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="inventario">
            <thead>
                <tr>
                    <th></th>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Teorico</th>
                    <th>Fisico</th>
                    <th>Diferencia</th>
                    <th>Dañado</th>
                </tr>
            </thead>
        </table>
    </div>

<!-- Modal para editar el estado y fecha de un inventario -->
<div class="modal fade" id="editarInventario" tabindex="-1" aria-labelledby="editarInventarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarInventarioModalLabel">Editar datos del inventario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form method="post" action="{{route('actualizar_inventario',$id)}}">
            @method('PATCH')
            {{csrf_field()}}
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="Editar_estado">Estado del inventario</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fas fa-check"></i></div>
                            </div>
                            <select class="form-control" name="estado">
                                <option value="En proceso">En proceso</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="nuevo_inventario">Fecha de inicio</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="far fa-calendar"></i></div>
                            </div>
                            <input type="date" class="form-control" name="fecha_inicial" value="{{$d->fecha_inicial}}">
                        </div>
                    </div>
                    <div class="form-group col">
                        <label for="nuevo_inventario">Fecha final</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="far fa-calendar"></i></div>
                            </div>
                            <input type="date" class="form-control" name="fecha_final" value="{{$d->fecha_final}}">
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <button type="submit" class="btn btn-secondary btn-block"><i class="fas fa-edit"></i> Actualizar</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<script id="details-template" type="text/x-handlebars-template">
@verbatim
    <div class="label label-info">Historial de ingresos del producto</div>
    <div class="table-responsive-sm">
        <table class="table details-table" id="post-{{cod_producto}}">
            <thead>
                <tr class="bg-success">
                    <th>Existencia</th>
                    <th>Existencia fisica</th>
                    <th>Diferencia</th>
                    <th>Descripción</th>
                    <th>Fecha de operación</th>
                    <th>Usuario</th>
                </tr>
            </thead>
        </table>
    </div>
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
    dom: 'Bfrtip',
    lengthMenu: [
        [ 10, 25, 50, -1 ],
        [ '10 filas', '25 filas', '50 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {   extend: 'pageLength',
            className: 'btn btn-dark',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {   extend: 'colvis',
            className: 'btn btn-dark',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {   extend: 'excelHtml5',
            className: 'btn btn-dark',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
        }
    ],
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
        { data: 'categoria', name: 'categoria'},
        { data: 'nombre_corto', name: 'nombre_corto'},
        { data: 'nombre_fiscal', name:'nombre_fiscal'},
        { data: 'existencia_teorica', name:'existencia_teorica', render: $.fn.dataTable.render.number(',','.',0 )},
        { data: 'existencia_fisica',name:'existencia_fisica', render: $.fn.dataTable.render.number(',','.',0 )},
        { data: 'diferencias', name: 'diferencias', render: $.fn.dataTable.render.number(',','.',0 )},
        { data: 'mal_estado', name: 'mal_estado'}
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
            { data: 'existencia', name: 'existencia'},
            { data: 'existencia_fisica', name: 'existencia_fisica', render: $.fn.dataTable.render.number(',','.',0 )},
            { data: 'diferencia', name: 'diferencia', render: $.fn.dataTable.render.number(',','.',0 )},
            { data: 'descripcion', name: 'descripcion'},
            { data: 'created_at', name: 'created_at'},
            { data: 'name', name: 'name'}
        ],
        "columnDefs": [
            { targets: 0, searchable: true },
            { targets: [0,1,2,3], searchable: true },
            { targets: '_all', searchable: false },
            { targets: [4], render:function(data){
                moment.locale('es');
                return moment(data).format('LLLL');}
            }
        ]
    })
}
</script>
@endsection
