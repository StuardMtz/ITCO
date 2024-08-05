<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Solicitud de gasto</title>
    </head>
    <body>
        <div class="card">
            <div class="card-header">
                <div class="alert alert-success" role="alert">
                    <h4>Nueva solicitud de autorización de gasto</h4>
                </div>
            </div>
            <div class="card-body">
                <p ><b>{{$usuario}}</b> solicita autorización de, {{$descripcion}}<p> 
                <p>La solicitud se encuentra en estado<b> {{$estado}}</p>
                <p>La solicitud fue generada el <b>{{date('d/m/Y',strtotime($fecha))}}</b></p>
            </div>
        </div>
  </body>
</html>