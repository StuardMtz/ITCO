@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}"><b><i class="fas fa-home"></i> Inicio</b></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('sucs')}}"><i class="fas fa-calendar-alt"></i> Ver Inventarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('sucur')}}"><i class="fas fa-clipboard"></i> Existencias</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('lis_us')}}"><i class="fas fa-clipboard"></i> Listado Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cre_se')}}"><i class="fas fa-calendar-alt"></i> Crear Semana</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('agu')}}"><i class="fas fa-user"></i> Agregar Usuario</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active bg-info" href="#"><i class="fas fa-user-edit"></i> Editar Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('v_pro') }}"><i class="fas fa-skull-crossbones"></i> UTF-8</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('vg')}}"><i class="fas fa-spinner"></i> Cargar Productos</a>
        </li>
    </ul>
    <br>
    <h5>Editar Usuarios</h5>
    <div class="table-responsive">
        <table class="table table-sm" id="usuarios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Opciones</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $('#usuarios').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        responsive: false,
        ajax:{
            url: "{{route('dat_us')}}",
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
                { data: 'name', name: 'name'},
                { data: 'email', name: 'email'},
                { data: null, render: function(data,type,row){
                    return "<a href='{{url('edius')}}/"+data.id+"' class='btn btn-sm btn-dark'>Editar</button>"}
                }
                ],
        "columnDefs": [
        { bSortable: false, targets: [1]},
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
    ],
    });
</script>
@endsection
