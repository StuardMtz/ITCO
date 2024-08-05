@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/locale/es.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="{{route('vista_asesores')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('vista_clientes',$sucursal)}}"><i class="fas fa-users"></i> Clientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('vista_entregas',$sucursal)}}"><i class="fas fa-list"></i> Mis Solicitudes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="export-btn"><i class="fas fa-file-excel"></i> Exportar en Excel</a>
        </li>
    </ul>
    <h5>Listado de Entregas sin Finalizar</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Comprobante</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                    <th>Camión</th>
                    <th>Placa</th>
                    <th>Editar</th>
                    <th>Ver</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
    <script>
    $('#sucursales').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        ajax:{
            url: "{{ route('listado_rutas',$sucursal) }}",
            dataSrc: "data",
        },
        "order": [[ 0,"asc" ]],
        columns: [
            { data: 'nombre', name: 'inventario_web_clientes.nombre'},
            {data: 'comprobante', name: 'inventario_web_entregas.comprobante'},
            {data: 'fecha_solicitud', name: 'inventario_web_entregas.created_at'},
            {data: 'estado', name: 'inventario_web_estados.nombre'},
            {data: 'marca', name: 'maraca'},
            {data: 'placa', name: 'inventario_web_camiones.placa'},
            { data: null, render: function(data,type,row){
                return "<a href='{{url('guardSol/')}}/"+data.ide+"'  class= 'btn btn-danger btn-sm' ><i class='fas fa-edit'></i> Editar</button>"}
            },
            { data: null, render: function(data,type,row){
                return "<a href='{{url('v_solicitud/')}}/"+data.ide+"'  class= 'btn btn-dark btn-sm' ><i class='fas fa-info-circle'></i> Ver Detalles</button>"}
            }
        ],
        "columnDefs": [
            { targets: 0, searchable: true },
            { targets: [0,1,2,3,5], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [2], render:function(data){
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

<script>
$(document).ready(function() {
	$('#export-btn').on('click', function(e){
		e.preventDefault();
		ResultsToTable();
	});
	function ResultsToTable(){
        $("#sucursales").table2excel({
           filename: "Reporte_Sucursal.xls"
		});
	}
});
</script>
@endsection