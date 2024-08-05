<!DOCTYPE html>
  <html lang="es">
    <head>
      <meta charset="utf-8">
      <meta name="theme-color" content="#317EFB"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="stylesheet" href="{{asset('css/app.css')}}" type="text/css">
      <link rel="stylesheet" href="{{asset('css/estilo.css')}}" type="text/css">
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
              width: 50%;
              box-shadow: 5px 5px 1px grey;
              margin: 10px;
              background-image: linear-gradient(#FFFFFF 5%,#DFDFDF 100%,#FFFFFF 105%);
          }
      </style>
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
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
      integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    </head>
    <body style="background-image: url(storage/bg.jpg);">
    @yield('content')
  <main class="py-4">
    @yield('vue')
  </main>
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
