@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <a class="btn btn-dark btn-sm" href="{{route('grafClasSuc',['sucursal'=>$sucursal,'bodega'=>$bodega])}}">Atrás</a>
    <a class="btn btn-primary btn-sm" href="{{route('grafClasSucHis',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>$clase])}}">Ver historial</a>
    <div class="row" id="graficaBarras">
    </div>
    <div class="table-responsive-sm">
		    <table class="table table-sm table-borderless" id="existencia">
			    <thead >
				    <tr class="flotant">
					    <th class="flotant">Código</th>
					    <th class="flotant">Producto</th>
                        <th class="flotant">Porcentaje</th>
					    <th class="flotant">Existencia</th>
					    <th class="flotant">Mínimo</th>
					    <th class="flotant">Reorden</th>
					    <th class="flotant">Máximo</th>
				    </tr>
			    </thead>
		    </table>
	    </div>
    </div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script>
    var url_global='{{url("/")}}';
</script>
<script type="text/javascript">
var punteo =  <?php echo json_encode($punteo) ?>;
var nombre =   <?php echo json_encode($nombre); ?>;
var contador = 0;
var contador2 = 0;
var arreglo = [];
data = [];
$.each(punteo, function(index, value){
        var color;
        if (value >= 0 & value <= 8) color = '#EA241DE0';
        else if (value > 8 & value <= 16) color = '#FD130FBA';
        else if (value > 16 & value <= 24) color = '#FA3731CF';
        else if (value > 24 & value <= 32) color = '#E65B23D4';
        else if (value > 32 & value <= 40) color = '#EE4C0BC4';
        else if (value > 40 & value <= 48) color = '#D9A802D6';
        else if (value > 48 & value <= 56) color = '#E8B402AB';
        else if (value > 56 & value <= 64) color = '#E3D60D73';
        else if (value > 64 & value <= 72) color = '#ECEC0380';
        else if (value > 72 & value <= 80) color = '#A4F20791';
        else if (value > 80 & value <= 88) color = '#5DF2077D';
        else if (value > 88 & value <= 100) color = '#19CD247D';
        else color = '#C9231E ';
        data.push({y:value, color: color});
    });

    Highcharts.chart('graficaBarras', {
        chart: {
            type: 'column'
        },
        title: {
           text: {!! json_encode($clas) !!}
        },
        subtitle: {
           text: 'Existencia de productos por sucursal'
        },
        xAxis: {
            type: 'text',
            categories: nombre
        },
        yAxis: {
            title: {
            text: 'Porcentaje'
            }
        },
        legend: {
            ayout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        plotOptions: {
            bar: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    color: 'black',
                    format: '{point.y:.1f}%'
                }
            }
        },
        series: [{
            name: 'Porcentaje',
            data: data
        }],
        responsive: {
            rules: [{
                condition: {
                    callback: true,
                    maxWidth: 500,
                    mixHeight: 1000
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
    });
</script>
<script>
var table = $('#existencia').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    ajax:{
        url: "{{url('grafClasTSucPro',['sucursal'=>$sucursal,'bodega'=>$bodega,'clase'=>$clase])}}",
        dataSrc: "data",
    },
    "order": [[ 2,"asc" ]],
	dom: 'Bfrtip',
    lengthMenu: [
        [ 10, 25, 50, -1 ],
        [ '10 filas', '25 filas', '50 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {
            extend: 'pageLength',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {
            extend: 'colvis',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {
            extend: 'excel' ,
            autoFilter: true,
            sheetName: 'Exported data',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel'
        }
    ],
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
        { data: 'nombre_corto', name: 'nombre_corto'},
        { data: 'nombre_fiscal', name: 'nombre_fiscal'},
        { data: 'punteo', name: 'punteo', render: $.fn.dataTable.render.number(',','.',2)},
        { data: 'existencia1', name: 'existencia1', render: $.fn.dataTable.render.number(',','.',0 )},
        { data: 'minimo', name: 'minimo', render: $.fn.dataTable.render.number(',','.',0 )},
		{ data: 'piso_sugerido', name: 'piso_sugerido', render: $.fn.dataTable.render.number(',','.',0 )},
		{ data: 'maximo', name: 'maximo', render: $.fn.dataTable.render.number(',','.',0 )}
    ],
    "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,4,5], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if((data['punteo'] >= 0) & (data['punteo'] <= 8)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#EA241DB0");
			$($(row).find("td")[5]).css("background-color","#EA241DB0");
			$($(row).find("td")[6]).css("background-color","#EA241DB0");
        }
        else if((data['punteo'] > 8) & (data['punteo'] <= 16)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#B41E1A");
			$($(row).find("td")[5]).css("background-color","#B41E1A");
			$($(row).find("td")[6]).css("background-color","#B41E1A");
        }
		else if((data['punteo'] > 16) & (data['punteo'] <= 24)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#ED0D07");
			$($(row).find("td")[5]).css("background-color","#ED0D07");
			$($(row).find("td")[6]).css("background-color","#ED0D07");
        }
        else if((data['punteo'] > 24) & (data['punteo'] <= 32)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#CF4F1C");
			$($(row).find("td")[5]).css("background-color","#CF4F1C");
			$($(row).find("td")[6]).css("background-color","#CF4F1C");
        }
        else if((data['punteo'] > 32) & (data['punteo'] <= 40)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#EE4C0B");
			$($(row).find("td")[5]).css("background-color","#EE4C0B");
			$($(row).find("td")[6]).css("background-color","#EE4C0B");
        }
		else if((data['punteo'] > 40) & (data['punteo'] <= 48)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#D9A802");
			$($(row).find("td")[5]).css("background-color","#D9A802");
			$($(row).find("td")[6]).css("background-color","#D9A802");
        }
        else if((data['punteo'] > 48) & (data['punteo'] <= 56)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#E8B402");
			$($(row).find("td")[5]).css("background-color","#E8B402");
			$($(row).find("td")[6]).css("background-color","#E8B402");
        }
        else if((data['punteo'] > 56) & (data['punteo'] <= 64)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#E3D60D");
			$($(row).find("td")[5]).css("background-color","#E3D60D");
			$($(row).find("td")[6]).css("background-color","#E3D60D");
        }
		else if((data['punteo'] > 64) & (data['punteo'] <= 72)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#ECEC03");
			$($(row).find("td")[5]).css("background-color","#ECEC03");
			$($(row).find("td")[6]).css("background-color","#ECEC03");
        }
        else if((data['punteo'] > 72) & (data['punteo'] <= 80)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#A4F207");
			$($(row).find("td")[5]).css("background-color","#A4F207");
			$($(row).find("td")[6]).css("background-color","#A4F207");
        }
        else if((data['punteo'] > 80) & (data['punteo'] <= 88)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#5DF207");
			$($(row).find("td")[5]).css("background-color","#5DF207");
			$($(row).find("td")[6]).css("background-color","#5DF207");
        }
        else if((data['punteo'] > 88) & (data['punteo'] <= 100)){
			$($(row).find("td")[3]).css("background-color","#B0DBDC");
			$($(row).find("td")[4]).css("background-color","#19CD24");
			$($(row).find("td")[5]).css("background-color","#19CD24");
			$($(row).find("td")[6]).css("background-color","#19CD24");
        }
        else
        {
            $($(row).find("td")[3]).css("background-color","#B0DBDC");
        }
    },
});
$('#inventario tbody').on('click', 'td.details-control', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var tableId = 'post-' + row.data().cod_producto;
    if(row.child.isShown()){
        row.child.hide();
        tr.removeClass('shown');
    }else
    {
        row.child(template(row.data())).show();
        initTable(tableId, row.data());
        tr.next().find('td').addClass('no-padding bg-gray');
    }
});
$('#existencia').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var redirectWindow = window.open(url_global+'/rgraficado/'+row.data().cod_unidad+'/'+row.data().cod_bodega+'/'+row.data().cod_producto, '_blank');
    redirectWindow.location;
});
</script>
@endsection
