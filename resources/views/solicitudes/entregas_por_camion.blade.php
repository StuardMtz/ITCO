@extends('layouts.app')
@section('content')
    <link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
    <script src="{{ asset('js/datatables.min.js') }}"></script>

    <div class="container-fluid">
        <a class="btn btn-dark btn-sm" href="{{ route('entregas_en_espera') }}"><i class="fas fa-arrow-left"></i> Atrás</a>
        <blockquote class="blockquote text-center">
            <p class="mb-0">Total de entregas realizadas</p>
        </blockquote>
        <div class="table-reponsive-sm">
            <table class="table table-sm table-borderless" id="entregas" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="2"><input class="form-control" type="text" id="column1_search"
                                placeholder="Cliente"></th>
                        <th colspan="2"><input class="form-control" type="text" id="column2_search"
                                placeholder="Comprobante"></th>
                        <th><input class="form-control" type="text" id="column4_search" placeholder="2022-01-05"></th>
                        <th><input class="form-control" type="text" id="column5_search" placeholder="2022-01-01"></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Comprobante</th>
                        <th>Camión</th>
                        <th>Fecha en ruta</th>
                        <th>Fecha de entrega</th>
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
            pageLength: 100,
            searching: true,
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
            scroller: true,
            stateSave: true,
            ajax: {
                url: "{{ route('datos_entregas_por_camion', $id) }}",
                dataSrc: "data",
            },
            "order": [
                [0, "desc"]
            ],
            dom: 'Bfrtip',
            lengthMenu: [
                [100, 200, 300, -1],
                ['100 filas', '200 filas', '300 filas', 'Mostrar Todo']
            ],
            buttons: [{
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
                    data: 'placa',
                    name: 'placa'
                },
                {
                    data: 'fecha_ruta',
                    name: 'fecha_ruta'
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
            rowCallback: function(row, data) {
                if (data['fecha_ruta'] == null) {
                    $($(row).find("td")[4]).html('');
                }
                if (data['fecha_entregado'] == null) {
                    $($(row).find("td")[5]).html('');
                }
            },
            "columnDefs": [{
                    targets: 1,
                    searchable: true
                },
                {
                    targets: [0],
                    searchable: true
                },
                {
                    targets: [0, 1, 2, 4, 5],
                    searchable: true
                },
                {
                    targets: [4, 5],
                    render: function(data) {
                        moment.locale('es');
                        return moment(data).format('L');
                    }
                }
            ],
        });
        $('#column1_search').on('keyup', function() {
            table.columns(1).search(this.value).draw();
        });
        $('#column2_search').on('keyup', function() {
            table.columns(2).search(this.value).draw();
        });
        $('#column4_search').on('keyup', function() {
            table.columns(4).search(this.value).draw();
        });
        $('#column5_search').on('keyup', function() {
            table.columns(5).search(this.value).draw();
        });
        $('#entregas').on('click', 'tbody tr', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            window.location.href = (url_global + '/vcitud/' + row.data().id);
            redirectWindow.location;
        });
    </script>
@endsection
