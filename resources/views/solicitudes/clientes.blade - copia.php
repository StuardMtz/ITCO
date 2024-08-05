@extends('layouts.app')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
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
    <div class="btn-group dropright">
      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cogs"></i> Opciones</button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="{{route('home')}}"><i class="fas fa-clipboard"></i> Inventarios</a>
        <a class="dropdown-item" href="{{route('s_inicio')}}"><i class="fas fa-shipping-fast"></i> Solicitudes en Tránsito</a>
        <a class="dropdown-item" href="{{route('s_inicio')}}"><i class="fas fa-clipboard-check"></i> Solicitudes Entregadas</a>
      </div>
    </div>
    <a class="btn btn-success" href="{{route('n_cliente')}}"><i class="fas fa-user-plus"></i> Entrega Nuevo Cliente</a>
  </div>
  <h4>Listado de Clientes</h4>
  <hr>
  <div class="table-condensed">
    <table class="table-sm" id="clientes">
      <thead>
        <tr>
          <th>Código</th>
          <th>Nit</th>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Editar</th>
          <th></th>
        </tr>
      </thead>
    </table>
  </div>
  <script>
    $('#clientes').DataTable({
        processing: false,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: 'd_cliente',
            type: 'get',
            dataSrc: function (json) {
              var data = {};
              return json;
            },
            },

        "order": [[ 0,"desc" ]],
        columns: [
            { data: 'id'},
            { data: 'nit' },
            { data: 'nombre'},
            { data: 'correo'},
            { data: 'telefono'},
            { data: null, render: function(data,type,row){
                return "<a id='boton'  class= 'btn btn-dark btn-sm' ><i class='fas fa-eye'></i> Ver</button>"}
            },
            { data: null, render: function(data,type,row){
                return "<a id='boton' class= 'btn btn-sm btn-primary'><i class='fas fa-edit'></i> Operar</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [1,2], searchable: true },
        { targets: '_all', searchable: false },
    ]
        });
        </script>
</div>
@endsection
