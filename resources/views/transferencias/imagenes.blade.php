@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                @if($transferencia->id_estado == 17 && $transferencia->cod_unidad != 15)
                <a class="nav-link" href="{{route('verficar_trans',$id)}}">Atrás</a>
                @elseif($transferencia->id_estado < 20 && $transferencia->cod_unidad != 15)
                <a class="nav-link" href="{{route('validad_tranf',$id)}}">Atrás</a>
                @elseif($transferencia->cod_unidad != 15)
                <a class="nav-link" href="{{route('VeTran',$id)}}">Atrás</a>
                @else
                <a class="nav-link" href="{{route('transc_ver_transfina',$id)}}">Atrás</a>
                @endif
                <a class="nav-link" href="#" id="active">Agregar imágenes</a>
                @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                <li class="nav-item">
                    @if (Route::has('register'))
                        <!--<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> -->
                    @endif
                </li>
                @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
                @endguest
            </div>
        </div>
    </nav>

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
        <hr>
        <form method="post" action="{{route('g_imag_tran',$id)}}" enctype="multipart/form-data">
        {{csrf_field()}}
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" d="customFileLang" lang="es" name="imagen" accept="image/*">
                    <label class="custom-file-label" for="inputGroupImagen">Seleccionar imagen</label>
                </div>
            </div>
            <div class="mb-3">
                <label for="validationTextarea">Descripción</label>
                <textarea class="form-control" id="validationTextarea" placeholder="Descripción del material dañado" name="descripcion" required></textarea>
            </div>
            <button class="btn btn-dark" type="submit">Guardar</button>
        </form>

        @foreach($imagenes as $img)
        <form method="post" action="{{route('edit_des_img',$img->id)}}" enctype="multipart/form-data">
        {{csrf_field()}}
            <div class="card mb-3">
                <img src="{{asset($img->imagen)}}" class="card-img-top" style="width:60%;height:550px;">
                <div class="card-body">
                <p class="card-text">{{$img->descripcion}}</p>
                    @if($transferencia->id_estado == 20 && Auth::user()->sucursal == 27)
                    <div class="input-group">
                        <textarea class="form-control" id="validationTextarea" value="{{$img->descripcion}}" name="descripcion" required>{{$img->descripcion}}</textarea>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Guardar</button>
                        </div>
                    </div>
                    @else
                    @endif
                </div>
            </div>
        </form>
        @endforeach
    </div>
@endsection
