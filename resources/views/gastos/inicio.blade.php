@extends('layouts.app')
<script src="{{ asset('js/proveedores.js') }}" defer></script>
<script src="{{ asset('js/tipoGasto.js') }}" defer></script>
<script src="{{ asset('js/codcui.js') }}" defer></script>
@section('content')
@yield('content', View::make('layouts.gastos'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Gastos en espera de autorización</p>
    </blockquote>
    <button type="button" class="nav-link" id="modalb" data-toggle="modal" data-target="#nuevaTransferencia">
        Solicitar autorización
    </button>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="gastos" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Serie documento</th>
                    <th>No. documento</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Retención</th>
                    <th>No retención</th>
                    <th>Solicita</th>
                    <th>Ver</th>
                    <th>Operar</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="nuevaTransferencia" aria-labelledby="nuevaTransferenciaLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva solicitud de gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nit-tab" data-toggle="tab" data-target="#nit" type="button" role="tab"
                        aria-controls="nit" aria-selected="true">Gastos con proveedores</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="CUI-tab" data-toggle="tab" data-target="#CUI" type="button" role="tab"
                        aria-controls="CUI" aria-selected="false">Gastos con CUI</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="persona-tab" data-toggle="tab" data-target="#persona" type="button" role="tab"
                        aria-controls="persona" aria-selected="false">Agregar persona</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="nit" role="tabpanel" aria-labelledby="nit-tab">
                        <form class="needs-validation" novalidate method="post" action="{{url('gua_gast')}}">
                        {{csrf_field()}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Proveedor</b></label>
                                    <select class="form-control" id="proveedor" name="cod_proveedor" required style="width: 100%" aria-label="proveedor"></select>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Fecha del documento</b></label>
                                    <input type="date" class="form-control" name="fecha_documento" placeholder="Fecha del documento" aria-label="fecha_documento" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Serie documento</b></label>
                                    <input type="text" class="form-control" name="serie_documento" placeholder="Serie de documento" aria-label="serie_documento" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>No. documento</b></label>
                                    <input type="text" class="form-control" name="no_documento" placeholder="No de documento" aria-label="no_documento" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Descripción</b></label>
                                    <input type="text" class="form-control" name="descripcion" placeholder="Ingrese una descripción del gasto" aria-label="descripcion" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Retenciones (IVA, otras)</b></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" aria-label="Checkbox for following text input" onChange="comprobar(this);">
                                            </div>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" name="retencion" id="iva" style="display:none" placeholder="12.01"
                                        aria-label="retencion" disabled required>
                                        <div class="valid-tooltip">
                                            Muy bien.
                                        </div>
                                        <div class="invalid-tooltip">
                                            No debes dejar este campo en blanco.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-6">
                                    <label><b>No retención</b></label>
                                    <input type="text" class="form-control" name="no_retencion" placeholder="Número retención" aria-label="no_retencion" required
                                    style="display:none" id="retencion" disabled>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Monto</b></label>
                                    <input type="number" step="0.01" class="form-control" name="monto" placeholder="300.00" aria-label="monto" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Tipo de gasto</b></label>
                                    <select class="form-control" id="tipo_gasto" name="tipo_gasto" required style="width: 100%" aria-label="tipo_de_gasto">
                                        <option value="">Seleccione el tipo de gasto</option>
                                        @foreach($tipo_gastos as $tg)
                                        <option value="{{$tg->id}}" id="{{$tg->nombre}}">{{$tg->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Seleccione el vehículo</b></label>
                                    <select class="form-control" name="vehiculo" id="vehiculo" aria-label="vehiculo" required disabled>
                                        <option value="">Seleccione el vehículo</option>
                                        @foreach($vehiculos as $vh)
                                        <option value="{{$vh->id}}">{{$vh->marca}} {{$vh->placa}}</option>
                                        @endforeach
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
                                <div class="form-group col-md-12">
                                    <label><b>Monto factura</b></label>
                                    <input type="number" class="form-control" name="monto_factura" placeholder="Monto factura" aria-label="monto_factura" required
                                    style="display:none" id="MontoFactura" disabled>
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
                                    <button type="submit" class="nav-link nav-link-info nav-link-block" id="guardar">Solicitar autorización</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="CUI" role="tabpanel" aria-labelledby="CUI-tab">
                        <form class="needs-validation" novalidate method="post" action="{{url('gua_gast')}}">
                        {{csrf_field()}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>CUI</b></label>
                                    <select class="form-control" id="noCui" name="num_cui" required style="width: 100%" aria-label="noCui"></select>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Fecha del documento</b></label>
                                    <input type="date" class="form-control" name="fecha_documento" placeholder="Fecha del documento" aria-label="fecha_documento" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Descripción</b></label>
                                    <input type="text" class="form-control" name="descripcion" placeholder="Ingrese una descripción del gasto" aria-label="descripcion" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Retenciones (IVA, otras)</b></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" aria-label="Checkbox for following text input" onChange="comprobarCUI(this);">
                                            </div>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" name="retencion" id="ivaCUI" style="display:none" placeholder="12.01"
                                        aria-label="retencion" disabled required>
                                        <div class="valid-tooltip">
                                            Muy bien.
                                        </div>
                                        <div class="invalid-tooltip">
                                            No debes dejar este campo en blanco.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-6">
                                    <label><b>No retención</b></label>
                                    <input type="text" class="form-control" name="no_retencion" placeholder="Número retención" aria-label="no_retencion" required
                                    style="display:none" id="retencionCUI" disabled>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Monto</b></label>
                                    <input type="number" step="0.01" class="form-control" name="monto" placeholder="300.00" aria-label="monto" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Tipo de gasto</b></label>
                                    <select class="form-control" name="tipo_gasto" id="tipo_gastoCUI" style="width: 100%" onchange="handleSelectCUI()" aria-label="tipo_de_gastoCUI" required>
                                        <option value="">Seleccione el tipo de gasto</option>
                                        @foreach($tipo_gastos as $tg)
                                        <option value="{{$tg->id}}" id="{{$tg->nombre}}">{{$tg->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Seleccione el vehículo</b></label>
                                    <select class="form-control" name="vehiculo" id="vehiculoCUI" aria-label="vehiculo" required disabled>
                                        <option value="">Seleccione el vehículo</option>
                                        @foreach($vehiculos as $vh)
                                        <option value="{{$vh->id}}">{{$vh->marca}} {{$vh->placa}}</option>
                                        @endforeach
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
                                <div class="form-group col-md-12">
                                    <label><b>Monto factura</b></label>
                                    <input type="number" class="form-control" name="monto_factura" placeholder="Monto factura" aria-label="monto_facturaCUI" required
                                    style="display:none" id="MontoFacturaCUI" disabled>
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
                                    <button type="submit" class="nav-link nav-link-info nav-link-block" id="guardar">Solicitar autorización</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="persona" role="tabpanel" aria-labelledby="persona-tab">
                    <form class="needs-validation" novalidate method="post" action="{{url('guar_perso')}}">
                        {{csrf_field()}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>CUI</b></label>
                                    <input type="number" step="1" class="form-control" name="num_cui" placeholder="No de documento" aria-label="num_cui" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Nombre</b></label>
                                    <input type="text" class="form-control" name="nombre" placeholder="Ingrese el nombre completo" aria-label="nombre" required>
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
                                    <button type="submit" class="nav-link nav-link-info nav-link-block" id="guardar">Guardar registro</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="nav-link nav-link-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
var table = $('#gastos').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('datos_gastos_inicio')}}",
        dataSrc: 'data',
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
        { data: 'id', name: 'id'},
        { data: 'proveedor', name: 'proveedor'},
        { data: 'serie_documento', name: 'serie_documento'},
        { data: 'no_documento', name: 'no_documento'},
        { data: 'descripcion', name: 'descripcion'},
        { data: 'fecha_registrado', name: 'fecha_registrado'},
        { data: 'monto', name:'monto', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'iva', name: 'iva', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'no_retencion', name: 'no_retencion'},
        { data: 'name', name: 'name'},
        { data: null, render: function(data,type,row){
            return "<a id='boton' href='{{url('vergasto/')}}/"+data.id+"' class= 'btn btn-sm btn-warning'>Ver</button>"}
        },
        { data: null, render: function(data,type,row){
            return "<a id='ver' href='{{url('autogas/')}}/"+data.id+"' class= 'btn btn-sm btn-dark'>Operar</button>"}
        }
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [5], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ]
});
</script>
<script>
function comprobar(obj)
{
    if (obj.checked)
    {
        $("#iva").attr('disabled',false);
        $("#retencion").attr('disabled',false);
        document.getElementById('iva').style.display = "";
        document.getElementById('retencion').style.display = "";
    }
    else
    {
        document.getElementById('iva').style.display = "none";
        document.getElementById('retencion').style.display = "none";
        $("#iva").attr('disabled',true);
        $("#retencion").attr('disabled',true);
    }
};
function comprobarCUI(obj)
{
    if (obj.checked)
    {
        $("#ivaCUI").attr('disabled',false);
        $("#retencionCUI").attr('disabled',false);
        document.getElementById('ivaCUI').style.display = "";
        document.getElementById('retencionCUI').style.display = "";
    }
    else
    {
        document.getElementById('ivaCUI').style.display = "none";
        document.getElementById('retencionCUI').style.display = "none";
        $("#ivaCUI").attr('disabled',true);
        $("#retencionCUI").attr('disabled',true);
    }
};
$(document).ready(function(){
	document.getElementById('tipo_gasto').onchange = function(handleSelect){
        if(this.value == '3'){
            $("#vehiculo").attr('disabled',false);
            $("#MontoFactura").attr('disabled',true);
            document.getElementById('MontoFactura').style.display = "none";
        }
        else if(this.value == '9'){
            $("#vehiculo").attr('disabled',true);
            $("#MontoFactura").attr('disabled',false);
            document.getElementById('MontoFactura').style.display = "";
        }
        else {
            $("#vehiculo").attr('disabled',true);
            $("#MontoFactura").attr('disabled',true);
            document.getElementById('MontoFactura').style.display = "none";
        }
    }
});
$(document).ready(function(){
	document.getElementById('tipo_gastoCUI').onchange = function(handleSelectCUI){
        if(this.value == '3'){
            $("#vehiculoCUI").attr('disabled',false);
            $("#MontoFacturaCUI").attr('disabled',true);
            document.getElementById('MontoFacturaCUI').style.display = "none";
        }
        else if(this.value == '9'){
            $("#vehiculoCUI").attr('disabled',true);
            $("#MontoFacturaCUI").attr('disabled',false);
            document.getElementById('MontoFacturaCUI').style.display = "";
        }
        else {
            $("#vehiculoCUI").attr('disabled',true);
            $("#MontoFacturaCUI").attr('disabled',true);
            document.getElementById('MontoFacturaCUI').style.display = "none";
        }
    }
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
