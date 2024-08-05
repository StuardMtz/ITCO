@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{asset('css/carga.css')}}" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
<script>
$(window).on('load', function () {   
    $(".loader-page").css({visibility:"hidden",opacity:"0"})
     
});
</script> 
<script>
    $(document).ready(function(){
        $("#crear").click(function(){
            $("#crear").css('display','none');
        });
    });
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
<div class="container-fluid">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" id="active"><i class="fas fa-spinner"></i> Inventarios pendientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('fina')}}"><i class="fas fa-tasks"></i> Finalizados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('s_inicio')}}"><i class="fas fa-truck"></i> Solicitudes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('exist',['sucu'=>$datos->sucursal,'bod'=>$datos->bodega,'todo'=>'1'])}}"><i class="fas fa-chart-bar"></i> Mín y Máx</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('tran_su')}}"><i class="fas fa-truck"></i> Transferencias</a>
        </li>
    </ul>
    <br>
    <h5>Inventarios pendientes</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Encargado</th>
                    <th>Fecha inicial</th>
                    <th>Fecha final</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Estado</th>
                    <th>Ver</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventarios as $inventario)
                @if($inventario->estado == 'En proceso')
                @if($fecha >= date('Y-m-d',strtotime($inventario->fecha_inicial)) && $fecha <= date('Y-m-d',strtotime($inventario->fecha_final)))
                <tr>
                    <td>{{$inventario->numero}}</td>
                    <td>{{$inventario->encargado}}</td>
                    <td>{{date('d/m/Y', strtotime($inventario->fecha_inicial))}}</td>
                    <td>{{date('d/m/Y', strtotime($inventario->fecha_final))}}</td>
                    <td>{{$inventario->uninombre}}</td>
                    <td>{{$inventario->bonombre}}</td>
                    <td>{{$inventario->estado}}</td>
                    @if($inventario->creado =='Si')
                        @if($inventario->estado !='En proceso')
                        <td><a class="btn btn-outline-dark btn-sm" href="{{ route('ver', $inventario->numero) }}" target="_blank"> Ver</a></td>
                        <td></td>
                        @else
                        <td><a class="btn btn-outline-dark btn-sm" href="{{ route('ver', $inventario->numero) }}" target="_blank"> Ver</a></td>
                        <td><a class="btn btn-outline-info btn-sm" href="{{ route('cat', $inventario->numero) }}"><i class="fas fa-edit"></i> Editar</a></td>
                        @endif
                    @else
                    <td><a class="btn btn-outline-dark btn-sm" href="{{ route('crear', $inventario->numero) }}" id="crear"><i class="fas fa-edit"></i> Crear</a></td>
                    <td></td>
                    @endif
                </tr>
                @elseif($inventario->fecha_inicial == '')
                <tr>
                    <td>{{$inventario->numero}}</td>
                    <td>{{$inventario->encargado}}</td>
                    <td>{{date('d/m/Y', strtotime($inventario->created_at))}} <i class="fas fa-arrows-alt-h"></i> {{date('d/m/Y', strtotime($inventario->updated_at))}}</td>
                    <td>{{$inventario->uninombre}}</td>
                    <td>{{$inventario->bonombre}}</td>
                    <td>{{$inventario->estado}}</td>
                    @if($inventario->creado =='Si')
                        @if($inventario->estado !='En proceso')
                        <td><a class="btn btn-outline-dark btn-sm" href="{{ route('ver', $inventario->numero) }}" target="_blank"><i class="fas fa-eye"></i> Ver</a></td>
                        @else
                        <td><a class="btn btn-outline-dark btn-sm" href="{{ route('ver', $inventario->numero) }}" target="_blank"><i class="fas fa-eye"></i> Ver</a>
                        <a class="btn btn-outline-info btn-sm" href="{{ route('cat', $inventario->numero) }}"><i class="fas fa-edit"></i> Editar</a>
                        @endif
                    @else
                    <td><a class="btn btn-outline-dark btn-sm" href="{{ route('crear', $inventario->numero) }}" id="crear"><i class="fas fa-edit"></i> Crear</a></td>
                    @endif
                </tr>
                @else
                <tr>
                    <td>{{$inventario->numero}}</td>
                    <td>{{$inventario->encargado}}</td>
                    <td>{{date('d/m/Y', strtotime($inventario->fecha_inicial))}} <i class="fas fa-arrows-alt-h"></i> {{date('d/m/Y', strtotime($inventario->fecha_final))}}</td>
                    <td>{{$inventario->uninombre}}</td>
                    <td>{{$inventario->bonombre}}</td>
                    <td>{{$inventario->estado}}</td>
                    <td><a class="btn btn-dark btn-sm" href="{{ route('ver', $inventario->numero) }}" target="_blank"><i class="fas fa-eye"></i> Ver</a>
                    <td></td>
                </tr>
                 @endif
                @else
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
