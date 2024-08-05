@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}"><i class="fas fa-home"></i> Inicio</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cre_se')}}"><i class="fas fa-calendar-alt"></i> Crear Semana</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('sucs')}}"><i class="fas fa-calendar-alt"></i> Ver Inventarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('lis_us')}}"><i class="fas fa-clipboard"></i> Listado Usuarios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('v_pro') }}"><i class="fas fa-skull-crossbones"></i> UTF-8</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active bg-info" href="#"><b><i class="fas fa-spinner"></i> Cargar Productos</b></a>
        </li>
    </ul>
    {!! Form::open(array('route'=>'cpro','before'=>'csrf','method'=>'post')) !!}
    <div id="form">
    <div class="form-row">
        <div class="form-group col">
            <label for="nuevo_inventario">Sucursal</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-home"></i></div>
                </div>
                <select class="form-control" id="sucursal" name="sucursal">
                    @foreach($sucursales as $sucursal)
                    <option value="{{$sucursal->cod_unidad}}" name="id">{{$sucursal->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col">
            <label for="nuevo_inventario">Bodega</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-dolly"></i></div>
                </div>
                <select class="form-control" id="bodega" name="bodega">
                    <option></option>
                </select>
            </div>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <button type="submit" class="btn btn-success btn-block">Crear</button>
    </div>
    </form>
    {!! Form::close() !!}
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
                html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
                $('#bodega').html(html_select);
        });
        }
</script>
</div>
@endsection