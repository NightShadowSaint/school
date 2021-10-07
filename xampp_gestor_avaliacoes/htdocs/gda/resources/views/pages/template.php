<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('app.name', 'Gestão de Avaliações')}}</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <link href="../../css/select2.min.css" rel="stylesheet" />
    <!-- <script src="{{ asset('js/app.js') }}"></script> !-->
    <style>
        #outer {
            width: 100%;
            height: 100vh;
            display: flex;
        }

        #inner {
            margin: auto;
        }
    </style>
</head>

<body>
    <h1 style="margin: 1%">Gestão de Avaliações</h1>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#thenavbar" aria-controls="navbarSupportedContent15" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="thenavbar">
            <ul class="navbar-nav">
                <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/') }}">Início</a>
                </li>
                <li class="nav-item {{ Request::is('calendario') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('calendario') }}">Calendário</a>
                </li>
                <li class="nav-item {{ Request::is('inscricao') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('inscricao') }}">Inscrições</a>
                </li>
                <li class="nav-item {{ Request::is('classaln') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('classaln') }}">Classificações</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
                @endguest
            </ul>
        </div>
    </nav>
    
</body>

</html>