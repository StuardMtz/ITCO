@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.inventario'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0 titulos">Inventarios en proceso</p>
    </blockquote>

    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Inventario</th>
                    <th>Fecha inicial</th>
                    <th>Fecha final</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th>Realizado</th>
                    <th></th> 
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventarios as $inventario)
                <tr>
                    <td>{{$inventario->id}}</td>
                    <td>{{$inventario->encargado}}</td>
                    <td>{{$inventario->semana}}</td>
                    <td>{{date('d/m/Y', strtotime($inventario->created_at))}}</td>
                    <td>{{date('d/m/Y', strtotime($inventario->updated_at))}}</td>
                    <td>{{$inventario->sucursal}}</td>
                    <td>{{$inventario->bodega}}</td>
                    <td>{{number_format($inventario->porcentaje,2)}}%</td>
                    @if($inventario->creado =='Si')
                    <td><a class="btn btn-dark btn-sm" href="{{ route('ver_inventario', $inventario->id) }}" target="_blank">Ver</a></td>
                    @if($inventario->estado !='En proceso')
                    <td></td>
                    @else
                    @if(Auth::user()->roles == 3 && $inventario->fecha_final >= $fecha)
                    <td><a class="btn btn-warning btn-sm" href="{{ route('productos_inventario', $inventario->id)}}" target='_black'>Editar</a>
                    @elseif(Auth::user()->roles == 3 && $fecha > $inventario->fecha_final)
                    <td></td>
                    @else
                    <td><a class="btn btn-warning btn-sm" href="{{ route('productos_inventario', $inventario->id)}}" target='_black'>Editar</a>
                    @endif
                    @endif
                    @else
                    <td><a class="btn btn-dark btn-sm" href="{{ route('cargar_productos', $inventario->id) }}" id="crear">Crear</a></td>
                    <td><a class="btn btn-danger btn-sm" href="{{route('eliminar_inventario',$inventario->id)}}">Eliminar</a></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection