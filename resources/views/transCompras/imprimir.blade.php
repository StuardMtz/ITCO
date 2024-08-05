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
            left: 5px;
            right: 5px;
        }
        @page {
            margin-top: 160px;
            margin-bottom: 145px;
            text-align: justify;
        }
        header { position: fixed;
        left: 8px;
        top: -130px;
        right: 8px;
        text-align: center;
        }
        footer {
        position: fixed;
        left: 0px;
        bottom: -30px;
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
            text-align: center;
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
                    <tr>
                        <td><b>No. transferencia: </b>{{$d->num_movi}}</td>
                        <td><b>Realizado por: </b>{{$d->usuario}}</td>
                        <td><b>Estado: </b>{{$d->estado}}</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td><b>Descripción: </b>{{$d->observacion}}</td>
                        <td><b>Fecha de creación: </b>{{date('d/m/Y H:i', strtotime($d->created_at))}}</td>
                        <td><b>Documento valido solo para realizar carga</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
        </header>
        <footer>
            <table>
                <tr style="border:solid 1px black;">
                    <td><p class="izq">Sistegua S.A.</p></td>
                    <td><p>Firma Verificador</p></td>
                    <td><p>Firma Piloto</p></td>
                    <td></td>
                </tr>
            </table>
        </footer>
        <div class="container">
            <table >
                <thead>
                    <tr>
                        <th><b>Código</b></th>
                        <th width="9cm;"><b>Producto</b></th>
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
                    <tr style="border:solid 1px black;">
                        <td style="border-bottom:solid 1px black;">{{utf8_encode($vi->nombre_corto)}}</td>
                        <td style="border-bottom:solid 1px black;">{{$vi->nombre_fiscal}}</td>
                        <td style="border-bottom:solid 1px black; text-align:right;">{{number_format($vi->cantidad)}}</td>
                        <td style="border-bottom:solid 1px black; text-align:right;">{{number_format($vi->costo)}}</td>
                        <td style="border-bottom:solid 1px black; text-align:right;">{{number_format($vi->peso)}}</td>
                        <td style="border-bottom:solid 1px black; text-align:right;">{{number_format($vi->volumen)}}</td>
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
                        <th style="text-align:right;">{{number_format(($peso/1000),2)}} Toneladas</th>
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