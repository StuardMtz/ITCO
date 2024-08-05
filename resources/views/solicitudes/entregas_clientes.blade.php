@extends('layouts.app')
@section('content')
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>
    <div class="container-fluid">
        <a class="btn btn-dark btn-sm" href="{{ route('vista_clientes') }}"><i class="fas fa-arrow-left"></i> Atrás</a>
        <blockquote class="blockquote text-center">
            <p class="mb-0">Total de entregas realizadas</p>
        </blockquote>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="entregas" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Comprobante</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th>Fecha Entregado</th>
                        <th>Tiempo de entrega</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <script>
        var table = $('#entregas').DataTable({
            processing: true,
            serverSide: false,
            pageLength: 50,
            searching: true,
            scrollX: true,
            scrollY: '75vh',
            scrollCollapse: true,
            scroller: true,
            stateSave: true,
            ajax: {
                url: "{{ route('total_entregas', $id) }}",
                dataSrc: "data",
            },
            "order": [
                [0, "desc"]
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
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'comprobante',
                    name: 'comprobante'
                },
                {
                    data: 'estado',
                    name: 'estado'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'fecha_entregado',
                    name: 'fecha_entregado'
                },
                {
                    data: 'tiempo',
                    name: 'tiempo'
                }
            ],
            "columnDefs": [{
                    targets: 1,
                    searchable: true
                },
                {
                    targets: [0, 1, 2, 3, 4],
                    searchable: true
                },
                {
                    targets: '_all',
                    searchable: false
                },
                {
                    targets: [4, 5],
                    render: function(data) {
                        moment.locale('es');
                        return moment(data).format('LLLL');
                    }
                }
            ],
            rowCallback: function(row, data) {
                if (data['fecha_entregado'] == null) {
                    $($(row).find("td")[5]).html('');
                }
                if (data['fecha_carga'] == null || data['fecha_entregado'] == null) {
                    $($(row).find("td")[6]).html('');
                }
            }
        });
        $('#entregas').on('click', 'tbody tr', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            window.location.href = (url_global + '/vcitud/' + row.data().id);
            redirectWindow.location;
        });
    </script>
@endsection
