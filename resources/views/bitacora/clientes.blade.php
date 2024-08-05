@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script> 
<script src="{{ asset('js/datatables.min.js') }}"></script>
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
    <ul class="nav nav-tabs"> 
      <li class="nav-item">
        <a class="nav-link" href="{{route('entre_muni')}}"><i class="fas fa-clipboard"></i> Por municipios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="active" href="#"><i class="fas fa-users"></i> Clientes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('lis_us')}}"> Listado Usuarios</a>
      </li>
    </ul>
  </div>
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
            url: 'd_cliente',
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
                return "<a id='ver' href='{{url('enCli/')}}/"+data.id+"' class= 'btn btn-outline-danger btn-sm'><i class='fas fa-list-ol'></i> Total Entregas</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,3,4], searchable: true },
        { targets: '_all', searchable: false },
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
