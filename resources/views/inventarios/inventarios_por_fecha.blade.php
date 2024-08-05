@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<nav class="navbar navbar-expand-lg">
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="{{route ('inventarios_realizados',$id)}}">Atrás</a>
            <a class="nav-link" href="#" data-toggle="modal" data-target="#exampleModal">Inventarios por fecha</a>
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
    <blockquote class="blockquote">
    <h5>Del {{date('d/m/Y',strtotime($inicio))}} al {{date('d/m/Y',strtotime($fin))}}</h5>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seleccione la fecha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="get" action="{{route('inventarios_por_fecha',['id'=>$id])}}">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputFechaInicial"><b>Fecha inicial</b></label>
                            <input type="date" class="form-control" name="inicio" placeholder="Fecha Inicial" value="{{$inicio}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputFechaFinal"><b>Fecha final</b></label>
                            <input type="date" class="form-control" name="fin" placeholder="Fecha Final" value="{{$fin}}">
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-warning btn-block"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
var table = $('#inventarios').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    paging: true,
    stateSave: true,
    "stateDuration": 300,
    ajax:{
        url: "{{url('datinfec',['id'=>$id,'inicio'=>$inicio,'fin'=>$fin])}}",
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
        { data: 'nombre', name: 'nombre'},
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
            return "<a href='{{url('prod_invent')}}/"+data.id+"'class= 'btn btn-warning btn-sm'>Editar</button>"}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [13,14]},
        { targets: 0, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [5,6], render:function(data){
             moment.locale('es');
            return moment(data).format('lll');
        }}
    ],
    rowCallback:function(row,data){
        if(data['estado'] == 'Finalizado'){
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
    }
});
</script>
@endsection
