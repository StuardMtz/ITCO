@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    @foreach($tran as $t)
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('/')}}/storage/sistegualogo.png" width="125"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
        aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{url('/')}}/storage/opciones.png" width="25">
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
			    <a class="nav-link" href="{{route('bod_trasf_bod')}}">Atrás</a>
	 		    <a class="nav-link active">Editando transferencia</a>
                @if($t->id_estado == 14 || $t->id_estado == 15 || $t->id_estado == 16 )
                <button type="button" class="btn btn-dark" id="modalb" data-toggle="modal" data-target="#Transferencia">
                    Modificar estado de transferencia
                </button>
                @else
                @endif
                <a class="nav-link" href="{{route('PTran',$id)}}"><i class="fas fa-print"></i> Imprimir</a>
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
            </div>
        </div>
    </nav>

<div class="container-fluid">
    <div class="card encabezado">
        <div class="card-body">
            <ul class="list-inline text-monospace text-wrap">
                <li class="list-inline-item"><b>Número de transferencia:</b>  {{$t->num_movi}}
                <li class="list-inline-item"><b>Fecha de creación:</b> {{date('d/m/Y H:i:s','strtotime'($t->created_at))}}
                <li class="list-inline-item"><b>Vehículo:</b> {{$t->propietario}} {{$t->placa_vehiculo}}
                <li class="list-inline-item"><b>Descripción:</b> {{$t->descripcion}}
                <li class="list-inline-item"><b>Referencia:</b> {{$t->referencia}}
                <li class="list-inline-item"><b>Observación:</b> {{$t->observacion}}
                <li class="list-inline-item"><b>Comentario:</b> {{$t->comentario}}
                @if($t->id_estado == 13)
                <li class="list-inline-item" style="background:#940000;color:white;">Estado: {{$t->estado}}
                @elseif($t->id_estado == 14)
                <li class="list-inline-item" style="background:#a8e4a0;"><b>Estado:</b> {{$t->estado}}
                @elseif($t->id_estado == 15 || $t->id_estado == 16)
                <li class="list-inline-item" style="background:#dcd0ff;"> <b>Estado:</b> {{$t->estado}}
                @elseif($t->id_estado == 17)
                <li class="list-inline-item" style="background:#ffb347;"><b>Estado:</b> {{$t->estado}}
                @elseif($t->id_estado >= 18 || $t->id_estado <= 19)
                <li class="list-inline-item" style="background:#ffb347;"><b>Estado:</b> {{$t->estado}}
                @elseif($t->id_estado == 20)
                <li class="list-inline-item" style="background:#f984ef;"><b>Estado:</b> {{$t->estado}}
                @endif
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md">
            <b>Fecha para carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_paraCarga))}}
        </div>
        <div class="col-md">
            <b>Fecha de entrega:</b> {{date('d/m/Y',strtotime($t->fechaEntrega))}}
        </div>
        @if($t->fechaUno == '')
        <div class="col-md">
            <b>Preparando carga:</b>
        </div>
        @else
        <div class="col-md">
            <b>Preparando carga:</b> {{date('d/m/Y H:i:s',strtotime($t->fechaUno))}}
        </div>
        @endif
        @if($t->fecha_enCarga == '')
        <div class="col-md">
            <b>Carga preparada:</b>
        </div>
        @else
        <div class="col-md">
            <b>Carga preparada:</b> {{date('d/m/Y H:i:s',strtotime($t->fecha_enCarga))}}
        </div>
        @endif
    </div>
    @endforeach

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
    <div class="table-responsive-sm">
        <table class="table table-sm table-borderless" id="existencia">
            <thead >
                <tr>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Peso Kg.</th>
                    <th>Volumen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $pro)
                @if($pro->id == null)
                @else
                <tr>
                    <td>{{$pro->nombre}}</td>
                    <td>{{utf8_encode($pro->nombre_corto)}}</td>
                    <td>{{$pro->nombre_fiscal}}</td>
                    <td>{{number_format($pro->cantidadSolicitada,0)}}</td>
                    @if($pro->cantidad1 == NULL)
                    <td>{{number_format($pro->peso,2)}}</td>
                    <td>{{number_format($pro->volumen,2)}}</td>
                    @else
                    <td>{{number_format($pro->peso2,2)}}</td>
                    <td>{{number_format($pro->volumen2)}}</td>
                    @endif
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="modal fade" id="Transferencia" tabindex="-1" aria-labelledby="TransferenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Transferencia">Cambiar el estado de la transferencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{url('editransbod',$id)}}">
                {{csrf_field()}}
                <label for="EstadodeTransferencia"><b>Estado de la transferencia</b></label>
                    <div class="input-group">
                        <select class="form-control" name="estado" required>
                            @foreach($estados as $e)
                            <option value="{{$e->id}}">{{$e->nombre}}</option>
                            @endforeach
                        </select>
                        <div class="input-group-addend">
                            <button type="submit" class="btn btn-success">Guardar</button>
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
<script>
var table = $('#existencia').DataTable({
    pageLength: 50,
    serverSide: false,
    "order": [[ 0,"asc"]],
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
        { targets: 1, searchable: true },
        { targets: [0,1,2,3], searchable: true },
        { targets: '_all', searchable: false },
    ],
    "footerCallback": function(row,data,start,end,display){
        var api = this.api(), data;
        var intVal = function(i){
            return typeof i == 'string' ?
            i.replace(/[\$,]/g, '')*1 :
            typeof i == 'number' ?
            i:0;
        };
        var kilosTotal = api
        .column(4)
        .data()
        .reduce(function (a,b){
            return intVal(a) + intVal(b);
        }, 0);
        var volumenTotal = api
        .column(5)
        .data()
        .reduce(function (a,b){
            return intVal(a) + intVal(b);
        }, 0);
        var numFormat = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
        numFormat(kilosTotal);
        var Format = $.fn.dataTable.render.number( '\,', '.', 2 ).display;
        Format(volumenTotal);
        pageTotal = api.column(5,{page: 'current'})
        .data()
        .reduce( function (a,b){
            return intVal(a) + intVal(b);
        }, 0);
        $(api.column(0).footer() ).html('Totales');
        $(api.column(4).footer()).html(Format(kilosTotal / 1000)+' Toneladas');
        $(api.column(5).footer()).html(numFormat(volumenTotal)+' Volumen');
    },
});
</script>
@endsection
