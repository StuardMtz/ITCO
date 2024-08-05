@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.administracion'))
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
        <blockquote class="blockquote text-center">
            <p class="mb-0">Usuarios</p>
        </blockquote>
        <a class="btn btn-success btn-sm" style="margin-bottom: 5px" href="{{ route('agu') }}"><i
            class="fas fa-user-plus"></i> Agregar usuario</a>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="usuarios">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Editar</th>
                        <th>Actividad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $us)
                        <tr>
                            <td>{{ $us->name }}</td>
                            <td>{{ $us->email }}</td>
                            <td>{{ $us->nombre }}</td>
                            <td><a class="btn btn-danger btn-sm" href="{{ route('edius', $us->id) }}">Editar</a>
                            <td><a class="btn btn-dark btn-sm" href="{{ route('histo', $us->id) }}">Ver</a>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var table = $('#usuarios').DataTable({
            pageLength: 50,
            serverSide: false,
            searching: true,
            responsive: false,
            "order": [
                [0, "asc"]
            ],
            scrollY: '70vh',
            scrollCollapse: true,
            scroller: true,
            stateSave: true,
            "stateDuration": 300,
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
            "columnDefs": [{
                    targets: 0,
                    searchable: true
                },
                {
                    targets: [0, 1, 2],
                    searchable: true
                },
                {
                    targets: '_all',
                    searchable: false
                },
            ],
        });
    </script>
@endsection
