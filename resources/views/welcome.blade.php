<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                background: no-repeat;
                background-position: center;
                backface-visibility: hidden;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .bottom-right {
                margin-left:4em;
                margin-top: 8em;
                /*position: fixed;
                bottom: 0px;
                margin: 0px;*/
            }
        </style>
    </head>
    <body style="background-image:url('storage/welcome_invierno.png'); background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;transparence: 0.5;">
        <div class="flex-center position-ref full-height">
        <!--    @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif
        -->
            <div class="content">
                <div class="title m-b-md">
                    <a class="btn btn-danger"  href="{{ route('login') }}"><img  width="350"  src="{{asset('storage/btn_inicio_i.png')}}" style="margin-top: -20000px; margin-right: -250px"></a>
                </div>
            </div>
        </div>
    </body>
</html>
