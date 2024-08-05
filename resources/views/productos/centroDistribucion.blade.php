@extends('layouts.app2')
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
<link href="{{ asset('css/DataTables-1.10.20/css/dataTables.bootstrap4.min.css')}}" type="text/css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('js/Buttons-1.6.1/css/buttons.bootstrap4.min.css') }}">
<script>
    var url_global='{{url("/")}}';
</script>
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{ asset('js/busqueda_inventario.js') }}" defer></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@section('content')
<div class="container-fluid">
  <a class="btn btn-dark" href="{{route('home')}}"><i class="fas fa-arrow-left"></i> Atrás</a>
  <h4>Productos sin Contar</h4>
  <h5>Inventario número {{$id}}</h5>
  <div class="flotante">
    {!! Form::open(['method'=>'get','route'=>['pro', $id]]) !!}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <select id="producto" name="producto[]" class="custom-select"></select>
      <button class="btn btn-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
      <input name="suma" value="{{$suma}}" style="display:none;">
      <a class="btn btn-info btn-sm" href="{{ route('cero',['id'=>$id,'suma'=>$suma])}}">En cero</a>
    {!! Form::close() !!}
    </nav>
    <div class="progress">
      @if($suma > 100)
      <div class="progress-bar" role="progressbar" style="width: {{$suma}}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><b>100%</b></div>
      @else
      <div class="progress-bar" role="progress-bar progress-bar-striped" style="width: {{$suma}}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><b>{{number_format($suma,2)}}%</b></div>
      @endif
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-sm" id="inventario">
      <thead>
        <tr>
          <th>Categoria</th>
          <th>Código Producto</th>
          <th>Nombre</th>
          <th>Teorico</th>
          <th>Fisico</th>
          <th>Diferencia</th>
          <th>Dañado</th>
          <th>Carga</th>
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
        ajax:{
            url: "{{route('dat_cd_inve',$id)}}",
            dataSrc: "data",
        },
        "order": [[ 0,"asc" ]],
        dom: 'Bfrtip',
            lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 filas', '25 filas', '50 filas', 'Mostrar Todo' ]
            ],
		    buttons: [
                {   extend: 'pageLength',
                    text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
                 },
                {   extend: 'colvis',
                    text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
                }
            ],
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
                { data: 'mal_estado', name: 'mal_estado'},
                { data: 'cantidad', name: 'cantidad', render: $.fn.dataTable.render.number(',','.',0 )}
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
      var redirectWindow = window.open('agre_ex/'+ row.data().id + '/{{$suma}}', '_blank');
      redirectWindow.location;
    });
    setInterval( function () {
    table.ajax.reload();
}, 50000 );
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
