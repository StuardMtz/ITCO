@extends('layouts.app')
@section('content')
    <link href="{{ asset('css/estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $message }}</p>
            </div>
        @endif
    </div>
    <div class="container-fluid">
        <div class="row">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-clipboard"></i> Inventarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('s_inicio') }}"><i class="far fa-pause-circle"></i> En espera</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('s_e_ruta') }}"><i class="fas fa-shipping-fast"></i> Solicitudes en
                        Ruta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('s_entregada') }}"><i class="fas fa-clipboard-check"></i>
                        Solicitudes Entregadas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"href="{{ route('v_camiones') }}"><i class="fas fa-map-marked-alt"></i> Asignar
                        Rutas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('v_cliente') }}"><i class="fas fa-users"></i> Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="active"><i class="fas fa-warehouse"></i> Entrega a
                        Sucursal</a>
                </li> <!--
          <li class="nav-item">
            <a class="nav-link" href="{{ route('p_recibir') }}"><i class="fas fa-parachute-box"></i> Por recibir</a>
          </li> -->
            </ul>
        </div>
        <h5>Listado de Sucursales</h5>
        <div class="table-responsive-sm">
            <table class="table table-sm" id="clientes">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Correo</th>
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
            pageLength: 50,
            searching: true,
            ajax: {
                url: 'sucursales',
                dataSrc: "data",
            },
            "order": [
                [0, "desc"]
            ],
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return "<a href='{{ url('v_en_sucursal/') }}/" + data.id +
                            "' class= 'btn btn-sm btn-outline-info'><i class='fas fa-eye'></i> Nueva Entrega</button>"
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return "<a id='ver' href='{{ url('v_ep_sucursal/') }}/" + data.id +
                            "' class= 'btn btn-sm btn-outline-warning'><i class='fas fa-list-ol'></i> Total Entregas</button>"
                    }
                }
            ],
            "columnDefs": [{
                    targets: 1,
                    searchable: true
                },
                {
                    targets: [0, 1, 2, 3],
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
    </div>
@endsection
