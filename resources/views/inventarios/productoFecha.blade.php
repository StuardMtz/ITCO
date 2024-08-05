@extends('layouts.app')
@section('content')
    <a class="btn btn-dark btn-sm" href="{{route ('inventarios_realizados',$id)}}">Atrás</a>
    <hr>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="inventario">
            <thead class="thead-dark">
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Teorico</th>
                    <th>Fisico</th>
                    <th>Dañado</th>
                    <th>Fecha</th>
                    <th>Semana</th>
                    <th>Realizado por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($producto as $pro)
                <tr>
                    <td>{{$pro->nombre_corto}}</td>
                    <td>{{$pro->nombre_fiscal}}</td>
                    <td>{{number_format($pro->existencia_teorica)}}</td>
                    <td>{{number_format($pro->existencia_fisica)}}</td>
                    <td>{{number_format($pro->mal_estado)}}</td>
                    <td>{{date('d/m/Y H:i',strtotime($pro->updated_at))}}</td>
                    <td>{{$pro->semana}}</td>
                    <td>{{$pro->name}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<script>
var table = $('#inventario').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    scrollX: false,
    scrollY: '62vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
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
    "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1], searchable: true },
        { targets: '_all', searchable: false },
    ],
});
$('#inventario').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var redirectWindow = window.open(url_global+'/ver/'+{{$pro->no_encabezado}});
    redirectWindow.location;
});
</script>
@endsection
