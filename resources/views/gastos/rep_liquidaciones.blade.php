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
    <div class="navbar-nav">
        <a class="nav-link" href="javascript:history.back()">Atrás</a>
        <a class="nav-link" href="{{route('inicio_gastos_espera')}}">Solicitudes</a>
        <a class="nav-link" href="{{route('rep_mgast_auto')}}">Reporte de gastos</a>
        <form method="get" action="{{url('repliquif')}}" class="form-inline">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">De</span>
                </div>
                <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Hasta</span>
                </div>
                <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" required>
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
</nav>

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Reporte de liquidaciones</p>
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
        <table class="table table-sm table-borderless" id="gastos" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Responsable</th>
                    <th>Sucursal</th>
                    <th>Documentos</th>
                    <th>Monto</th>
                    <th>Del</th>
                    <th>Al</th>
                    <th>Fecha creación</th>
                    <th>Estado</th>
                    <th>Verificado por</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="nuevaLiquidacion" aria-labelledby="nuevaLiquidacionLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva liquidación de gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nit-tab" data-toggle="tab" data-target="#nit" type="button" role="tab"
                        aria-controls="nit" aria-selected="true">Liquidación</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="nit" role="tabpanel" aria-labelledby="nit-tab">
                        <form class="needs-validation" novalidate method="post" action="{{url('nue_liq')}}">
                        {{csrf_field()}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><b>Nombre responsable</b></label>
                                    <input type="text" class="form-control" name="responsable" placeholder="Nombre del responsable" aria-label="responsable" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Fecha inicial</b></label>
                                    <input type="date" class="form-control" name="fecha_inicial" aria-label="fecha_inicial" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Fecha final</b></label>
                                    <input type="date" class="form-control" name="fecha_final" aria-label="fecha_final" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><b>Obersavaciones</b></label>
                                    <textarea type="text" class="form-control" name="observaciones" aria-label="observaciones" required></textarea>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-info btn-block" id="guardar">Gerenar liquidación</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
var table = $('#gastos').DataTable({
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
    ajax:{
        url: "{{route('repdautoliqui')}}",
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
        { data: 'responsable', name: 'responsable'},
        { data: 'sucursal', name: 'sucursal'},
        { data: 'docs', name: 'docs'},
        { data: 'suma', name: 'suma', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'fecha_inicial', name: 'fecha_inicial'},
        { data: 'fecha_final', name: 'fecha_final'},
        { data: 'fecha_creacion', name: 'fecha_creacion'},
        { data: 'estado', name: 'estado'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [5,6,7], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ],
    rowCallback:function(row,data){
        $($(row).find("td")[1]).css("text-transform", "capitalize");
        $($(row).find("td")[2]).css("text-transform", "capitalize");
        $($(row).find("td")[9]).css("text-transform", "capitalize");
        if(data['id_estado'] == 26){ //Red-Creada
            $($(row).find("td")[0]).css("background-color","#F3272A8A");
            $($(row).find("td")[1]).css("background-color","#F3272A8A");
			$($(row).find("td")[2]).css("background-color","#F3272A8A");
            $($(row).find("td")[3]).css("background-color","#F3272A8A");
            $($(row).find("td")[4]).css("background-color","#F3272A8A");
            $($(row).find("td")[5]).css("background-color","#F3272A8A");
            $($(row).find("td")[6]).css("background-color","#F3272A8A");
            $($(row).find("td")[7]).css("background-color","#F3272A8A");
            $($(row).find("td")[8]).css("background-color","#F3272A8A");
            $($(row).find("td")[9]).css("background-color","#F3272A8A");
        }
        else if(data['id_estado'] == 27){ //yellow-En espera
            $($(row).find("td")[0]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[1]).css("background-color","#F0EB4A7D");
			$($(row).find("td")[2]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[3]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[4]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[5]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[6]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[7]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[8]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[9]).css("background-color","#F0EB4A7D");
        }
        else if(data['id_estado'] == 28){ //green-revisada
            $($(row).find("td")[0]).css("background-color","#238C1175");
            $($(row).find("td")[1]).css("background-color","#238C1175");
			$($(row).find("td")[2]).css("background-color","#238C1175");
            $($(row).find("td")[3]).css("background-color","#238C1175");
            $($(row).find("td")[4]).css("background-color","#238C1175");
            $($(row).find("td")[5]).css("background-color","#238C1175");
            $($(row).find("td")[6]).css("background-color","#238C1175");
            $($(row).find("td")[7]).css("background-color","#238C1175");
            $($(row).find("td")[8]).css("background-color","#238C1175");
            $($(row).find("td")[9]).css("background-color","#238C1175");
        }
    }
});
$('#gastos').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/ve_dliqui/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
