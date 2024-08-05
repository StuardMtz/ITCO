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
    <a class="nav-link" href="{{route('inicio_transferencias')}}">En cola</a>
        <div class="dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">En bodega</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{route('trans_bodega')}}">Nacional en bodega</a>
            <a class="dropdown-item" href="{{route('bod_expor')}}">Exportaciones en bodega</a>
          </div>
        </div>
        <div class="dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Despachadas</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{route('despacho_transf')}}">Nacionales despachadas</a>
            <a class="dropdown-item" href="{{route('desp_expo')}}">Exportación despachadas</a>
          </div>
        </div>
        <div class="dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Finalizadas</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{route('finalizadas_transf')}}">Nacionales finalizadas</a>
            <a class="dropdown-item" href="{{route('fina_expor')}}">Exportaciones finalizadas</a>
          </div>
        </div>
        <a class="nav-link" href="{{route('trans_ot_sucursales')}}">Finalizadas otras unidades</a>
        <a class="nav-link" href="{{route('list_us_transf')}}">Integrantes de grupo</a>
        <a class="nav-link" href="{{route('rep_verf_transf')}}">Reporte transferencias</a>
        <a class="nav-link" href="{{route('rep_trans')}}">Reporte general</a>
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
  </div>
</nav>
<div class="container">
  @if($message= Session::get('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{ $message}}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if($message= Session::get('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>{{ $message}}</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
</div>
