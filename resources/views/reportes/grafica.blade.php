@extends('layouts.app2')

@section('content')
<div class="container-fluid">
<h3 style="text-align:center;">Producto: {{$pro}}</h3>
<a class="btn btn-dark" href="{{route('ingre_gra')}}">Ingresar Datos</a>
<div id="container" style="width:100%; height:400px;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
</div>
<script type="text/javascript">
    $(function () {
        $('#container').highcharts(
            {!! json_encode($greorden) !!}
        );
    })
</script>
@endsection