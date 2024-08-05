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
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{route('Vtran')}}"><i class="fas fa-truck"></i> Transferencias en proceso</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('BTran')}}"><i class="fas fa-people-carry"></i> Transferencias en bodega</a>
            </li>
            <li class="nav-item"><!--
                <a class="nav-link" href="{{route('DeTran')}}"><i class="fas fa-truck"></i> Transferencias despachadas</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link" href="#" id="active"><i class="fas fa-clipboard-check"></i> Transferencias finalizadas</a>
            </li>
            @if(Auth::id() == 75 || Auth::id() == 6 || Auth::id() == 81)
            <li class="nav-item">
                <a class="nav-link" href="{{route('lut')}}"><i class="fas fa-users"></i> Integrantes de grupo</a>
            </li>
            @else
            @endif
        </ul>
    </div>
    <h5>Transferencias finalizadas</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha de creación</th>
                    <th>Fecha de salida</th>
                    <th>Fecha de finalización</th>
                    <th>Observación</th>
                    <th>Placa</th>
                    <th>Estado</th>
                    <th>Eficacia</th>
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
            url: "{{route('DFTran')}}",
            dataSrc: "data",
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
                { data: 'num_movi', name: 'num_movi'},
                { data: 'nombre', name:'nombre'},
                { data: 'bodega', name: 'bodega'},
                { data: 'created_at', name: 'created_at'},
                { data: 'fechaSalida', name: 'fechaSalida'},
                { data: 'fecha_entregado', name: 'fecha_entregado'},
                { data: 'observacion', name: 'observacion'},
                { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
                    return row['propietario'] +' '+  row['placa_vehiculo']}
                },
                { data: 'estado', name: 'estado'},
                { data: 'porcentaje', name: 'portentaje', render: function(data, type, row){
                    return row['porcentaje']+''+ '%'}
                }
                ],
        "columnDefs": [
            { bSortable: false, targets: [8]},
            { targets: 0, searchable: true },
            { targets: [0,1,4,5,6], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [3,4,5], render:function(data){
                moment.locale('es');
                return moment(data).format('lll');
            }}
        ],

        rowCallback:function(row,data){
        if(data['estado'] == "En cola"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)").html('');
            $($(row).find("td")[5]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)").html('');
            $($(row).find("td")[6]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)").html('');
            $($(row).find("td")[8]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#5AD7690A,#5AD76973,#51B558B3)").html('');
        }
        else if(data['estado'] == "Preparando carga"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)").html('');
            $($(row).find("td")[5]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)").html('');
            $($(row).find("td")[6]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)").html('');
            $($(row).find("td")[8]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)").html('');
        }
        else if(data['estado'] == "Carga preparada" || data['estado'] == "Cargando"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)").html('');
            $($(row).find("td")[5]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)").html('');
            $($(row).find("td")[6]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)").html('');
            $($(row).find("td")[8]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)").html('');
        }
        else if(data['estado'] == "Despachado en camino"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)").html('');
            $($(row).find("td")[6]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#FFD1D1,#F07A7A,#FF6A6A)").html('');
        }
        else if(data['estado'] == "En sucursal"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#FAB052,#FFC97CB0,#F98744)").html('');
        }
        else if(data['estado'] == "Finalizada"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#B2E2A540,#B2E2A5,#87C47CDB)");
        }
    },

    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      window.location.href =(url_global+'/VeTran/'+row.data().num_movi);
      redirectWindow.location;
    });
</script>
@endsection
