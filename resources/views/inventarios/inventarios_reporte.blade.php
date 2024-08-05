@extends('layouts.app')
@section('content')
<script src="{{ asset('js/productosInventario.js')}}" async></script>
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
            <a class="nav-link vista" href="{{route ('listado_de_sucursales')}}">Atrás</a>
            <a class="nav-link" href="#">Inventarios realizado</a>
            <a class="nav-link vista" href="#" data-toggle="modal" data-target="#buscarPorFecha">Inventarios por fecha</a>
            @guest
            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            @if (Route::has('register'))
            @endif
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
    <form method="get" action="{{route('ulti_con_inve',$id)}}">
        <div class="input-group">
            <select id="producto" name="cod_producto" class="form-control"></select>
            <div class="input-group-append">
                <div class="input-group-append">
                    <button class="btn btn-warning btn-sm" type="submit">Buscar...</button>
                </div>
            </div>
        </div>
    </form>
    <blockquote class="blockquote text-center">
        <p class="mb-0">Todos los inventarios realizados</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="inventarios" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Inventario</th>
                    <th>Realizado por</th>
                    <th>Encargado</th>
                    <th>Sucursal</th>
                    <th>Fecha inicial</th>
                    <th>Fecha final</th>
                    <th>Realizado en</th>
                    <th>Contabilizados</th>
                    <th>Diferencias</th>
                    <th>Dañado</th>
                    <th>Porcentaje</th>
                    <th>Exactitud</th>
                    <th>Ver</th>
                    <th>Editar</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
    <!-- Modal -->
    <div class="modal fade" id="buscarPorFecha" tabindex="-1" role="dialog" aria-labelledby="buscarPorFechaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Seleccione la fecha</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                {!! Form::open(['method'=>'get','route'=>['inventarios_por_fecha','id'=>$id]]) !!}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputFechaInicial">Fecha inicial</label>
                            <input type="date" class="form-control" name="inicio" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputFechaFinal">Fecha final</label>
                            <input type="date" class="form-control" name="fin" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
<script>
var table = $('#inventarios').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 75,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '65vh',
    scrollCollapse: true,
    paging: true,
    stateSave: true,
    "stateDuration": 300,
    ajax:{
        url: "{{url('datinvereal',['id'=>$id])}}",
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
        { data: 'id', name:  'id'},
        { data: 'semana', name: 'semana'},
        { data: 'name', name: 'name'},
        { data: 'encargado', name: 'encargado'},
        { data: 'bodega', name: 'bodega'},
        { data: 'created_at', name: 'created_at' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'diferencia_tiempo', name: 'diferencia_tiempo'},
        { data: 'contado', name: 'contado'},
        { data: 'diferencia', name: 'diferencia'},
        { data: 'daniado', name: 'daniado'},
        { data: 'porcentaje', name:'porcentaje',render: $.fn.dataTable.render.number(',', '%', '%', 0, '')},
        { data: 'exactitud', name: 'exactitud',render: $.fn.dataTable.render.number(',', '%', '%', 0, '')},
        { data: null, render: function(data,type,row){
            return "<a href='{{url('ver')}}/"+data.id+"'class= 'btn btn-dark btn-sm' target='_black'>Ver</button>"}
        },
        { data: null, render: function(data,type,row){
            return "<a href='{{url('prod_invent')}}/"+data.id+"'class= 'btn btn-warning btn-sm' target='_black'>Editar</button>"}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [13,14]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [5,6], render:function(data){
             moment.locale('es');
            return moment(data).format('lll');
        }}
    ],
    rowCallback:function(row,data){
        if(data['estado'] == 'Finalizado' || data['estado'] == '11'){
            $($(row).find("td")[0]).css("background-color","#AAD14880");
            $($(row).find("td")[1]).css("background-color","#AAD14880");
            $($(row).find("td")[2]).css("background-color","#AAD14880");
            $($(row).find("td")[3]).css("background-color","#AAD14880");
            $($(row).find("td")[4]).css("background-color","#AAD14880");
            $($(row).find("td")[5]).css("background-color","#AAD14880");
            $($(row).find("td")[6]).css("background-color","#AAD14880");
            $($(row).find("td")[7]).css("background-color","#AAD14880");
            $($(row).find("td")[8]).css("background-color","#AAD14880");
            $($(row).find("td")[9]).css("background-color","#AAD14880");
            $($(row).find("td")[10]).css("background-color","#AAD14880");
            $($(row).find("td")[11]).css("background-color","#AAD14880");
            $($(row).find("td")[12]).css("background-color","#AAD14880");
            $($(row).find("td")[13]).css("background-color","#AAD14880");
            $($(row).find("td")[14]).css("background-color","#AAD14880").html('');
        }
        if(data['porcentaje'] >= 99 ){
            $($(row).find("td")[11]).html('100%');
        }
    },
});
</script>
@endsection
