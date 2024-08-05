@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script>
  var url_global='{{url("/")}}';
</script>
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
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="{{route('vista_rutas',$su)}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('nuevo_cliente')}}"><i class="fas fa-user-plus"></i> Nuevo Cliente</a>
    </li>
  </ul>
  <h5>Listado de Clientes</h5>
  <div class="table-responsive-sm">
    <table class="table table-sm" id="clientes">
      <thead>
        <tr>
          <th>Código</th>
          <th>Nit</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Teléfono</th>
          <th>Editar</th>
          <th>Nueva Entrega</th>
          <th>Total Entregas</th>
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
    url: "{{route('d_cliente')}}",
    dataSrc: "data",
  },
  "order": [[ 2,"asc" ]],
  columns: [
    { data: 'id', name: 'id'},
    { data: 'nit', name: 'nit'},
    { data: 'nombre',name:'nombre'},
    { data: 'correo',name:'correo'},
    { data: 'telefono',name:'telefono'},
    { data: null, render: function(data,type,row){
      return "<a href='{{url('edit_cli/')}}/"+data.id+'/'+{{$su}}+"'class='btn btn-dark btn-sm'><i class='fas fa-edit'></i> Editar</button>"}
    },
    { data: null, render: function(data,type,row){
      return "<a id='boton' href='{{url('nue_sol/')}}/"+data.id+"' class= 'btn btn-sm btn-primary'><i class='fas fa-eye'></i> Nueva Entrega</button>"}
    },
    { data: null, render: function(data,type,row){
      return "<a id='ver' href='{{url('entregas_cliente/')}}/"+data.id+'/'+{{$su}}+"' class= 'btn btn-danger btn-sm'><i class='fas fa-list-ol'></i> Total Entregas</button>"}
    }
  ],
  "columnDefs": [
    { targets: 1, searchable: true },
    { targets: [0,1,2,3,4], searchable: true },
    { targets: '_all', searchable: false },
  ],
});
</script>
@endsection
