
<div class="container-fluid">
  <div class="row">
  </div>
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="card">
        <ul class="list-group list-group-flush">
          @if($envio->id_departamento != '')
          <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$envio->cliente->nombre}}</li>
          <li class="list-group-item"><i class="fas fa-map"></i><b> Departamento:</b> {{$envio->departamento->nombre}}</li>
          <li class="list-group-item"><i class="fas fa-map-marker"></i><b> Municipio:</b> {{$envio->municipio->nombre}}</li>
          <li class="list-group-item"><i class="fas fa-map-signs"></i><b> Aldea/Caserio/Otros:</b> {{$envio->otros->nombre}}</li>
          <li class="list-group-item"><i class="fas fa-map-pin"></i><b> Dirección:</b> {{$envio->direccion}}</li>
          @else
          <li class="list-group-item"><i class="fas fa-user"></i><b> Cliente:</b> {{$envio->sucur->name}}</li>
          @endif
          <li class="list-group-item"><i class="fas fa-file-alt"></i><b> Comprobante:</b> {{$envio->comprobante}}</li>
          <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Entrega:</b> {{$envio->detalle_entrega}}</li>
          <li class="list-group-item"><i class="fas fa-info-circle"></i><b> Detalle Dirección:</b> {{$envio->detalle_direccion}}</li>
          @if($envio->fecha_entrega == '')
          @else
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Solicitud:</b> {{date('d-m-Y',strtotime($envio->fecha_entrega))}}</li>
          <li class="list-group-item"><i class="fas fa-clock"></i><b> Hora Sugerida:</b> {{$envio->hora}}</li>
          @endif
          <li class="list-group-item"><i class="fas fa-exclamation-circle"></i><b> Estado:</b> {{$envio->estado->nombre}}</li>
          @if($envio->fecha_carga != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Carga:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_carga))}}</li>
          @endif
          @if($envio->fecha_parqueo != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha Parqueo:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_parqueo))}}</li>
          @endif
          @if($envio->fecha_ruta != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha Ruta:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_ruta))}}</li>
          @endif
          @if($envio->fecha_destino != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Destino:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_destino))}}</li>
          @endif
          @if($envio->fecha_descarga != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Descarga:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_descarga))}}</li>
          @endif
          @if($envio->fecha_entregado != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> Fecha de Entrega:</b> {{date('d-m-Y H:i',strtotime($envio->fecha_entregado))}}</li>
          @endif
          @if($envio->id_camion != '')
          <li class="list-group-item"><i class="fas fa-calendar-alt"></i><b> En Camion:</b> {{$envio->camion->placa}}</li>
          @endif
          <li class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{number_format($envio->estado->porcentaje)}}%" ></div>
          </li>
        </ul>
    </div>
  </div>
</div>
