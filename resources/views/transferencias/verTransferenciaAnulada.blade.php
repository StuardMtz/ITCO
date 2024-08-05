@extends('layouts.app')
@section('content')
<link href="{{asset('css/css2/select2.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<script>
    var url_global='{{url("/")}}';
</script>
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
<div class="container-fluid" >
@foreach($tran as $t)
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#anuladaNav" aria-controls="navbarNav" aria-expanded="false" 
            aria-label="Toggle navigation">
            <img src="storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="anuladaNav">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link bg-light" href="{{route('tranAnulad')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="active"><i class="fas fa-eye"></i> Transferencia número {{$t->num_movi}}</a>
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opciones transferencia
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#staticBackdrop" id="modalb">
                                Verificar transferencia
                            </button>
                            <a class="dropdown-item" href="{{route('histo_transf',$id)}}">Historial de transferencias</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
	<div class="table-responsive-sm">
        <table class="table table-sm table-borderless">
            <tbody>
                <tr>
                    <td><b>Número de transferencia:</b>  {{$t->num_movi}}</td>
                    <td><b>Sucursal:</b> {{$t->nombre}}</td>
                    <td><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}</td>
                </tr>
                <tr>
                    <td><b>Descripción:</b> {{$t->descripcion}}</td>
                    <td><b>Observación:</b> {{$t->observacion}}</td>
                    <td><b>Comentario:</b> {{$t->comentario}}</td>
                </tr>
                <tr>
                    <td style="background:#f984ef;"><b>Estado:</b> {{$t->estado}}</td>
                    <td><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}</td>
                    <td><b>Fecha aprox. entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}</td>
                </tr>
                <tr>
                    @if($t->fechaUno == '')
                    <td><b>Peparando carga:</b></td> 
                    @else
                    <td><b>Peparando carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}</td>
                    @endif
                    @if($t->fecha_enCarga == '')
                    <td><b>Carga preparada:</b></td>
                    @else
                    <td><b>Carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}</td>
                    @endif
                    @if($t->fecha_cargado == '')
                    <td><b>Fecha de carga:</b></td>
                    @else
                    <td><b>Fecha de carga: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_cargado))}}</td>
                    @endif
                </tr>
                <tr>
                    @if($t->fechaSalida == '')
                    <td<b>Fecha de salida:</b></td>
                    @else
                    <td><b>Fecha de salida: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSalida))}}</td>
                    @endif
                    <td><b>Fecha en sucursal: </b>{{date('d/m/Y H:i:s',strtotime($t->fecha_entregado))}}</td>
                    <td><b>Observación Supervisor: </b>{{$t->observacionSup}} </td>
                </tr>
                <tr>
                    <td><b>Fecha finalizada: </b>{{date('d/m/Y H:i:s',strtotime($t->fechaSucursal))}}</td>
                    <td><b>Creada por: </b> {{$t->usuario}}</td>
                    <td><b>Revisada por: </b> {{$t->usuarioSupervisa}}</td>
                </tr>
                <tr>
                    <td><b>Anulada por: </b> {{$t->name}}</td>
                    <td colspan="2"><b>Sale de: </b>{{$t->usale, $t->bsale}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 
@endsection