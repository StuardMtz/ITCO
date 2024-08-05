@extends('layouts.app')

@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<div class="container-fluid">
    <h1>Categorias</h1>
    <hr>
    <div class="row">
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/shingle.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Shingle</h5>
                    <p class="card-text">Trabajar inventario de shingle</p>
                    <a href="{{route('scat','1')}}" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/tabla.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Tablas</h5>
                    <p class="card-text">Trabajar inventario de tablas</p>
                    <a href="{{route('scat','2')}}" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/poste.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Postes</h5>
                    <p class="card-text">Trabajar inventario de postes</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/pasta.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Pastas</h5>
                    <p class="card-text">Trabajar inventario de pastas</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/plancha_cielo.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Planchas Cielo</h5>
                    <p class="card-text">Trabajar inventario de planchas de cielo</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/cinta.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Cintas</h5>
                    <p class="card-text">Trabajar inventario de cintas</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/tornillos.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Tornillos</h5>
                    <p class="card-text">Trabajar inventario de tornillos</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/t_aluminio.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">SUS ALU</h5>
                    <p class="card-text">Trabajar inventario de SUS ALU</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/t_acero.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">SUS ACERO</h5>
                    <p class="card-text">Trabajar inventario de SUS ACERO</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/alambre.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Alambre</h5>
                    <p class="card-text">Trabajar inventario de alambre</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/canal.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Perfilm</h5>
                    <p class="card-text">Trabajar inventario de perfilm</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="{{ asset('storage/cards/molduras.jpg') }}" alt="categoria imagen">
                <div class="card-body">
                    <h5 class="card-title">Molduras</h5>
                    <p class="card-text">Trabajar inventario de molduras</p>
                    <a href="#" class="btn btn-dark">Iniciar</a>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection
