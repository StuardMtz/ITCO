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
        <a class="btn btn-dark btn-sm" href="{{ route('edi_cam_adm') }}"><i class="fas fa-arrow-left"></i> Atrás</a>
        <div class="card">
            <div class="card-header">
                <h6>Agregar un nuevo Camión</h6>
            </div>
            <div class="card-body">
                <form class="needs-validation" method="post" action="{{ route('g_camion') }}" novalidate>
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Marca</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Marca" name="marca" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Placa</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="placa" name="placa" required>
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
                                <input type="float" class="form-control" placeholder="Tonelaje" name="tonelaje" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">User / Pass</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="User/Pass" name="tipo" required>
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
                                <input type="text" class="form-control" placeholder="Volumen" name="volumen" required>
                                <div class="valid-tooltip">
                                    Bien!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo en blanco!
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nueva_solicitud">Asignar Piloto</label>
                            <div class="input-group">
                                <select class="form-control" name="piloto" required>
                                    <option value="">Seleccione un piloto para el camión</option>
                                    @foreach ($pilotos as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
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
                            <label>Asignar sucursal</label>
                            <div class="input-group">
                                <select class="form-control" name="sucursal" required>
                                    @foreach ($sucursales as $su)
                                        <option value="{{ $su->id }}">{{ $su->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-dark btn-block btn-sm" class="fas fa-save">Continuar</button>
                    </div>
                </form>
            </div>
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
