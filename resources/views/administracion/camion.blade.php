@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.administracion'))
    <div class="container-fluid">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Listado de camiones</p>
        </blockquote>
        <a class="btn btn-warning btn-sm" style="margin-bottom: 5px" href="{{ route('v_a_camion') }}"><i
                class="fas fa-truck-monster"></i> Agregar un nuevo camión
        </a>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="camion">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Marca</th>
                        <th>Placa</th>
                        <th>Tonelaje</th>
                        <th>User / Pass</th>
                        <th>Sucursal</th>
                        <th>Estado</th>
                        <th>Editar Camion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($edit_camiones as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->marca }}</td>
                            <td>{{ $c->placa }}</td>
                            <td>{{ $c->tonelaje }}</td>
                            <td>{{ $c->tipo_camion }}</td>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->nombre }}</td>
                            <td><a class="btn btn-danger btn-sm" href="{{ route('v_e_camion', $c->id) }}">Editar</a> </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var table = $('#camion').DataTable({
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
                { targets: [0,1,2,3,4,5,6], searchable: true },
                { targets: '_all', searchable: false },
            ],
        });
        </script>
@endsection
