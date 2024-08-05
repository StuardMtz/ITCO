@extends('layouts.app')
@section('content')

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="javascript:history.back()">Atrás</a>
                <form method="get" action="{{url('repinvf')}}" class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">De</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control" name="inicio" value="{{$inicio}}" required>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Hasta</span>
                        </div>
                        <input type="date" aria-label="Fecha inicial" class="form-control" name="fin" value="{{$fin}}" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-warning btn-sm" type="button">Buscar</button>
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
            <p class="mb-0">Reporte Inventarios {{date('d/m/Y', strtotime($inicio))}} al {{date('d/m/Y', strtotime($fin))}}</p>
        </blockquote>
        <div class="table-responsive-sm">
            <table class="table table-sm table-borderless" id="inventario" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Encargado</th>
                        <th>Inventario</th>
                        <th>Fecha inicial</th>
                        <th>Fecha final</th>
                        <th>Realizado en</th>
                        <th>Sucursal</th>
                        <th>Bodega</th>
                        <th>Contabilizados</th>
                        <th>Diferencias</th>
                        <th>Dañado</th>
                        <th>Realizado</th>
                        <th>Exactitud</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

<script>
var table = $('#inventario').DataTable({
    processing: true,
    serverSide: false,
    pageLength: 50,
    searching: true,
    responsive: false,
    scrollX: true,
    scrollY: '70vh',
    scrollCollapse: true,
    scroller:       true,
    stateSave:      true,
    "stateDuration": 300,
    ajax:{
        url: "{{route('datos_reporte_inventario_f',['inicio'=>$inicio,'fin'=>$fin])}}",
        dataSrc: 'data',
    },
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
        { data: 'numero', name:  'numero'},
        { data: 'nombre', name: 'nombre'},
        { data: 'semana', name: 'semana'},
        { data: 'created_at', name: 'created_at' },
        { data: 'updated_at', name: 'updated_at' },
        { data: 'diferencia_tiempo', name: 'diferencia_tiempo'},
        { data: 'sucursal', name: 'sucursal'},
        { data: 'bodega', name: 'bodega'},
        { data: 'contado', name: 'contado'},
        { data: 'diferencia', name: 'diferencia'},
        { data: 'daniado', name: 'daniado'},
        { data: 'realizado', name: 'realizado',
            render: function(data, type, row) {
                if (data > 0){
                    if (type === 'display' || type === 'filter') {
                        return parseFloat(data).toFixed(2) + '%';
                    }
                }
                if (data >= 100){
                    data = 100;
                }
                return data;
            }
        },
        { data: 'exactitud', name: 'exactitud',
            render: function(data, type, row) {
                if (data > 0){
                    if (type === 'display' || type === 'filter') {
                    return parseFloat(data).toFixed(2) + '%';
                }
                }
                return data;
            }
        }
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],
    rowCallback:function(row,data){
        if(data['estado'] == 'Finalizado'){
            $($(row).find("td")[0]).css("background-color","#238C1173");
            $($(row).find("td")[1]).css("background-color","#238C1173");
			$($(row).find("td")[2]).css("background-color","#238C1173");
            $($(row).find("td")[3]).css("background-color","#238C1173");
            $($(row).find("td")[4]).css("background-color","#238C1173");
            $($(row).find("td")[5]).css("background-color","#238C1173");
            $($(row).find("td")[6]).css("background-color","#238C1173");
            $($(row).find("td")[7]).css("background-color","#238C1173");
            $($(row).find("td")[8]).css("background-color","#238C1173");
            $($(row).find("td")[9]).css("background-color","#238C1173");
            $($(row).find("td")[10]).css("background-color","#238C1173");
            $($(row).find("td")[11]).css("background-color","#238C1173");
			$($(row).find("td")[12]).css("background-color","#238C1173");
        }
        else if(data['estado'] == 'En proceso'){
            $($(row).find("td")[0]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[1]).css("background-color","#F0EB4A7D");
			$($(row).find("td")[2]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[3]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[4]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[5]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[6]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[7]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[8]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[9]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[10]).css("background-color","#F0EB4A7D");
            $($(row).find("td")[11]).css("background-color","#F0EB4A7D");
			$($(row).find("td")[12]).css("background-color","#F0EB4A7D");
        }
        if(data['exactitud'] == null){
            $($(row).find("td")[12]).css("color","#238C1173").css("font-weight","800");
        }
        else if(data['exactitud'] < 85){
            $($(row).find("td")[12]).css("color","#AA0000").css("font-weight","800");
        }
        else if(data['exactitud'] < 95){
            $($(row).find("td")[12]).css("color","#E7EB00").css("font-weight","800");
        }
        else if(data['exactitud'] <= 100){
            $($(row).find("td")[12]).css("color","#005D04").css("font-weight","800");
        }
        if(data['realizado'] >= 100){
            $($(row).find("td")[11]).html("100%");
        }
    }
});
$('#inventario').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.open(url_global+'/ver/'+row.data().numero, '_blank');
    redirectWindow.location;
});
</script>
@endsection
