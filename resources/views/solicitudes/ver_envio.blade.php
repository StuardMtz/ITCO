@extends('layouts.app')
@section('content')
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h6>Ver información de la entrega solicitada</h6>
            </div>
            <div class="row">
                @foreach ($envio as $ev)
                    <div class="col-md">
                        <b>Número: </b>{{ $ev->id }}
                    </div>
                    <div class="col-md">
                        <b>Comprobante: </b>{{ $ev->comprobante }}
                    </div>
                    <div class="col-md">
                        <b>Cliente: </b>{{ $ev->nombre }}
                    </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <b>Departamento: </b>{{ $ev->departamento }}
                </div>
                <div class="col-md">
                    <b>Municipio: </b>{{ $ev->municipio }}
                </div>
                <div class="col-md">
                    <b>Aldea: </b>{{ $ev->aldea }}
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <b>Fecha creación: </b>{{ date('d/m/Y H:i:s', strtotime($ev->created_at)) }}
                </div>
                @if ($ev->fecha_entrega == '')
                    <div class="col-md">
                        <b>Solicitado para:</b>
                    </div>
                @else
                    <div class="col-md">
                        <b>Solicitado para: </b>{{ date('d/m/Y', strtotime($ev->fecha_entrega)) }}
                    </div>
                @endif
                <div class="col-md">
                    <b>Hora sugerida: </b>{{ $ev->hora }}
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <b>Camión: </b>{{ $ev->placa }}
                </div>
                @if ($ev->fecha_asignacion == '')
                    <div class="col-md">
                        <b>Fecha asignado: </b>
                    </div>
                @else
                    <div class="col-md">
                        <b>Fecha asignado: </b>{{ date('d/m/Y H:i:s', strtotime($ev->fecha_asignacion)) }}
                    </div>
                @endif
                <div class="col-md">
                    <b>Tiempo en espera: </b>{{ $ev->tiempo_en_espera_asignacion }}
                </div>
            </div>
            <div class="row">
                @if ($ev->fecha_carga == '')
                    <div class="col-md">
                        <b>Fecha de carga: </b>
                    </div>
                @else
                    <div class="col-md">
                        <b>Fecha de carga: </b>{{ date('d/m/Y H:i:s', strtotime($ev->fecha_carga)) }}
                    </div>
                @endif
                <div class="col-md">
                    <b>Tiempo esperando para carga: </b>{{ $ev->tiempo_en_espera_carga }}
                </div>
                <div class="col-md">
                    <b>Tiempo de carga: </b>{{ $ev->tiempo_carga }}
                </div>
            </div>
            <div class="row">
                @if ($ev->fecha_ruta == '')
                    <div class="col-md">
                        <b>Fecha salida a ruta: </b>
                    </div>
                @else
                    <div class="col-md">
                        <b>Fecha salida a ruta: </b>{{ date('d/m/Y H:i:s', strtotime($ev->fecha_ruta)) }}
                    </div>
                @endif
                <div class="col-md">
                    <b>Tiempo en ruta: </b>{{ $ev->tiempo_en_ruta }}
                </div>
                @if ($ev->fecha_entregado == '')
                    <div class="col-md">
                        <b>Fecha de entregado: </b>
                    </div>
                @else
                    <div class="col-md">
                        <b>Fecha de entregado: </b>{{ date('d/m/Y H:i:s', strtotime($ev->fecha_entregado)) }}
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md">
                    <b>Tiempo total de entrega: </b>{{ $ev->tiempo_desde_carga_a_entrega }}
                </div>
                @if ($ev->latitud == '')
                    <div class="col-md">
                        <b>Tiempo total de entrega: </b>
                    </div>
                @else
                    <div class="col-md">
                        <a class="btn btn-outline-warning btn-block btn-sm" href="{{ route('mapa', $id) }}"><i
                                class="fas fa-user-plus"></i> Ubicación de Entrega</a>
                    </div>
                @endif
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar"
                    style="width: {{ number_format($ev->porcentaje) }}%"></div>
            </div>
            @endforeach
            <a class="btn  btn-secondary btn-block" href="{{ route('bit', $id) }}"><i class="fas fa-user-plus"></i>
                Historial de Entrega</a>
            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#AnularEntrega">
                Anular entrega
            </button>

            <!-- Modal -->
            <div class="modal fade" id="AnularEntrega" tabindex="-1" aria-labelledby="AnularEntregaLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="AnularEntregaLabel">Anular entrega</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="needs-validation" method="post" action="{{ url('anulSol', $id) }}" novalidate>
                                {{ csrf_field() }}
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="anular_etiqueta">Describa la razón</label>
                                        <div class="input-group">
                                            <textarea type="text" class="form-control" placeholder="Cliente no cancelo" name="comentario" required></textarea>
                                            <div class="valid-tooltip">
                                                Bien!
                                            </div>
                                            <div class="invalid-tooltip">
                                                No puede dejar este campo en blanco!
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <button type="submit" class="btn btn-dark btn-sm btn-block" class="fas fa-save">Guardar
                                        cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endsection
