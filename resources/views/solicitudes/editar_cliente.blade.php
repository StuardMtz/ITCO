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
    <a class="btn btn-dark btn-sm" href="{{route('vista_clientes')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="card">
        <div class="card-header">
            <h6>Editar información del cliente</h6>
        </div>
        <div class="card-body">
            <form class="needs-validation" action="{{url('ediclien',$id)}}" method="post" novalidate>
            {{csrf_field()}}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Cliente</label>
                    @error('cliente')
                        <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                    @enderror
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Nombre del cliente" name="cliente" value="{{$cliente->nombre}}" >
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">NIT</label>
                    <div class="input-group">
                        <input type="number" class="form-control" placeholder="NIT" name="nit" value="{{$cliente->nit}}">
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-goup col-md-6">
                    <label for="nueva_solicitud">Correo</label>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="ejemplo@correo.com" name="correo" value="{{$cliente->correo}}">
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Teléfono</label>
                    <div class="input-group">
                        <input type="number" class="form-control" placeholder="2230-0000" name="telefono" value="{{$cliente->telefono}}">
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud_soliitud">Departamento</label>
                    @error('departamento')
                        <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                    @enderror
                    <div class="input-group">
                        <select class="form-control" id="departamento" name="departamento" required>
                            <option value="{{$cliente->id_departamento}}">{{$cliente->departamento->nombre}}</option>
                            @foreach($departamentos as $d)
                            <option value="{{$d->id}}">{{$d->nombre}}</option>
                            @endforeach
                        </select>
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Municipio</label>
                    @error('municipio')
                        <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                    @enderror
                    <div class="input-group">
                        <select class="form-control" id="municipio" name="municipio" required>
                            <option value="{{$cliente->id_municipio}}">{{$cliente->municipio->nombre}}</option>
                            <option></option>
                        </select>
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud_soliitud">Aldea/Caserio/Otros</label>
                    @error('otros')
                        <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                    @enderror
                    <div class="input-group">
                        <select class="form-control" id="otros" name="otros" required>
                            <option value="{{$cliente->id_otros}}">{{$cliente->otros->nombre}}</option>
                            <option></option>
                        </select>
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Dirección</label>
                    @error('direccion')
                        <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                    @enderror
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Dirección" name="direccion" value="{{$cliente->direccion}}" required>
                        <div class="valid-tooltip">
                            Bien!
                        </div>
                        <div class="invalid-tooltip">
                            No puede dejar este campo en blanco!
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn-dark btn-block btn-sm">Guardar cambios</button>
                </div>
            </div>
        </form>
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