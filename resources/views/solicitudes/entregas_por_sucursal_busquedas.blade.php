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
            <a class="btn btn-dark" href="{{ route('v_ep_sucursal', $id) }}"><i class="fas fa-undo-alt"></i> Atrás</a>
        </div>
        <h4>Listado de Entregas</h4>
        <hr>
        <div class="row">
            <div class="col">
                {!! Form::open(['route' => ['b_c_entregas', $id], 'method' => 'get']) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-receipt"></i></div>
                    </div>
                    <input type="text" class="form-control" placeholder="Comprobante" name="comprobante">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i
                            class="fas fa-search"></i></button>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="col">
                {!! Form::open(['route' => ['b_e_entregas', $id], 'method' => 'get']) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><b class="fas fa-map"></b></div>
                    </div>
                    <select class="form-control" name="estado" required>
                        <option value="">Seleccione un Estado</option>
                        @foreach ($estados as $e)
                            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i
                            class="fas fa-search"></i></button>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="col">
                {!! Form::open(['route' => ['b_f_entregas', $id], 'method' => 'get']) !!}
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
                        <th>Nombre</th>
                        <th>Comprobante</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th>Fecha Entregado</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entregas as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>{{ $e->cliente->nombre }}</td>
                            <td>{{ $e->comprobante }}</td>
                            <td>{{ $e->estado->nombre }}</td>
                            <td>{{ date('d-m-Y', strtotime($e->created_at)) }}</td>
                            @if ($e->fecha_entregado != '')
                                <td>{{ date('d-m-Y', strtotime($e->fecha_entregado)) }}</td>
                            @else
                                <td></td>
                            @endif
                            <td><a class="btn" id="ver" href="{{ route('v_solicitud', $e->id) }}"><i
                                        class="fas fa-eye"></i> Ver</a></td>
                        </tr>
                    @endforeach
            </table>
        </div>
    </div>
@endsection
