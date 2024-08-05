@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script src="{{ asset('js/productos.js') }}" defer></script>

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
    aria-expanded="false" aria-label="Toggle navigation">
        <img src="{{url('/')}}/storage/opciones.png" width="25">
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('inicio_adm')}}">Atrás</a>
            <a class="nav-link" href="#">Agregar y/o eliminar productos</a>
        </div>
    </div>
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
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Agregue productos que no están en el listado de abajo</p>
    </blockquote>
    <form method="post" action="{{url('agre_prod',$semana)}}">
    {{csrf_field()}}
        <div class="row">
            <div class="col-10">
                <select id="producto" name="producto" class="custom-select" required></select>
            </div>
            <div class="col">
                <button class="btn btn-dark btn-sm" type="submit">Agregar</button>
            </div>
        </div>
    </form>

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

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="semana">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Agregar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
$('#semana').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollY: '68vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('prod_semana',$semana->id)}}",
        dataSrc: "data",
    },
    "order": [[ 0,"asc" ]],
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
        { data: 'cod_tipo_prod', name: 'cod_tipo_prod'},
        { data: 'nombre_corto', name: 'nombre_corto'},
        { data: 'nombre_fiscal', name:'nombre_fiscal'},
        { data: null, render: function(data,type,row){
                return "<a href='{{url('spro',$semana->semana)}}/"+ data.id +"' class='btn btn-sm btn-dark'>Agregar</button>"}
        },
        { data: null, render: function(data,type,row){
            return "<a href='{{url('epro',$semana->semana)}}/"+data.id+"' class='btn btn-sm btn-danger'>Eliminar</button>"}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [3,4]},
        { targets: 1, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if(data['semana'] != null){
            $($(row).find("td")[0]).css("background-color","#b2dbbf");
            $($(row).find("td")[1]).css("background-color","#b2dbbf");
            $($(row).find("td")[2]).css("background-color","#b2dbbf");
            $($(row).find("td")[3]).html('').css("background-color","#b2dbbf");
            $($(row).find("td")[4]).css("background-color","#b2dbbf");
        }
        else{
            $($(row).find("td")[4]).html('');
        }
    },
});
</script>
<script>
window.onload=function(){
    var pos=window.name || 0;
    window.scrollTo(0,pos);
}
window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
}
</script>
@endsection
