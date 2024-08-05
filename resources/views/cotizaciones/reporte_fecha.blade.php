@extends('layouts.app')
<script src="{{ asset('js/clientes.js') }}" defer></script>
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
      integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ url('/') }}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('reporte_cotizaciones') }}">Atras</a>
                <form method="get" action="{{url('repcotf')}}" class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Inicio</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" value="{{ $inicio }}" required>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Fin</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" value="{{ $fin }}" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-warning" type="button">Buscar</button>
                        </div>
                    </div>
                </form>
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
        <p class="mb-0">Reporte Cotizaciones Fecha</p>
    </blockquote>


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

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="cotizaciones" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nit</th>
                    <th>Cliente</th>
                    <th>Referencia</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>SKU</th>
                    <th>Creado por</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#cotizaciones').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('datos_reporte_cotizaciones_fecha',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: 'data',
    },
    "order": [[ 0,"desc" ]],
    "language": {
        "lengthMenu": "<span class='text-paginate'>Mostrar _MENU_ registros</span>",
        "zeroRecords": "No se encontraron resultados",
        "EmptyTable":     "Ningún dato disponible en esta tabla =(",
        "info": "<span class='text-paginate'>Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros</span>",
        "infoEmty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "InfoPostFix":    "",
        "search": "<span class='text-paginate'>Buscar</span>",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "paginate": {
            "First": "Primero",
            "Last": "Último",
            "next": "Siguiente",
            "previous": "Anterior",
        },
    },
    columns: [
        { data: 'num_movi', name:  'num_movi'},
        { data: 'nit', name: 'nit'},
        { data: 'Nombre_cliente', name: 'Nombre_cliente'},
        { data: 'referencia', name: 'referencia'},
        { data: 'fecha', name: 'fecha'},
        { data: 'total', name: 'total', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'sku', name: 'sku'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ]
});
$('#cotizaciones').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/edicot/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
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
@endsection
