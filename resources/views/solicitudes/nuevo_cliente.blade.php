@extends('layouts.app')
@section('content')
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
    <a class="btn btn-dark btn-sm" href="{{route('vista_clientes')}}"><i class="fas fa-arrow-left"></i> Atrás</a> 
    <div class="card">
        <div class="card-header">
            <h6>Agregar nuevo cliente al sistema</h6>
        </div>
        <div class="card-body">
            <form class="needs-validation" action="{{url('g_cliente')}}" method="post" novalidate>  
            {{csrf_field()}}
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nueva_solicitud">Cliente</label>
                    @error('cliente')
                        <span class="badge badge-pill badge-danger">No dejar el campo en blanco.</span>
                    @enderror
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-user"></b></div>
                        </div>
                        <input type="text" class="form-control" placeholder="Nombre del cliente" name="cliente" >
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                        </div>
                        <input type="number" class="form-control" placeholder="NIT" name="nit">
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-at"></i></div>
                        </div>
                        <input type="email" class="form-control" placeholder="ejemplo@correo.com" name="correo">
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-phone"></i></div>
                        </div>
                        <input type="number" class="form-control" placeholder="2230-0000" name="telefono">
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map"></b></div>
                        </div>
                        <select class="form-control" id="departamento" name="departamento" required>
                            <option value="">Seleccione un departamento</option>
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-marker-alt"></b></div>
                        </div>
                        <select class="form-control" id="municipio" name="municipio" required>
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-signs"></b></div>
                        </div>
                        <select class="form-control" id="otros" name="otros" required>
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
                        <div class="input-group-prepend">
                            <div class="input-group-text"><b class="fas fa-map-pin"></b></div>
                        </div>
                        <input type="text" class="form-control" placeholder="Dirección" name="direccion" required>
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
                    <button type="submit" class="btn btn-dark btn-block btn-sm">Guardar nuevo cliente</button>
                </div>
            </div>
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
        $.get('muni/'+id,function(data){
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
        $.get('otros/'+id,function(data){
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
