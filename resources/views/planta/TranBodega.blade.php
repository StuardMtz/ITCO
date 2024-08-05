@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
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
                <a class="nav-link active" href="#" id="active"><i class="fas fa-people-carry"></i> Transferencias en bodega</a>
            </li><!--
            <li class="nav-item">
                <a class="nav-link" href="{{route('DeTran')}}"><i class="fas fa-truck"></i> Transferencias despachadas</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link" href="{{route('FTran')}}"><i class="fas fa-clipboard-check"></i> Transferencias finalizadas</a>
            </li>
        </ul>
    </div>
    <h5>Transferencias en área de carga</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha entrega</th>
                    <th>Creado por</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Editar</th>
                    <th>Ver</th>
                    <th ></th>
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
            url: "{{route('DBTran')}}",
            dataSrc: "data",
        },
        "order": [[ 3,"asc" ]],
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
                { data: 'fecha', name: 'fecha'},
                { data: 'usuario', name: 'usuario'},
                { data: 'DESCRIPCION', name: 'DESCRIPCION'},
                { data: 'estado', name: 'estado'},
                { data: null, render: function(data,type,row){
                    return "<a href='{{url('EdTran')}}/"+ data.num_movi +"' class='btn btn-sm btn-outline-danger'><i class='fas fa-edit'></i> Editar</button>"}
                },
                { data: null, render: function(data,type,row){
                    return "<a href='{{url('VeTran')}}/"+ data.num_movi +"' class='btn btn-sm btn-outline-dark'><i class='fas fa-eye'></i> Ver</button>"}
                },
                { data: 'id', name: 'id'}
                ],
        "columnDefs": [
            { bSortable: false, targets: [6,7]},
            { targets: 0, searchable: true },
            { targets: [0,1,4,5], searchable: true },
            { targets: '_all', searchable: false },
            {targets: [3], render:function(data){
            moment.locale('es');
            return moment(data).format('LL');
            }}
        ],

        rowCallback:function(row,data){
        if(data['estado'] == "En cola" && data['fecha'] == "{{date('Y-m-d',strtotime($hoy))}}"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#F0F00D38,#F0F00D8A,#F0F00D)").html('');
        }
        else if(data['estado'] == "En cola" && data['fecha'] < "{{date('Y-m-d',strtotime($hoy))}}"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
			$($(row).find("td")[6]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)").html("Atrasada");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#F43C3C5E,#F43C3CC9,#F14949D4)").html('');
        }
        else if(data['estado'] == "En cola" && data['fecha'] > "{{date('Y-m-d',strtotime($hoy))}}" && data['fecha'] <= "{{date('Y-m-d',strtotime($proxima))}}"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#AAD1484F,#AAD1489C,#AAD148EB)").html('');
        }
        else if(data['estado'] == "En cola" && data['fecha'] > "{{date('Y-m-d',strtotime($proxima))}}"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#21B20C2E,#21B20C7D,#21B20CC2)").html('');
        }
        else if(data['estado'] == "Preparando carga"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#fffafa,#C08ECA91,#B68DC9)").html('');
        }
        else if(data['estado'] == "Carga preparada" || data['estado'] == "Cargando"){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
			$($(row).find("td")[1]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[2]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#8BC8D33B,#8BC8D3A8,#5FC4C4)").html('');
        }
    },
    });
</script>
@endsection
