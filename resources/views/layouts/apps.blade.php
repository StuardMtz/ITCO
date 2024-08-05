<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#317EFB"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        var url_global='{{url("/")}}';
    </script>
    <link rel="stylesheet" href="{{asset('css/estilo.css')}}" type="text/css">
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
</head>
<body style="background-image: url(storage/bg.jpg);">
    <div>
        <nav style="background-color: #454343;" class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">

                <a class="navbar-brand" href="{{ url('/home') }}"><h5 style="color: white;">Inicio</h5></a>
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
