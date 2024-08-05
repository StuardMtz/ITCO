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
                <a class="nav-link active" href="#">Transferencias en bodega</a>
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
            </div>
        </div>
    </nav>

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias en área de carga</p>
    </blockquote>

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
    <table class="table table-sm" id="sucursales">
        <thead>
            <tr>
                <th>Número</th>
                <th>Inicio de carga</th>
                <th>Termino carga</th>
                <th>Observación</th>
                <th>Comentario</th>
                <th>Grupo</th>
                <th>Placa</th>
                <th>Estado</th>
            </tr>
        </thead>
    </table>

</div>
<script>
var table = $('#sucursales').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollY: '65vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{route('dabod_transf_bod')}}",
        dataSrc: "data",
    },
    "order": [[ 2,"asc" ]],
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
        { data: 'num_movi', name: 'num_movi'},
        { data: 'fecha_paraCarga', name: 'fecha_paraCarga'},
        { data: 'fecha_cargado', name: 'fecha_cargado'},
        { data: 'observacion', name: 'observacion'},
        { data: 'comentario', name: 'comentario'},
        { data: 'grupo', name: 'grupo'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
            return row['propietario'] +' '+  row['placa_vehiculo']}
        },
        { data: 'estado', name: 'estado'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,3,4], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [1,2], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
    rowCallback:function(row,data){
        if(data['id_estado'] == "13"){
            $($(row).find("td")[0]).css("background-color","#B2EC5DA8");
			$($(row).find("td")[1]).css("background-color","#B2EC5DA8");
            $($(row).find("td")[2]).css("background-color","#B2EC5DA8").html("");
            $($(row).find("td")[3]).css("background-color","#B2EC5DA8")
            $($(row).find("td")[4]).css("background-color","#B2EC5DA8");
            $($(row).find("td")[5]).css("background-color","#B2EC5DA8");
            $($(row).find("td")[6]).css("background-color","#B2EC5DA8");
            $($(row).find("td")[7]).css("background-color","#B2EC5DA8");
        }
        else if(data['id_estado'] == "14"){
            $($(row).find("td")[0]).css("background-color","#D2C5FECC");
			$($(row).find("td")[1]).css("background-color","#D2C5FECC");
            $($(row).find("td")[2]).css("background-color","#D2C5FECC").html("");
            $($(row).find("td")[3]).css("background-color","#D2C5FECC")
            $($(row).find("td")[4]).css("background-color","#D2C5FECC");
            $($(row).find("td")[5]).css("background-color","#D2C5FECC");
            $($(row).find("td")[6]).css("background-color","#D2C5FECC");
            $($(row).find("td")[7]).css("background-color","#D2C5FECC");
        }
        else if(data['id_estado'] == "15"){
            $($(row).find("td")[0]).css("background-color","#80C8F89E");
			$($(row).find("td")[1]).css("background-color","#80C8F89E");
            $($(row).find("td")[2]).css("background-color","#80C8F89E").html("");
            $($(row).find("td")[3]).css("background-color","#80C8F89E")
            $($(row).find("td")[4]).css("background-color","#80C8F89E");
            $($(row).find("td")[5]).css("background-color","#80C8F89E");
            $($(row).find("td")[6]).css("background-color","#80C8F89E");
            $($(row).find("td")[7]).css("background-color","#80C8F89E");
        }
        else if(data['id_estado'] == "16"){
            $($(row).find("td")[0]).css("background-color","#FFB347CF");
			$($(row).find("td")[1]).css("background-color","#FFB347CF");
            $($(row).find("td")[2]).css("background-color","#FFB347CF");
            $($(row).find("td")[3]).css("background-color","#FFB347CF")
            $($(row).find("td")[4]).css("background-color","#FFB347CF");
            $($(row).find("td")[5]).css("background-color","#FFB347CF");
            $($(row).find("td")[6]).css("background-color","#FFB347CF");
            $($(row).find("td")[7]).css("background-color","#FFB347CF");
        }
    },
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/edbodtr/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection
