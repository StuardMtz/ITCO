<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="theme-color" content="#317EFB"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- CSRF Token -->
        <link rel="stylesheet" href="{{asset('css/css/bootstrap.css')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap.css.map')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap.min.css')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap.min.css.map')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.css')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.css.map')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.min.css')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-grid.min.css.map')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.css')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.css.map')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.min.css')}}" type="text/css">
        <link rel="stylesheet" href="{{asset('css/css/bootstrap-reboot.min.css.map')}}" type="text/css">
        <style>
            body, html{
                height: 100%;
            }
            .row{
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .card-header{
                text-align: center;
                color: white;
                background-image: linear-gradient(to top, #5c779966, #456284, #2e4e696b);
                font-size:large;
                border-radius: 15px;
                margin: 15px;
            }
            .card-body{
                color: black;
                font-size: 15px;
                background-color: #4d4d4d24;
                border-radius: 10px;
                margin: 15px;
            }
            .form-control{
                border-left:none;
                border-top: none;
                border-right: none;
                border-bottom: solid 1px black;
            }
            .logo{
                display: block;
                margin: 0 auto;
                border: none;
            }
            .continuar{
                text-align: center;
                display: block;
                margin: 0 auto;
                border: none;
                margin-top: 15px;
            }
            blockquote{
                border-radius: 15px;
                text-align: center;
                font-weight: bold;
                color: rgb(255, 255, 255);
                font-size: 25px;
                text-shadow:
                    0 1px 0 rgba(0, 0, 0, 0.1),  /* Black shadow with 10% opacity */
                    0 2px 0 rgba(0, 0, 0, 0.2),  /* Black shadow with 20% opacity */
                    0 3px 0 rgba(0, 0, 0, 0.3),  /* Black shadow with 30% opacity */
                    0 4px 0 rgba(0, 0, 0, 0.4),  /* Black shadow with 40% opacity */
                    0 5px 0 rgba(0, 0, 0, 0.5),  /* Black shadow with 50% opacity */
                    0 6px 1px rgba(0, 0, 0, 0.6),  /* Black shadow with 60% opacity, slight blur */
                    0 0 5px rgba(0, 0, 0, 0.1),  /* Inner black shadow with 10% opacity */
                    0 1px 3px rgba(0, 0, 0, 0.3),  /* Black inner shadow with 30% opacity */
                    0 3px 5px rgba(0, 0, 0, 0.2),  /* Black inner shadow with 20% opacity */
                    0 5px 10px rgba(0, 0, 0, 0.25), /* Black inner shadow with 25% opacity */
                    0 10px 10px rgba(0, 0, 0, 0.2), /* Black inner shadow with 20% opacity */
                    0 20px 20px rgba(0, 0, 0, 0.15); /* Black inner shadow with 15% opacity */
            }
        </style>
    </head>
    <body <body style="background-image: url({{url('/')}}/storage/invierno.jpg); background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;transparence: 0.5;">>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="">
                        <img class="logo" src="storage/sistegualogob.png" width="250">
                        <div class="card-header">
                            <blockquote>
                                <b>Iniciar sesión</b>
                            </blockquote>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label style="color: white" for="email">Correo</label>
                                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label style="color: white" for="password">Contraseña</label>
                                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="continuar">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-dark btn-sm">
                                                <b>Clic para continuar</b>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
