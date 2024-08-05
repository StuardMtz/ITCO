@extends('layouts.app')
@section('content')
<link href="{{asset('css/asesores_form.css')}}" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script> 
<script>
    var url_global='{{url("/")}}';
</script>
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
    <a class="btn btn-dark btn-sm" href="{{route('vista_asesores')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="card">
        <div class="card-header">
            Editar Entrega
        </div>
        {!! Form::model($solicitudes, ['method'=>'PATCH','route'=>['guaSol', $solicitudes->id]]) !!}
        <form>
            <div class="form-row">
            <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Cliene</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-user"></b></div>
                        </div>
                        <input type="text" class="form-control" value="{{$solicitudes->cliente->nombre}}" disabled>
                        <input type="text" class="form-control" id="cliente" placeholder="Cliente" name="cliente" value="{{$solicitudes->id_cliente}}" style="display:none;">
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">NIT</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                        </div>
                        <input type="number" class="form-control" value="{{$solicitudes->cliente->nit}}" disabled>
                    </div>
                </div>
            </div>
            <div class="form-row">
            <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Correo</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-at"></i></div>
                        </div>
                        <input type="email" class="form-control" value="{{$solicitudes->cliente->correo}}" disabled>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Teléfono</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-phone"></i></div>
                        </div>
                        <input type="number" class="form-control" value="{{$solicitudes->cliente->telefono}}" disabled>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-row">
            <div class="form-grup col-md-6">
                    <label for="nueva_solicitud_soliitud">Departamento</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="departamento" name="departamento" required>
                            <option value="{{$solicitudes->id_departamento}}">{{$solicitudes->departamento->nombre}}</option>
                            @foreach($departamentos as $d)
                            <option value="{{$d->id}}">{{$d->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Municipio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-marker-alt"></b></div>
                        </div>
                        <select class="form-control" id="municipio" name="municipio" required>
                            <option value="{{$solicitudes->id_municipio}}">{{$solicitudes->municipio->nombre}}</option>
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
            <div class="form-group col-md-6">
                    <label for="nueva_solicitud_soliitud">Aldea/Caserio/Otros</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-signs"></b></div>
                        </div>
                        <select class="form-control" id="otros" name="otros" required>
                            <option value="{{$solicitudes->id_otros}}">{{$solicitudes->otros->nombre}}</option>
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Dirección</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-pin"></b></div>
                        </div>
                        <input type="text" class="form-control" id="direccion" placeholder="Dirección" name="direccion" value="{{$solicitudes->direccion}}" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
            <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Comprobante</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-receipt"></i></div>
                        </div>
                        <input type="text" class="form-control" id="comprobante" placeholder="Comprobante" value="{{$solicitudes->comprobante}}" name="comprobante" required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Solicitar a Sucursal</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="sucursal" name="sucursal" required>
                            <option value="{{$solicitudes->id_sucursal}}">{{$solicitudes->sucursal->nombre}}</option>
                            @foreach($sucursales as $s)
                            <option value="{{$s->id}}">{{$s->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
            <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Fecha de Entrega</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    <input type="date" class="form-control" id="fecha_entrega" placeholder="Fecha" name="fecha_entrega" value="{{$solicitudes->fecha_entrega}}" required>
                </div>
            </div>
            <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Hora Aproximada</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-clock"></i></div>
                        </div>
                    <input type="time" class="form-control" id="hora" placeholder="Hora" name="hora" value="{{$solicitudes->hora}}">
                </div>
            </div>
        </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                <label for="nueva_solicitud">Detalles Dirección</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-comment-alt"></i></div>
                        </div>
                        <input type="text" class="form-control" id="detalle_direccion" placeholder="Edificio, Apartamento, 
                        Número de Bodega, u otro detalle adicional que ayude a ubicar el lugar de entrega" 
                        name="detalle_direccion" value="{{$solicitudes->detalle_direccion}}">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fas fa-comment-alt"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
            <div class="form-group col-md-12">
                    <label for="nueva_solicitud">Detalles de Entrega</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-comment-alt"></i></div>
                        </div>
                        <input type="text" class="form-control" id="detalle_entrega" placeholder="Horario de atención, solicitud o entrega de documentos por 
                        parte del piloto, teléfono adicional, u otro dato que se deba considerar." name="detalle_entrega" value="{{$solicitudes->detalle_entrega}}">
                    </div>
                </div>
            </div>
        
        <div class="row justify-content-md-center">
            <button type="submit" class="btn btn-block" id="boton"><b class="fas fa-angle-double-right"> Continuar</b></button> 
        </div>
    </form>
    {!! Form::close() !!}
</div>
  <script>
  $(function(){
    $('#departamento').on('change', onSelectDepartamentoChange);
  });
  function onSelectDepartamentoChange(){
    var id = $(this).val();
    if(! id){
      $('#municipio').html('<option value ="">Seleccione una opcion</option>');
      return;
    };
    $.get(url_global+'/muni/'+id,function(data){
      var html_select = '<option value ="">Seleccione una opcion</option>';
      for (var i=0; i<data.length; ++i)
      html_select += '<option value="'+data[i].id+'">'+data[i].nombre+'</option>';
      $('#municipio').html(html_select);
    });
  }
  $(function(){
    $('#municipio').on('change', onSelectMunicipioChange);
  });
  function onSelectMunicipioChange(){
    var id = $(this).val();
    if(! id){
      $('#otros').html('<option value ="">Seleccione una opcion</option>');
      return;
    };
    $.get(url_global+'/otros/'+id,function(data){
      var html_select = '<option value ="">Seleccione una opcion</option>';
      for (var i=0; i<data.length; ++i)
      html_select += '<option value="'+data[i].id+'">'+data[i].nombre+'</option>';
      $('#otros').html(html_select);
    });
  }
  </script>
@endsection