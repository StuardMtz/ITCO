@extends('layouts.app')
@section('content')

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
	 		<a class="nav-link" id="export-btn" href="{{route('repminimax',['sucursal'=>$sucursal,'bodega'=>$bodega,'todo'=>'1'])}}"> Atrás</a>
	 		<a class="nav-link" href="{{route('repminimax',['sucursal'=>$sucursal,'bodega'=>$bodega,'todo'=>'2'])}}">Abajo del mínimo</a>
			<a class="nav-link" href="#">Abajo del reorden</a>
            @guest
            @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
            @endguest
        </div>
    </div>
</nav>
<div class="container-fluid">
	<blockquote class="blockquote text-center">
        <p class="mb-0">{{$sucu->nombre}} {{$bod->nombre}}</p>
    </blockquote>
	<div class="table-responsive-sm">
		<table class="table table-sm table-borderless" id="existencia" style="width:100%">
			<thead >
				<tr class="flotant">
					<th class="flotant">Categoria</th>
					<th class="flotant">Código</th>
					<th class="flotant">Producto</th>
					<th class="flotant">Abajo del máximo</th>
					<th class="flotant">Abajo del reorden</th>
					<th class="flotant">Existencia</th>
					<th class="flotant">Mínimo</th>
					<th class="flotant">Reorden</th>
					<th class="flotant">Máximo</th>
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
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{url('rdat_exist_max',['sucursal'=>$sucursal,'bodega'=>$bodega])}}",
        dataSrc: "data",
    },
    "order": [[ 0,"asc" ]],
	dom: 'Bfrtip',
    lengthMenu: [
        [ 10, 25, 50, -1 ],
        [ '10 filas', '25 filas', '50 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {
            className: 'nav-link',
            extend: 'pageLength',
            className: 'btn btn-dark',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {
            className: 'nav-link',
            extend: 'colvis',
            className: 'btn btn-dark',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {
            className: 'nav-link',
            extend: 'excel' ,
            autoFilter: true,
            className: 'btn btn-dark',
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
        if((data['porcentaje'] >= 75) & (data['porcentaje'] <= 100)){
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
		else if((data['porcentaje'] < 50) & (data['porcentaje'] >= 30)){
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
    var redirectWindow = window.open(url_global+'/rgraficado/'+row.data().cod_unidad+'/'+row.data().cod_bodega+'/'+row.data().cod_producto, '_blank');
    redirectWindow.location;
});
</script>
@endsection
