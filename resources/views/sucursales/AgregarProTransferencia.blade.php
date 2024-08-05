@extends('layouts.app2')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/app.js')}}" defer></script>
<script src="{{asset('js/jquery.js')}}"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js" defer></script>
<script type="text/javascript" src="{{asset('js/productosmanual.js')}}" defer></script>
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
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
<div class="container-fluid" id="boton">
    @foreach($tran as $t)
    <ul class="nav nav-tabs">
        <li class="nav-item">
			<a class="nav-link bg-light" href="{{route('tran_su')}}"><b><i class="fas fa-arrow-left"></i> Atrás</b></a>
        </li>
        <li class="nav-item">
            <a class="nav-link active bg-info"><b><i class="fas fa-tasks"></i> Agregar productos</b></a>
        </li>
        
	</ul>
	<div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <b>Número de transferencia:</b>  {{$t->num_movi}}
                </div>
                <div class="col">
                    <b>Sucursal:</b> {{$t->nombre}}
                </div>
                <div class="col">
                    <b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}
                </div>
                <div class="col">
                    <b>Vehículo:</b> {{$t->placa_vehiculo}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Descripción:</b> {{$t->descripcion}}
                </div>
                <div class="col">
                    <b>Observación:</b> {{$t->observacion}}
                </div>
                <div class="col">
                    <b>Comentario:</b> {{$t->comentario}}
                </div>
                <div class="col">
                    <b>Estado:</b> {{$t->estado}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Grupo que cargo:</b> {{$t->grupoCarga}}
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div> 
    
    <div class="container-fluid"> 
    <div id="app" class="content"><!--La equita id debe ser app, como hemos visto en app.js-->
        <agregarsucursal-component></agregarsucursal-component><!--Añadimos nuestro componente vuejs-->
    </div>
</div>
<script type="application/json" name="server-data">
    {{ $id }}
</script>
<script type="application/javascript">
       var json = document.getElementsByName("server-data")[0].innerHTML;
       var server_data = JSON.parse(json);
</script>
@endsection
