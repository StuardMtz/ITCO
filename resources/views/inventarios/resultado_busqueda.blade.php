@extends('layouts.app2')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link href="{{asset('css/css2/select2.css')}}" rel="stylesheet">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/busqueda_inventario.js') }}" defer></script>
<script src="{{ asset('js/DataTables-1.10.20/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/DataTables-1.10.20/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{asset('js/select2/select2.js')}}"></script>
@section('content')
<div class="container-fluid">
  <a class="btn btn-dark" href="{{route('productos_inventario',$id)}}"><i class="fas fa-arrow-left"></i> Atrás</a>
  <h5>Inventario número {{$id}}</h5>
  <div class="card flotante">
    <form method="get" action="{{route('resultado_busqueda',$id)}}">
      <div class="form-row">
        <div class="form-gruop col-md-10">
          <select id="producto" name="producto" class="form-control">
          </select>
        </div>
        <div class="form-group col-md-2">
          <div class="input-group-append">
            <button class="btn btn-success btn-sm" type="submit"><i class="fas fa-search"></i> Buscar...</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="table-responsive-sm">
    <table class="table table-sm" id="inventario" style="width:100%">
      <thead>
        <tr>
          <th>Categoria</th>
          <th>Código Producto</th>
          <th>Nombre</th>
          <th>Teorico</th>
          <th>Fisico</th>
          <th>Diferencia</th>
          <th>Dañado</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
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
    url: "{{route('datos_resultado_busqueda',['id'=>$id,'categoria'=>$categoria])}}",
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
}, 20000 );
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
