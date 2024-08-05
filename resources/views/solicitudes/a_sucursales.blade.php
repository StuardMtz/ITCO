@extends('layouts.app')
@section('content')
    <link href="{{ asset('css/solicitud.css') }}" rel="stylesheet">
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
            <div class="btn-group dropright">
                <button id="opciones" type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false"><i class="fas fa-cogs"></i> Opciones</button>
                <div class="dropdown-menu">
                    <a id="drop" class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-clipboard"></i>
                        Inventarios</a>
                    <a id="drop" class="dropdown-item" href="{{ route('s_e_ruta') }}"><i
                            class="fas fa-shipping-fast"></i> Solicitudes en Ruta</a>
                    <a id="drop" class="dropdown-item" href="{{ route('s_entregada') }}"><i
                            class="fas fa-clipboard-check"></i> Solicitudes Entregadas</a>
                    <a id="drop" class="dropdown-item" href="{{ route('r_inicio') }}"><i class="fas fa-route"></i>
                        Rutas Creadas</a>
                </div>
            </div>
            <a id="n_cliente" class="btn" href="{{ route('n_cliente') }}"><i class="fas fa-user-plus"></i> Entrega Nuevo
                Cliente</a>
            <a id="cliente" class="btn" href="{{ route('v_cliente') }}"><i class="fas fa-users"></i> Entrega a
                Cliente</a>
            <a id="sucursal" class="btn" href="{{ route('v_sucursales') }}"><i class="fas fa-warehouse"></i> Entrega a
                Sucursal</a>
        </div>
        <h4>Solicitudes para entregar a Clientes en Espera</h4>
        <hr>
        <div class="table-condensed">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Factura o Comprobante</th>
                        <th>Cliente</th>
                        <th>Fecha de Solicitud</th>
                        <th>Editar</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitudes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->comprobante }}</td>
                            <td>{{ $s->cliente->nombre }}</td>
                            <td>{{ date('d-m-Y', ('strtotime')($s->fecha_entrega)) }}</td>
                            <td><a class="btn btn-dark" href="{{ route('v_e_solicitud', $s->id) }}"><i
                                        class='fas fa-edit'></i> Editar</a></td>
                            <td><a class="btn" id="ver" href="{{ route('v_solicitud', $s->id) }}"><i
                                        class="fas fa-eye"></i> Ver</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $solicitudes->links() }}
    </div>
@endsection
