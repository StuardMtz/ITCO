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
    <a class="btn btn-dark" href="{{ route('s_inicio') }}"><i class="fas fa-undo-alt"></i> Atrás</a>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Editar entrega a Sucursal</h4>
            </div>
            {!! Form::model($solicitudes, ['method' => 'PATCH', 'route' => ['e_e_sucursal', $solicitudes->id]]) !!}
            <form>
                <div class="row">
                    <div class="col">
                        <label for="nueva_solicitud"><b>Sucursal</b></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><b class="fas fa-user"></b></div>
                            </div>
                            <input type="text" class="form-control" value="{{ $solicitudes->sucur->name }}" disabled>
                            <input type="text" class="form-control" id="cliente" placeholder="Cliente" name="cliente"
                                value="{{ $solicitudes->id_cliente }}" style="display:none;">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <label for="nueva_solicitud"><b>Correo</b></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fas fa-at"></i></div>
                            </div>
                            <input type="email" class="form-control" value="{{ $solicitudes->sucur->email }}" disabled>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <label for="nueva_solicitud"><b>Comprobante</b></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fas fa-receipt"></i></div>
                            </div>
                            <input type="text" class="form-control" id="comprobante" placeholder="Comprobante"
                                value="{{ $solicitudes->comprobante }}" name="comprobante" required>
                        </div>
                    </div>
                    <div class="col">
                        <label for="nueva_solicitud_soliitud"><b>Solicitar a Sucursal</b></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><b class="fas fa-map"></b></div>
                            </div>
                            <select class="form-control" id="departamento" name="id_sucursal" required>
                                <option value="0">Seleccione una Sucursal</option>
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
                                <div class="input-group-text"><i class="fas fa-info-circle"></i></div>
                            </div>
                            <input type="text" class="form-control" id="detalle_entrega"
                                placeholder="Detalles de Entrega" name="detalle_entrega"
                                value="{{ $solicitudes->detalle_entrega }}">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <label for="nueva_solicitud"><b>Fecha de Entrega</b></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                            <input type="date" class="form-control" id="fecha_entrega" placeholder="Fecha"
                                name="fecha_entrega" value="{{ $solicitudes->fecha_entrega }}" required>
                        </div>
                    </div>
                    <div class="col">
                        <label for="nueva_solicitud"><b>Hora Sugerida</b></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fas fa-clock"></i></div>
                            </div>
                            <input type="time" class="form-control" id="hora" placeholder="Hora" name="hora"
                                value="{{ $solicitudes->hora }}">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row justify-content-md-center">
                    <button id="boton" type="submit" class="btn btn-block"><b class="fas fa-angle-double-right">
                            Continuar</b></button>
                </div>
            </form>
            {!! Form::close() !!}
        </div>
    </div>
    <script>
        $(function() {
            $('#departamento').on('change', onSelectDepartamentoChange);
        });

        function onSelectDepartamentoChange() {
            var id = $(this).val();
            if (!id) {
                $('#municipio').html('<option value ="">Seleccione una opcion</option>');
                return;
            };
            $.get('muni/' + id, function(data) {
                var html_select = '<option value ="">Seleccione una opcion</option>';
                for (var i = 0; i < data.length; ++i)
                    html_select += '<option value="' + data[i].id + '">' + data[i].nombre + '</option>';
                $('#municipio').html(html_select);
            });
        }
        $(function() {
            $('#municipio').on('change', onSelectMunicipioChange);
        });

        function onSelectMunicipioChange() {
            var id = $(this).val();
            if (!id) {
                $('#otros').html('<option value ="">Seleccione una opcion</option>');
                return;
            };
            $.get('otros/' + id, function(data) {
                var html_select = '<option value ="">Seleccione una opcion</option>';
                for (var i = 0; i < data.length; ++i)
                    html_select += '<option value="' + data[i].id + '">' + data[i].nombre + '</option>';
                $('#otros').html(html_select);
            });
        }
    </script>
@endsection
