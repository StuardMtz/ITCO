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
    <a class="btn btn-dark" href="{{route('inicio_adm')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="card">
        <div class="card-header">
            <h6>Actualizar fecha de inventario</h6>
        </div>
        <div class="card-body">
            <form class="needs-validation" novalidate method="post" action="{{url('ac_se',$semana)}}">
            {{csrf_field()}}
                <div class="form-row">
                    <div class="form-grop col-md-6">
                        <label for="nuevo_inventario">Fecha de inicio</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="fecha_inicial" value="{{$semana->fecha_inicial}}">
                        </div>
                    </div>
                    <div class="form-grop col-md-6">
                        <label for="nuevo_inventario">Fecha Final</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="fecha_final" value="{{$semana->fecha_final}}">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row justify-content-md-center">
                    <button type="submit" class="btn btn-dark btn-block">Actualizar</button>
                </div>
            </form>
        </div> 
    </div>
</div>
@endsection