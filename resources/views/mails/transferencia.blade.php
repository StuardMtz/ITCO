<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" 
    integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Transferencia</title>
  </head>
  <body>
      <div class="card">
            <div class="card-header">
                <div class="alert alert-success" role="alert">
                    <h4>Alerta de transferencia</h4>
                </div>
            </div>
            <div class="card-body">
                <p ><b>{{$usuario}}</b> está trabajando en una transferencia para tu bodega.<p> 
                <p>La transferencia {{$numero}} se encuentra en estado: <b>{{$estado}}</p>
                <p>La fecha estimada de la entrega es: <b>{{date('d/m/Y',strtotime($fecha))}}</b></p>
                <p>Recuerda que puedes visualizar tu transferencia en el siguitente enlace <a>http://200.6.238.150:8080/inventario/public/v_tran/{{$numero}}</a></p>
                <p>Si deseas que se realize alguna modificación a esta transferencia, notifica a {{$usuario}} a traves del {{$correo}}.</p>

                <p>Nota: Si la transferencia ya fue despachada por CD, no será posible realizar modificaciones.</p>

                <table>
                  <thead>
                    <tr>
                      <th>Estado</th>
                      <th>Descripción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>En cola</td>
                      <td>Transferencia creada, los productos y fecha de entrega pueden cambiar sin previo aviso.</td>
                    </tr>
                    <tr>
                      <td>Programada</td>
                      <td>Transferencia programada, la fecha de entrega está confirmada.</td>
                    </tr>
                  </tbody>
                </table>
            </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>