<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/locale/es.js"></script>
@extends('layouts.app2')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
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
<div class="container-fluid">
  <div class="row">
    <a class="btn btn-dark" href="{{ route('r_inicio')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
  </div>
  <h4>Solicitudes Finalizadas</h4>
  <hr>
  <table class="table" id="en_ruta">
            <thead>
                <tr>
                    <th>Factura o Comprobante</th>
                    <th>Cliente</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                    <th>Camión</th>
                    <th>Ubicación de Entrega</th>
                    <th>Ver</th>
                </tr>
            </thead>
        </table>
    </div>
<script>
    $('#en_ruta').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: 'd_s_finalizadas',
            dataSrc: "data",
        },
        "order": [[ 3,"asc" ]],
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
            { data: 'comprobante', name: 'inventario_web_entregas.comprobante'},
            { data: 'nombre',name:'inventario_web_clientes.nombre'},
            { data: 'created_at',name:'inventario_web_entregas.created_at'},
            { data: 'estado', name: 'inventario_web_estados.nombre'},
            { data: 'placa', name: 'inventario_web_camiones.placa'},
            { data: 'aldea',name:'inventario_web_aldeas_otros.nombre'},
            { data: null, render: function(data,type,row){
                return "<a href='{{url('v_solicitud/')}}/"+data.ide+"'  class= 'btn btn-dark btn-sm' ><i class='fas fa-eye'></i> Ver</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,3,4,5], searchable: true },
        { targets: '_all', searchable: false },
            {targets: [2], render:function(data){
            return moment(data).format('LLLL');
        }}
    ]
        });
        </script>
@endsection
