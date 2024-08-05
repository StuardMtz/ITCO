@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <a class="btn btn-dark btn-sm" href="{{ route('entregas_en_espera')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
    <div class="row justify-content-md-center">
        <form method="post" action="{{url('a_fecha',$id)}}">
        {{csrf_field()}}
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Ingrese la fecha en que debe realizarce la ruta</div> 
                    </div>
                    <input type="date" class="form-control" name="fecha_entrega" value="{{$rut->fecha_entrega}}" required>
                    <button class="btn btn-outline-dark btn-sm" type="submit"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </div>
        </form>
    </div>
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
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive-sm">
                <table class="table table-sm table-borderless">
                    <thead>
                        <tr>
                            <th colspan="6" style="text-align:center;">Solicitudes pendientes de agregar a ruta</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Comprobante</th>
                            <th>Sucursal</th>
                            <th>Fecha de solicitud</th>
                            <th>Ubicación</th>
                            <th>Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $s)
                        <tr>
                            <td><a class="btn btn-warning btn-sm" href="{{route('a_e_ruta',array('id'=>$s->id,'ruta'=>$id))}}">Agregar</a></td>
                            <td>{{$s->comprobante}}</td>
                            <td>{{$s->usuario->name}}</td>
                            <td>{{date('d-m-Y',strtotime($s->created_at))}}</td>
                            <td>{{$s->departamento->nombre}} - {{$s->municipio->nombre}} - {{$s->otros->nombre}}</td>
                            <td><button  class="btn btn-dark btn-sm" data-toggle="modal" id="button" value="{{$s->id}}" data-target="#ver_info"></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive-sm">
                <table class="table table-sm table-borderless">
                    <thead>
                        <tr>
                            <th colspan="6" style="text-align:center;">Solicitudes en ruta</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Comprobante</th>
                            <th>Sucursal</th>
                            <th>Fecha de solicitud</th>
                            <th>Ubiacación</th>
                            <th>Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($soli_agregadas as $sa)
                        <tr>
                            <td><a class="btn btn-danger btn-sm" href="{{route('e_e_ruta',array('id'=>$sa->id,'ruta'=>$id))}}">Eliminar</a></th>
                            <td>{{$sa->comprobante}}</td>
                            <td>{{$sa->usuario->name}}</td>
                            <td>{{date('d-m-Y',strtotime($sa->created_at))}}</td>
                            <td>{{$sa->departamento->nombre}} - {{$sa->municipio->nombre}} - {{$sa->otros->nombre}}</td>
                            <td><button class="btn btn-dark btn-sm" data-toggle="modal" id="button" value="{{$sa->id}}" data-target="#ver_info"></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="ver_info" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script >
     $(document).on("click","#button", function(){
        id = $(this).val();
        $.ajax({
            url: 'p/'+id,
            type:"get",
            dataType:"html",
            data:{id:id},
          }).done(function(data) {
   $("#ver_info .modal-body").html(data);
});
    });
</script>
<script>
window.onload=function(){
var pos=window.name || 0;
window.scrollTo(0,pos);
}
window.onunload=function(){
window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
}
</script>
@endsection
