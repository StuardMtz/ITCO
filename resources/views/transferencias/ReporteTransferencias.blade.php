@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="javascript:history.go(-1)">Atrás</a>
                <a class="nav-link" href="{{route('inicio_transferencias')}}">Transferencias WEB</a>
                <a class="nav-link" href="{{route('transc_inicio')}}">Transferencias Compras</a>
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
                <form method="get" action="{{url('RepTranF')}}" class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">De</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="inicio" required>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Hasta</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="fin" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-warning btn-sm" type="button">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </nav>
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Reporte general transferencias</p>
    </blockquote>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="3"><input class="form-control" type="text" id="column2_search" placeholder="Bodega salida"></th>
                    <th colspan="2"><input class="form-control" type="text" id="column3_search" placeholder="Fecha entrega"></th>
                    <th colspan="2"><input class="form-control" type="text" id="column6_search" placeholder="Transporte"></th>
                    <th colspan="3"><input class="form-control" type="text" id="column8_search" placeholder="Sale bodega"></th>
                    <th colspan="2"><input class="form-control" type="text" id="column9_search" placeholder="Estado"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha entrega</th>
                    <th>Descripción</th>
                    <th>Creado por</th>
                    <th>Transporte</th>
                    <th>Estado</th>
                    <th>Verificacion</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
                    <th>Eficacia</th>
                </tr>
            </thead>
        </table>
    </div>
