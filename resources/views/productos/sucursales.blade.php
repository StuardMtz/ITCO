@extends('layouts.app2')
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
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{route('home')}}"><i class="fas fa-home"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="active"><i class="fas fa-clipboard"></i> Mín y Máx</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('mimageneral')}}"><b><i class="fas fa-filter"></i> Minimax general</b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route ('Vtran')}}"><i class="fas fa-truck"></i> Transferencias</a>
            </li>
            @if(Auth::id() == 44 || Auth::id()== 6)
            <li class="nav-item">
                <a class="nav-link" href="{{route('rtrans')}}"><i class="fas fa-file-signature"></i> Reportes transferencias</a>
            </li>
            @else
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{route('grafClasSucR')}}"><i class="fas fa-clipboard"></i> Graficas por sucursal</a>
            </li>
            <li class="nav-item">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Graficas por clases
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item nav-link" href="{{route('grafClasPSuc',['clase'=>'1'])}}">Graficar productos clase A</a>
                        <a class="dropdown-item nav-link" href="{{route('grafClasPSuc',['clase'=>'2'])}}">Graficar productos clase B</a>
                        <a class="dropdown-item nav-link" href="{{route('grafClasPSuc',['clase'=>'3'])}}">Graficar productos clase C</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <br>
    <h5>Sucursales</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Sucursal</th>
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
      window.location.href =(url_global+'/exist/'+row.data().sucursal+'/'+row.data().bodega+'/'+1);
      redirectWindow.location;
    });
</script>
@endsection
