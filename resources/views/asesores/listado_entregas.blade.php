@extends('layouts.app2')
@section('content')
<link href="{{asset('css/asesores.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/locale/es.js"></script>
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
    <a class="btn btn-dark" href="{{route('vista_rutas',$sucursal)}}"><i class="fas fa-undo-alt"></i> Atrás</a>
  </div>
  <h4>Mis Solicitudes</h4>
  <hr>
    <table class="table-hover" id="mis_entregas">
      <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Comprobante</th>
            <th>Fecha_Solicitud</th>
            <th>Estado</th>
            <th>Camión</th>
            <th>Placa</th>
            <th>Sucursal</th>
            <th>Ver Detalles</th>
        </tr>
      </thead>
    </table>
  </div>
  <script>
    $('#mis_entregas').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: "{{ route('listado_entregas',$sucursal) }}",
            dataSrc: "data",
        },
        "order": [[ 0,"desc" ]],
        columns: [
            { data: 'ide', name: 'inventario_web_entregas.id'},
            { data: 'nombre_cliente', name: 'inventario_web_clientes.nombre'},
            { data: 'comprobante', name:'inventario_web_entregas.comprobante'},
            { data: 'fecha_solicitud', name:'fecha_solicitud'},
            { data: 'estado', name:'inventario_web_estados.nombre'},
            { data: 'marca', name: 'inventario_web_camiones.marca'},
            { data: 'placa', name: 'inventario_web_camiones.placa'},
            { data: 'sucursal', name: 'users.name'},
            { data: null, render: function(data,type,row){
                return "<a id='ver' href='{{url('v_solicitud/')}}/"+data.ide+"' class= 'btn btn-dark btn-sm'><i class='fas fa-info-circle'></i> Ver Detalles</button>"}
            }
                ],
        "columnDefs": [
            { targets: [5,6], columns:null },
            { targets: 1, searchable: true },
            { targets: [1,2,4,5,6,7], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [3], render:function(data){
      return moment(data).format('LLLL');
        }}
    ],
    initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var input = document.createElement("input");
                $(input).appendTo($(column.footer()).empty())
                .on('change', function () {
                    column.search($(this).val(), false, false, true).draw();
                });
            });
        }
        });
        </script>
</div>
@endsection
