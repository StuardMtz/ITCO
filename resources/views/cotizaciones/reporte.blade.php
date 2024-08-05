@extends('layouts.app')
<script src="{{ asset('js/clientes.js') }}" defer></script>
@section('content')
@yield('content', View::make('layouts.cotizacion'))
    <div class="container-fluid">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Reporte Cotizaciones</p>
        </blockquote>
        <div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <strong>¡{{ $message }}!</strong>
                </div>
            @endif
            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <strong>¡{{ $message }}!</strong>
                </div>
            @endif
        </div>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="cotizaciones" style="width:100%">
                <thead>
                    <tr>
                        <th>Unidad</th>
                        <th>Nombre</th>
                        <th>Primera</th>
                        <th>Ultima</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Realizadas</th>
                        <th>SKU Total</th>
                        <th>Monto Total</th>
                    </tr>
                </thead>
            </table>
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
            scroller: true,
            stateSave: true,
            "stateDuration": 300,
            ajax: {
                url: "{{ route('datos_reporte_cotizaciones') }}",
                dataSrc: 'data',
            },
            "order": [
                [0, "desc"]
            ],
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
            columns: [{
                    data: 'Cod_unidad',
                    name: 'Cod_unidad'
                },
                {
                    data: 'Nombre',
                    name: 'Nombre'
                },
                {
                    data: 'Primera_realizada',
                    name: 'Primera_realizada'
                },
                {
                    data: 'Ultima_realizada',
                    name: 'Ultima_realizada'
                },
                {
                    data: 'months',
                    name: 'months'
                },
                {
                    data: 'years',
                    name: 'years'
                },
                {
                    data: 'Cotizaciones_realizadas',
                    name: 'Cotizaciones_realizadas'
                },
                {
                    data: 'SKU_total',
                    name: 'SKU_total'
                },
                {
                    data: 'Monto_total',
                    name: 'Monto_total',
                    render: $.fn.dataTable.render.number(',','.',2 )
                }
            ],
            "columnDefs": [{
                    targets: 0,
                    searchable: true
                },
                {
                    targets: [0, 1],
                    searchable: true
                },
                {
                    targets: '_all',
                    searchable: false
                }
            ],
            rowCallback:function(row,data){
                $($(row).find("td")[6]).css("text-align","right");
                $($(row).find("td")[7]).css("text-align","right");
                $($(row).find("td")[8]).css("text-align","right");
            },
        });
        // $('#cotizaciones').on('click', 'tbody tr', function() {
        //     var tr = $(this).closest('tr');
        //     var row = table.row(tr);
        //     window.location.href = (url_global + '/edicot/' + row.data().num_movi);
        //     redirectWindow.location;
        // });
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
