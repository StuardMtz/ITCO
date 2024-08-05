@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<div class="container-fluid">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th>Sub Categoria</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><img id="imgref" class="mr-3" src="{{ asset('storage/cards/shingle.jpg') }}" alt="categoria imagen"></td>
                    <td>Single Clasico</td>
                    <td><a class="btn btn-dark" href="{{route('prod','1')}}">Ver más</a></td>
                </tr>
                <tr>
                    <td width="60px"><img id="imgref" class="mr-3" src="{{ asset('storage/cards/shingle.jpg') }}" alt="categoria imagen"></td>
                    <td>Single Tridimencional</td>
                    <td><a class="btn btn-dark" href="#">Ver más</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection