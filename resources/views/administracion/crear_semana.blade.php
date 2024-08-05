@extends('layouts.app')
@section('content')
    @yield('content', View::make('layouts.administracion'))
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
        <div class="card">
            <div class="card-header">
                <h6>Crear nuevo inventario</h6>
            </div>
            <div class="card-body">
                <form class="needs-validation" novalidate method="post" action="{{ url('g_se') }}">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="nuevo_inventario">Descripción</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="semana" name="semana"
                                    placeholder="Ingrese la descripción" maxlength="15" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-grop col-md-6">
                            <label for="nuevo_inventario">Fecha de inicio</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="fecha_inicial" required>
                            </div>
                        </div>
                        <div class="form-grop col-md-6">
                            <label for="nuevo_inventario">Fecha Final</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="fecha_final" required>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-dark btn-block btn-sm">Crear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
