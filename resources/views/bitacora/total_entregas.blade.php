@extends('layouts.app')
@section('content')
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
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
    <a class="btn btn-dark" href="{{route('pClient')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
  </div>
  <h4>Total de Entregas</h4>
  <hr>
    <div class="table-responsive-sm">
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
        "order": [[ 0,"desc" ]],
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
