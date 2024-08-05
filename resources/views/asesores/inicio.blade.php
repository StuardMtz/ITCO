@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<div class="container-fluid">
    <h5>Listado de Sucursales</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Mínimos y Máximos</th>
                    <th>Solicitud de Entrega</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
    <script>
    $('#sucursales').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: 'listado_sucursales',
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
            { data: 'name', name: 'name'},
            { data: null, render: function(data,type,row){
                return "<a href='{{url('existencia_sucursal/')}}/"+data.sucursal+"/"+data.bodega+"/"+1+"'  class= 'btn btn-dark btn-sm' ><i class='fas fa-chart-line'></i> Ver Minimax</button>"}
            },
            { data: null, render: function(data,type,row){
                return "<a href='{{url('vis_ru/')}}/"+data.id+"'  class= 'btn btn-info btn-sm' ><i class='fas fa-chart-line'></i> Solicitud Entrega</button>"}
            }
        ],
        "columnDefs": [
            { targets: 0, searchable: true },
            { targets: [0], searchable: true },
            { targets: '_all', searchable: false },
        ],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var input = document.createElement("input");
                $(input).appendTo($(column.footer()).empty())
                .on('change', function () {
                    column.search($(this).val(), false, false, true).draw();
                });
            });
        }
    });
    </script>
@endsection
