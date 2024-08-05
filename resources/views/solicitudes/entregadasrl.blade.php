@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script> 
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
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
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="{{route('sel_suc')}}"><i class="fas fa-list-ol"></i> Listado de sucursales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('inv_suc',['sucursal'=>$suc,'bodega'=>$bod])}}"><i class="far fa-calendar-alt"></i> Inventario por fecha</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="active"><i class="fas fa-truck"></i> Entregas por sucursal</a>
        </li>
    </ul>
    <h5>Solicitudes entregadas</h5>
    <div class="table table-responsive">
        <table class="table table-sm" id="clientes">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Factura o comprobante</th>
                    <th>Cliente</th>
                    <th>Ubicación</th>
                    <th>Fecha de Solicitud</th>
                    <th>Fecha de Entrega</th>
                    <th>Camión</th>
                    <th>Ver</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
$('#clientes').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: "{{url('denpsu',['suc'=>$suc,'bod'=>$bod])}}",
            dataSrc: "data",
        },
        "order": [[ 0,"desc" ]],
        columns: [
            { data: 'id', name: 'inventario_web_entregas.id'},
            { data: 'comprobante', name: 'comprobante'},
            { data: 'nombre',name:'inventario_web_clientes.nombre'},
            { data: 'ubicacion', name: 'inventario_web_aldeas_otros.nombre'},
            { data: 'created_at',name:'inventario_web_entregas.created_at'},
            { data: 'fecha_entregado',name:'fecha_entregado'},
            { data: 'placa',name:'inventario_web_camiones.id'},
            { data: null, render: function(data,type,row){
                return "<a href='{{url('v_solicitud/')}}/"+data.id+"' class= 'btn btn-danger btn-sm'><i class='fas fa-list-ol'></i> Ver Solicitud</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [1,2,3,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4,5], render:function(data){
            moment.locale('es');
      return moment(data).format('LLLL');
        }}
    ]
});
</script>
@endsection
