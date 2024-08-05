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
            <div class="dropdown">
                <button class="nav-link dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    Gastos pendientes
                </button>
                <div class="dropdown-menu">
                <a class="dropdown-item" href="{{route('inicio_gastos_espera')}}">Mis gastos pendientes de autorización</a>
                    <a class="dropdown-item" href="{{route('lis_us_gasp')}}">Gastos pendientes de autorizar</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="nav-link dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    Gastos autorizados
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{route('gastos_auto')}}">Mis gastos autorizados</a>
                    <!--<a class="dropdown-item" href="{{route('lis_suc_gas')}}">Otros gastos autorizados</a> -->
                    <a class="dropdown-item" href="{{route('rep_mgast_auto')}}">Reporte de gastos</a>
                    <a class="dropdown-item" href="{{route('resumen_de_gastos')}}">Resumen</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="nav-link dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    Liquidaciones
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{route('liquidaciones')}}">Mis liquidaciones</a>
                    <a class="dropdown-item" href="{{route('lisusliq')}}">Listado de liquidaciones</a>
                    <a class="dropdown-item" href="{{route('rep_liquida')}}">Reporte de liquidaciones</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="nav-link dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    Gastos rechazados
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{route('gast_recha')}}">Gastos rechazados</a>
                </div>
            </div>
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

<div class="container">
    @if($message= Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>¡{{ $message}}!</strong>
    </div>
    @endif
    @if($message= Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>¡{{ $message}}!</strong>
    </div>
    @endif
</div>
