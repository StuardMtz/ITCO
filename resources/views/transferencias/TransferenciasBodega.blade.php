@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.transferencias'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias en área de carga</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="4"><input class="form-control" type="text" id="column2_search" placeholder="Bodega salida"></th>
                    <th colspan="2"><input class="form-control" type="text" id="column3_search" placeholder="Fecha entrega"></th>
                    <th colspan="2"><input class="form-control" type="text" id="column6_search" placeholder="Transporte"></th>
                    <th colspan="3"><input class="form-control" type="text" id="column8_search" placeholder="Sale bodega"></th>
                    <th colspan="3"><input class="form-control" type="text" id="column9_search" placeholder="Estado"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha entrega</th>
                    <th>Descripción</th>
                    <th>Comentario</th>
                    <th>Creado por</th>
                    <th>Transporte</th>
                    <th>Estado</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
                    <th>Peso Ton.</th>
                    <th>Agrupada en</th>
                    <th></th>
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
    scrollY: '62vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    lengthMenu: false,
    "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
    ajax:{
        url: "{{route('datos_trans_bodega')}}",
        dataSrc: "data",
    },
    lengthMenu: [
        [ 100, 200, 300, -1 ],
        [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {   extend: 'pageLength',
            className: 'btn btn-dark',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {   extend: 'colvis',
            className: 'btn btn-dark',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {   extend: 'excelHtml5',
            className: 'btn btn-dark',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true,
            title: '{{Auth::user()->name}}'
        }
    ],
    "order": [[ 3,"desc" ]],
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
        { data: 'fecha', name: 'fecha', render: function(data,type,row){
            if(type === "sort" || type === "type"){
                return data;
            }
            return moment(data).format('DD/MM/YYYY');
        }},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'comentario', name: 'comentario'},
        { data: 'usuario', name: 'usuario'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo'},
        { data: 'estado', name: 'estado'},
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'},
        { data: 'peso', name: 'peso', render: $.fn.dataTable.render.number(',','.',3)},
        { data: 'grupo', name: 'grupo'},
        { data: 'id', name: 'id'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [13]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6,7,8,9,10,11,12], searchable: true },
        { targets: '_all', searchable: false },


    ],
    rowCallback:function(row,data){
        if(data['id'] == 14 && data['fecha'] < "{{$hoy}}"){
            $($(row).find("td")[0]).css("background-color","#DD040461");
            $($(row).find("td")[1]).css("background-color","#DD040461");
            $($(row).find("td")[2]).css("background-color","#DD040461");
            $($(row).find("td")[3]).css("background-color","#DD040461");
            $($(row).find("td")[4]).css("background-color","#DD040461");
            $($(row).find("td")[5]).css("background-color","#DD040461");
            $($(row).find("td")[6]).css("background-color","#DD040461");
            $($(row).find("td")[7]).css("background-color","#DD040461");
            $($(row).find("td")[8]).css("background-color","#DD040461");
            $($(row).find("td")[9]).css("background-color","#DD040461");
            $($(row).find("td")[10]).css("background-color","#DD040461");
            $($(row).find("td")[11]).css("background-color","#DD040461");
            $($(row).find("td")[12]).css("background-color","#DD040461");
            $($(row).find("td")[13]).css("background-color","#DD040461").html('');
        }
        else if(data['id'] == 14 && data['fecha'] == "{{$next}}" && data['fecha'] <= "{{$proxima}}"){
            $($(row).find("td")[0]).css("background-color","#DC900073");
            $($(row).find("td")[1]).css("background-color","#DC900073");
            $($(row).find("td")[2]).css("background-color","#DC900073");
            $($(row).find("td")[3]).css("background-color","#DC900073");
            $($(row).find("td")[4]).css("background-color","#DC900073");
            $($(row).find("td")[5]).css("background-color","#DC900073");
            $($(row).find("td")[6]).css("background-color","#DC900073");
            $($(row).find("td")[7]).css("background-color","#DC900073");
            $($(row).find("td")[8]).css("background-color","#DC900073");
            $($(row).find("td")[9]).css("background-color","#DC900073");
            $($(row).find("td")[10]).css("background-color","#DC900073");
            $($(row).find("td")[11]).css("background-color","#DC900073");
            $($(row).find("td")[12]).css("background-color","#DC900073");
            $($(row).find("td")[13]).css("background-color","#DC900073").html('');
        }
        else if(data['id'] == 14 && data['fecha'] > "{{$next}}" && data['fecha'] <= "{{$proxima}}"){
            $($(row).find("td")[0]).css("background-color","#AAD14880");
            $($(row).find("td")[1]).css("background-color","#AAD14880");
            $($(row).find("td")[2]).css("background-color","#AAD14880");
            $($(row).find("td")[3]).css("background-color","#AAD14880");
            $($(row).find("td")[4]).css("background-color","#AAD14880");
            $($(row).find("td")[5]).css("background-color","#AAD14880");
            $($(row).find("td")[6]).css("background-color","#AAD14880");
            $($(row).find("td")[7]).css("background-color","#AAD14880");
            $($(row).find("td")[8]).css("background-color","#AAD14880");
            $($(row).find("td")[9]).css("background-color","#AAD14880");
            $($(row).find("td")[10]).css("background-color","#AAD14880");
            $($(row).find("td")[11]).css("background-color","#AAD14880");
            $($(row).find("td")[12]).css("background-color","#AAD14880");
            $($(row).find("td")[13]).css("background-color","#AAD14880").html('');
        }
        else if(data['id'] == 14 && data['fecha'] > "{{$proxima}}"){
            $($(row).find("td")[0]).css("background-color","#21B20C70");
            $($(row).find("td")[1]).css("background-color","#21B20C70");
            $($(row).find("td")[2]).css("background-color","#21B20C70");
            $($(row).find("td")[3]).css("background-color","#21B20C70");
            $($(row).find("td")[4]).css("background-color","#21B20C70");
            $($(row).find("td")[5]).css("background-color","#21B20C70");
            $($(row).find("td")[6]).css("background-color","#21B20C70");
            $($(row).find("td")[7]).css("background-color","#21B20C70");
            $($(row).find("td")[8]).css("background-color","#21B20C70");
            $($(row).find("td")[9]).css("background-color","#21B20C70");
            $($(row).find("td")[10]).css("background-color","#21B20C70");
            $($(row).find("td")[11]).css("background-color","#21B20C70");
            $($(row).find("td")[12]).css("background-color","#21B20C70");
            $($(row).find("td")[13]).css("background-color","#21B20C70").html('');
        }
        else if(data['id'] == 14){
            $($(row).find("td")[0]).css("background-color","#F0F00D6E");
            $($(row).find("td")[1]).css("background-color","#F0F00D6E");
            $($(row).find("td")[2]).css("background-color","#F0F00D6E");
            $($(row).find("td")[3]).css("background-color","#F0F00D6E");
            $($(row).find("td")[4]).css("background-color","#F0F00D6E");
            $($(row).find("td")[5]).css("background-color","#F0F00D6E");
            $($(row).find("td")[6]).css("background-color","#F0F00D6E");
            $($(row).find("td")[7]).css("background-color","#F0F00D6E");
            $($(row).find("td")[8]).css("background-color","#F0F00D6E");
            $($(row).find("td")[9]).css("background-color","#F0F00D6E");
            $($(row).find("td")[10]).css("background-color","#F0F00D6E");
            $($(row).find("td")[11]).css("background-color","#F0F00D6E");
            $($(row).find("td")[12]).css("background-color","#F0F00D6E");
            $($(row).find("td")[13]).css("background-color","#F0F00D6E").html('');
        }
        else if(data['id'] == 15){
            $($(row).find("td")[0]).css("background-color","#C08ECA91");
            $($(row).find("td")[1]).css("background-color","#C08ECA91");
            $($(row).find("td")[2]).css("background-color","#C08ECA91");
            $($(row).find("td")[3]).css("background-color","#C08ECA91");
            $($(row).find("td")[4]).css("background-color","#C08ECA91");
            $($(row).find("td")[5]).css("background-color","#C08ECA91");
            $($(row).find("td")[6]).css("background-color","#C08ECA91");
            $($(row).find("td")[7]).css("background-color","#C08ECA91");
            $($(row).find("td")[8]).css("background-color","#C08ECA91");
            $($(row).find("td")[9]).css("background-color","#C08ECA91");
            $($(row).find("td")[10]).css("background-color","#C08ECA91");
            $($(row).find("td")[11]).css("background-color","#C08ECA91");
            $($(row).find("td")[12]).css("background-color","#C08ECA91");
            $($(row).find("td")[13]).css("background-color","#C08ECA91").html('');
        }
        else if(data['id'] == 16 || data['id'] == "17"){
            $($(row).find("td")[0]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[1]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[2]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[3]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[4]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[5]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[6]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[7]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[8]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[9]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[10]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[11]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[12]).css("background-color","#8BC8D3A8");
            $($(row).find("td")[13]).css("background-color","#8BC8D3A8").html('');
        }
    },
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column3_search').on('keyup', function(){
    table.columns(3).search(this.value).draw();
});
$('#column6_search').on('keyup', function(){
    table.columns(6).search(this.value).draw();
});
$('#column8_search').on('keyup', function(){
    table.columns(8).search(this.value).draw();
});
$('#column9_search').on('keyup', function(){
    table.columns(9).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/EdTran/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection
