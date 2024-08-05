@extends('layouts.app2')
@section('content')
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('rep_suc_lis')}}">Atrás</a>
                <a class="nav-link active" href="#">Existencia bodega terminado</a>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    <li class="nav-item">
                        @if (Route::has('register'))
                            <!--<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> -->
                        @endif
                    </li>
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
            </ul>
        </div>
    </nav>

    <blockquote class="blockquote text-center">
        <p class="mb-0">Máximo y Mínimo general</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="ordenes">
            <thead>
                <tr>
                    <th>Nombre corto</th>
                    <th>Nombre fiscal</th>
                    <th>Existencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($existencia as $ex)
                <tr>
                    <td>{{$ex->nombre_corto}}</td>
                    <td>{{$ex->nombre_fiscal}}</td>
                    <td>{{number_format($ex->existencia1,0)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    var table = $('#ordenes').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 100,
        searching: true,
        responsive: true,

            lengthMenu: [
            [ 100, 200, 300, -1 ],
            [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
            ],
            buttons: [
                {   extend: 'pageLength',
                    text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
                 },
                {   extend: 'colvis',
                    text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
                },
                {   extend: 'excelHtml5',
                    text: '<i class="far fa-file-excel"></i>  Exportar a excel',
                    autoFilter: true,
                    title: 'Reporte Rendimiento'
                }
            ],
        "order": [[ 2,"asc" ]],
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
            { bSortable: false, targets: [0,1,2]},
            { targets: 0, searchable: true },
            { targets: [0,1,2], searchable: true },
            { targets: '_all', searchable: false }
        ]
    });
    $(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#ordenes thead tr').clone(true).appendTo( '#ordenes thead' );
    $('#ordenes thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" />' );
        $( 'input', this ).on( 'keyup change', function () {
            var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( val ? '^'+val+'$' : '', true, false )
                    .draw();
                }
            });
        });
    });
</script>
@endsection
