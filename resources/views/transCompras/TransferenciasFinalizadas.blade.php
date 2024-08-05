@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.transferenciasCompras'))

<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias finalizadas</p>
    </blockquote>

    <form method="get" action="{{url('trc_finfe')}}" class="form-inline">
    {{csrf_field()}}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Inicio</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="inicio">
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Fin</span>
            </div>
            <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="fin">
            <div class="input-group-append">
                <button type="submit" class="btn btn-warning btn-sm" type="button">Buscar</button>
            </div>
        </div>
    </form>

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="4"><input class="form-control" type="text" id="column2_search" placeholder="Bodega ingresa"></th>
                    <th colspan="4"><input class="form-control" type="text" id="column4_search" placeholder="Fecha salida"></th>
                    <th colspan="4"><input class="form-control" type="text" id="column7_search" placeholder="Placa"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha de creación</th>
                    <th>Fecha de salida</th>
                    <th>Fecha de finalización</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Placa</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
                    <th>Eficacia</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '60vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('transc_datos_finalizadas')}}",
        dataSrc: "data",
    },
    "order": [[ 5,"desc" ]],
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
        { data: 'fecha_entregado', name: 'fecha_entregado', render: function(data,type,row){
            if(type === "sort" || type === "type"){
                return data;
            }
            return moment(data).format('DD/MM/YYYY');
        }},
        { data: 'observacion', name: 'observacion'},
        { data: 'estado', name: 'estado'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
            return row['propietario'] +' '+  row['placa_vehiculo']}
        },
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'},
        { data: 'porcentaje', name: 'porcentaje'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [8]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,6,7,8,9], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4,5], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
    rowCallback:function(row,data){
        if(data['id_estado'] == 18 || data['id_estado'] == 19){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[1]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
			$($(row).find("td")[2]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[10]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[11]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
        }
        if(data['erroresVerificados'] == 1){
            $($(row).find("td")[10]).css("background-image","linear-gradient(#1490B1A1,#1490B18F,#1490B1A1)");
            $($(row).find("td")[11]).css("background-image","linear-gradient(#1490B1A1,#1490B18F,#1490B1A1)");
        }
    },
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column4_search').on('keyup', function(){
    table.columns(4).search(this.value).draw();
});
$('#column7_search').on('keyup', function(){
    table.columns(7).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/trc_vrfin/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection
