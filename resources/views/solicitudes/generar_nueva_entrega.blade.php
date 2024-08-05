@extends('layouts.app')
@section('content')
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

    <a class="btn btn-dark btn-sm" href="{{route('vista_clientes')}}"><i class="fas fa-arrow-left"></i>  Atrás</a>
    <div class="card">
        <div class="card-header">
            <h6>Solicitud para generar una nueva entrega</h6>
        </div> 
        <div class="card-body">
            <form class="needs-validation" action="{{url('g_solicitud',$id)}}" method="post" novalidate>
            {{csrf_field()}}
                @foreach($cliente as $cl)
                <blockquote class="blockquote text-center">
                    <p class="mb-0">Información del cliente</p>
                </blockquote>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nombre del cliente</label>
                        <div class="input-group"> 
                            <input type="text" class="form-control" value="{{$cl->nombre}}" disabled>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Número de NIT del cliente</label>
                        <div class="input-group">
                            <input type="number" class="form-control" value="{{$cl->nit}}" disabled>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Correo del cliente</label>
                        <div class="input-group">
                            <input type="email" class="form-control" value="{{$cl->correo}}" disabled>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Teléfono del cliente</label>
                        <div class="input-group">
                            <input type="number" class="form-control" value="{{$cl->telefono}}" disabled>
                        </div>
                    </div>
                </div>
                <hr>
                <blockquote class="blockquote text-center">
                    <p class="mb-0">Ingrese los detalles necesarios para realizar la entrega</p>
                </blockquote>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Departamento de entrega</label>
                        @error('departamento')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <select class="form-control" id="departamento" name="departamento" required>
                                <option value="{{$cl->id_departamento}}">{{$cl->departamento}}</option>
                                @foreach($departamentos as $d)
                                <option value="{{$d->id}}">{{$d->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Municipio de entrega</label>
                        @error('municipio')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <select class="form-control" id="municipio" name="municipio" required>
                                <option value="{{$cl->id_municipio}}">{{$cl->municipio}}</option>
                                <option></option>
                            </select>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Aldea / caserio de entrega</label>
                        @error('otros')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <select class="form-control" id="otros" name="otros" required>
                                <option value="{{$cl->id_aldea}}">{{$cl->aldea}}</option>
                                <option></option>
                            </select>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Dirección de entrega</label>
                        @error('direccion')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <input type="text" class="form-control" id="direccion" placeholder="Dirección" name="direccion" value="{{$cl->direccion}}" required>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Detalles opcionales para la entrega</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="detalle_direccion" placeholder="Edificio, apartamento, número de bodega, u otro detalle adicional que ayude a ubicar el lugar de entrega" 
                            name="detalle_direccion"> 
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Número de comprobante para la entrega</label>
                        @error('comprobante')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <input type="text" class="form-control" id="comprobante" placeholder="Comprobante" name="comprobante" required>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form group col-md-6">
                        <label>Sucursal que realizara la entrega al cliente</label>
                        @error('sucursal')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <select class="form-control" id="sucursal" name="sucursal" required>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($sucursales as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="nueva_solicitud">Detalles de Entrega</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="detalle_entrega" placeholder="Horario de atención, solicitud o entrega de documentos por parte del piloto, teléfono adicional, u otro dato que se deba considerar." name="detalle_entrega">
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nueva_solicitud">Fecha de Entrega</label>
                        @error('fecha_entrega')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <input type="date" class="form-control" name="fecha_entrega" required>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nueva_solicitud">Hora Aproximada</label>
                        @error('hora')
                            <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                        @enderror
                        <div class="input-group">
                            <input type="time" class="form-control" name="hora" required>
                            <div class="valid-tooltip">
                                Bien!
                            </div>
                            <div class="invalid-tooltip">
                                No dejar el campo en blanco.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-block btn-sm btn-dark"> Continuar</button> 
                </div>
                @endforeach
            </form>
        </div>
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
<script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
@endsection
