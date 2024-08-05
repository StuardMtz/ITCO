@extends('layouts.app')
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/min_max.js')}}" defer></script>
@section('content')
<div class="container-fluid">
	<ul class="nav nav-tabs">
		<li class="nav-item">
            <a class="nav-link"  href="{{route('minimax',['sucursal'=>$sucursal,'bodega'=>$bodega,'todo'=>'1'])}}"><i class="fas fa-arrow-left"></i> Atrás</a>
		</li>
		<li class="nav-item">
	 		<a class="nav-link" href="{{route('minimax',['sucursal'=>$sucursal,'bodega'=>$bodega,'todo'=>'2'])}}">Abajo del mínimo</a>
		</li>
		<li class="nav-item">
	 		<a class="nav-link" href="{{route('minimax',['sucursal'=>$sucursal,'bodega'=>$bodega,'todo'=>'3'])}}">Abajo del reorden</a>
		</li>
	</ul>
    <div class="flotante">
        <form class="needs-validation" novalidate method="get" action="{{route('bMaxmin',[$sucursal,$bodega])}}">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <select id="producto" name="producto[]" class="custom-select" required></select>
                <div class="valid-tooltip">
                    Muy bien.
                </div>
                <div class="invalid-tooltip">
                    No debes dejar este campo en blanco.
                </div>
                <button class="btn btn-dark btn-sm my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
            </nav>
        </form>
    </div>
	<h5>{{$sucu->nombre}} {{$bod->nombre}}</h5>
	<div class="table-responsive">
		<table class="table table-sm" id="existencia">
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
    <div class="modal fade" id="Transferencia" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva transferencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('TransN',$sucursal)}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="col">
                            <label for="validationCustom02"><b>Descripción</b></label>
                            <textarea type="text" class="form-control" id="validationCustom02" name="descripcion" required></textarea>
                            <div class="valid-feedback">
                                Excelente!
                            </div>
                            <div class="invalid-feedback">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="validationCustom01"><b>Fecha para carga</b></label>
                            <input type="datetime-local" class="form-control" id="validationCustom01" name="fechaCarga" required>
                            <div class="valid-feedback">
                                Excelente!
                            </div>
                            <div class="invalid-feedback">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="inputFechaEntrega"><b>Fecha estimada entrega</b></label>
                            <input type="date" class="form-control" id="validationCustom03" name="fechaEntrega" required>
                            <div class="valid-feedback">
                                Excelente!
                            </div>
                            <div class="invalid-feedback">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-success btn-block" type="submit"><b><i class="fas fa-arrow-alt-circle-right"></i> Crear nueva transferencia</b></button>
                </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
	<script>
    var table = $('#existencia').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        ajax:{
            url: "{{url('dbMaxmin',['sucursal'=>$sucursal,'bodega'=>$bodega,'cate'=>$cate])}}",
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
                {   extend: 'excelHtml5',
                    text: '<i class="far fa-file-excel"></i>  Exportar a excel',
                    autoFilter: true,
                    title: '{{$sucu->nombre}} {{$bod->nombre}}'
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
      var redirectWindow = window.open(url_global+'/graficado/'+row.data().cod_unidad+'/'+row.data().cod_bodega+'/'+row.data().cod_producto, '_blank');
      redirectWindow.location;
    });
</script>
<script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
@endsection
