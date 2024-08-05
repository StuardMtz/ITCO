@extends('layouts.app')
@section('content')
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link" href="{{route('fina_expor')}}">Atrás</a>
            <form method="get" action="{{url('tran_otr_sucurf')}}">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Inicio</span>
                    </div>
                    <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" value="{{$inicio}}" required>
                    <div class="input-group-prepend">
                        <span class="input-group-text">Inicio</span>
                    </div>
                    <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" value="{{$fin}}" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-warning" type="button">Buscar</button>
                    </div>
                </div>
            </form>
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
        <p class="mb-0">Transferencias de exportación finalizadas por fecha</p>
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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="5"><input type="text" id="column2_search" placeholder="Bodega" class="form-control"></th>
                    <th colspan="3"><input type="text" id="column4_search" placeholder="Fecha salida" class="form-control"></th>
                    <th colspan="4"><input type="text" id="column7_search" placeholder="Placa" class="form-control"></th>
                    <th colspan="3"><input type="text" id="column9_search" placeholder="Sale bodega" class="form-control"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha de creación</th>
                    <th>Fecha de salida</th>
                    <th>Fecha de finalización</th>
                    <th>Descripción</th>
                    <th>Flota</th>
                    <th>Placa</th>
                    <th>Peso</th>
                    <th>Creada por</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
                    <th>Estado</th>
                    <th>Eficacia</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '65vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
    ajax:{
        url: "{{route('fda_fina_expor',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: "data",
    },
    "order": [],
    lengthMenu: [
        [ 100, 200, 300, -1 ],
        [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {   extend: 'pageLength',
            className: 'nav-link',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {   extend: 'colvis',
            className: 'nav-link',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {   extend: 'excelHtml5',
            className: 'nav-link',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true,
            title: '{{Auth::user()->name}}'
        }
    ],
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
        { data: 'nombre', name:'nombre'},
        { data: 'bodega', name: 'bodega'},
        { data: 'created_at', name: 'created_at'},
        { data: 'fechaSalida', name: 'fechaSalida'},
        { data: 'fecha_entregado', name: 'fecha_entregado'},
        { data: 'observacion', name: 'observacion'},
        { data: 'propietario', name: 'propietario'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo'},
        { data: 'opcionalUno', name: 'opcionalUno', render: $.fn.dataTable.render.number(',','.',3)},
        { data: 'usuario', name: 'usuario'},
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'},
        { data: 'erroresVerificados', name: 'erroresVerificados'},
        { data: 'porcentaje', name: 'portentaje', render: function(data, type, row){
            return row['porcentaje']+''+ '%'}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [13,14]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6,7,8,9,10,11,12], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4,5], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
    rowCallback:function(row,data){
        if(data['porcentaje'] == null && data['erroresVerificados'] == null){
            $($(row).find("td")[0]).css("background-color","#F07A7ABF");
            $($(row).find("td")[1]).css("background-color","#F07A7ABF");
			$($(row).find("td")[2]).css("background-color","#F07A7ABF");
            $($(row).find("td")[3]).css("background-color","#F07A7ABF");
            $($(row).find("td")[4]).css("background-color","#F07A7ABF");
            $($(row).find("td")[5]).css("background-color","#F07A7ABF");
            $($(row).find("td")[6]).css("background-color","#F07A7ABF");
            $($(row).find("td")[7]).css("background-color","#F07A7ABF");
            $($(row).find("td")[8]).css("background-color","#F07A7ABF");
            $($(row).find("td")[9]).css("background-color","#F07A7ABF");
            $($(row).find("td")[10]).css("background-color","#F07A7ABF");
            $($(row).find("td")[11]).css("background-color","#F07A7ABF");
            $($(row).find("td")[12]).css("background-color","#F07A7ABF");
            $($(row).find("td")[13]).css("background-color","#F07A7ABF").html('Pendiente');
            $($(row).find("td")[14]).css("background-color","#F07A7ABF").html('Pendiente');
        }
        else if(data['porcentaje'] != null && data['erroresVerificados'] == null){
            $($(row).find("td")[0]).css("background-color","#FFC97CB3");
            $($(row).find("td")[1]).css("background-color","#FFC97CB3");
			$($(row).find("td")[2]).css("background-color","#FFC97CB3");
            $($(row).find("td")[3]).css("background-color","#FFC97CB3");
            $($(row).find("td")[4]).css("background-color","#FFC97CB3");
            $($(row).find("td")[5]).css("background-color","#FFC97CB3");
            $($(row).find("td")[6]).css("background-color","#FFC97CB3");
            $($(row).find("td")[7]).css("background-color","#FFC97CB3");
            $($(row).find("td")[8]).css("background-color","#FFC97CB3");
            $($(row).find("td")[9]).css("background-color","#FFC97CB3");
            $($(row).find("td")[10]).css("background-color","#FFC97CB3");
            $($(row).find("td")[11]).css("background-color","#FFC97CB3");
            $($(row).find("td")[12]).css("background-color","#FFC97CB3");
            $($(row).find("td")[13]).css("background-color","#FFC97CB3").html('Pendiente');
            $($(row).find("td")[14]).css("background-color","#FFC97CB3");
        }
        else if(data['porcentaje'] != null && data['erroresVerificados'] == 2){
            $($(row).find("td")[0]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[1]).css("background-color","#B2E2A5E8");
			$($(row).find("td")[2]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[3]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[4]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[5]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[6]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[7]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[8]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[9]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[10]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[11]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[12]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[13]).css("background-color","#B2E2A5E8").html('Verificado');
            $($(row).find("td")[14]).css("background-color","#B2E2A5E8");
        }
        else if(data['erroresVerificados'] == 1 && data['opcionalDos'] == null){
            $($(row).find("td")[0]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[1]).css("background-color","#B2E2A5D1");
			$($(row).find("td")[2]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[3]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[4]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[5]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[6]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[7]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[8]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[9]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[10]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[11]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[12]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[13]).css("background-color","#B2E2A5D1").html('Verificado');
            $($(row).find("td")[14]).css("background-color","#B2E2A5D1");
        }
        else if(data['erroresVerificados'] == 1 && data['opcionalDos'] != null){
            $($(row).find("td")[0]).css("background-color","#B2E2A5E8","border","solid 4px #C794DE");
			$($(row).find("td")[1]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[2]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[3]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[4]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[5]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[6]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[7]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[8]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[9]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[10]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[11]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[12]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[13]).css("background-color","#DBB7EDCC").html('Verificado');
            $($(row).find("td")[14]).css("background-color","#DBB7EDCC");
        }
    },
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column4_search').on('keyup', function(){
    table.columns(4).search(this.value).draw();
});
$('#column7_search').on('keyup', function(){
    table.columns(7).search(this.value).draw();
});
$('#column9_search').on('keyup', function(){
    table.columns(9).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTran/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection
