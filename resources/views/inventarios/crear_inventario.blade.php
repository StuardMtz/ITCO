@extends('layouts.app')
@section('content')
@yield('navbar', View::make('layouts.inventario'))
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Crear inventario general</h6>
        </div> 
        <div class="card-body">
            <form class="needs-validation" novalidate action="{{url('guarinvgen')}}" method="post">
            {{csrf_field()}}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputNombres">Nombres</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="NombreCompleto" placeholder="Nombre del encargado" name="encargado" required>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inpuyApellidos">Apellidos</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="Apellidos" placeholder="Apellido del encargado" name="apellidos" required>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputDPI">Número de identificación</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="Identificación" placeholder="Número de identificación" name="no_identificacion" required>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="selectSucursal">Sucursal</label>
                        <div class="input-group">
                            <select class="form-control" id="sucursal" name="sucursal" required>
                                <option value="">Selecciones una sucursal</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{$sucursal->cod_unidad}}" name="id">{{$sucursal->resolucion_autorizacion}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="selectBodega">Bodega</label>
                        <div class="input-group">
                            <select class="form-control" id="bodega" name="bodega" required>
                                <option>Primero seleccione la sucursal</option>
                            </select>
                            <div class="valid-tooltip">
                                Excelente!
                            </div>
                            <div class="invalid-tooltip">
                                No puede dejar este campo vacio.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-dark btn-block btn-sm" class="fas fa-save"><i class="fas fa-plus-circle"></i> Crear inventario general</b></button>
                </div>
            </form>
        </div>
    </div>
</div>
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