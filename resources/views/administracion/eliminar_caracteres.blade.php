@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.administracion'))
<div class="container-fluid">
    <blockquote class="blockquote text-center">
        <p class="mb-0">Actualizar información de productos</p>
    </blockquote>

    <div class="card">
        <div class="card-header">
            Actualizar nombres de categorias
        </div>
        <div class="card-body">
            <p>Al crear una nueva categoría dentro de la tabla llamada tipos_prod, es necesario actualizar los datos de la tabla,
            para poder agregar la nueva categoria</p>
            <a class="btn btn-danger" href="cat_nom">Actualizar</a>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            Eliminar caracteres especiales
        </div>
        <div class="card-body">
            <p>Cuando actualice los datos de las categorias, la aplicación no podra mostrar los nombres que contengan tildes, o cualquier otro caracter especial.
            Precione actualizar para eliminar los caracteres especiales.</p>
            <a class="btn btn-danger" href="cat_mod">Actualizar</a>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            Actualizar nombres de productos
        </div>
        <div class="card-body">
            <p>Al agregar nuevos productos a la tabla productos_inve, es necesario actualizar los datos de la tabla.</p>
            <a class="btn btn-danger" href="pro_nom">Actualizar</a>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            Eliminar caracteres especiales
        </div>
        <div class="card-body">
            <p>Cuando actualice los nombres de los productos, la aplicación no podra mostrar los nombres que contengan tildes,
            o cualquier otro caracter especial. Precione actualizar para eliminar los caracteres especiales.</p>
            <a class="btn btn-danger" href="pro_mod">Actualizar</a>
        </div>
    </div>
    <b>Importante: </b>Si se crea una nueva categoría en la tabla tipos_prod, y luego se agregan productos relacionados a esa nueva categoría, es necesario realizar todas las actualizaciones en orden, de arriba hacia abajo. Si solo se crean nuevos productos a categorías
     ya existentes entonces no es necesario realizar las primeras dos actualizaciones, únicamente se realizaran las ultimas 3, igualmente de arriba hacia abajo.
</div>
@endsection
