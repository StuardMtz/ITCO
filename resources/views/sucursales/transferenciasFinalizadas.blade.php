@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
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
        <a class="nav-link" href="{{route('home')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('tran_su')}}"><i class="fas fa-truck"></i> Transferencias en proceso</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="active"><i class="fas fa-clipboard-check"></i> Transferencias finalizadas</a>
        </li>
    </ul>
    <h5>Transferencias en área de carga</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Fecha de creación</th>
                    <th>Fecha de salida</th>
                    <th>Fecha de finalización</th>
                    <th>Observación</th>
                    <th>Placa</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    var table = $('#sucursales').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        responsive: false,
        ajax:{
            url: "{{route('df_tran')}}",
            dataSrc: "data",
        },
        "order": [[ 0,"asc" ]],
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
                { data: 'num_movi', name: 'num_movi'},
                { data: 'nombre', name:'nombre'},
                { data: 'created_at', name: 'created_at'},
                { data: 'fechaSalida', name: 'fechaSalida'},
                { data: 'fecha_entregado', name: 'fecha_entregado'},
                { data: 'observacion', name: 'observacion'},
                { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
                    return row['propietario'] +' '+  row['placa_vehiculo']}
                }
                ],
        "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,4,5], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [2,3,4], render:function(data){
             moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],
    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      window.location.href =(url_global+'/v_tran/'+row.data().num_movi);
      redirectWindow.location;
    });
</script>
@endsection
