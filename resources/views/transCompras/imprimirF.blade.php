<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <style>
        body{
            font-family: sans-serif;
            font-size: 12px;
            margin: 0.3cm;
        }
        @page {
            margin-top: 190px;
            margin-bottom: 190px;
            text-align: justify;
        }

        { position: fixed; right: 0px; bottom: 10px; text-align: center;border-top: 1px solid black;}
            #footer .page:after { content: counter(page, decimal); }
            header { position: fixed;
            top: -190px;
            text-align: center;
            width: 100%;
        }
        footer {
            position: fixed;
            left: 0px;
            bottom: 20px;
            right: 0px;
            height: 0px;
        }
        
        footer table {
            width: 100%;
        }
        footer p {
            text-align: right; 
        }
        footer .izq {
            text-align: left;
        }
        th{ 
            text-size: 14px;
        }
  </style>
    <body>
        <header>
            <table>
                <tbody>
                    <tr>
                        <td colspan="3" style="text-align:center;"><b>Sistemas Técnicos de Guatemala S.A.</b></td>
                    </tr>
                    @foreach($tran as $d)
                    <tr >
                        <td><b>No. transferencia: </b>{{$d->num_movi}}</td>
                        <td><b>Sucursal: </b>{{$d->nombre}}, {{$d->bodega}}</td>
                        <td><b>Descripción: </b>{{$d->observacion}}</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td><b>Observación: </b>{{$d->descripcion}}</td>
                        <td><b>Comentario: </b>{{$d->comentario}}</td>
                        <td><b>Fecha de creación: </b>{{date('d/m/Y H:i', strtotime($d->created_at))}}</td>
                    </tr>
                    <tr>
                        <td><B>Fecha de salida: </b>{{date('d/m/Y H:i',strtotime($d->fechaSalida))}}</td>
                        <td><b>Creada por: </b>{{$d->usuario}}</td>
                    </tr>
                    <tr>
                        <td><b>Revisada por: </b>{{$d->usuarioSupervisa}}</td>
                        <td><b>Grupo que cargo: </b>{{$d->grupoCarga}}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><b>Observación sucursal: </b>{{$d->observacionSucursal}}</td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
        </header>
        <footer>
            <table>
                <tr>
                    <th colspan="3" style="font-size:0.6cm;">Vehículo: {{$d->propietario}} {{$d->placa_vehiculo}}</th>
                </tr>
                <tr>
                    <th colspan="3" style="font-size:0.4cm;">Estado: {{$d->estado}}</th>
                </tr>
                <tr>
                    <th colspan="3">Tiene 24 horas para finalizar la transferencia luego de recibido este documento</th>
                </tr>
                <tr>
                    <th colspan="3">___________________________________________________________________________________________________</th>
                </tr>
                <tr rowspan="4">
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <td><b>Documento para el piloto</b></td>
                    <td><p>Firma Verificador</p></td>
                    <td><p>Firma Piloto</p></td>
                    <td id="footer"><p class="page">Página </p></td>
                </tr>
            </table>
        </footer>
        <br>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th><b>Código</b></th>
                        <th><b>Producto</b></th>
                        <th><b>Cantidad</b></th>
                        <th><b>Bultos</b></th>
                        <th><b>Peso</b></th>
                        <th><b>Volumen</b></th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $peso = 0;
                        $volumen = 0;
                    @endphp
                    @foreach($productos as $vi)
                    <tr>
                        <td width="3cm;">{{utf8_encode($vi->nombre_corto)}}</td>
                        <td width="8cm;">{{$vi->nombre_fiscal}}</td>
                        <td style="text-align:right;">{{number_format($vi->cantidad1,2)}}</td>
                        <td style="text-align:right;">{{number_format($vi->costo)}}</td>
                        <td style="text-align:right;">{{number_format($vi->peso)}}</td>
                        <td style="text-align:right;">{{number_format($vi->volumen)}}</td>
                    </tr>
                    @php
                        $peso += $vi->peso;
                        $volumen += $vi->volumen;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr> 
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="text-align:right;">{{number_format(($peso/1000),2)}} Kg</th>
                        <th style="text-align:right;">{{number_format($volumen,3)}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>
<script type="text/php">
if ( isset($pdf) ) { 
    $pdf->page_script('
        if ($PAGE_COUNT > 1) {
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 10;
            $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
            $y = 15;
            $x = 520;
            $pdf->text($x, $y, $pageText, $font, $size);
        } 
    ');
}
</script>