@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}"><i class="fas fa-home"></i> Listado de sucursales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="active"><i class="fas fa-calendar-alt"></i> Inventarios por fecha</a>
        </li><!--
        <li class="nav-item">
            <a class="nav-link" href="{{route('sucur')}}"><i class="fas fa-clipboard"></i> Entregas por sucursal</a>
        </li> -->
    </ul>
    <div class="col">
        {!! Form::open(['method'=>'get','route'=>['por_fe']]) !!}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Fecha inicial</span>
            </div>
            <input type="date" class="form-control" name="fecha_inicial">
            <div class="input-group-prepend">
                <span class="input-group-text">Fecha final</span>
            </div>
            <input type="date" class="form-control" name="fecha_final">
            <div class="input-group-prepend">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <br>
    <h5>Listado de sucursales</h5>
    <div class="table-responsive">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Sucursales</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    var table = $('#sucursales').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        responsive: false,
        ajax:{
            url: "{{route('list_sucs')}}",
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
                { data: 'name', name: 'name'}
                ],
        "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
    ],
    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      window.location.href =(url_global+'/invps/'+row.data().sucursal+'/'+row.data().bodega);
      redirectWindow.location;
    });
</script>
@endsection
