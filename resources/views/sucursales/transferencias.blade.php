@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="active"><i class="fas fa-truck"></i> Transferencias en proceso</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('f_tran')}}"><b><i class="fas fa-clipboard-check"></i> Transferencias finalizadas</b></a>
        </li>
    </ul>
    <h5>Transferencias en proceso</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Fecha</th>
                    <th>Fecha aprox. entrega</th>
                    <th>Observación</th>
                    <th>Placa</th>
                    <th>Estado</th>
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
            url: "{{route('da_tran_su')}}",
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
                { data: 'fecha', name: 'fecha'},
                { data: 'fechaEntrega', name: 'fechaEntrega'},
                { data: 'observacion', name: 'observacion'},
                { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
                    return row['propietario'] +' '+  row['placa_vehiculo']}
                },
                { data: 'estado', name: 'estado'}
                ],
        "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [2,3], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],
    rowCallback:function(row,data){
        if(data['estado'] == "Despachado en camino"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#87cefa)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#87cefa)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#87cefa)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#fffafa,#87cefa)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa,#87cefa)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#fffafa,#87cefa)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#fffafa,#87cefa)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#fffafa,#87cefa)");
        }
        else if(data['estado'] == "En sucursal"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#ffb347)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#ffb347)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#ffb347)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#fffafa,#ffb347)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa,#ffb347)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#fffafa,#ffb347)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#fffafa,#ffb347)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#fffafa,#ffb347)");
        }
    },
    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      window.location.href =(url_global+'/v_tran/'+row.data().num_movi);
      redirectWindow.location;
    });
</script>
@endsection
