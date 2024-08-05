@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
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
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="{{route('vista_clientes',$suc)}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    </li>
  </ul>
  <h5>Total de Entregas</h5>
    <table class="table table-sm" id="entregas">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Comprobante</th>
          <th>Estado</th>
          <th>Fecha Solicitud</th>
          <th>Fecha Entregado</th>
          <th>Ver</th>
        </tr>
      </thead>
    </table>
  </div>
  <script>
    $('#entregas').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: "{{ route('total_entregas',$id) }}",
            dataSrc: "data",
        },
        "order": [[ 2,"asc" ]],
        columns: [
            { data: 'id', name: 'id'},
            { data: 'nombre',name:'nombre'},
            { data: 'comprobante',name:'comprobante'},
            { data: 'estado',name:'estado'},
            { data: 'created_at', name: 'created_at'},
            { data: 'fecha_entregado', name: 'fecha_entregado'},
            { data: null, render: function(data,type,row){
                return "<a id='ver' href='{{url('v_solicitud/')}}/"+data.id+"' class= 'btn btn-dark btn-sm'><i class='fas fa-info-circle'></i> Ver Detalles</button>"}
            }
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,3,4], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4,5], render:function(data){
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
