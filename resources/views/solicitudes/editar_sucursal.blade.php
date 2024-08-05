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
        {!! Form::model($sucursal, ['method' => 'PATCH', 'route' => ['e_sucursal', $sucursal->id]]) !!}
        <form>
            <h4>Editar Sucursal</h4>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud"><b>Sucursal</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-warehouse"></b></div>
                        </div>
                        <select class="form-control" name="cod_unidad" disabled>
                            <option value="{{ $sucursal->nombre }}">{{ $sucursal->nombre }}</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud"><b>Correo</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-at"></i></div>
                        </div>
                        <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com"
                            name="correo" required value="{{ $sucursal->correo }}">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud"><b>Departamento</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="departamento" name="departamento" required>
                            <option value="0">Seleccione un Departamento</option>
                            @foreach ($departamentos as $d)
                                <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="nueva_solicitud"><b>Municipio</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-marker-alt"></b></div>
                        </div>
                        <select class="form-control" id="municipio" name="municipio" required>
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="nueva_solicitud_soliitud"><b>Aldea/Caserio/Otros</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-signs"></b></div>
                        </div>
                        <select class="form-control" id="otros" name="otros">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row justify-content-md-center">
                <button type="submit" class="btn btn-block"id="boton"><b class="fas fa-angle-double-right">
                        Continuar</b></button>
            </div>
        </form>
        {!! Form::close() !!}
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
    </div>
@endsection
