@extends('layouts.app')
@section('content')
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script>
  var url_global='{{url("/")}}';
</script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <a class="navbar-brand" href="{{ url('/home') }}">Menu principal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link"  href="{{route('reporte_entregas_municipios',$id)}}">Atrás</a>
                <a class="nav-link" id="active" href="#"><i class="fas fa-clipboard"></i> Entregas en el municipio de {{$municipio->nombre}}</a>
                <form method="get" action="{{url('repmunfec',$id)}}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="span">Desde</span>
                        </div>
                        <input type="date"  class="form-control" placeholder="Inicio" name="desde" value="{{$inicio}}">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="span">Hasta</span>
                        </div>
                        <input type="date" step="0.01" class="form-control" placeholder="Fin" name="hasta" value="{{$fin}}">
                        <div class="input-group-append">
                            <button class="btn btn-light" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
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
    <h5>Reporte de entregas de {{$municipio->nombre}}</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="entregas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Entrego</th>
                    <th>Aldea</th>
                    <th>Cliente</th>
                    <th>Fecha asignado</th>
                    <th>Fecha carga</th>
                    <th>Fecha ruta</th>
                    <th>Fecha destino</th>
                    <th>Fecha entregado</th>
                    <th>Tiempo entrega</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
//var filterColumns = [1,2,3];
var table = $('#entregas').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    ajax:{
        url: "{{url('darepmunfec',['id'=>$id,'inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: "data",
    },
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
            title: '{{$municipio->nombre}}'
        }
    ],
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
        { data: 'name', name: 'name'},
        { data: 'aldea', name:'aldea'},
        { data: 'nombre', name: 'nombre'},
        { data: 'fecha_asignacion', name: 'fecha_asignacion'},
        { data: 'fecha_carga', name: 'fecha_carga'},
        { data: 'fecha_ruta', name: 'fecha_ruta'},
        { data: 'fecha_destino', name: 'fecha_destino'},
        { data: 'fecha_entregado', name: 'fecha_entregado'},
        { data: 'tiempo', name: 'tiempo'}
    ],
    "columnDefs": [
        //{ bSortable: false, targets: [1,2]},
        { targets: 1, searchable: true },
        { targets: [0,1,2,3], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4,5,6,7,8], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],/*
    initComplete: function () {
      this.api().columns(filterColumns).every( function () {
        $(select).click(function(e){
          e.stopPropagation();
        });
        var column = this;
        var select = $('<select class="form-control"><option value=""></option></select>')
        .appendTo( $(column.header()) ).on( 'change', function () {
          var val = $.fn.dataTable.util.escapeRegex(
            $(this).val()
          );
          column
          .search( val ? '^'+val+'$' : '', true, false )
          .draw();
        });
        column.data().unique().sort().each( function ( d, j ) {
          select.append( '<option value="'+d+'">'+d+'</option>' )
        });
      });
    },*/
    rowCallback:function(row,data){
        if(data['fecha_asignado'] == null){
            $($(row).find("td")[4]).html('');
        }
        if(data['fecha_carga'] == null){
            $($(row).find("td")[5]).html('');
        }
        if(data['fecha_ruta'] == null){
            $($(row).find("td")[6]).html('');
        }
        if(data['fecha_destino'] == null){
            $($(row).find("td")[7]).html('');
        }
        if(data['fecha_entregado'] == null){
            $($(row).find("td")[8]).html('');
        }
  }
});
$('#entregas').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/vcitud/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
