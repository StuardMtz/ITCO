@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('js/FixedColumns-3.3.0/css/fixedColumns.dataTables.css') }}">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('js/FixedHeader-3.1.6/js/dataTables.fixedHeader.js')}}"></script>
<div class="container-fluid">
    <ul class="nav nav-tabs">
		<li class="nav-item">
    		<a class="nav-link" href="{{route('vista_asesores')}}"><i class="fas fa-clipboard"></i> Listado sucursales</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{route('existencia_sucursal',['sucursal'=>$sucu,'bodega'=>$bod,'todo'=>'1'])}}"><i class="fas fa-dolly-flatbed"></i> Existencia</a>
		</li>
		<li class="nav-item">
    		<a class="nav-link" href="{{route('existencia_sucursal',['sucursal'=>$sucu,'bodega'=>$bod,'todo'=>'2'])}}"><i class="fas fa-arrow-circle-down"></i> Abajo del mínimo</a>
		</li>
		<li class="nav-item">
    		<a class="nav-link" id="active"><i class="fas fa-arrow-circle-down"></i> Abajo del reorden</a>
    	<li>
	</ul>
    <h4>Minimos {{$sucursal->nombre}} {{$bodega->nombre}}</h4>
    <div class="table-responsive-sm">
		<table class="table table-sm" id="existencia">
			<thead >
				<tr>
					<th>Categoria</th>
					<th>Código</th>
					<th>Producto</th>
					<th>Abajo del máximo</th>
					<th>Abajo del reorden</th>
					<th>Existencia</th>
					<th>Mínimo</th>
					<th>Reorden</th>
					<th>Máximo</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
	<script>
    var table = $('#existencia').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        responsive: false,
        ajax:{
            url: "{{url('dat_ase_exreo',['sucursal'=>$sucu,'bodega'=>$bod])}}",
            dataSrc: "data",
        },
        "order": [[ 0,"asc" ]],
		  dom: 'Bfrtip',
            lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 filas', '25 filas', '50 filas', 'Mostrar Todo' ]
            ],
		  buttons: [
                {   extend: 'pageLength',
                    text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
                 },
                {   extend: 'colvis',
                    text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
                },
                {   extend: 'excel' ,
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
                { data: 'cod_tipo_prod', name: 'cod_tipo_prod'},
                { data: 'nom_corto', name: 'nom_corto'},
                { data: 'nom_producto', name: 'nom_producto'},
                { data: 'baj_max', name:'baj_max', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'baj_reorden',name:'baj_reorden', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'existencia', name: 'existencia', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'min', name: 'min', render: $.fn.dataTable.render.number(',','.',0 )},
					 { data: 'reorden', name: 'reorden', render: $.fn.dataTable.render.number(',','.',0 )},
					 { data: 'max', name: 'max', render: $.fn.dataTable.render.number(',','.',0 )}
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,4,5], searchable: true },
        { targets: '_all', searchable: false },
    ],
    rowCallback:function(row,data){
        if((data['porcentaje'] >= 75) & (data['porcentaje'] < 100)){
			$($(row).find("td")[3]).css("background-color","#A5E4A2");
			$($(row).find("td")[4]).css("background-color","#A5E4A2");
			$($(row).find("td")[5]).css("background-color","#B0DBDC");
			$($(row).find("td")[6]).css("background-color","#A5E4A2");
			$($(row).find("td")[7]).css("background-color","#A5E4A2");
			$($(row).find("td")[8]).css("background-color","#A5E4A2");
        }
        else if((data['porcentaje'] >= 50) & (data['porcentaje'] < 75)){
			$($(row).find("td")[3]).css("background-color","#C9E9B8");
			$($(row).find("td")[4]).css("background-color","#C9E9B8");
			$($(row).find("td")[5]).css("background-color","#B0DBDC");
			$($(row).find("td")[6]).css("background-color","#C9E9B8");
			$($(row).find("td")[7]).css("background-color","#C9E9B8");
			$($(row).find("td")[8]).css("background-color","#C9E9B8");
        }
		else if((data['porcentaje'] < 50) & (data['porcentaje'] > 30)){
			$($(row).find("td")[3]).css("background-color","#F5EB98");
			$($(row).find("td")[4]).css("background-color","#F5EB98");
			$($(row).find("td")[5]).css("background-color","#B0DBDC");
			$($(row).find("td")[6]).css("background-color","#F5EB98");
			$($(row).find("td")[7]).css("background-color","#F5EB98");
			$($(row).find("td")[8]).css("background-color","#F5EB98");
        }
        else if(data['porcentaje'] < 30){
			$($(row).find("td")[3]).css("background-color","#F78181");
			$($(row).find("td")[4]).css("background-color","#F78181");
			$($(row).find("td")[5]).css("background-color","#B0DBDC");
			$($(row).find("td")[6]).css("background-color","#F78181");
			$($(row).find("td")[7]).css("background-color","#F78181");
			$($(row).find("td")[8]).css("background-color","#F78181");
        }
        else
        {
            $($(row).find("td")[5]).css("background-color","#B0DBDC");
        }
   },
    });

    $('#existencia').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      var redirectWindow = window.open(url_global+'/graficado/' + '{{$sucu}}' + '/{{$bod}}/' + row.data().cod_producto, '_blank');
      redirectWindow.location;
    });
</script>
@endsection
