@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<div class="container">
      @if(count($errors))<!-- Despliga los mensajes de error que se generan al crear un nuevo registro-->
	<div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error}}</li>
            @endforeach
        </ul>
	</div>
    @endif
</div>
<div class="container-fluid">
   <a class="btn btn-dark" href="{{route('home')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
   <h5>Actualizar</h5>
   </div> 
</div>
@endsection