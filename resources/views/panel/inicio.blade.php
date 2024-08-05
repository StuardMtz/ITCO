@extends('layouts.app')
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
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#" id="active"><i class="fas fa-home"></i> Inicio</a>
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
            <a class="nav-link" href="{{ route('vg')}}"><i class="fas fa-spinner"></i> Cargar Productos</a>
        </li>
    </ul>
    <br>
    <h5>Generar inventarios semanales</h5>
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Número de semana</th>
                <th>Fecha de inicio</th>
                <th>Fecha final</th>
                <th>Agregar/eliminar productos</th>
                <th>Modificar fechas</th>
                <th>Iniciar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semanas as $se)
            @if ($fecha < $se->fecha_inicial || $fecha > $se->fecha_final)
            <tr>
                <td>{{$se->semana}}</td>
                <td>{{date('d/m/Y', strtotime($se->fecha_inicial))}}</td>
                <td>{{date('d/m/Y', strtotime($se->fecha_final))}}</td>
                <td><a class="btn btn-danger btn-sm" href="{{route('agr_pro',$se->id)}}">Agregar/Eliminar productos</a></td>
                <td><a class="btn btn-primary btn-sm" href="{{route('actu_se',$se->id)}}"> Actualizar</a></td>
                 <td><a class="btn btn-dark btn-sm" href="{{route('ivs',$se->id)}}"><i class="fas fa-play-circle"></i> Iniciar</a></td>
            </tr>
			@elseif ($fecha >= $se->fecha_inicial && $fecha <= $se->fecha_final)
            <tr style="background-color:#b2dbbf">
                <td>{{$se->semana}}</td>
                <td>{{date('d/m/Y', strtotime($se->fecha_inicial))}}</td>
                <td>{{date('d/m/Y', strtotime($se->fecha_final))}}</td>
                <td><a class="btn btn-danger btn-sm" href="{{route('agr_pro',$se->id)}}">Agregar/Eliminar productos</a></td>
                <td><a class="btn btn-primary btn-sm" href="{{route('actu_se',$se->id)}}"> Actualizar</a></td>
                @if($fecha >= $se->created_at && $fecha <= $se->updated_at)
                <td><a class="btn btn-dark btn-sm" href="{{route('ivs',$se->id)}}"><i class="fas fa-play-circle"></i> Iniciar</a></td>
                @else
                <td><a class="btn btn-dark btn-sm" href="{{route('ivs',$se->id)}}"><i class="fas fa-play-circle"></i> Iniciar</a></td>
                @endif
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection