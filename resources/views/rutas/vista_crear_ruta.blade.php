@extends('layouts.app')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
<script src="{{asset('js/jquery.js')}}"></script>
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
    <h4>Nueva Ruta</h4>
    <a class="btn btn-dark" href="{{ route('v_camiones')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
    <div class="row justify-content-md-center">
        {!! Form::model($rut, ['method'=>'PATCH','route'=>['a_fecha', $rut->id,$ruta]]) !!}
        <form>
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Fecha a realizar la ruta</div>
                    </div>
                    <input type="date" class="form-control" name="fecha_entrega" value="{{$rut->fecha_entrega}}">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fas fa-save"></i></button>
                </div>
            </div>
        </form>
        {!! Form::close() !!}
    </div>
    <hr>
    <div class="row">
        <div class="col">
            <div class="table-condensed">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Sucursal</th>
                            <th>Fecha de Solicitud</th>
                            <th>Ubicación</th>
                            <th>Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $s)
                        <tr>
                            <td><a class="fas fa-check-square" href="{{route('a_e_ruta',array('id'=>$s->id,'ruta'=>$ruta))}}"></a>{{$s->comprobante}}</td>
                            <td>{{$s->usuario->name}}</td>
                            <td>{{date('d-m-Y',strtotime($s->created_at))}}</td>
                            <td>{{$s->departamento->nombre}} - {{$s->municipio->nombre}} - {{$s->otros->nombre}}</td>
                            <td><button  class="fas fa-info-circle" data-toggle="modal" id="button" value="{{$s->id}}" data-target="#ver_info"></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col">
            <div class="table-condensed">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Sucursal</th>
                            <th>Fecha de Solicitud</th>
                            <th>Ubicación</th>
                            <th>Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($soli_agregadas as $sa)
                        <tr>
                            <td><a class="fas fa-times-circle" href="{{route('e_e_ruta',array('id'=>$sa->id,'ruta'=>$ruta))}}"></a>{{$sa->comprobante}}</td>
                            <td>{{$sa->usuario->name}}</td>
                            <td>{{date('d-m-Y',strtotime($sa->created_at))}}</td>
                            <td>{{$sa->departamento->nombre}} - {{$sa->municipio->nombre}} - {{$sa->otros->nombre}}</td>
                            <td><button  class="fas fa-info-circle" data-toggle="modal" id="button" value="{{$sa->id}}" data-target="#ver_info"></button></td>
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
                <div class="modal-body"></div>
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