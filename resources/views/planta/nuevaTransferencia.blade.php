@extends('layouts.app2')
<script src="{{asset('js/app.js')}}" defer></script>
<script src="{{asset('js/jquery.js')}}"></script>
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
        @if($t->id_estado == 13)
		<li class="nav-item">
			<a class="nav-link bg-light" href="{{route('Vtran')}}"><i class="fas fa-clipboard"></i> Transferencias</a>
        </li>
        @else
        <li class="nav-item">
			<a class="nav-link bg-light" href="{{route('BTran')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
        </li>
        @endif
		<li class="nav-item">
	 		<a class="nav-link" id="active"><i class="fas fa-tasks"></i> Agregando productos</a>
		</li>
        <li class="nav-item">
            <a class="nav-link bg-light" id="export-btn" href="{{route('EdTran',$id)}}"><i class="fas fa-edit"></i> Editar transferencia</a>
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
            </div>
            <div class="row">
                <div class="col">
                    <b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}
                </div>
                <div class="col">
                    <b>Descripción:</b> {{$t->descripcion}}
                </div>
                <div class="col">
                    <b>Observación:</b> {{$t->observacion}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Comentario:</b> {{$t->comentario}}
                </div>
                @if($t->id_estado == 13)
                <div class="col" style="background:#940000;color:white;">
                    Estado: {{$t->estado}}
                </div>
                @elseif($t->id_estado == 14)
                <div class="col" style="background:#a8e4a0;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 15)
                <div class="col" style="background:#dcd0ff;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 16 || $t->id_estado == 17)
                <div class="col" style="background:#87cefa;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado >= 18 || $t->id_estado <= 19)
                <div class="col" style="background:#ffb347;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @elseif($t->id_estado == 20)
                <div class="col" style="background:#f984ef;">
                    <b>Estado:</b> {{$t->estado}}
                </div>
                @endif
                <div class="col">
                    <b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <br>
</div>
<div class="container-fluid">
    <div id="app" class="content"><!--La equita id debe ser app, como hemos visto en app.js-->
        <plantagregar-component></plantagregar-component><!--Añadimos nuestro componente vuejs-->
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
