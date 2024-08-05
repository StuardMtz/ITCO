@extends('layouts.app')
@section('content')
<script src="{{asset('js/productosmanual.js')}}" defer></script>
<div class="container-fluid">
  <a class="btn btn-dark btn-sm" href="{{route('productos_inventario',$encabezado)}}"><i class="fas fa-arrow-left"></i> Atrás</a>
  <blockquote class="blockquote text-center">
    <p class="mb-0">Inventario #{{$encabezado}}</p>
  </blockquote>
  <!--{!! Form::open(['method'=>'post','route'=>['agrePro',$encabezado]]) !!}
    {{csrf_field()}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <select id="producto" name="producto" class="custom-select" required></select>
        <button class="btn btn-secondary btn-sm" type="submit">Agregar</button>
    </nav>
  {!! Form::close() !!}-->
  <br>
  <div class="row" id="app">
    <cuadrado-component></cuadrado-component><!--Añadimos nuestro componente vuejs-->
  </div>  
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
<script type="application/json" name="server-data">
    {{ $encabezado }}
</script>
<script type="application/javascript">
       var json = document.getElementsByName("server-data")[0].innerHTML;
       var server_data = JSON.parse(json);
</script>
<script>
var table = $('#inventario').DataTable({
  processing: true,
  serverSide: false,
  pageLength: 100,
  searching: true,
  scrollX: true,
  scrollY: '55vh',
  scrollCollapse: true,
  paging: true,
  stateSave: true,
  "stateDuration": 300,
  ajax:{
    url: "{{route('datos_existencia_cero',$encabezado)}}",
    dataSrc: "data",
  },
  "order": [[ 0,"asc" ]],
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
        columns: [
                { data: 'categoria', name: 'categoria'},
                { data: 'nombre_corto', name: 'nombre_corto'},
                { data: 'nombre_fiscal', name:'nombre_fiscal'},
                { data: 'existencia_teorica', name:'existencia_teorica', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'existencia_fisica',name:'existencia_fisica', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'diferencias', name: 'diferencias', render: $.fn.dataTable.render.number(',','.',0 )},
                { data: 'mal_estado', name: 'mal_estado'}
                ],
        "columnDefs": [
        { targets: 1, searchable: true },
        { targets: [0,1,2,3,4], searchable: true },
        { targets: '_all', searchable: false },
    ],
    });

    $('#inventario').on('click','tbody tr', function(){
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      var redirectWindow = window.open(url_global+'/agrexisin/'+ row.data().id, '_blank');
      redirectWindow.location;
    });
    setInterval( function () {
    table.ajax.reload();
}, 90000 );
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
