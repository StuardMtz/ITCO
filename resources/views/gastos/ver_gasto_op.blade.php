@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
            @guest
            @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
            @endguest
        </div>
    </div>
</nav>

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Detalles del gasto</p>
    </blockquote>

    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach($gastos as $ga)
                <li><b>#: </b> {{$ga->numero}}</li>
                <li><b>Serie documento: </b> {{$ga->serie_documento}}</li>
                <li><b>No documento: </b> {{$ga->no_documento}}</li>
                <li><b>Descripción: </b> {{$ga->descripcion}}</li>
                <li><b>Monto: </b> {{number_format($ga->monto,2)}}</li>
                <li><b>Retención: </b> {{number_format($ga->iva,2)}}</li>
                <li><b>No rentencion: </b> {{$ga->no_retencion}}</li>
                <li><b>Total: </b> {{number_format($ga->monto - $ga->iva,2)}}</li>
                <li><b>Proveedor: </b> {{utf8_encode($ga->proveedor)}} ({{$ga->cod_proveedor}}{{$ga->id}})</li>
                <li><b>NIT/CUI: </b> {{$ga->nit}}  {{$ga->cui}}</b>
                <li><b>Fecha documento: </b> {{date('d/m/Y',strtotime($ga->fecha_documento))}}</li>
                <li><b>Fecha solicitud: </b> {{date('d/m/Y H:i',strtotime($ga->fecha_registrado))}}</li>
                <li><b>Fecha operado: </b> {{date('d/m/Y H:i', strtotime($ga->fecha_autorizacion))}}</li>
                <li><b>Tipo de gasto: </b> {{$ga->nombre}}</li>
                <li><b>Solicito: </b> {{$ga->usuario}}</li>
                <li><b>Autorizado por: </b> {{$ga->name}}</li>
                <li><b>Vehículo: </b> {{$ga->marca}} {{$ga->placa}}</li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
@endsection
