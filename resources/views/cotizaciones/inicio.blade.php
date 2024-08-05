@extends('layouts.app')
<script src="{{ asset('js/clientes.js') }}" defer></script>
@section('content')
@yield('content', View::make('layouts.cotizacion'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Cotizaciones creadas</p>
    </blockquote>
    <div class="container">
        @if($message= Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>¡{{ $message}}!</strong>
        </div>
        @endif
        @if($message= Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>¡{{ $message}}!</strong>
        </div>
        @endif
    </div>
    <button type="button" class="btn btn-info btn-sm" style="margin-bottom: 10px" id="modalb" data-toggle="modal" data-target="#nuevaTransferencia">
        Crear cotización
    </button>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="cotizaciones" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nit</th>
                    <th>Cliente</th>
                    <th>Referencia</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>SKU</th>
                    <th>Usuario</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="nuevaTransferencia" aria-labelledby="nuevaTransferenciaLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva cotización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('crcot')}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label><b>Seleccione sucursal</b></label>
                            <select id="sucursales" name="sucursal" class="form-control"required>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($sucursales as $su)
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
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label><b>Código cliente</b></label>
                            <select class="form-control" id="clientes" name="cod_cliente" required style="width: 100%"></select>
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
                            <button type="submit" class="btn btn-info btn-block" id="guardar">Generar cotizacion</button>
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
var table = $('#cotizaciones').DataTable({
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
        url: "{{route('datos_inicio_cotizaciones')}}",
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
        { data: 'num_movi', name:  'num_movi'},
        { data: 'nit', name: 'nit'},
        { data: 'Nombre_cliente', name: 'Nombre_cliente'},
        { data: 'referencia', name: 'referencia'},
        { data: 'fecha', name: 'fecha'},
        { data: 'total', name: 'total', render: $.fn.dataTable.render.number(',','.',2 )},
        { data: 'sku', name: 'sku'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ]
});
$('#cotizaciones').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/edicot/'+row.data().num_movi);
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
