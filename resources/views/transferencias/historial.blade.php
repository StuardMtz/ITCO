@extends('layouts.app')
<script>
    var url_global='{{url("/")}}';
</script>
@section('content')
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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless display nowrap" id="encabezado" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Estado</th>
                    <th>Modifico</th>
                    <th>Descripción</th>
                    <th>Comentario</th>
                    <th>Referencia</th>
                    <th>Propietario</th>
                    <th>Placa</th>
                    <th>Modificado</th>
                    <th>Fecha para carga</th>
                    <th>Fecha para entrega</th>
                    <th>Fecha en cola</th>
                    <th>Fecha en carga</th>
                    <th>Fecha cargado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($encabezado as $ec)
                <tr>
                    <td>{{$ec->num_movi}}</td>
                    <td>{{$ec->estado}}</td>
                    <td>{{$ec->usuario}}</td>
                    <td>{{$ec->descripcion}}</td>
                    <td>{{$ec->comentario}}</td>
                    <td>{{$ec->referencia}}</td>
                    <td>{{$ec->propietario}}</td>
                    <td>{{$ec->placa_vehiculo}}</td>
                    <td>{{$ec->created_at}}</td>
                    <td>{{$ec->fecha_paraCarga}}</td>
                    <td>{{$ec->fechaEntrega}}</td>
                    <td>{{$ec->fecha_enCola}}</td>
                    <td>{{$ec->fecha_enCarga}}</td>
                    <td>{{$ec->fecha_cargado}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <hr>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless display nowrap" id="productos" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Modifico</th>
                    <th>Acción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $pr)
                <tr>
                    <td>{{number_format($pr->num_movi)}}</td>
                    <td>{{$pr->nombre_corto}}</td>
                    <td>{{$pr->nombre_fiscal}}</td>
                    <td>{{number_format($pr->cantidadSolicitada,0)}}</td>
                    <td>{{$pr->name}}</td>
                    <td>{{$pr->accion}}</td>
                    <td>{{date('d/m/Y H:i',strtotime($pr->created_at))}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
var table = $('#encabezado').DataTable({
    processing: false,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '400px',
    scrollCollapse: true,
    "order": [[ 8,"asc" ]],
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
    "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [1,2,3,4,5,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [8,9,10,11,12,13], render:function(data){
            moment.locale('es');
            return moment(data).format('lll');
        }}
    ],rowCallback:function(row,data){
        if(data[8] == '') {
            $($(row).find("td")[8]).html('');
        }
        else if(data[8] != null){
        }
        if(data[9] == '') {
            $($(row).find("td")[9]).html('');
        }
        else if(data[9] != null){
        }
        if(data[10] =='') {
            $($(row).find("td")[10]).html('');
        }
        else if(data[10] != null){
        }
        if(data[11] == '') {
            $($(row).find("td")[11]).html('');
        }
        else if(data[11] != null){
        }
        if(data[12] == '') {
            $($(row).find("td")[12]).html('');
        }
        else if(data[12] != null){
        }
        if(data[13] == '') {
            $($(row).find("td")[13]).html('');
        }
        else if(data[13] != null){
        }
    }
});
</script>
<script>
var table = $('#productos').DataTable({
    processing: false,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '300px',
    scrollCollapse: true,
    "order": [[ 6,"desc" ]],
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
    "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [1,2,3,4,5], searchable: true },
        { targets: '_all', searchable: false }
    ]
});
</script>
@endsection
