@extends('layouts.app')
@section('content')
    <link href="{{ asset('css/estilo.css') }}" rel="stylesheet">
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
                <a class="nav-link" href="{{ route('s_entregada') }}"><i class="fas fa-clipboard-check"></i> Solicitudes
                    Entregadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"href="{{ route('v_camiones') }}"><i class="fas fa-map-marked-alt"></i> Asignar Rutas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('v_cliente') }}"><i class="fas fa-users"></i> Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('v_sucursales') }}"><i class="fas fa-warehouse"></i> Entrega a
                    Sucursal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active bg-info" href="#"><b><i class="fas fa-parachute-box"></i> Por
                        recibir</b></a>
            </li>
        </ul>
        <h5>Entregas por Recibir</h5>
        <div class="table-condensed">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Factura o Comprobante</th>
                        <th>Sucursal que Envía</th>
                        <th>Fecha de LLegada</th>
                        <th>Hora Estimada</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitudes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->comprobante }}</td>
                            <td>{{ $s->usuario->name }}</td>
                            <td>{{ date('d-m-Y', ('strtotime')($s->fecha_entrega)) }}</td>
                            <td>{{ $s->hora }}</td>
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
