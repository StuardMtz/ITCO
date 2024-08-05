@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
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

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" href="{{route('finalizadas_transf')}}">Atrás</a>
                <a class="nav-link" href="#">Integrantes de grupo</a>
                <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#nuevoIntegrante">
                    Agregar integrante
                </button>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    <li class="nav-item">
                        @if (Route::has('register'))
                            <!--<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> -->
                        @endif
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
    <h5>Integrantes en grupos de carga</h5>
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="sucursales">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Verificador de grupo</th>
                    <th>Guardar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($integrantes as $igr)
                <tr>
                <form  method="post" action="{{url('edus',$igr->id)}}">
                {{csrf_field()}}
                    <td><input type="text" class="form-control" value="{{$igr->nombre}}" name="nombre" required></td>
                    <td><select class="form-control" name="id" required>
                        <option value="{{$igr->gru}}">{{$igr->name}}</option>
                        <option value="">No asignado a grupo</option>
                        @foreach($grupos as $g)
                        @if($igr->gru == $g->id)
                        @else
                        <option value="{{$g->id}}">{{$g->name}}</option>
                        @endif
                        @endforeach
                    </select></td>
                    <td><button type="submit" class="btn btn-dark btn-sm" href="#">Guardar</button></td>
                </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="nuevoIntegrante" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar nuevo integrante</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="needs-validation" novalidate method="post" action="{{url('gnug')}}">
            {{csrf_field()}}
            <div class="form-group col-md-12">
                <label for="formGroupExampleInput">Nombre del integrante</label>
                <input type="text" class="form-control" placeholder="Nombre y Apellido" name="nombre" required></input>
                <div class="valid-tooltip">
                    Muy bien.
                </div>
                <div class="invalid-tooltip">
                    No debes dejar este campo en blanco.
                </div>
            </div>
            <div class="form-group col-md-12">
                <label for="formGroupExampleInput2">Grupo de trabajo</label>
                <select class="form-control"  name="id" required>
                    <option value="">Seleccione un grupo</option>
                    @foreach($grupos as $gr)
                    <option value="{{$gr->id}}">{{$gr->name}}</option>
                    @endforeach
                </select>
                <div class="valid-tooltip">
                    Muy bien.
                </div>
                <div class="invalid-tooltip">
                    No debes dejar este campo en blanco.
                </div>
            </div>
            <button class="btn btn-dark btn-sm btn-block" type="submit">Guardar cambios</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
    var table = $('#sucursales').DataTable({
        pageLength: 100,
        serverSide: false,
        searching: true,
        responsive: false,
        "order": [[ 0,"desc" ]],
        "language": {
        "lengthMenu": "<span class='text-paginate'>Mostrar _MENU_ registros</span>",
        "zeroRecords": "No se encontraron resultados",
        "EmptyTable":     "Ningún dato disponible en esta tabla =(",
        "info": "<span class='text-paginate'>Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros</span>",
        "infoEmty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "InfoPostFix":    "",
        "search": "<span class='text-paginate'>Buscar</span>",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "paginate": {
            "First": "Primero",
            "Last": "Último",
            "next": "Siguiente",
            "previous": "Anterior",
            },
        },

        "columnDefs": [
            { bSortable: false, targets: [0]},
            { targets: 0, searchable: true },
            { targets: [0,1], searchable: true },
            { targets: '_all', searchable: false }
        ],
    });
</script>
@endsection
