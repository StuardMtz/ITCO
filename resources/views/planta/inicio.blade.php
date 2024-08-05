@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('css/css2/select2.css') }}">
<script>
    var url_global='{{url("/")}}';
</script>
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script src="{{ asset('js/serie.js') }}" defer></script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('/js/select2/select2.min.js')}}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
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
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" id="active" href="#"><i class="fas fa-truck"></i> Transferencias en proceso</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('BTran')}}"><i class="fas fa-people-carry"></i> Transferencias en bodega</a>
            </li><!--
            <li class="nav-item">
                <a class="nav-link" href="{{route('DeTran')}}"><i class="fas fa-truck"></i> Transferencias despachadas</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link" href="{{route('FTran')}}"><i class="fas fa-clipboard-check"></i> Transferencias finalizadas</a>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" id="modalb" data-toggle="modal" data-target="#nuevaTransferencia">
                    Crear transferencia
                </button>
            </li>
        </ul>
    </div>
    <h5>Transferencias en proceso</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm" id="sucursales">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Fecha</th>
                    <th>Fecha para carga</th>
                    <th>Creado por</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="nuevaTransferencia" aria-labelledby="nuevaTransferenciaLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Factura, pedido o manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{url('agreFac')}}">
                {{csrf_field()}}
                    <div class="form-check">
                        <div class="form-check col-md-4">
                            <input class="form-check-input" type="radio" name="tipo" id="docfactura" value="1">
                            <label class="form-check-label" for="inlineRadio1">Con factura</label>
                        </div>
                        <div class="form-check col-md-4">
                            <input class="form-check-input" type="radio" name="tipo" id="pedido" value="2">
                            <label class="form-check-label" for="inlineRadio2">Por medio de pedido</label>
                        </div>
                        <div class="form-check col-md-4">
                            <input class="form-check-input" type="radio" name="tipo" id="manual" value="3">
                            <label class="form-check-label" for="inlineRadio3">Sin documento</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><b>Sale de Sucursal</b></label>
                            <select name="saleDe" class="form-control"required>
                                <option value="{{$saleDe->cod_unidad}}">{{$saleDe->nombre}}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label><b>Sale de Bodega</b></label>
                            <select name="saleBo" class="form-control" required>
                                @foreach($saleBo as $bo)
                                <option value="{{$bo->cod_bodega}}">{{$bo->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><b>Entra a sucursal</b></label>
                            <select name="entraSu" class="form-control" required id="sucursal" disabled>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($entraSu as $su)
                                <option value="{{$su->cod_unidad}}">{{$su->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nuevo_inventario"><b>Entra a Bodega</b></label>
                            <select class="form-control" id="bodega" name="entraBo" required disabled>
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6" id="divserie">
                            <label><b>Serie factura</b></label>
                            <select class="form-control" id="noserie" name="serie" required disabled></select>
                        </div>
                        <div class="form-group col-md-6" style="display:none;" id="npedido">
                            <label><b>Número de pedido</b></label>
                            <input class="form-control" name="serie" required disabled id="cpedido">
                        </div>
                        <div class="form-group col-md-6">
                            <label><b>Número factura</b></label>
                            <input type="number" min="1" class="form-control" placeholder="Número factura" name="numero" required id="factura" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-dark btn-block">Cargar productos</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
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
            url: "{{route('Dtran')}}",
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
                { data: 'num_movi', name: 'num_movi'},
                { data: 'nombre', name:'nombre'},
                { data: 'bodega', name: 'bodega'},
                { data: 'created_at', name: 'created_at'},
                { data: 'fecha_paraCarga', name: 'fecha_paraCarga'},
                { data: 'usuario', name: 'usuario'},
                { data: 'DESCRIPCION', name: 'DESCRIPCION'},
                { data: 'estado', name: 'estado'}
                ],
        "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [4], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }},
        {targets: [3], render:function(data){
            moment.locale('es');
            return moment(data).format('LL');
        }}
    ],

    });
    $('#sucursales').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      window.location.href =(url_global+'/EdTran/'+row.data().num_movi);
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
		$(document).ready(function(){
			$("#manual").click(function(){
                $("#factura").attr('disabled',true);
                $("#sucursal").attr('disabled',false);
                $('#noserie').attr('disabled',true);
                $('#bodega').attr('disabled',false);
                document.getElementById('divserie').style.display = "none";
                document.getElementById('npedido').style.display = "none";
			});
			$("#docfactura").click(function(){
				$("#noserie").attr('disabled',false);
                $("#factura").attr('disabled',false);
                $("#sucursal").attr('disabled',true);
                $('#bodega').attr('disabled',true);
                document.getElementById('divserie').style.display = "block";
                document.getElementById('npedido').style.display = "none";
                $
			});
            $("#pedido").click(function(){
				$("#noserie").attr('disabled',true);
                $("#factura").attr('disabled',false);
                $("#sucursal").attr('disabled',false);
                $('#bodega').attr('disabled',false);
                $('#factura').attr('disabled',false);
                $('#cpedido').attr('disabled',false);
                document.getElementById('divserie').style.display = "none";
                document.getElementById('npedido').style.display = "block";
			});
		});
	</script>
@endsection
