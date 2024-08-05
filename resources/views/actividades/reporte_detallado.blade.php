@extends('layouts.app')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
        integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous">
    </script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ url('/') }}/storage/sistegualogo.png"
                width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ url('/') }}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="javascript: history.go(-1)">Atrás</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" id="active" disabled>Detalles del reporte</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('map_det_acti_us', $id) }}">Mapa visitas</a>
                </li>
                <form method="get" action="{{ url('lisdetActiUsFe', $id) }}" class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Inicio</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" required>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Fin</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-warning" type="button">Buscar</button>
                        </div>
                    </div>
                </form>
                @guest
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
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
        <blockquote class="blockquote text-center">
            <p class="mb-0">Listado de actividades por usuario</p>
        </blockquote>
    </div>

    <div id="app"><!--La equita id debe ser app, como hemos visto en app.js-->
        <detalleactividades-component></detalleactividades-component><!--Añadimos nuestro componente vuejs-->
    </div>
    <script type="text/javascript" defer>
        var json = "{{ $id }}";
        var server_data = json;
    </script>
@endsection
