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
            <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
            <a class="nav-link" href="#">Ver liquidación</a>
            <button type="button" class="nav-link" data-toggle="modal" data-target="#liquidacionModal">
                Marcar como revisada
            </button>
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
        <p class="mb-0">Ver detalles de liquidación</p>
    </blockquote>

    <div class="card encabezado">
        <div class="card-body">
        @foreach($liquidacion as $liq)
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Responsable: </b>{{$liq->responsable}}</li>
                <li class="list-inline-item"><b>Del</b> {{date('d/m/Y', strtotime($liq->fecha_inicial))}} <b>al</b> {{date('d/m/Y', strtotime($liq->fecha_final))}}</li>
                <li class="list-inline-item"><b>Fecha creación</b> {{date('d/m/Y H:i', strtotime($liq->fecha_creacion))}}</li>
                <li class="list-inline-item"><b>Fecha finalizada</b> {{date('d/m/Y H:i', strtotime($liq->fecha_finalizada))}}</li>
                <li class="list-inline-item"><b>Estado: </b> {{$liq->estado}}</li>
            </ul>
            <ul class="list-inline text-monospace text-wrap">
                @if($liq->fecha_revision == null)
                <li class="list-inline-item"><b>Fecha verificada: </b></li>
                @else
                <li class="list-inline-item"><a class="btn btn-sm btn-warning" href="{{route('impri_liqui',$id)}}"><b>Imprimir liquidación</b></a></li>
                <li class="list-inline-item"><b>Fecha verificada: </b>{{date('d/m/Y H:i',strtotime($liq->fecha_revision))}}</li>
                @endif
                <li class="list-inline-item"><b>Verificado por: </b> {{$liq->name}}</li>
                <li class="list-inline-item"><b>Observaciones: </b> {{$liq->observaciones}}</li>
            </ul>
            @endforeach
        </div>
    </div>

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="gastos" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Serie documento</th>
                    <th>No. documento</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Retención</th>
                    <th>No retención</th>
                    <th>Fecha solicitud</th>
                    <th>Fecha autorizado</th>
                    <th>Solicitado por</th>
                    <th>Autorizo</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal fade" id="liquidacionModal" tabindex="-1" aria-labelledby="liquidacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="liquidacionModalLabel">Marcar liquidación como revisada</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <a class="btn btn-sm btn-danger btn-block" href="{{route('rev_liquid',$id)}}">Revisada</a>
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
    scrollY: '57vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('da_dliqui',$id)}}",
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
        { data: 'descripcion', name: 'descripcion'},
        { data: 'monto', name: 'monto', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'iva', name: 'iva', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'no_retencion', name: 'no_retencion'},
        { data: 'fecha_registrado', name: 'fecha_registrado'},
        { data: 'fecha_autorizacion', name: 'fecha_autorizacion'},
        { data: 'usuario', name: 'usuario'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [8,9], render:function(data){
             moment.locale('es');
            return moment(data).format('LLL');
        }}
    ],
    "footerCallback": function(row,data,start,end,display){
        var api = this.api(), data;
        var intVal = function(i){
            return typeof i == 'string' ?
                i.replace(/[\$,]/g, '')*1 :
            typeof i == 'number' ?
                i:0;
        };
        monto = api
        .column(5)
        .data()
        .reduce(function (a,b){
            return intVal(a) + intVal(b);
        }, 0);

        retencion = api
        .column(6)
        .data()
        .reduce(function (a,b){
            return intVal(a) + intVal(b);
        }, 0);

        var Format = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
        Format(monto);
        $(api.column(4).footer()).html('Total liquidación:');
        $(api.column(5).footer()).html(Format(monto));
        $(api.column(6).footer()).html(Format(retencion));

    }
});
$('#gastos').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/vdgasop/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
