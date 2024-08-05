@extends('layouts.app2')
@section('content')
<link href="{{asset('css/estilo2.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<div class="container">
	<div class="row justify-content-md-center">
		{!! Form::open(['method'=>'get','route'=>'grafica']) !!}
		<form>
			<div class="form-group">
				<label><i class="fas fa-calendar-alt"></i> <b>Fecha Inicial</b></label>
				<input type="date" class="form-control" name="mesu" value="{{date('Y-m-d',strtotime($mes))}}">
			</div>
			<div class="form-group">
				<label><i class="fas fa-calendar-alt"></i> <b>Fecha_Final</b></label>
				<input type="date" class="form-control" name="mesd" value="{{date('Y-m-d',strtotime($atras))}}">
			</div>
			<div class="form-group">
				<label><i class="fas fa-barcode"></i> <b>Código Producto</b></label>
				<select id="tag_list" name="tag_list[]" class="form-control" required></select>
			</div>
			<b>Dato Opcional</b>
			<div class="form-group">
				<label for="nuevo_inventario"><i class="fas fa-store"></i> <b>Sucursal</b></label>
				{!! Form::select('cod_unidad',$sucursales,null,array('class'=>'form-control','id'=>'sucursal')) !!}
            </div>
			<div class="form-group">
				<label for="nuevo_inventario"><i class="fas fa-store"></i> <b>Bodega</b></label>
				<select class="form-control" id="bodega" name="bodega" required>
					<option></option>
				</select>
            </div>    
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block">Buscar</button>
			</div>
		</form>
		{!! Form::close() !!}
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
                html_select += '<option value="'+data[i].cod_bodega+'">'+data[i].nombre+'</option>';
                $('#bodega').html(html_select);
        });
        }
</script>

      </div>
	  
@endsection