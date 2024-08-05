@extends('layouts.app')
@section('content')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<style type="text/css">
    @media print{
        .oculto-impresion, .oculto-impresion *{
        display: none !important;
        }
    }
</style>

<div class="container-fluid">
    <a class="btn btn-dark" href="{{ route('ver_d', $id) }}"><i class="fas fa-undo-alt"></i> Atrás</a>
    <a class="btn btn-dark" href="{{ route('pdf_dif_neg',array('id'=>$id))}}"><i class="fas fa-print"></i> PDF</a>
    <h1>Inventario número {{$id}}</h1>
    <div class="table-condensed">
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
                    <td colspan="5"><b>Dirección: </b>{{$d->direccion}}</td>
                    @if($suma > 100)
                    <td>Realizado en un 100%</td>
                    @else
                    <td>Realizado en un {{number_format($suma)}}%</td>
                    @endif
                    @if($d->estado == 'En proceso')
                    <td>{!! Form::open(['method' => 'get','route' => ['fin', $id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Finalizar', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}</td>
                    @else
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tbody>
                <tr>
                    <td><b>Categoria</b></td>
                    <td><b>Código - Producto</b></td>
                    <td><b>Diferencia</b><a href="{{route('ver_d_m',$id)}}" data-toggle="tooltip" data-placement="top" title="Diferencias Positivas"><i class="fas fa-plus"></i></a></td>
                    <td><b>Teorico</b></td>
                    <td><b>Fisico</b></td>
                    <td><b>Dañado</b></td>
                    <td></td>
                </tr>
            </tbody>
            <tbody>
                @foreach($ver_inv as $vi)
                @if($vi->existencia_teorica - $vi->existencia_fisica > 0 || $vi->existencia_teorica + $vi->existencia_fisica < 0)
                <tr>
                    <td>{{$vi->categoria}}</td>
                    <td>{{$vi->nombre_corto}} - {{$vi->nombre_fiscal}}</td>
                @if($vi->existencia_teorica >= 0 && $vi->existencia_fisica >= 0)
							@if($vi->existencia_fisica - $vi->existencia_teorica > 0)
							<td style="background-color:#B6E1A4;">{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
							@elseif($vi->existencia_fisica - $vi->existencia_teorica < 0)
							<td style="background-color: #DE7976;">{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
							@else
							<td>{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
							@endif
					@elseif($vi->existencia_teorica > 0 && $vi->existencia_fisica < 0)
							@if($vi->existencia_teorica + $vi->existencia_fisica > 0)
							<td style="background-color:#B6E1A4;">{{number_format($vi->existencia_teorica + $vi->existencia_fisica)}}</td>
							@elseif($vi->existencia_teorica + $vi->existencia_fisica < 0)
							<td style="background-color: #DE7976;">{{number_format($vi->existencia_teorica + $vi->existencia_fisica)}}</td>
							@else
							<td>{{number_format($vi->existencia_teorica + $vi->existencia_fisica)}}</td>
							@endif
					@elseif($vi->existencia_teorica < 0 && $vi->existencia_fisica < 0)
						@if($vi->existencia_fisica - $vi->existencia_teorica > 0)
                       <td style="background-color:#B6E1A4;">{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
                       @elseif($vi->existencia_fisica - $vi->existencia_teorica < 0)
                       <td style="background-color: #DE7976;">{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
                       @else
                       <td>{{number_format($vi->existencia_fisica - $vi->existencia_teorica)}}</td>
                       @endif
					@elseif($vi->existencia_teorica < 0 && $vi->existencia_fisica >= 0)
						@if($vi->existencia_fisica + $vi->existencia_teorica > 0)
                       <td style="background-color:#B6E1A4;">{{number_format($vi->existencia_fisica + $vi->existencia_teorica)}}</td>
                       @elseif($vi->existencia_fisica + $vi->existencia_teorica < 0)
                       <td style="background-color: #DE7976;">{{number_format($vi->existencia_fisica + $vi->existencia_teorica)}}</td>
                       @else
                       <td>{{number_format($vi->existencia_fisica + $vi->existencia_teorica)}}</td>
                       @endif
					@endif
                    <td>{{number_format($vi->existencia_teorica)}}</td>
                    <td>{{number_format($vi->existencia_fisica)}}</td>
                    <td>{{$vi->mal_estado}}</td>
                    <td class="oculto-impresion"><a type="button" class="btn btn-dark btn-sm" href="{{ route('finven',array('cod'=>$vi->cod_producto,'id'=>$id))}}">Ver</a></td>
                </tr>
                @else
                @endif
                @endforeach
            </tbody>
        </table>
    </div> 
</div>
@endsection