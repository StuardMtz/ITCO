@extends('layouts.app')
@section('content')
<style type="text/css">
    @media print{
        .oculto-impresion, .oculto-impresion *{
        display: none !important;
        }
    }
</style>
<div class="container-fluid">
    <h1>Inventario número {{$id}}</h1>
    <div class="table">
        <table class="table table-sm">
            <tbody>
                @foreach($datos as $d)
                <tr>
                    <td><b>Nombre del Encargado: </b>{{$d->encargado}}</td>
                    <td><b>Realizado por: </b>{{$d->nombre}}</td>
                    <td><b>Sucursal: </b>{{$d->uninombre}}</td>
                    <td><b>Bodega: </b>{{$d->bonombre}}</td>
                    <td><b>Fecha: </b>{{date('d-m-Y H:i', strtotime($d->updated_at))}}</td>
                    <td><b>Estado: </b>{{$d->estado}}</td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="4"><b>Dirección: </b>{{$d->direccion}}</td>
                    <td>Realizado en un {{number_format($suma,2)}}%</td>
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr>
                    <td><b>Categoria</b></td>
                    <td><b>Código - Producto</b></td>
                    <td><b>Teorico</b></td>
                    <td><b>Fisico</b></td>
                    <td><b>Diferencia</b></td>
                    <td><b>Dañado</b></td>
                </tr>
            </tbody>
            <tbody>
                @foreach($ver_inv as $vi)
                <tr>
                    <td>{{$vi->categoria}}</td>
                    <td>{{$vi->nombre_corto}} - {{$vi->nombre_fiscal}}</td>
                    <td>{{number_format($vi->existencia_teorica)}}</td>
                    <td>{{$vi->existencia_fisica}}</td>
                    @if($vi->existencia_teorica >= 0 && $vi->existencia_fisica >= 0)
                    <td >{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
					@elseif($vi->existencia_teorica > 0 && $vi->existencia_fisica < 0)
					<td >{{number_format($vi->existencia_teorica + $vi->existencia_fisica)}}</td>
					@elseif($vi->existencia_teorica < 0 && $vi->existencia_fisica < 0)
					<td >{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
					@elseif($vi->existencia_teorica < 0 && $vi->existencia_fisica >= 0)
					<td >{{number_format($vi->existencia_fisica + $vi->existencia_teorica)}}</td>
					@endif
                    <td>{{$vi->mal_estado}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
</div>
@endsection