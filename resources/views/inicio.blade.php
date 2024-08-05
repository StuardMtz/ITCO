@extends('layouts.app')
@section('content')
<blockquote class="blockquote text-center">
    <img class="logo" src="storage/sistegualogob.png" width="125">
    <p>
        <b>Bienvenido <cite title="Nombre usuario">{{Auth::user()->name}}</cite>, selecciona la opción que deseas visualizar.</b>
    </p>
</blockquote>
<div class="container-fluid">

    <div class="container">
        @if($message= Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ $message}}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if($message= Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ $message}}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div >
    <div class="row">
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Control de inventario</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Inventarios semanales, generales, reporte de inventarios.</p>
                    <a href="{{route('inventarios_pendientes')}}" class="btn btn-danger btn-sm">Ver inventarios</a>
                    <a href="{{route('reporte_inventarios')}}" class="btn btn-dark btn-sm">Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Control de entregas de camiones</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Creación de entregas, asignación de rutas a pilotos y reporte de entregas.</p>
                    <a href="{{route('entregas_en_espera')}}" class="btn btn-danger btn-sm">Ver solicitudes</a>
                    <a class="btn btn-dark btn-sm" href="{{route('rep_gen')}}">Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Transferencias web</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Creación, edición, recepción de transferencias y reporte de transferencias.</p>
                    @if(Auth::user()->roles != 17)
                        <a href="{{route('inicio_transferencias')}}" class="btn btn-danger btn-sm">Ver transferencias</a>
                    @else
                        <a href="{{route('bod_trasf_bod')}}" class="btn btn-danger btn-sm">Crear transferencia</a>
                    @endif
                    <a class="btn btn-dark btn-sm" href="{{route('rep_trans')}}">Reporte</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Transferencias compras</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Transferencias por compras, importaciones, etc...</p>
                    <a href="{{route('transc_inicio')}}" class="btn btn-danger btn-sm">Ver transferencias</a>
                    <a class="btn btn-dark btn-sm" href="{{route('rep_trans')}}">Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Transferencias entre sucursales</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Creación, edición, de transferencias entre sucursales.</p>
                    <a href="{{route('transferencias_en_espera')}}" class="btn btn-danger btn-sm">Ver transferencias</a>
                    <a class="btn btn-dark btn-sm" href="{{route('tra_reporte')}}">Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Gráficas</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Gráficas de existencias, minimax, etc...</p>
                        <a class="btn btn-danger btn-sm" href="{{route('mimageneral')}}">Minimax general</a>
                        <a class="btn btn-dark btn-sm" href="{{route('rep_suc_lis')}}">Minimax sucursal</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Reporte de actividades</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Reporte de actividades realizadas por vendedores, asesores, supervisores, etc...</p>
                    <a href="{{route('listado_vendedores')}}" class="btn btn-danger btn-sm">Clic para continuar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Reporte ordenes de compras</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Ingreso de ordenes de compra</p>
                    <a href="{{route('rep_compras')}}" class="btn btn-danger btn-sm">Clic para continuar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Cotizaciones</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Permite realizar cotizaciones a clientes</p>
                    <a href="{{route('inicio_cotizaciones')}}" class="btn btn-danger btn-sm">Ver cotizaciones</a>
                    <a class="btn btn-dark btn-sm" href="{{route('reporte_cotizaciones')}}">Reporte</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Gastos y liquidaciones</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Solicitar autorización para aprobación de gastos y liquidaciones</p>
                    <a href="{{route('inicio_gastos_espera')}}" class="btn btn-danger btn-sm">Ver gastos y liq.</a>
                    <a href="{{route('lis_us_gasp')}}" class="btn btn-dark btn-sm">Autorizar Gastos</a>
                    <a href="{{route('lisusliq')}}" class="btn btn-primary btn-sm">Verificar liquidaciones</a>
                    <a href="{{route('resumen_de_gastos')}}" class="btn btn-info btn-sm">Resumen</a>
                    <a href="{{route('rep_liquida')}}" class="btn btn-light btn-sm">Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card" style="background-color: rgba(170, 170, 170, 0.2);border:none">
                <div class="card-header">
                    <h6 class="card-title">Panel administrativo</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Usuarios, permisos, etc...</p>
                    <a href="{{route('inicio_adm')}}" class="btn btn-danger btn-sm">Clic para continuar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
