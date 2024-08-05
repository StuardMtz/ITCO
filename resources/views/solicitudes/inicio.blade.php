@extends('layouts.app')
@section('content')
@yield('content', View::make('layouts.solicitudes'))
<div class="container-fluid">
  <blockquote class="blockquote text-center">
    <p class="mb-0">Listado de camiones</p>
  </blockquote>
        {{-- <a class="btn btn-dark btn-sm" style="margin-bottom: 5px" href="{{route('v_a_camion')}}"><i class="fas fa-truck-monster"></i> Agregar un nuevo camión</a></th> --}}
  <button type="button" class="btn btn-warning btn-sm" style="margin-bottom: 5px" data-toggle="modal" data-target="#staticBackdrop">
    Listado de unidades por sucursal
  </button>
    <div class="table-responsive-sm">
      <table class="table table-sm table-borderless">
        <thead>
          <tr>
            <th>#</th>
            <th>Marca</th>
            <th>Placa</th>
            <th>Tonelaje</th>
            {{--<th>Tipo</th>--}}
            <th>Estado</th>
            <th>Nueva ruta</th>
            <th>Ver</th>
            {{-- <th>Editar Camion</th> --}}
          </tr>
        </thead>
        <tbody>
          @foreach ($camiones as $c)
          <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->marca }}</td>
            <td>{{ $c->placa }}</td>
            <td>{{ $c->tonelaje }}</td>
            {{--<td>{{ $c->tipo_camion }}</td>--}}
            <td>{{ $c->nombre }}</td>
            <td><a class="btn btn-success btn-sm" href="{{ route('nueva_ruta', $c->id) }}">Nueva ruta</a></td>
            <td><a class="btn btn-warning btn-sm" href="{{ route('entregas_por_camion', $c->id) }}">Ver entregas</a></td>
            {{-- <td><a class="btn btn-danger btn-sm" href="{{ route('v_e_camion', $c->id) }}">Editar</a> </td> --}}
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <blockquote class="blockquote text-center">
      <p class="mb-0">Listado de rutas pendientes de finalizar</p>
    </blockquote>
    <div class="table-responsive-sm">
      <table class="table table-sm table-borderless">
        <thead>
          <tr>
            <th>#</th>
            <th>Placa Camion</th>
            <th>Fecha de Entrega</th>
            <th>Entregas sin finalizar</th>
            <th>Editar</th>
            <th>Ver</th>
            <th>Finalizar</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($rutas as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->placa }}</td>
            @if ($r->fecha_entrega == '')
            <td style="background-color:#0D3E7F; color:white;"><b>Agregar fecha a la ruta</b></td>
            @else
            <td>{{ date('d/m/Y', ('strtotime')($r->fecha_entrega)) }}</td>
            @endif
            <td><span class="badge badge-danger">
              <h6>{{ $r->pendientes }}</h6>
            </span></td>
            <td><a class="btn btn-danger btn-sm" href="{{ route('v_e_ruta', $r->id) }}">Editar</a></td>
            <td><a class="btn btn-warning btn-sm" href="{{ route('v_ruta', $r->id) }}">Ver</a></td>
            <td><a class="btn btn-dark btn-sm" href="{{ route('finalizar_ruta', $r->id) }}">Finalizar</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <blockquote class="blockquote text-center">
      <p class="mb-0">Solicitudes sin ruta</p>
    </blockquote>
    <div class="table-responsive-sm">
      <table class="table table-sm table-borderless">
        <thead>
          <tr>
          <th>#</th>
          <th>Factura o Comprobante</th>
          <th>Cliente</th>
          <th>Fecha de entrega</th>
          <th>Hora de entrega</th>
          <th>Solicita</th>
          <th>Editar</th>
          <th>Ver</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($solicitudes as $s)
        <tr>
          <td>{{ $s->id }}</td>
          <td>{{ $s->comprobante }}</td>
          <td>{{ $s->nombre }}</td>
          @if ($s->fecha_entrega == '')
          <td></td>
          @else
          <td>{{ date('d/m/Y', ('strtotime')($s->fecha_entrega)) }}</td>
          @endif
          <td>{{ $s->hora }}</td>
          <td>{{ $s->name }}</td>
          @if ($s->id_usuario == Auth::id())
          <td><a class="btn btn-danger btn-sm" href="{{ route('editar_solicitud', $s->id) }}">Editar</a></td>
          @else
          <td></td>
          @endif
          <td><a class="btn btn-warning btn-sm" href="{{ route('ver_solicitud', $s->id) }}"> Ver</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="staticBackdropLabel">Listado de rutas pendientes de finalizar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-borderless" id="rutas_modal" style="width:100%">
            <thead>
              <tr>
                <th></th>
                <th>#</th>
                <th>Sucursal</th>
                <th>Placa Camion</th>
                <th>Tonelaje</th>
                <th>Fecha de Entrega</th>
                <th>Pendientes</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

    {{-- ----------------------------------------------------------------------------------------------------------------- --}}

<script id="details-template" type="text/x-handlebars-template">
@verbatim
  <div class="table" style="display: grid; place-items: center;">
    <table class="tablen table-sm" style="" id="post-{{ruta_id}}">
      <thead>
        <tr class="table">
          <th></th>
          <th class="bg-warning text-dark">#</th>
          <th class="bg-warning text-dark">Comprobante</th>
          <th class="bg-warning text-dark">Cliente</th>
          <th class="bg-warning text-dark">Fecha de solicitud</th>
          <th class="bg-warning text-dark">Fecha de entrega</th>
          <th class="bg-warning text-dark">Estado</th>
          <th class="bg-warning text-dark">Porcentaje</th>
        </tr>
      </thead>
    </table>
  </div>
  @endverbatim
