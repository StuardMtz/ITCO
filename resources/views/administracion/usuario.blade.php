@extends('layouts.app')
@section('content')
    <script>
        var url_global = '{{ url('/') }}';
    </script>
    <script src="{{ asset('js/permisos.js') }}" defer></script>
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
        <a class="btn btn-dark btn-sm" href="{{ route('lis_us') }}"><i class="fas fa-arrow-left"></i> Atrás</a>
        <div class="card">
            <div class="card-header">
                <h6>Nuevo usuario</h6>
            </div>
            <div class="card-body">
                <form class="needs-validation" novalidate method="post" action="{{ url('cru') }}"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nuevo_inventario">Nombre de usuario</label>
                            @error('name')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <div class="input-group">
                                <input type="text" class="form-control" id="NombreUsuario"
                                    placeholder="Nombre de Usuario" name="name" required>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nuevo_inventario">Correo</label>
                            @error('email')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <div class="input-group">
                                <input type="email" class="form-control" id="Correo" placeholder="Correo"
                                    name="email" required>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nuevo_inventario">Sucursal</label>
                            @error('sucursal')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <div class="input-group">
                                <select class="form-control" id="sucursal" name="sucursal" required>
                                    <option value="">Seleccione una Sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->cod_unidad }}" name="id">{{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nuevo_inventario">Bodega</label>
                            @error('bodega')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <div class="input-group">
                                <select class="form-control" id="bodega" name="bodega" required>
                                    <option></option>
                                </select>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="nuevo_inventario">Rol</label>
                            @error('roles')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <div class="input-group">
                                <select class="form-control" id="rol" name="roles" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->rol }}">{{ $r->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="nuevo_inventario">No identificación</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="codigo" placeholder="No identificación"
                                    name="no_identificacion">
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="nuevo_inventario">Contraseña</label>
                            @error('password')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <div class="input-group">
                                <input type="password" class="form-control" id="Contraseña" placeholder="Contraseña"
                                    name="password" required>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="nuevo_inventario">Código vendedor</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="codigo_vendedor"
                                    placeholder="código vendedor">
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nuevo_inventario">Serie cotización</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Serie cotización"
                                    name="serie_cotizacion">
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nuevo_inventario">Código diamante</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Código diamante"
                                    name="codigo_diamante">
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" d="customFileLang" lang="es"
                                        name="icono_actividad" accept="image/png">
                                    <label class="custom-file-label" for="inputGroupImagen">Seleccionar imagen</label>
                                </div>
                                <div class="valid-tooltip">
                                    Excelente!
                                </div>
                                <div class="invalid-tooltip">
                                    No puede dejar este campo vacio.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Seleccione los permisos para los usuarios</label>
                            @error('permisos')
                                <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                            @enderror
                            <select id="permisos" name="permisos[]" class="custom-select"></select>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-dark btn-block" class="fas fa-save">Crear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('#sucursal').on('change', onSelectSucursalChange);
        });

        function onSelectSucursalChange() {
            var cod_unidad = $(this).val();
            if (!cod_unidad) {
                $('#bodega').html('<option value ="">Seleccione una opcion</option>');
                return;
            };
            $.get('select/' + cod_unidad, function(data) {
                var html_select = '<option value ="">Seleccione una opcion</option>';
                for (var i = 0; i < data.length; ++i)
                    html_select += '<option value="' + data[i].cod_bodega + '">' + data[i].observacion +
                    '</option>';
                $('#bodega').html(html_select);
            });
        }
    </script>
@endsection
