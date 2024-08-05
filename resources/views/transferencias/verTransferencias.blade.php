@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.transferencias'))
<script src="{{ asset('js/serie.js') }}" defer></script>
<script src="{{ asset('js/devoluciones.js') }}" defer></script>
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias en proceso</p>
    </blockquote>
    <button type="button" class="btn btn-dark" id="modalb" data-toggle="modal" data-target="#nuevaTransferencia">
          Crear transferencia
    </button>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha</th>
                    <th>Fecha para carga</th>
                    <th>Creado por</th>
                    <th>Descripción</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
                    <th>Peso Ton.</th>
                    <th>Editar</th>
                    <th>Programar</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="nuevaTransferencia" aria-labelledby="nuevaTransferenciaLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Factura, pedido o manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('agreFac')}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="btn-group" role="group" aria-label="Tipo de transferencia">
                            <button type="button" class="btn btn-sm btn-warning" id="docfactura">Factura</button>
                            <button type="button" class="btn btn-sm btn-warning" id="pedido">Pedido</button>
                            <button type="button" class="btn btn-sm btn-warning" id="manual">Sin documento</button>
                            <a type="button" class="btn btn-sm btn-warning" id="minmax" href="{{route('lis_transf_mm')}}">MiniMax</a>
                            <button type="button" class="btn btn-sm btn-warning" id="devolucion">Devolución</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6" id="saleSuc">
                            <label>
                                <b>Sale de sucursal</b>
                            </label>
                            <select id="sasucursal" name="saleDe" class="form-control"required>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($saleDe as $sd)
                                <option value="{{$sd->cod_unidad}}">{{$sd->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="saleBod">
                            <label>
                                <b>Sale de bodega</b>
                            </label>
                            <select id="bodegasa" name="saleBo" class="form-control" required>
                                <option></option>
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="sucursalbodega">
                        <div class="form-group col-md-6" id="divsucursal">
                            <label><b>Entra a sucursal</b></label>
                            <select name="entraSu" class="form-control" required id="sucursal" disabled>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($entraSu as $su)
                                <option value="{{$su->cod_unidad}}">{{$su->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="divbodega">
                            <label for="nuevo_inventario"><b>Entra a bodega</b></label>
                            <select class="form-control" id="bodega" name="entraBo" required disabled>
                                <option></option>
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6" id="divserie">
                            <label><b>Serie factura</b></label>
                            <select class="form-control" id="noserie" name="serie" required disabled></select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="divseriedev">
                            <label><b>Serie devolución</b></label>
                            <select class="form-control" id="noseriedev" name="serie" required disabled>
                                <option value="">Seleccione una opción</option>
                                @foreach($seriesDevoluciones as $sd)
                                <option value="{{$sd->cod_serie_movi}}">{{$sd->cod_serie_movi}}/{{$sd->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6" style="display:none;" id="npedido">
                            <label><b>Serie pedido</b></label>
                            <input class="form-control" name="serie" required disabled id="cpedido" placeholder="456">
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="divfactura">
                            <label><b>Número factura o pedido</b></label>
                            <input type="number" min="1" class="form-control" placeholder="Número factura" name="numero" required id="factura" disabled>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-info btn-block" id="guardar" disabled>Cargar productos</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '65vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('datos_inicio')}}",
        dataSrc: "data",
    },
    "order": [[ 0,"asc" ]],
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
        { data: 'fecha_paraCarga', name: 'fecha_paraCarga'},
        { data: 'usuario', name: 'usuario'},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'},
        { data: 'peso', name: 'peso', render: $.fn.dataTable.render.number(',','.',3)},
        { data: null, render: function(data,type,row){
            return "<a href='{{url('EdTran')}}/"+data.num_movi+"'class= 'btn btn-dark btn-sm'>Editar</button>"}
        },
        { data: null, render: function(data,type,row){
            return "<a href='{{url('protrans')}}/"+data.num_movi+"'class= 'btn btn-warning btn-sm'>Programar</button>"}
        }
    ],
    "columnDefs": [
        { bSortable: false, targets: [10,11]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,6,7,8], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
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
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].observacion+'</option>';
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
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].observacion+'</option>';
        $('#bodegasa').html(html_select);
    });
}
</script>
<script>
$(document).ready(function(){
	$("#manual").click(function(){
        $("#factura").attr('disabled',true);
        $("#factura").removeAttr('required');
        $("#sucursal").attr('disabled',false);
        $("#sucursal").prop('required',true);
        $('#noserie').attr('disabled',true);
        $('#noserie').removeAttr('required');
        $('#noseriedev').attr('disabled',true);
        $('#noseriedev').removeAttr('required');
        $('#bodega').attr('disabled',false);
        $('#bodega').prop('required',true);
        $('#guardar').attr('disabled',false);
        $('#sasucursal').attr('disabled',false);
        $('#sasucursal').prop('required',true);
        $('#bodegasa').attr('disabled',false);
        $('#bodegasa').prop('required',true);
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divseriedev').style.display = "none";
        document.getElementById('divfactura').style.display = "none";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divsucursal').style.display = "block";
        document.getElementById('divbodega').style.display = "block";
        document.getElementById('saleSuc').style.display = "block";
        document.getElementById('saleBod').style.display = "block";
	});
	$("#docfactura").click(function(){
		$("#noserie").attr('disabled',false);
        $("#noserie").prop('required',true);
        $('#noseriedev').attr('disabled',true);
        $('#noseriedev').removeAttr('required');
        $("#factura").attr('disabled',false);
        $("#factura").prop('required',true);
        $("#sucursal").attr('disabled',true);
        $("#sucursal").removeAttr('required');
        $('#bodega').attr('disabled',true);
        $("#bodega").removeAttr('required');
        $('#factura').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        $('#cpedido').removeAttr('required');
        $('#sasucursal').attr('disabled',false);
        $('#sasucursal').prop('required',true);
        $('#bodegasa').attr('disabled',false);
        $('#bodegasa').prop('required',true);
        document.getElementById('divserie').style.display = "block";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('divsucursal').style.display = "none";
        document.getElementById('divbodega').style.display = "none";
        document.getElementById('divseriedev').style.display = "none";
        document.getElementById('saleSuc').style.display = "block";
        document.getElementById('saleBod').style.display = "block";
	});
    $("#pedido").click(function(){
		$("#noserie").attr('disabled',true);
        $("#noserie").removeAttr('required');
        $('#noseriedev').attr('disabled',true);
        $('#noseriedev').removeAttr('required');
        $("#factura").attr('disabled',false);
        $("#factura").prop('required',true);
        $("#sucursal").attr('disabled',true);
        $("#sucursal").removeAttr('required');
        $('#bodega').attr('disabled',true);
        $("#bodega").removeAttr('required');
        $('#factura').attr('disabled',false);
        $('#cpedido').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        $('#sasucursal').attr('disabled',false);
        $('#sasucursal').prop('required',true);
        $('#bodegasa').attr('disabled',false);
        $('#bodegasa').prop('required',true);
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divseriedev').style.display = "none";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('npedido').style.display = "block";
        document.getElementById('divsucursal').style.display = "none";
        document.getElementById('divbodega').style.display = "none";
        document.getElementById('saleSuc').style.display = "block";
        document.getElementById('saleBod').style.display = "block";
	});
    $("#devolucion").click(function(){
		$("#noserie").attr('disabled',true);
        $("#noserie").removeAttr('required');
        $('#noseriedev').attr('disabled',false);
        $('#noseriedev').prop('required',true);
        $("#factura").attr('disabled',false);
        $("#factura").prop('required',true);
        $("#sucursal").attr('disabled',false);
        $("#sucursal").prop('required',true);
        $('#bodega').attr('disabled',false);
        $("#bodega").prop('required',true);
        $('#factura').attr('disabled',false);
        $('#cpedido').attr('disabled',true);
        $("#cpedido").removeAttr('required');
        $('#guardar').attr('disabled',false);
        $('#sasucursal').attr('disabled',true);
        $('#sasucursal').removeAttr('required');
        $('#bodegasa').attr('disabled',true);
        $('#bodegasa').removeAttr('required');
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divseriedev').style.display = "block";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divsucursal').style.display = "block";
        document.getElementById('divbodega').style.display = "block";
        document.getElementById('saleSuc').style.display = "none";
        document.getElementById('saleBod').style.display = "none";
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
