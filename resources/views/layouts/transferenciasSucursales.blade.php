<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#317EFB"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('css/estilo.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('css/css2/select2.css') }}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}" type="text/css">
    <link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
    <title>Sistegua Web</title>

    <!-- Styles -->
    <style>
        .flotante {
            overflow: hidden;
            position: sticky;
            top: 0;
            width: 50%%;
            box-shadow: 5px 5px 1px grey;
            margin: 10px;
            background-image: linear-gradient(#FFFFFF 5%,#DFDFDF 100%,#FFFFFF 105%);
        }
    </style>
    <script>
        var url_global='{{url("/")}}';
    </script>
    <script src="{{asset('js/jquery.js')}}"></script>
    <script src="{{asset('js/select2/select2.js')}}"></script>
    <script src="{{ asset('js/moment.js')}}"></script>
    <script src="{{ asset('js/moment_with_locales.js')}}"></script>
    <script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
</head>
<body style="background-image: url(storage/bg.jpg);">
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('transferencias_en_espera')}}">Transferencias creadas</a>
                <a class="nav-link" href="{{route('tran_final_suc')}}">Finalizadas</a>
                <a class="nav-link" href="{{route('tran_pendientes_suc')}}">Por autorizar</a>
                <a class="nav-link" href="{{route('tran_mi_bodega')}}">Cargadas a mi sucursal</a>
                <a class="nav-link" href="{{route('rep_tra_Sucursales')}}">Reporte transferencias</a>
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
    <main class="py-6">
        @yield('content')
    </main>
    <main class="py-4">
        @yield('vue')
    </main>
</body>
<script src="{{ asset('js/app.js') }}" defer></script>
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
</html>
