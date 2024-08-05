@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.gastos'))
<script src="{{ asset('js/proveedores.js') }}" defer></script>


<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Listado de mis liquidaciones</p>
    </blockquote>
    <button type="button" class="nav-link" id="modalb" data-toggle="modal" data-target="#nuevaLiquidacion">
                    Nueva liquidación
                </button>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="gastos" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Responsable</th>
                    <th>Sucursal</th>
                    <th>Del</th>
                    <th>Al</th>
                    <th>Fecha creación</th>
                    <th>Estado</th>
                    <th>Verificado por</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="nuevaLiquidacion" aria-labelledby="nuevaLiquidacionLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva liquidación de gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nit-tab" data-toggle="tab" data-target="#nit" type="button" role="tab"
                        aria-controls="nit" aria-selected="true">Liquidación</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="nit" role="tabpanel" aria-labelledby="nit-tab">
                        <form class="needs-validation" novalidate method="post" action="{{url('nue_liq')}}">
                        {{csrf_field()}}
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><b>Nombre responsable</b></label>
                                    <input type="text" class="form-control" name="responsable" placeholder="Nombre del responsable" aria-label="responsable" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label><b>Fecha inicial</b></label>
                                    <input type="date" class="form-control" name="fecha_inicial" aria-label="fecha_inicial" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label><b>Fecha final</b></label>
                                    <input type="date" class="form-control" name="fecha_final" aria-label="fecha_final" required>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label><b>Obersavaciones</b></label>
                                    <textarea type="text" class="form-control" name="observaciones" aria-label="observaciones" required></textarea>
                                    <div class="valid-tooltip">
                                        Muy bien.
                                    </div>
                                    <div class="invalid-tooltip">
                                        No debes dejar este campo en blanco.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="nav-link nav-link-info nav-link-block" id="guardar">Gerenar liquidación</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="nav-link nav-link-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
var table = $('#gastos').DataTable({
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
        url: "{{route('dliquidaciones')}}",
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
        { data: 'id', name:  'id'},
        { data: 'responsable', name: 'responsable'},
        { data: 'sucursal', name: 'sucursal'},
        { data: 'fecha_inicial', name: 'fecha_inicial'},
        { data: 'fecha_final', name: 'fecha_final'},
        { data: 'fecha_creacion', name: 'fecha_creacion'},
        { data: 'estado', name: 'estado'},
        { data: 'name', name: 'name'}
    ],
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0,1,2], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3,4,5], render:function(data){
             moment.locale('es');
            return moment(data).format('LL');
        }}
    ],
    rowCallback:function(row,data){
        if(data['estado'] == 'Creada'){
            $($(row).find("td")[0]).css("background-color","#F07A7ABF");
            $($(row).find("td")[1]).css("background-color","#F07A7ABF");
			$($(row).find("td")[2]).css("background-color","#F07A7ABF");
            $($(row).find("td")[3]).css("background-color","#F07A7ABF");
            $($(row).find("td")[4]).css("background-color","#F07A7ABF");
            $($(row).find("td")[5]).css("background-color","#F07A7ABF");
            $($(row).find("td")[6]).css("background-color","#F07A7ABF");
            $($(row).find("td")[7]).css("background-color","#F07A7ABF");
        }
        else if(data['estado'] == 'Revisada'){
            $($(row).find("td")[0]).css("background-color","#238C1173");
            $($(row).find("td")[1]).css("background-color","#238C1173");
			$($(row).find("td")[2]).css("background-color","#238C1173");
            $($(row).find("td")[3]).css("background-color","#238C1173");
            $($(row).find("td")[4]).css("background-color","#238C1173");
            $($(row).find("td")[5]).css("background-color","#238C1173");
            $($(row).find("td")[6]).css("background-color","#238C1173");
            $($(row).find("td")[7]).css("background-color","#238C1173");
        }
    }
});
$('#gastos').on('click','tbody tr', function(){
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    window.location.href =(url_global+'/ed_liquid/'+row.data().id);
    redirectWindow.location;
});
</script>
@endsection
