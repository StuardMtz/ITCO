@extends('layouts.app')
@section('content')
    @yield('content', View::make('layouts.solicitudes'))
    <div class="container-fluid">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Listado de clientes</p>
        </blockquote>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="clientes" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="8"><a class="btn btn-light btn-sm" href="{{ route('crear_nuevo_cliente') }}"><i
                                    class="fas fa-user-plus"></i> Agregar un nuevo cliente</a></th>
                    </tr>
                    <tr>
                        <th>Código</th>
                        <th>Nit</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Editar</th>
                        <th>Nueva Entrega</th>
                        <th>Total Entregas</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <script>
        $('#clientes').DataTable({
            processing: true,
            serverSide: false,
            pageLength: 70,
            searching: true,
            scrollX: true,
            scrollY: '65vh',
            scrollCollapse: true,
            scroller: true,
            stateSave: true,
            ajax: {
                url: 'datclie',
                dataSrc: "data",
            },
            "order": [
                [2, "asc"]
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
                    data: 'nit',
                    name: 'nit'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'correo',
                    name: 'correo'
                },
                {
                    data: 'telefono',
                    name: 'telefono'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return "<a href='{{ url('visedcli/') }}/" + data.id +
                            "'  class= 'btn btn-danger btn-sm' ><i class='fas fa-edit'></i> Editar</button>"
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return "<a id='boton' href='{{ url('nuesol/') }}/" + data.id +
                            "' class= 'btn btn-sm btn-warning'>Nueva entrega</button>"
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return "<a id='ver' href='{{ url('e_p_cliente/') }}/" + data.id +
                            "' class= 'btn btn-dark btn-sm'>Ver entregas</button>"
                    }
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
            ],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                        .on('change', function() {
                            column.search($(this).val(), false, false, true).draw();
                        });
                });
            }
        });
    </script>
@endsection
