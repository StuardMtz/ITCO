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
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Comprobante</th>
                        <th>Cliente</th>
                        <th>Fecha de Solicitud</th>
                        <th>Fecha de Entrega</th>
                        <th>Estado</th>
                        <th>Porcentaje</th>
                        <th>Info</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($soli_agregadas as $sa)
                        <tr>
                            <td>{{ $sa->id }}</td>
                            <td>{{ $sa->comprobante }}</td>
                            <td>{{ $sa->cliente->nombre }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($sa->created_at)) }}</td>
                            @if ($sa->fecha_entregado != '')
                                <td>{{ date('d/m/Y H:i', strtotime($sa->fecha_entregado)) }}</td>
                            @else
                                <td></td>
                            @endif
                            <td>{{ $sa->estado->nombre }}</td>
                            <td>{{ $sa->estado->porcentaje }}%</td>
                            <td><a class="btn btn-sm btn-outline-dark" id="ver"
                                    href="{{ route('ver_solicitud', $sa->id) }}"><i class="fas fa-info-circle"></i> Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        window.onload = function() {
            var pos = window.name || 0;
            window.scrollTo(0, pos);
        }
        window.onunload = function() {
            window.name = self.pageYOffset || (document.documentElement.scrollTop + document.body.scrollTop);
        }
    </script>
@endsection