</script>
<script>
var template = Handlebars.compile($("#details-template").html());
var table = $('#rutas_modal').DataTable({
  processing: true,
  serverSide: false,
  pageLength: 100,
  searching: true,
  responsive: false,
  scrollX: false,
  //scrollY: '62vh',
  scrollCollapse: false,
  scroller: false,
  stateSave: true,
  paginate: false,
  "stateDuration": 300,
  ajax: {
    url: "{{ route('v_ruta_s') }}",
    dataSrc: "data",
  },
  "order": [
    [2, "desc"]
  ],
//   dom: 'Bfrtip',
//   lengthMenu: [
//     [10, 25, 50, -1],
//     ['10 filas', '25 filas', '50 filas', 'Mostrar Todo']
//   ],
//   buttons: [
//     {
//       extend: 'pageLength',
//       className: 'btn btn-dark',
//       text: '<i class="fas fa-plus-circle"></i>  Mostrar más'
//     },
//     {
//       extend: 'colvis',
//       className: 'btn btn-dark',
//       text: '<i class="fas fa-minus-circle"></i>  Eliminar columnas'
//     },
//     {
//       extend: 'excelHtml5',
//       className: 'btn btn-dark',
//       text: '<i class="far fa-file-excel"></i>  Exportar a excel',
//     }
//   ],

"language": {
    "lengthMenu": "<span class='text-paginate'>Mostrar _MENU_ registros</span>",
    "zeroRecords": "No se encontraron resultados",
    "EmptyTable": "Ningún dato disponible en esta tabla =(",
    //"info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    //"infoEmty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    //"infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "InfoPostFix": "",
    //"search": "Buscar",
    "loadingRecords": "Cargando...",
    "processing": "Procesando...",
    "paginate": {
        "First": "Primero",
        "Last": "Último",
        //"next": "Siguiente",
        //"previous": "Anterior",
    },
  },
  columns: [
    {
      "className": 'details-control',
      "orderable": false,
      "searchable": false,
      "data": null,
      "defaultContent": '<div class="text-center" style="width:100%; color: #3dc728; cursor:pointer;"><i class="fa fa-plus-circle"></i></div>'
    },
    { data: 'ruta_id',name: 'ruta_id'},
    { data: 'name', name: 'name'},
    { data: 'placa', name: 'placa'},
    { data: 'tonelaje', name: 'tonalaje'},
    { data: 'fecha_entrega', name: 'fecha_entrega'},
    { data: 'pendientes', name: 'pendientes'}
  ],
  "columnDefs": [
    { targets: 1, searchable: true },
    { targets: [0, 1, 2, 3, 4], searchable: true},
    { targets: '_all', searchable: false },
    { targets: [5], render: function(data) {
        moment.locale('es');
        return moment(data).format('L');
      }
    }
  ],
  rowCallback: function(row, data) {
    if (data['fecha_entrega'] == null) {
      $($(row).find("td")[5]).html('');
    }
  }
});
$('#rutas_modal tbody').on('click', 'td.details-control', function() {
  var tr = $(this).closest('tr');
  var row = table.row(tr);
  var tableId = 'post-' + row.data().ruta_id;
  if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass('shown');
  }
  else {
    row.child(template(row.data())).show();
    initTable(tableId, row.data());
    tr.next().find('td').addClass('no-padding bg-gray');
  }
});

function initTable(tableId, data) {
  $('#' + tableId).DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    lengthChange: false,
    paginate: false,
    info: false,
    ajax: data.details_url,
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
      { data: 'id', name: 'id' },
      { data: 'comprobante', name: 'comprobante' },
      { data: 'nombre', name: 'nombre' },
      { data: 'fecha_asignacion', name: 'fecha_asignacion', render: function(data, type, row) {
          if (type === "sort" || type === "type") {
            return data;
          }
          return moment(data).format('DD/MM/YYYY');
        }
      },
      { data: 'fecha_entrega', name: 'fecha_entrega', render: function(data, type, row) {
          if (type === "sort" || type === "type") {
            return data;
          }
          return moment(data).format('DD/MM/YYYY');
        }
      },
      { data: 'estado', name: 'estado' },
      { data: 'porcentaje', name: 'porcentaje', render: function(data, type, row) {
          if (type === "sort" || type === "type") {
            return data;
          }
          return data + '%';
        }
      }
    ],
    "columnDefs": [
      { targets: 0, searchable: true },
      { targets: [0, 1, 2, 3], searchable: true },
      { targets: '_all', searchable: false },
      { targets: [4], render: function(data) {
          moment.locale('es');
          return moment(data).format('LLLL');
        }
      }
    ],
    rowCallback: function(row, data) {
        if (data['porcentaje'] == 100) {
          $($(row).find("td")[0]).css("background-color", "#25B53EB0");
          $($(row).find("td")[1]).css("background-color", "#25B53EB0");
          $($(row).find("td")[2]).css("background-color", "#25B53EB0");
          $($(row).find("td")[3]).css("background-color", "#25B53EB0");
          $($(row).find("td")[4]).css("background-color", "#25B53EB0");
          $($(row).find("td")[5]).css("background-color", "#25B53EB0");
          $($(row).find("td")[6]).css("background-color", "#25B53EB0");
        }
      }
    })
  }
</script>
@endsection
