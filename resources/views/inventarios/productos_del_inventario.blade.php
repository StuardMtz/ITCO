@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <a class="btn btn-dark btn-sm"  onclick="window.close();" style="color:white;"><i class="fas fa-arrow-left"></i> Atrás</a>
  <a class="btn btn-warning btn-sm" href="{{ route('existencia_cero',['id'=>$datos->id])}}">Productos cuadrados</a>

  <blockquote class="blockquote text-center">
    <p class="mb-0">Productos sin contar, inventario #{{$datos->id}}</p>
  </blockquote>

  <div class="row" id="app">
    <inventario-component></inventario-component><!--Añadimos nuestro componente vuejs-->
  </div>
</div>  
<script src="{{ asset('js/app.js') }}" defer></script>
<script type="application/json" name="server-data">
    {{ $numero }}
</script>
<script type="application/javascript">
       var json = document.getElementsByName("server-data")[0].innerHTML;
       var server_data = JSON.parse(json);
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
