@extends('layouts.app')
@section('content')
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>
    <link href="{{ asset('css/solicitud_form.css') }}" rel="stylesheet">
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
            <a class="btn btn-dark" href="{{ route('v_sucursales') }}"><i class="fas fa-undo-alt"></i> Atrás</a>
        </div>
    </div>
    <hr>
    <div class="container-fluid">
        {!! Form::open(['route' => 'g_n_entrega', 'before' => 'csrf', 'method' => 'post']) !!}
        <form>
            <h4>Nueva Entrega a Sucursal</h4>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Sucursal</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-warehouse"></i></div>
                        </div>
                        <input type="text" class="form-control" value="{{ $sucursal->name }}" disabled>
                        <input type="text" class="form-control" id="cliente" placeholder="Sucursal" name="cliente"
                            value="{{ $sucursal->id }}" style="display:none;">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Correo</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-at"></i></div>
                        </div>
                        <input type="email" class="form-control" value="{{ $sucursal->email }}" disabled>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Comprobante</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-receipt"></i></div>
                        </div>
                        <input type="text" class="form-control" id="comprobante" placeholder="Comprobante"
                            name="comprobante" required>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud_soliitud"><b>Solicitar a Sucursal</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="departamento" name="id_sucursal" required>
                            <option value="">Seleccione una Sucursal</option>
                            @foreach ($sucursales as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Detalles de Entrega</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-comment-alt"></i></div>
                        </div>
                        <input type="text" class="form-control" id="detalle_entrega" placeholder="Detalles de Entrega"
                            name="detalle_entrega">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud">Fecha de Entrega</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                        <input type="date" class="form-control" id="fecha_entrega" placeholder="Fecha"
                            name="fecha_entrega" required>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud">Hora Aproximada</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-clock"></i></div>
                        </div>
                        <input type="time" class="form-control" id="hora" placeholder="Hora" name="hora">
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-md-center">
                <button type="submit" class="btn btn-success btn-block" id="boton"><b
                        class="fas fa-angle-double-right"> Continuar</b></button>
            </div>
        </form>
        {!! Form::close() !!}
    </div>
@endsection
