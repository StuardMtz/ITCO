@extends('layouts.app')
@section('content')
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
    <a class="btn btn-sm btn-dark" href="{{route('mimageneral')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="card">
        <div class="card-header">
            <h6>Historial de existencia de un producto</h6>
        </div>
        <div class="card-body">
            <form class="needs-validation" novalidate method="get" action="{{route('v_ge')}}">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-calendar-alt"></i> Fecha inicial</label>
                        <input type="date" class="form-control" name="mesu" value="{{date('Y-m-d',strtotime($mes))}}" required>
                        <div class="valid-tooltip">
                            Excelente!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo vacio.
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><i class="fas fa-calendar-alt"></i> Fecha final</label>
                        <input type="date" class="form-control" name="mesd" value="{{date('Y-m-d',strtotime($atras))}}">
                        <div class="valid-tooltip">
                            Excelente!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo vacio.
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label><i class="fas fa-barcode"></i> Código producto</label>
                        <select id="tag_list" name="cod_producto" class="form-control" required></select>
                        <div class="valid-tooltip">
                            Excelente!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo vacio.
                        </div>
                    </div>
                </div>
                <h6>Dato Opcional</h6>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nuevo_inventario"><i class="fas fa-store"></i> Sucursal</label>
                        <select name="cod_unidad" class="form-control" id="sucursal">
                            <option value="">Seleccione una sucursal</option>
                            @foreach($sucursales as $s)
                            <option value="{{$s->cod_unidad}}">{{$s->nombre}}</option>
                            @endforeach
                        </select>
                        <div class="valid-tooltip">
                            Excelente!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo vacio.
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nuevo_inventario"><i class="fas fa-store"></i> Bodega</label>
                        <select class="form-control" id="bodega" name="bodega">
                            <option></option>
                        </select>
                        <div class="valid-tooltip">
                            Excelente!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo vacio.
                        </div>
                    </div> 
                </div>
                <div class="form-row">   
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-dark btn-block btn-sm">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>		
<script>
$('#tag_list').select2({
    placeholder: "Producto...",
    minimumInputLength: 2,
    multiple: false,
    ajax: {
        type:'get',
        url: '{{url('producto')}}',
        dataType: 'json',
        data: function (params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
     }
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
        html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].observacion+'</option>';
        $('#bodega').html(html_select);
    });
}
</script> 
@endsection