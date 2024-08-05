@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
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
    <h5>Transferencias </h5>
    <form method="get" action="{{url('transps',['id'=>$id,'bodega'=>$bodega])}}">
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Desde</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Inicio" name="inicio" value="{{$inicio}}">
                </div>
            </div>
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Hasta</span>
                    </div>
                    <input type="date" class="form-control" placeholder="Fin" name="fin" value="{{$fin}}">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <br>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="sucursales">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Fecha en cola</th>
                    <th>Fecha de finalización</th>
                    <th>Creada por</th>
                    <th>Verificada por</th>
                    <th>Propietario</th>
                    <th>Placa</th>
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
        url: "{{route('dtransps',['id'=>$id,'bodega'=>$bodega,'inicio'=>$inicio,'fin'=>$fin])}}",
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
        { data: 'fecha_enCola', name: 'fecha_enCola'},
        { data: 'fecha_entregado', name: 'fecha_entregado'},
        { data: 'usuario', name: 'usuario'},
        { data: 'usuarioSupervisa', name: 'usuarioSupervisa'},
        { data: 'propietario', name: 'propietario'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo'},
        { data: 'porcentaje', name: 'portentaje', render: function(data, type, row){
            return row['porcentaje']+''+ '%'}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [8]},
        { targets: 0, searchable: true },
        { targets: [0,1,4,5], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [2,3], render:function(data){
            moment.locale('es');
            return moment(data).format('lll');
        }}
    ],
    rowCallback:function(row,data){
        if(data['porcentaje'] == null && data['erroresVerificados'] == null){
            $($(row).find("td")[0]).css("background-color","#F07A7ABF");
            $($(row).find("td")[1]).css("background-color","#F07A7ABF");
			$($(row).find("td")[2]).css("background-color","#F07A7ABF");
            $($(row).find("td")[3]).css("background-color","#F07A7ABF");
            $($(row).find("td")[4]).css("background-color","#F07A7ABF");
            $($(row).find("td")[5]).css("background-color","#F07A7ABF");
            $($(row).find("td")[6]).css("background-color","#F07A7ABF");
            $($(row).find("td")[7]).css("background-color","#F07A7ABF");
            $($(row).find("td")[8]).css("background-color","#F07A7ABF");
            $($(row).find("td")[9]).css("background-color","#F07A7ABF");
            $($(row).find("td")[10]).css("background-color","#F07A7ABF");
            $($(row).find("td")[11]).css("background-color","#F07A7ABF");
            $($(row).find("td")[12]).css("background-color","#F07A7ABF");
            $($(row).find("td")[13]).css("background-color","#F07A7ABF").html('Pendiente');
            $($(row).find("td")[14]).css("background-color","#F07A7ABF").html('Pendiente');
        }
        else if(data['porcentaje'] != null && data['erroresVerificados'] == null){
            $($(row).find("td")[0]).css("background-color","#FFC97CB3");
            $($(row).find("td")[1]).css("background-color","#FFC97CB3");
			$($(row).find("td")[2]).css("background-color","#FFC97CB3");
            $($(row).find("td")[3]).css("background-color","#FFC97CB3");
            $($(row).find("td")[4]).css("background-color","#FFC97CB3");
            $($(row).find("td")[5]).css("background-color","#FFC97CB3");
            $($(row).find("td")[6]).css("background-color","#FFC97CB3");
            $($(row).find("td")[7]).css("background-color","#FFC97CB3");
            $($(row).find("td")[8]).css("background-color","#FFC97CB3");
            $($(row).find("td")[9]).css("background-color","#FFC97CB3");
            $($(row).find("td")[10]).css("background-color","#FFC97CB3");
            $($(row).find("td")[11]).css("background-color","#FFC97CB3");
            $($(row).find("td")[12]).css("background-color","#FFC97CB3");
            $($(row).find("td")[13]).css("background-color","#FFC97CB3").html('Pendiente');
            $($(row).find("td")[14]).css("background-color","#FFC97CB3");
        }
        else if(data['porcentaje'] != null && data['erroresVerificados'] == 2){
            $($(row).find("td")[0]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[1]).css("background-color","#B2E2A5E8");
			$($(row).find("td")[2]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[3]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[4]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[5]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[6]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[7]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[8]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[9]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[10]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[11]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[12]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[13]).css("background-color","#B2E2A5E8").html('Verificado');
            $($(row).find("td")[14]).css("background-color","#B2E2A5E8");
        }
        else if(data['erroresVerificados'] == 1 && data['opcionalDos'] == null){
            $($(row).find("td")[0]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[1]).css("background-color","#B2E2A5D1");
			$($(row).find("td")[2]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[3]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[4]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[5]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[6]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[7]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[8]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[9]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[10]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[11]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[12]).css("background-color","#B2E2A5D1");
            $($(row).find("td")[13]).css("background-color","#B2E2A5D1").html('Verificado');
            $($(row).find("td")[14]).css("background-color","#B2E2A5D1");
        }
        else if(data['erroresVerificados'] == 1 && data['opcionalDos'] != null){
            $($(row).find("td")[0]).css("background-color","#B2E2A5E8","border","solid 4px #C794DE");
			$($(row).find("td")[1]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[2]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[3]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[4]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[5]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[6]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[7]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[8]).css("background-color","#B2E2A5E8");
            $($(row).find("td")[9]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[10]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[11]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[12]).css("background-color","#DBB7EDCC");
            $($(row).find("td")[13]).css("background-color","#DBB7EDCC").html('Verificado');
            $($(row).find("td")[14]).css("background-color","#DBB7EDCC");
        }
    },
});
$('#sucursales').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var redirectWindow = window.open(url_global+'/vrtrans/'+ row.data().num_movi, '_blank');
    redirectWindow.location;
});
</script>
@endsection
