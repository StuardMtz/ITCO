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
    <a class="btn btn-dark btn-sm" href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="card">
        <div class="card-header">
            <h6>Editar datos del camión</h6>
        </div>
        <div class="card-body">
            <form class="needs-validation" method="post" action="{{ url('e_camion', $id) }}" novalidate>
                {{ csrf_field() }}
                @foreach ($camion as $camion)
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Marca</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Marca" value="{{ $camion->marca }}"
                                    name="marca" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                        <div class="from-group col-md-6">
                            <label for="nueva_solicitud">Placa</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="placa" value="{{ $camion->placa }}"
                                    name="placa" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Tonelaje</label>
                            <div class="input-group">
                                <input type="number" class="form-control" step="0.01" placeholder="Tonelaje"
                                    value="{{ $camion->tonelaje }}" name="tonelaje" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Tipo Camión</label>
                            <div class="input-group">
                                <select class="form-control" name="tipo" required>
                                    <option value="{{ $camion->tipo_camion }}">{{ $camion->tipo_camion }}</option>
                                    <option value="Plataforma">Plataforma</option>
                                    <option value="Cerrado">Cerrado</option>
                                </select>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Volumen</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Volumen" name="volumen"
                                    value="{{ $camion->espacio }}" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="EstadoCamion">Estado del camión</label>
                            <div class="input-group">
                                <select class="form-control" name="estado" required>
                                    <option value="{{ $camion->id_estado }}">{{ $camion->nombre }}</option>
                                    @foreach ($estados as $es)
                                        <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Cambiar de sucursal</label>
                            <div class="input-group">
                                <select class="form-control" name="sucursal" required>
                                    <option value="{{ $camion->id_sucursal }}">{{ $camion->name }}</option>
                                    @foreach ($sucursales as $su)
                                        <option value="{{ $su->id }}">{{ $su->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-dark btn-sm btn-block" class="fas fa-save">Guardar cambios</button>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endsection
