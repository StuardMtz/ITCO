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
<div class="container-fluid">
	<a class="btn btn-dark" href="{{ route('sucs')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <h5>Inventarios</h5>
    <div class="table-responsive">
        <table class="table table-sm" id="inventarios">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Encargado</th>
                    <th>Fecha Inicial</th>
                    <th>Fecha Final</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Estado</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    var table = $('#inventarios').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 50,
        searching: true,
        responsive: false,
        ajax:{
            url: "{{url('dat_invent',['sucursal'=>$sucursal,'bodega'=>$bodega])}}",
            dataSrc: 'data',
        },
        "order": [[ 0,"desc" ]],
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
            { data: 'numero', name:  'numero'},
            { data: 'nombre', name: 'nombre'},
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'uninombre', name: 'uninombre'},
            { data: 'bonombre', name: 'bonombre'},
            { data: 'porcentaje', name:'porcentaje',render: $.fn.dataTable.render.number(',', '%', '%', 0, '')}
                ],
        "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [2,3], render:function(data){
             moment.locale('es');
            return moment(data).format('LLLL');
        }}
        ],
        rowCallback:function(row,data){
            if(data['porcentaje'] > 99 ){
                $($(row).find("td")[6]).html('100%');
            }

        },
    });
    $('#inventarios').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      var redirectWindow = window.open(url_global+'/ver/' + row.data().numero, '_blank');
      redirectWindow.location;
    });
</script>
@endsection
