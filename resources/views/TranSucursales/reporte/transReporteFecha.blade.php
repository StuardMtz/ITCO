@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('rep_tra_Sucursales')}}">Atrás</a>
                <a class="nav-link">Listado de transferencias</a>
                <form method="get" action="{{url('rtranPF')}}">
                    <div class="form-row">
                        <div class="form-group col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Desde</span>
                                </div>
                                <input type="date" class="form-control" name="inicio" value="{{$inicio}}" required>
                            </div>
                        </div>
                        <div class="form-group col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Hasta</span>
                                </div>
                                <input type="date" class="form-control" name="fin" value="{{$fin}}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-warning" type="submit">Buscar</button>
                                </div>
                            </div>
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
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias realizadas</p>
    </blockquote>
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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Creada el</th>
                    <th>Finalizada</th>
                    <th>Creado por</th>
                    <th>Descripción</th>
                    <th>No. factura</th>
                    <th>Serie factura</th>
                    <th>Estado</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '60vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    ajax:{
        url: "{{route('drtran_pfecha',['inicio'=>$inicio,'fin'=>$fin])}}",
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
            autoFilter: true
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
        { data: 'num_movi', name: 'num_movi'},
        { data: 'nombre', name:'nombre'},
        { data: 'bodega', name: 'bodega'},
        { data: 'created_at', name: 'created_at'},
        { data: 'fechaSucursal', name: 'fechaSucursal'},
        { data: 'usuario', name: 'usuario'},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'numeroFactura', name: 'numeroFactura'},
        { data: 'serieFactura', name: 'serieFactura'},
        { data: 'estado', name: 'estado'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,6], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTranS/'+row.data().id);
    redirectWindow.location;
});
</script>
<script>
    $(function(){
        $('#sucursal').on('change', onSelectSucursalChange);
    });
    function onSelectSucursalChange(){
        var cod_unidad = $(this).val();
        if(! cod_unidad){
            $('#bodega').html('<option value ="">Seleccione una opcion</option>');
            return;
        };

    $.get('select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
        $('#bodega').html(html_select);
    });
}
</script>
<script>
    $(function(){
        $('#sasucursal').on('change', onSelectSucursaleChange);
    });
    function onSelectSucursaleChange(){
        var cod_unidad = $(this).val();
        if(! cod_unidad){
            $('#bodegasa').html('<option value ="">Seleccione una opcion</option>');
            return;
        };

    $.get('select/'+cod_unidad,function(data){
        var html_select = '<option value ="">Seleccione una opcion</option>';
        for (var i=0; i<data.length; ++i)
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
        $('#bodegasa').html(html_select);
    });
}
</script>
<script>
$(document).ready(function(){
	$("#manual").click(function(){
        $("#factura").attr('disabled',true);
        $("#sucursal").attr('disabled',false);
        $('#noserie').attr('disabled',true);
        $('#bodega').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divfactura').style.display = "none";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divsucursal').style.display = "block";
        document.getElementById('divbodega').style.display = "block";
	});
	$("#docfactura").click(function(){
		$("#noserie").attr('disabled',false);
        $("#factura").attr('disabled',false);
        $("#sucursal").attr('disabled',true);
        $('#bodega').attr('disabled',true);
        $('#factura').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        document.getElementById('divserie').style.display = "block";
        document.getElementById('npedido').style.display = "none";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('divsucursal').style.display = "none";
        document.getElementById('divbodega').style.display = "none";
	});
    $("#pedido").click(function(){
		$("#noserie").attr('disabled',true);
        $("#factura").attr('disabled',false);
        $("#sucursal").attr('disabled',false);
        $('#bodega').attr('disabled',false);
        $('#factura').attr('disabled',false);
        $('#cpedido').attr('disabled',false);
        $('#guardar').attr('disabled',false);
        document.getElementById('divserie').style.display = "none";
        document.getElementById('divfactura').style.display = "block";
        document.getElementById('npedido').style.display = "block";
        document.getElementById('divsucursal').style.display = "block";
        document.getElementById('divbodega').style.display = "block";
	});
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
