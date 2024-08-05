<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#317EFB"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('css/estilo3.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap.css.map')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap.min.css.map')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.css.map')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.min.css.map')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.css.map')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.min.css.map')}}" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
    integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ URL::asset('css/css2/select2.css') }}">
    <link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
    <title>Sistegua Web</title>
    <script>
        var url_global='{{url("/")}}';
    </script>
    <script src="{{asset('js/jquery.js')}}"></script>
    <script src="{{ asset('js/moment.js')}}"></script>
    <script src="{{ asset('js/moment_with_locales.js')}}"></script>
    <script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{asset('js/select2/select2.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
</head>
<body style="background-image: url(storage/bg.jpg);">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="{{ url('/home') }}"><h5 style="color: white;">Menu principal</h5></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <img src="storage/opciones.png" width="25">
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            @if (Route::has('register'))
                                <!--<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> -->
                            @endif
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <b style="color:white;">{{ Auth::user()->name }}</b> <span class="caret"></span>
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
                </ul>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
        <main class="py-4">
            @yield('vue')
        </main>
    </div>
</body>
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
