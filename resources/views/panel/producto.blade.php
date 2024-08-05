@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet"> 
<div class="container-fluid">
    <h5>Producto @foreach($todos_productos as $t)<b style="color: blue;">{{$t->nombre}}</b>, código <b style="color: blue;">{{$t->nombre_corto}}</b>, existencia actual <b style="color:blue;">{{number_format($t->existencia),0}}</b> unidades</h5>
    @endforeach
    <div id="form">
    {!! Form::open(array('route'=>'agp','before'=>'csrf','method'=>'post')) !!}
    <form>
        <div class="col my-1">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="far fa-clipboard"></i></div>
                </div>
                <input type="number" class="form-control" id="ExistenciaFisaca" placeholder="Existencia Fisica" name="existencia_fisica">
            </div>
        </div>
        <hr>
        <div class="col my-1">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-edit"></i></div>
                </div>
                <input type="text" class="form-control" id="Descripcion" placeholder="Descripción" name="descripcion">
            </div>
        </div>
        <hr>
        <div class="col-auto">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="autoSizingCheck" name="dañado" value="Si">
                <label class="form-check-label" for="autoSizingCheck">Dañado</label>
            </div>
        </div>
        <div class="form-group" style="display:none;">
            <label>No. de Inventario</label>
            <select class="form-control" id="exampleFormControlSelect1" name="id">
                <option value="{{$id}}" name="id">{{$id}}</option>
            </select>
            <hr>
            <label>Código del Producto</label>
            <select class="form-control" id="exampleFormControlSelect1" name="cod">
                <option value="{{$cod}}" name="cod">{{$cod}}</option>
            </select>
        </div>
        <hr>
        <button type="submit" class="btn btn-dark"><i class="fas fa-check"></i> Agregar</button>
        </form>
    {!! Form::close() !!}
    </div>
    <div class="row">
        <div class="col">
            <div class="table-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Teorica</th>
                            <th>Fisica</th>
                            <th>Diferencia</th>
                            <th>Detalles</th>
                            <th>Dañado</th>
                            <th>Ingreso</th>
                            <th>Fecha</th>
                        </tr>
                    </thead> 
                    <tbody>
                   @foreach($existencia as $ex)
                        <tr>
                            <td>{{number_format($ex->existencia)}}</td>
                            <td>{{number_format($ex->existencia_fisica)}}</td>
                            <td>{{$ex->existencia_fisica - $ex->existencia}}</td>
                            <td>{{$ex->descripcion}}</td>
                            <td>{{$ex->mal_estado}}</td>
                            <td>{{$ex->nombre->name}}</td>
                            <td>{{date('d-m-Y H:i', strtotime($ex->created_at))}}</td>
                        </tr>
                    @endforeach
                        <tr>
                            <td style="background-color:#CCF3CC; color:black;"></td>
                            <td style="background-color:#CCF3CC; color:black;"><b>Diferencia: {{$diferencia}}</b></td>
                            <td style="background-color: #CCF3CC; color:black;"><b>Total Ingresado: {{$suma}}</b></td>
                            <td colspan="2" style="background-color: #CCF3CC; color:black;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection