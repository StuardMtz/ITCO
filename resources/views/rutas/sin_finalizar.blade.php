@extends('layouts.app')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
<div class="container">
    @if($message= Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message}}</p>
    </div>
    @endif
    @if($message= Session::get('error'))
    <div class="alert alert-danger">
        <p>{{ $message}}</p>
    </div>
    @endif
</div>
<div class="container-fluid">
    <div class="row">
        <a class="btn btn-dark" href="{{ route('r_inicio')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
    </div>
    <h4>Rutas sin Finalizar</h4>
    <hr>
    <div class="table-condensed">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Placa Camion</th>
                    <th>Fecha de Entrega</th>
                    <th>Editar</th>
                    <th>Ver</th>
                    <th>Finalizar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rutas as $r)
                <tr>
                    <td>{{$r->id}}</td>
                    <td>{{$r->camion->placa}}</td>
                    @if($r->fecha_entrega == '')
                    <td>Agregar Fecha</td>
                    @else
                    <td>{{date('d-m-Y','strtotime'($r->fecha_entrega))}}</td>
                    @endif
                    <td><a class="btn btn-dark" href="{{route('v_e_ruta',$r->id)}}"><i class='fas fa-edit'></i> Editar</a></td>
                    <td><a class="btn" id="ver" href="{{route('v_ruta',$r->id)}}"><i class="fas fa-eye"></i> Ver</a></td>
                    <td><a class="btn btn-danger" href="{{route('f_ruta',$r->id)}}"><i class="fas fa-clipboard-check"></i> Finalizar</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{$rutas->links()}}
</div>
@endsection