@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.solicitudes'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Solicitudes entregadas</p>
    </blockquote>
    <form method="get" action="{{url('solentrefe')}}">
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="span">Desde</span>
                    </div>
                    <input type="date"  class="form-control" placeholder="Inicio" name="inicio">
                </div>
            </div>
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="span">Hasta</span>
                    </div>
                    <input type="date" step="0.01" class="form-control" placeholder="Fin" name="fin">
                    <div class="input-group-append">
                        <button class="btn btn-warning" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="table table-responsive-sm">
        <table class="table table-sm table-borderless display nowrap" id="entregas" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Ubicación</th>
                    <th>Fecha de Solicitud</th>
                    <th>Fecha de Entrega</th>
                    <th>Tiempo de entrega</th>
                    <th>Camión</th>
                    <th>Estado</th>
                    <th>Comentario</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#entregas').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    scrollX: true,
    scrollY: '55vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: 'datsolentre',
        dataSrc: "data",
    },
    "order": [[ 5,"desc" ]],
    dom: 'Bfrtip',
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
        { data: 'comprobante', name: 'comprobante'},
        { data: 'cliente',name:'cliente'},
        { data: 'aldea', name: 'aldea'},
        { data: 'created_at',name:'inventario_web_entregas.created_at'},
        { data: 'fecha_entregado',name:'fecha_entregado'},
        { data: 'tiempo', name: 'tiempo'},
        { data: 'placa',name:'placa'},
        { data: 'estado', name: 'estado'},
        { data: 'comentarios', name: 'comentarios'}
    ],
    "columnDefs": [
        { bSortable: false, targets: [7]},
        { targets: 1, searchable: true },
        { targets: [0,1,2,3,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4,5], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ]
});
$('#entregas').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/vcitud/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
