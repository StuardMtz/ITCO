<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
@extends('layouts.app2')
@section('content')
<div class="container">
  @if($message= Session::get('success'))
  <div class="alert alert-success">
    <p>{{ $message}}</p>
  </div>
  @endif
  @if($message= Session::get('error'))
  <div class="alert alert-danger">
    <p>{{ $message}}</p>
  </div>
  @endif
</div>
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="{{route('home')}}"><i class="fas fa-clipboard"></i> Inventarios</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active bg-info" href="#"><b><i class="far fa-pause-circle"></i> En espera</b></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('s_e_ruta')}}"><i class="fas fa-shipping-fast"></i> Solicitudes en Ruta</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('s_entregada')}}"><i class="fas fa-clipboard-check"></i> Solicitudes Entregadas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link"href="{{route('v_camiones')}}"><i class="fas fa-map-marked-alt"></i> Asignar Rutas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('v_cliente')}}"><i class="fas fa-users"></i> Clientes</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('v_sucursales')}}"><i class="fas fa-warehouse"></i> Entrega a Sucursal</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('p_recibir')}}"> Por Recibir</a>
    </li>
  </ul>
  <h5>Solicitudes pendientes de asignar a Ruta</h5>
  <hr>
    <table class="table" id="pendiente">
        <thead>
            <tr>
                <th>#</th>
                <th>Comprobante</th>
                <th>Cliente</th>
                <th>Fecha de Solicitud</th>
                <th>Ubicación</th>
                <th>Ver</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $('#pendiente').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: 'dr_inicio',
            dataSrc: "data",
        },
        "order": [[ 4,"asc" ]],
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
            { data: 'ide', name: 'inventario_web_entregas.id'},
            { data: 'comprobante', name: 'inventario_web_entregas.comprobante'},
            { data: 'nombre',name:'inventario_web_clientes.nombre'},
            { data: 'created_at',name:'inventario_web_entregas.created_at'},
            { data: 'aldea',name:'inventario_web_aldeas_otros.nombre'},
            { data: null, render: function(data,type,row){
                return "<a href='{{url('v_solicitud/')}}/"+data.ide+"'  class= 'btn btn-dark btn-sm' ><i class='fas fa-eye'></i> Ver</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,4], searchable: true },
        { targets: '_all', searchable: false },
            {targets: [3], render:function(data){
            return moment(data).format('LLLL');
        }}
    ]
        });
        </script>
@endsection
