@extends('layouts.app')
@section('content')
<script src="{{ asset('js/moment.js')}}"></script>
<script src="{{ asset('js/moment_with_locales.js')}}"></script>
<div class="container-fluid">
    <a class="btn btn-danger" style="margin-bottom: 15px;" href="{{ route('lis_us') }}">
        <i class="fa-solid fa-right-from-bracket"></i> Atrás</a>
    <form method="get" action="{{url('histofe',$id)}}">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="span">Desde</span>
            </div>
            <input type="date"  class="form-control" placeholder="Inicio" name="inicio">
            <div class="input-group-prepend">
                <span class="input-group-text" id="span">Hasta</span>
            </div>
            <input type="date" step="0.01" class="form-control" placeholder="Fin" name="fin">
            <div class="input-group-append">
                <button class="btn btn-warning" style="margin-left: 25px;" type="submit">Buscar</button>
            </div>
        </div>
    </form>
    <div style="margin-top: 20px">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Historial de suarios</p>
        </blockquote>
        <div class="table-reponsive-sm">
            <table class="table table-sm table-borderless" id="usuarios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Actividad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $hi)
                    <tr>
                        <td>{{$hi->id}}</th>
                        <td>{{$hi->nombre->name}}</td>
                        <td>{{$hi->actividad}}</td>
                        <td>{{$hi->created_at}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var table = $('#usuarios').DataTable({
    pageLength: 50,
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
    "columnDefs": [
        { targets: 0, searchable: true },
        { targets: [0], searchable: true },
        { targets: '_all', searchable: false },
        {targets: [3], render:function(data){
            moment.locale('es');
            return moment(data).format('LLLL');
        }}
    ],
});
</script>
@endsection
