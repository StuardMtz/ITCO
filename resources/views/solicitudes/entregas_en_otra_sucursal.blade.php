@extends('layouts.app')
@section('content')
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('listado_sucursales_entregas') }}">Atrás</a>
                <a class="nav-link active" href="{{ route('solicitudes_en_ruta') }}">Entregas en proceso de
                    {{ $sucursal->name }}</a>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
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
            <p class="mb-0">Listado de camiones</p>
        </blockquote>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Marca</th>
                        <th>Placa</th>
                        <th>Tonelaje</th>
                        {{--  --}}
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($camiones as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->marca }}</td>
                            <td>{{ $c->placa }}</td>
                            <td>{{ $c->tonelaje }}</td>
                            {{--<td>{{ $c->tipo_camion }}</td>  --}}
                            <td>{{ $c->nombre }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr>
        <h5>Listado de rutas pendientes de finalizar</h5>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Placa Camion</th>
                        <th>Fecha de Entrega</th>
                        <th>Entregas sin finalizar</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rutas as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->placa }}</td>
                            @if ($r->fecha_entrega == '')
                                <td style="background-color:#0D3E7F; color:white;">
                                    <vb>Agregar fecha a la ruta</b>
                                </td>
                            @else
                                <td>{{ date('d/m/Y', ('strtotime')($r->fecha_entrega)) }}</td>
                            @endif
                            <td><span class="badge badge-danger">
                                    <h6>{{ $r->pendientes }}</h6>
                                </span></td>
                            <td><a class="btn btn-dark btn-sm" href="{{ route('v_ruta', $r->id) }}">Ver</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr>
        <h5>Solicitudes sin ruta</h5>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Comprobante</th>
                        <th>Cliente</th>
                        <th>Fecha de entrega</th>
                        <th>Hora de entrega</th>
                        <th>Dirección</th>
                        <th>Solicita</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitudes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->comprobante }}</td>
                            <td>{{ $s->nombre }}</td>
                            @if ($s->fecha_entrega == '')
                                <td></td>
                            @else
                                <td>{{ date('d/m/Y', ('strtotime')($s->fecha_entrega)) }}</td>
                            @endif
                            <td>{{ $s->hora }}</td>
                            <td>{{ $s->direccion }}</td>
                            <td>{{ $s->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
