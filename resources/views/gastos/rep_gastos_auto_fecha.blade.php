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
        <a class="nav-link" href="{{route('rep_liquida')}}">Reporte de liquidaciones</a>
        <form method="get" action="{{url('rep_mgast_autof')}}" class="form-inline">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">De</span>
                </div>
                <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" value="{{$inicio}}" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Hasta</span>
                </div>
                <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" value="{{$fin}}" required>
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

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Reporte de gastos</p>
        <p><b>{{date('d/m/Y',strtotime($inicio)) }} a {{ date('d/m/Y',strtotime($fin)) }}</b></p>
    </blockquote>

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="gastos" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Serie documento</th>
                    <th>No. documento</th>
                    <th>Tipo gasto</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Retención</th>
                    <th>No retencion</th>
                    <th>Fecha solicitud</th>
                    <th>Fecha autorizado</th>
                    <th>Solicitado por</th>
                    <th>Estado</th>
                    <th>Operó</th>
                </tr>
            </thead>
        </table>
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
    scrollY: '67vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{route('rep_dmgast_auto_f',['inicio'=>$inicio,'fin'=>$fin])}}",
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
        { data: 'proveedor', name: 'proveedor'},
        { data: 'serie_documento', name: 'serie_documento'},
        { data: 'no_documento', name: 'no_documento'},
        { data: 'tipo', name: 'tipo'},
        { data: 'descripcion', name: 'descripcion'},
        { data: 'monto', name: 'monto', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'iva', name: 'iva', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'no_retencion', name: 'no_retencion'},
        { data: 'fecha_registrado', name: 'fecha_registrado'},
        { data: 'fecha_autorizacion', name: 'fecha_autorizacion'},
        { data: 'usuario', name: 'usuario'},
        { data: 'nombre', name: 'nombre'},
        { data: 'name', name: 'name'},
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [9,10], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ],
    rowCallback:function(row,data){
        if(data['id_estado'] == 29){ //Red-Rechazado
            $($(row).find("td")[0]).css("background-color","#FF656578");
            $($(row).find("td")[1]).css("background-color","#FF656578");
			$($(row).find("td")[2]).css("background-color","#FF656578");
            $($(row).find("td")[3]).css("background-color","#FF656578");
            $($(row).find("td")[4]).css("background-color","#FF656578");
            $($(row).find("td")[5]).css("background-color","#FF656578");
            $($(row).find("td")[6]).css("background-color","#FF656578");
            $($(row).find("td")[7]).css("background-color","#FF656578");
            $($(row).find("td")[8]).css("background-color","#FF656578");
            $($(row).find("td")[9]).css("background-color","#FF656578");
            $($(row).find("td")[10]).css("background-color","#FF656578");
            $($(row).find("td")[11]).css("background-color","#FF656578");
            $($(row).find("td")[12]).css("background-color","#FF656578");
            $($(row).find("td")[13]).css("background-color","#FF656578");
            $($(row).find("td")[14]).css("background-color","#FF656578");
        }
        else if(data['id_estado'] == 24){ //Yellow-En espera
            $($(row).find("td")[0]).css("background-color","#FFFB6596");
            $($(row).find("td")[1]).css("background-color","#FFFB6596");
			$($(row).find("td")[2]).css("background-color","#FFFB6596");
            $($(row).find("td")[3]).css("background-color","#FFFB6596");
            $($(row).find("td")[4]).css("background-color","#FFFB6596");
            $($(row).find("td")[5]).css("background-color","#FFFB6596");
            $($(row).find("td")[6]).css("background-color","#FFFB6596");
            $($(row).find("td")[7]).css("background-color","#FFFB6596");
            $($(row).find("td")[8]).css("background-color","#FFFB6596");
            $($(row).find("td")[9]).css("background-color","#FFFB6596");
            $($(row).find("td")[10]).css("background-color","#FFFB6596").html('');
            $($(row).find("td")[11]).css("background-color","#FFFB6596");
            $($(row).find("td")[12]).css("background-color","#FFFB6596");
            $($(row).find("td")[13]).css("background-color","#FFFB6596");
            $($(row).find("td")[14]).css("background-color","#FFFB6596");
        }
        else if(data['id_estado'] == 25){ //Green-Autorizado
            $($(row).find("td")[0]).css("background-color","#76DD548C");
            $($(row).find("td")[1]).css("background-color","#76DD548C");
			$($(row).find("td")[2]).css("background-color","#76DD548C");
            $($(row).find("td")[3]).css("background-color","#76DD548C");
            $($(row).find("td")[4]).css("background-color","#76DD548C");
            $($(row).find("td")[5]).css("background-color","#76DD548C");
            $($(row).find("td")[6]).css("background-color","#76DD548C");
            $($(row).find("td")[7]).css("background-color","#76DD548C");
            $($(row).find("td")[8]).css("background-color","#76DD548C");
            $($(row).find("td")[9]).css("background-color","#76DD548C");
            $($(row).find("td")[10]).css("background-color","#76DD548C");
            $($(row).find("td")[11]).css("background-color","#76DD548C");
            $($(row).find("td")[12]).css("background-color","#76DD548C");
            $($(row).find("td")[13]).css("background-color","#76DD548C");
            $($(row).find("td")[14]).css("background-color","#76DD548C");
        }
    }
});
$('#gastos').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/vergasto/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