<script>
var table = $('#transferencias').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 100,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '62vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
    ajax:{
        url: "{{route('datos_rep_trans')}}",
        dataSrc: "data",
    },
    lengthMenu: [
        [ 100, 200, 300, -1 ],
        [ '100 filas', '200 filas', '300 filas', 'Mostrar Todo' ]
    ],
	buttons: [
        {   extend: 'pageLength',
            className: 'nav-link',
            text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
        },
        {   extend: 'colvis',
            className: 'nav-link',
            text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
        },
        {   extend: 'excelHtml5',
            className: 'nav-link',
            text: '<i class="far fa-file-excel"></i>  Exportar a excel',
            autoFilter: true,
            title: '{{Auth::user()->name}}'
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
        { data: 'fecha', name: 'fecha', render: function(data,type,row){
            if(type === "sort" || type === "type"){
                return data;
            }
            return moment(data).format('DD/MM/YYYY');
        }},
        { data: 'DESCRIPCION', name: 'DESCRIPCION'},
        { data: 'usuario', name: 'usuario'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo'},
        { data: 'estado', name: 'estado'},
        { data: 'id', name: 'id'},
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'},
        { data: 'porcentaje', name: 'porcentaje',
            render: function(data, type, row) {
                if (data > 0){
                    if (type === 'display' || type === 'filter') {
                    return parseFloat(data).toFixed(2) + '%';
                }
                }
                return data;
            }
        },
    ],
    "columnDefs": [
        { bSortable: false, targets: [10]},
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6,7,8,9], searchable: true },
        { targets: '_all', searchable: false },


    ],
    rowCallback:function(row,data){
        if(data['id'] == 13){
            $($(row).find("td")[0]).css("background-color","#E6B0AA ");
            $($(row).find("td")[1]).css("background-color","#E6B0AA ");
            $($(row).find("td")[2]).css("background-color","#E6B0AA ");
            $($(row).find("td")[3]).css("background-color","#E6B0AA ");
            $($(row).find("td")[4]).css("background-color","#E6B0AA ");
            $($(row).find("td")[5]).css("background-color","#E6B0AA ");
            $($(row).find("td")[6]).css("background-color","#E6B0AA ");
            $($(row).find("td")[7]).css("background-color","#E6B0AA ");
            $($(row).find("td")[8]).css("background-color","#E6B0AA ");
            $($(row).find("td")[9]).css("background-color","#E6B0AA ");
            $($(row).find("td")[10]).css("background-color","#E6B0AA ");
            $($(row).find("td")[11]).css("background-color","#E6B0AA ");
        }
        else if(data['id'] == 14){
            $($(row).find("td")[0]).css("background-color","#F5B7B1");
            $($(row).find("td")[1]).css("background-color","#F5B7B1");
            $($(row).find("td")[2]).css("background-color","#F5B7B1");
            $($(row).find("td")[3]).css("background-color","#F5B7B1");
            $($(row).find("td")[4]).css("background-color","#F5B7B1");
            $($(row).find("td")[5]).css("background-color","#F5B7B1");
            $($(row).find("td")[6]).css("background-color","#F5B7B1");
            $($(row).find("td")[7]).css("background-color","#F5B7B1");
            $($(row).find("td")[8]).css("background-color","#F5B7B1");
            $($(row).find("td")[9]).css("background-color","#F5B7B1");
            $($(row).find("td")[10]).css("background-color","#F5B7B1");
            $($(row).find("td")[11]).css("background-color","#F5B7B1");
        }
        else if(data['id'] == 15){
            $($(row).find("td")[0]).css("background-color","#EDBB99");
            $($(row).find("td")[1]).css("background-color","#EDBB99");
            $($(row).find("td")[2]).css("background-color","#EDBB99");
            $($(row).find("td")[3]).css("background-color","#EDBB99");
            $($(row).find("td")[4]).css("background-color","#EDBB99");
            $($(row).find("td")[5]).css("background-color","#EDBB99");
            $($(row).find("td")[6]).css("background-color","#EDBB99");
            $($(row).find("td")[7]).css("background-color","#EDBB99");
            $($(row).find("td")[8]).css("background-color","#EDBB99");
            $($(row).find("td")[9]).css("background-color","#EDBB99");
            $($(row).find("td")[10]).css("background-color","#EDBB99");
            $($(row).find("td")[11]).css("background-color","#EDBB99");
        }
        else if(data['id'] == 16){
            $($(row).find("td")[0]).css("background-color","#F5CBA7");
            $($(row).find("td")[1]).css("background-color","#F5CBA7");
            $($(row).find("td")[2]).css("background-color","#F5CBA7");
            $($(row).find("td")[3]).css("background-color","#F5CBA7");
            $($(row).find("td")[4]).css("background-color","#F5CBA7");
            $($(row).find("td")[5]).css("background-color","#F5CBA7");
            $($(row).find("td")[6]).css("background-color","#F5CBA7");
            $($(row).find("td")[7]).css("background-color","#F5CBA7");
            $($(row).find("td")[8]).css("background-color","#F5CBA7");
            $($(row).find("td")[9]).css("background-color","#F5CBA7");
            $($(row).find("td")[10]).css("background-color","#F5CBA7");
            $($(row).find("td")[11]).css("background-color","#F5CBA7");
        }
        else if(data['id'] == 17){
            $($(row).find("td")[0]).css("background-color","#FAD7A0");
            $($(row).find("td")[1]).css("background-color","#FAD7A0");
            $($(row).find("td")[2]).css("background-color","#FAD7A0");
            $($(row).find("td")[3]).css("background-color","#FAD7A0");
            $($(row).find("td")[4]).css("background-color","#FAD7A0");
            $($(row).find("td")[5]).css("background-color","#FAD7A0");
            $($(row).find("td")[6]).css("background-color","#FAD7A0");
            $($(row).find("td")[7]).css("background-color","#FAD7A0");
            $($(row).find("td")[8]).css("background-color","#FAD7A0");
            $($(row).find("td")[9]).css("background-color","#FAD7A0");
            $($(row).find("td")[10]).css("background-color","#FAD7A0");
            $($(row).find("td")[11]).css("background-color","#FAD7A0");
        }
        else if(data['id'] == 18){
            $($(row).find("td")[0]).css("background-color","#F9E79F");
            $($(row).find("td")[1]).css("background-color","#F9E79F");
            $($(row).find("td")[2]).css("background-color","#F9E79F");
            $($(row).find("td")[3]).css("background-color","#F9E79F");
            $($(row).find("td")[4]).css("background-color","#F9E79F");
            $($(row).find("td")[5]).css("background-color","#F9E79F");
            $($(row).find("td")[6]).css("background-color","#F9E79F");
            $($(row).find("td")[7]).css("background-color","#F9E79F");
            $($(row).find("td")[8]).css("background-color","#F9E79F");
            $($(row).find("td")[9]).css("background-color","#F9E79F");
            $($(row).find("td")[10]).css("background-color","#F9E79F");
            $($(row).find("td")[11]).css("background-color","#F9E79F");
        }
        else if(data['id'] == 19){
            $($(row).find("td")[0]).css("background-color","#ABEBC6");
            $($(row).find("td")[1]).css("background-color","#ABEBC6");
            $($(row).find("td")[2]).css("background-color","#ABEBC6");
            $($(row).find("td")[3]).css("background-color","#ABEBC6");
            $($(row).find("td")[4]).css("background-color","#ABEBC6");
            $($(row).find("td")[5]).css("background-color","#ABEBC6");
            $($(row).find("td")[6]).css("background-color","#ABEBC6");
            $($(row).find("td")[7]).css("background-color","#ABEBC6");
            $($(row).find("td")[8]).css("background-color","#ABEBC6");
            $($(row).find("td")[9]).css("background-color","#ABEBC6");
            $($(row).find("td")[10]).css("background-color","#ABEBC6");
            $($(row).find("td")[11]).css("background-color","#ABEBC6");
        }
        else if(data['id'] == 20){
            $($(row).find("td")[0]).css("background-color","#A9DFBF");
            $($(row).find("td")[1]).css("background-color","#A9DFBF");
            $($(row).find("td")[2]).css("background-color","#A9DFBF");
            $($(row).find("td")[3]).css("background-color","#A9DFBF");
            $($(row).find("td")[4]).css("background-color","#A9DFBF");
            $($(row).find("td")[5]).css("background-color","#A9DFBF");
            $($(row).find("td")[6]).css("background-color","#A9DFBF");
            $($(row).find("td")[7]).css("background-color","#A9DFBF");
            $($(row).find("td")[8]).css("background-color","#A9DFBF");
            $($(row).find("td")[9]).css("background-color","#A9DFBF");
            $($(row).find("td")[10]).css("background-color","#A9DFBF");
            $($(row).find("td")[11]).css("background-color","#A9DFBF");
        }
        if(data['porcentaje'] == null && data['erroresVerificados'] == null){
            $($(row).find("td")[8]).html(' ');
        }
        else if(data['porcentaje'] != null && data['erroresVerificados'] == null){
            $($(row).find("td")[8]).css("color","#E7EB00 ").css("font-weight","800").html('Pendiente');
        }
        else if(data['porcentaje'] != null && data['erroresVerificados'] == 2){
            $($(row).find("td")[8]).css("color","#E7EB00 ").css("font-weight","800").html('Modificaciones');
        }
        else if(data['erroresVerificados'] == 1 && data['opcionalDos'] == null){
            $($(row).find("td")[8]).css("color","#005D04 ").css("font-weight","800").html('Verificada');
        }
        else if(data['erroresVerificados'] == 1 && data['opcionalDos'] != null){
            $($(row).find("td")[8]).css("color","#005D04 ").css("font-weight","800").html('Verificada con modificaciones');
        }
        if(data['porcentaje'] <= 98){
            $($(row).find("td")[11]).css("color","#AA0000").css("font-weight","800");
        }
        else if(data['porcentaje'] >= 99 && data['porcentaje'] < 100){
            $($(row).find("td")[11]).css("color","#E7EB00").css("font-weight","800");
        }
        else{
            $($(row).find("td")[11]).css("color","#005D04").css("font-weight","800");
        }
    },
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column3_search').on('keyup', function(){
    table.columns(3).search(this.value).draw();
});
$('#column6_search').on('keyup', function(){
    table.columns(6).search(this.value).draw();
});
$('#column8_search').on('keyup', function(){
    table.columns(8).search(this.value).draw();
});
$('#column9_search').on('keyup', function(){
    table.columns(9).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/VeTran/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection





















