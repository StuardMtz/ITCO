@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.TransSucursales'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias finalizadas</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="6"><input class="form-control" type="text" id="column4_search" placeholder="Fecha finalizada"></th>
                    <th colspan="6"><input class="form-control" type="text" id="column9_search" placeholder="Sale bodega"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Creada el</th>
                    <th>Finalizada</th>
                    <th>Creado por</th>
                    <th>Descripción</th>
                    <th>No. factura</th>
                    <th>Serie factura</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>

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
    scrollY: '65vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('dtran_fin_suc')}}",
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
        { data: 'fechaSucursal', name: 'fechaSucursal'},
        { data: 'usuario', name: 'usuario'},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'numeroFactura', name: 'numeroFactura'},
        { data: 'serieFactura', name: 'serieFactura'},
        { data: 'sale', name: 'sale'},
        { data: 'bsale', name: 'bsale'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,4,5,6,7,8,9,10], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
});
$('#column4_search').on('keyup', function(){
    table.columns(4).search(this.value).draw();
});
$('#column9_search').on('keyup', function(){
    table.columns(7).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTranS/'+row.data().id);
    redirectWindow.location;
});
</script>
<script>
    $(function(){
        $('#sucursal').on('change', onSelectSucursalChange);
    });
    function onSelectSucursalChange(){
        var cod_unidad = $(this).val();
        if(! cod_unidad){
            $('#bodega').html('<option value ="">Seleccione una opcion</option>');
            return;
        };

    $.get('select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
        $('#bodega').html(html_select);
    });
}
</script>
<script>
    $(function(){
        $('#sasucursal').on('change', onSelectSucursaleChange);
    });
    function onSelectSucursaleChange(){
        var cod_unidad = $(this).val();
        if(! cod_unidad){
            $('#bodegasa').html('<option value ="">Seleccione una opcion</option>');
            return;
        };

    $.get('select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
        $('#bodegasa').html(html_select);
    });
}
</script>
<script>
$(document).ready(function(){
	$("#manual").click(function(){
        $("#factura").attr('disabled',true);
        $("#sucursal").attr('disabled',false);
        $('#noserie').attr('disabled',true);
        $('#bodega').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divfactura').style.display = "none";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divsucursal').style.display = "block";
        document.getElementById('divbodega').style.display = "block";
	});
	$("#docfactura").click(function(){
		$("#noserie").attr('disabled',false);
        $("#factura").attr('disabled',false);
        $("#sucursal").attr('disabled',true);
        $('#bodega').attr('disabled',true);
        $('#factura').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        document.getElementById('divserie').style.display = "block";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('divsucursal').style.display = "none";
        document.getElementById('divbodega').style.display = "none";
	});
    $("#pedido").click(function(){
		$("#noserie").attr('disabled',true);
        $("#factura").attr('disabled',false);
        $("#sucursal").attr('disabled',false);
        $('#bodega').attr('disabled',false);
        $('#factura').attr('disabled',false);
        $('#cpedido').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('npedido').style.display = "block";
        document.getElementById('divsucursal').style.display = "block";
        document.getElementById('divbodega').style.display = "block";
	});
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
