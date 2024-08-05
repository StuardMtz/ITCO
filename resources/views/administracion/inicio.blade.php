@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.administracion'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Generar inventarios semanales</p>
    </blockquote>
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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless">
            <thead>
                <tr>
                    <th>Inventario</th>
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
                    <td><a class="btn btn-danger btn-sm" href="{{route('agr_produc',$se->id)}}">Agregar/Eliminar productos</a></td>
                    <td><a class="btn btn-warning btn-sm" href="{{route('actu_se',$se->id)}}"> Actualizar</a></td>
                    <td><a class="btn btn-dark btn-sm" href="{{route('ivs',$se->id)}}">Iniciar</a></td>
                </tr>
			    @elseif ($fecha >= $se->fecha_inicial && $fecha <= $se->fecha_final)
                <tr style="background-color:#b2dbbf">
                    <td>{{$se->semana}}</td>
                    <td>{{date('d/m/Y', strtotime($se->fecha_inicial))}}</td>
                    <td>{{date('d/m/Y', strtotime($se->fecha_final))}}</td>
                    <td><a class="btn btn-danger btn-sm" href="{{route('agr_produc',$se->id)}}">Agregar/Eliminar productos</a></td>
                    <td><a class="btn btn-warning btn-sm" href="{{route('actu_se',$se->id)}}"> Actualizar</a></td>
                    @if($fecha >= $se->created_at && $fecha <= $se->updated_at)
                    <td><a class="btn btn-dark btn-sm" href="{{route('ivs',$se->id)}}">Iniciar</a></td>
                    @else
                    <td><a class="btn btn-dark btn-sm" href="{{route('ivs',$se->id)}}">Iniciar</a></td>
                    @endif
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
