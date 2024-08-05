@extends('layouts.app')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
<div class="container">
    @if($message= Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message}}</p>
    </div>
    @endif
    @if($message= Session::get('error'))
    <div class="alert alert-danger">
        <p>{{ $message}}</p>
    </div>
    @endif
</div>
<div class="container-fluid">
    <div class="card">
        <ul class="list-group list-group-flush">
            @if($envio->id_departamento != '')
            <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$envio->cliente->nombre}}</li>
            <li class="list-group-item"><i class="fas fa-map"></i><b> Departamento:</b> {{$envio->departamento->nombre}} - <b>Municipio:</b> {{$envio->municipio->nombre}} - <b> Aldea/Caserio/Otros:</b> {{$envio->otros->nombre}} - <b> Dirección:</b> {{$envio->direccion}}</li>
            <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Dirección:</b> {{$envio->detalle_direccion}}</li>
            <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Entrega:</b> {{$envio->detalle_entrega}}</li>
            @else
            <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$envio->sucur->name}}</li>
            @endif
            <li class="list-group-item"><i class="fas fa-file-alt"></i><b> Comprobante:</b> {{$envio->comprobante}}</li>
            @if($envio->fecha_entrega == '')
            @else
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Entrega:</b> {{date('d-m-Y',strtotime($envio->fecha_entrega))}}</li>
            <li class="list-group-item"><i class="fas fa-clock"></i><b> Hora Sugerida:</b> {{$envio->hora}}</li>
            @endif
            <li class="list-group-item"><i class="fas fa-exclamation-circle"></i><b> Estado:</b> {{$envio->estado->nombre}}</li>
            @if($envio->fecha_carga != '')
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Carga:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_carga))}}</li>
            @endif
            @if($envio->fecha_parqueo != '')
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha Ruta:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_parqueo))}}</li>
            @endif
            @if($envio->fecha_ruta != '')
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha a Destino:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_ruta))}}</li>
            @endif
            @if($envio->fecha_destino != '')
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha en Destino:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_destino))}}</li>
            @endif
            @if($envio->fecha_descarga != '')
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Descarga:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_descarga))}}</li>
            @endif
            @if($envio->fecha_entregado != '')
            <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Entrega:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_entregado))}}</li>
            <li class="list-group-item"><b> Tiempo total de la entrega: </b> {{$total}} minutos</li>
          @endif
          @if($envio->id_camion != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Placa Camión:</b> {{$envio->camion->placa}}</li>
          @endif
          <li class="list-group-item">
              <b>Progreso de la Solicitud {{number_format($envio->estado->porcentaje)}}%</b>
            </li>
            <li class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{number_format($envio->estado->porcentaje)}}%"></div>
          </li>
        </ul>
    </div>
  </div>
<hr>
@if($envio->fecha_carga!='')
  <a  class="btn  btn-primary btn-block" href="{{route('bit',$envio->id)}}"><i class="fas fa-user-plus"></i> Historial de Entrega</a>
  <a  class="btn  btn-warning btn-block" href="{{route('map',$envio->id)}}"><i class="fas fa-user-plus"></i> Ubicación de Entrega</a>
@else
@endif
@endsection
