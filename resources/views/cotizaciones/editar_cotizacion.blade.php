@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.cotizacion'))
<script src="{{ asset('js/clientes.js') }}" defer></script>
<script src="{{asset('js/productoscotizacion.js')}}" defer></script>
<div class="container-fluid">
    <button type="button" style="margin-bottom: 10px" class="btn btn-info btn-sm" id="modalb" data-toggle="modal" data-target="#nuevaTransferencia">
        Crear cotización
    </button>
    <a class="btn btn-primary btn-sm" style="margin-bottom: 10px" href="{{route('print_coti',['id'=>$id,'tipo_im'=>1])}}">
        Imprimir moneda local
    </a>
    <a class="btn btn-success btn-sm" style="margin-bottom: 10px" href="{{route('print_coti',['id'=>$id,'tipo_im'=>2])}}">
        Imprimir en dólares
    </a>

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
</div>

<div id="app"><!--La equita id debe ser app, como hemos visto en app.js-->
    <editarcotizacion-component></editCotizacion-component><!--Añadimos nuestro componente vuejs-->
</div>

<div class="modal fade" id="nuevaTransferencia" aria-labelledby="nuevaTransferenciaLabel" aria-hidden="false" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva cotización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate method="post" action="{{url('crcot')}}">
                {{csrf_field()}}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label><b>Seleccione sucursal</b></label>
                            <select id="sucursales" name="sucursal" class="form-control"required>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($sucursales as $su)
                                <option value="{{$su->cod_unidad}}">{{$su->nombre}}</option>
                                @endforeach
                            </select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label><b>Código cliente</b></label>
                            <select class="form-control" id="clientes" name="cod_cliente" required style="width: 100%"></select>
                            <div class="valid-tooltip">
                                Muy bien.
                            </div>
                            <div class="invalid-tooltip">
                                No debes dejar este campo en blanco.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-info btn-block" id="guardar">Generar cotizacion</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
<script type="application/json" name="server-data">
    {{ $id }}
</script>
<script type="application/javascript">
       var json = document.getElementsByName("server-data")[0].innerHTML;
       var server_data = JSON.parse(json);
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
