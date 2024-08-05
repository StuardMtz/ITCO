@extends('layouts.app')
@section('content')
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
    <a class="btn btn-dark btn-sm" href="{{ route('solicitudes_en_ruta')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="card">
        <div class="card-header">
            <h6>Editar entrega manualmente</h6>
        </div>
        <div class="card-body">
            <form method="post" action="{{url('g_can_sol',$id)}}">
            {{csrf_field()}}
                <div class="form-row">
                    <div class="form-group col-md-12">
                    <label>Seleccione un estado</label>
                        <select class="form-control" name="estado" required>
                            <option value="">Seleccione una opción</option>
                            @foreach($estados as $es)
                                @if($es->id > $entrega->id_estado)
                                    <option value="{{$es->id}}">{{$es->nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12"> 
                        <label>Explique por qué ha realizado cambios de forma manual en esta entrega</label>
                        <div class="input-group">
                            <textarea type="text" class="form-control" placeholder="El piloto olvido realizar el cambio de estado....." name="comentario" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-dark btn-block btn-sm">Guardar</button>
                </div>
            </form>
            <br>
            <div class="card">
                <ul class="list-group list-group-flush">
                    @if($entrega->id_departamento != '')
                    <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$entrega->cliente->nombre}}</li>
                    <li class="list-group-item"><i class="fas fa-map-signs"></i><b> Aldea/Caserio/Otros:</b> {{$entrega->otros->nombre}}</li>
                    <li class="list-group-item"><i class="fas fa-map-pin"></i><b> Dirección:</b> {{$entrega->direccion}}</li>
                    @else
                    <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$entrega->sucur->name}}</li>
                    @endif
                    <li class="list-group-item"><i class="fas fa-file-alt"></i><b> Comprobante:</b> {{$entrega->comprobante}}</li>
                    <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Entrega:</b> {{$entrega->detalle_entrega}}</li>
                    <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Dirección:</b> {{$entrega->detalle_direccion}}</li>
                    <li class="list-group-item"><i class="fas fa-exclamation-circle"></i><b> Estado:</b> {{$entrega->estado->nombre}}</li>
                    <li class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{number_format($entrega->estado->porcentaje)}}%"></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
