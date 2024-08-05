@extends('layouts.app')
@section('content')
<script src="{{ asset('js/datatables.min.js') }}"></script>

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('transc_finalizadas')}}">Atrás</a>
                <ul>
                    <form method="get" action="{{url('FTranFe')}}">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Inicio</span>
                            </div>
                            <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="inicio" value="{{$inicio}}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Inicio</span>
                            </div>
                            <input type="date" aria-label="Fecha inicial" class="form-control col-form-label-sm" name="fin" value="{{$fin}}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-warning btn-sm" type="button">Buscar</button>
                            </div>
                        </div>
                    </form>
                </ul>
            </div>
        </div>
    </nav>

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
    <blockquote class="blockquote text-center">
        <p class="mb-0">Transferencias finalizadas</p>
    </blockquote>
    <div class="table-responsive-md">
        <table class="table table-sm table-borderless" id="transferencias" style="width:100%">
            <thead>
                <tr>
                    <th colspan="4"><input class="form-control" type="text" id="column2_search" placeholder="Bodega salida"></th>
                    <th colspan="4"><input class="form-control" type="text" id="column4_search" placeholder="Fecha salida"></th>
                    <th colspan="4"><input class="form-control" type="text" id="column7_search" placeholder="Placa"></th>
                </tr>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha de creación</th>
                    <th>Fecha de salida</th>
                    <th>Fecha de finalización</th>
                    <th>Observación</th>
                    <th>Estado</th>
                    <th>Placa</th>
                    <th>Sale sucursal</th>
                    <th>Sale bodega</th>
                    <th>Eficacia</th>
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
    "stateDuration": 300,
    "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
    ajax:{
        url: "{{route('transc_dafife',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: "data",
    },
    "order": [[ 5,"desc" ]],
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
        { data: 'fechaSalida', name: 'fechaSalida'},
        { data: 'fecha_entregado', name: 'fecha_entregado', render: function(data,type,row){
            if(type === "sort" || type === "type"){
                return data;
            }
            return moment(data).format('DD/MM/YYYY');
        }},
        { data: 'observacion', name: 'observacion'},
        { data: 'estado', name: 'estado'},
        { data: 'placa_vehiculo', name: 'placa_vehiculo', render: function (data, type, row){
            return row['propietario'] +' '+  row['placa_vehiculo']}
        },
        { data: 'usale', name: 'usale'},
        { data: 'bsale', name: 'bsale'},
        { data: 'porcentaje', name: 'porcentaje'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2,3,4,5,6,7,8,9], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4,5], render:function(data){
            moment.locale('es');
            return moment(data).format('L');
        }}
    ],
    rowCallback:function(row,data){
        if(data['id_estado'] == 18 || data['id_estado'] == 19){
            $($(row).find("td")[0]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[1]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
			$($(row).find("td")[2]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[3]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[4]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[5]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[6]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[7]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[8]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[9]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[10]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
            $($(row).find("td")[11]).css("background-image","linear-gradient(#B901017D,#B90101A3,#B901017D)");
        }
        if(data['erroresVerificados'] == 1){
            $($(row).find("td")[10]).css("background-image","linear-gradient(#1490B1A1,#1490B18F,#1490B1A1)");
            $($(row).find("td")[11]).css("background-image","linear-gradient(#1490B1A1,#1490B18F,#1490B1A1)");
        }
    },
});
$('#column2_search').on('keyup', function(){
    table.columns(2).search(this.value).draw();
});
$('#column4_search').on('keyup', function(){
    table.columns(4).search(this.value).draw();
});
$('#column7_search').on('keyup', function(){
    table.columns(7).search(this.value).draw();
});
$('#transferencias').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/trc_vrfin/'+row.data().num_movi);
    redirectWindow.location;
});
</script>
@endsection
