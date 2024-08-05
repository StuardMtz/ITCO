@extends('layouts.app')
@section('content')
<link href="{{asset('css/solicitud.css')}}" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
  <a class="btn btn-dark btn-block" href="{{ route('p_inicio')}}"><i class="fas fa-undo-alt"></i> Atrás</a>
  <hr>
  <div class="row">
    <div class="col">
      <div class="table-condensed">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Comprobante</th>
              <th>Cliente</th>
              <th>Editar</th>
              <th>Info</th>
            </tr>
          </thead>
          <tbody>
            @foreach($entregas as $en)
            <tr>
              <td>{{$en->comprobante}}</td>
              @if($en->id_departamento)
              <td>{{$en->cliente->nombre}}</td>
              @else
              <td>{{$en->sucur->name}}</td>
              @endif
              <td><a class="btn btn-danger" href="{{route('v_ed_entrega',$en->id)}}">Editar</a></td>
              <td><button class="btn btn-success"   data-toggle="modal" id="button" value="{{$en->id}}" data-target="#ver_info"><i class="fas fa-info-circle" >Info</i></button></td>
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
