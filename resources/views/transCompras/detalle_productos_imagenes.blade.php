@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<link rel="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<div class="container-fluid">
    <a class="btn btn-dark" href="javascript: history.go(-1)">Atrás</a>
    @if($inicio == 0 && $fin == 0)
    <a class="btn btn-danger" href="{{url('printpdfdrepromeim',$cod_producto)}}">Imprimir PDF</a>
    @else
    <a class="btn btn-danger" href="{{url('printpdffechas',['cod_producto'=>$cod_producto,'inicio'=>$inicio,'fin'=>$fin])}}">Imprimir PDF</a>
    @endif
    <div class="table-responsive-md">
        <table class="table table-sm" id="reporte">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>No. transferencia</th>
                    <th>Observación</th>
                    <th>Fecha</th>
                    <th>Imagen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalles as $key =>$dt)
                <tr>
                    <td>{{$dt['nombre_corto']}}</td>
                    <td>{{$dt['nombre_fiscal']}}</td>
                    <td>{{$dt['num_movi']}}</td>
                    <td>{{$dt['observacion']}}</td>
                    <td>{{date('d/m/Y',strtotime($dt['fechaSucursal']))}}</td>
                    <td><img class="img-fluid" style="width:85%;height:400px;" src="data:image/jpg;base64,{{($dt['imagen'])}}"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
var table = $('#reporte').DataTable({
    processing: false,
    serverSide: false,
    pageLength: 100,
    searching: true,
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
    }
});
</script>
@endsection
