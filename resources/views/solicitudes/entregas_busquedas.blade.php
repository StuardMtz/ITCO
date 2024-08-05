@extends('layouts.app2')
@section('content')
    <link href="{{ asset('css/solicitud.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
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
        </div>
        <h4>Resultado de Busqueda</h4>
        <hr>
        <div class="row">
            <div class="col">
                {!! Form::open(['route' => ['b_f_solicitud'], 'method' => 'get']) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Fecha Inicial</div>
                    </div>
                    <input type="date" class="form-control" name="fecha_inicial">
                </div>
            </div>
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Fecha Final</div>
                    </div>
                    <input type="date" class="form-control" name="fecha_final">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i
                            class="fas fa-search"></i></button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="table-condensed">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Factura o Comprobante</th>
                        <th>Cliente</th>
                        <th>Fecha Solicitud</th>
                        <th>Fecha de Entrega</th>
                        <th>Camión</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitudes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->comprobante }}</td>
                            @if ($s->id_departamento != '')
                                <td>{{ $s->cliente->nombre }}</td>
                            @else
                                <td>{{ $s->sucur->name }}</td>
                            @endif
                            <td>{{ date('d-m-Y H:i', strtotime($s->created_at)) }}</td>
                            <td>{{ date('d-m-Y H:i', strtotime($s->fecha_entregado)) }}</td>
                            <td>{{ $s->camion->placa }}</td>
                            <td><a class="btn btn-outline-info" id="ver" href="{{ route('v_solicitud', $s->id) }}"><i
                                        class="fas fa-eye"></i> Ver</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
