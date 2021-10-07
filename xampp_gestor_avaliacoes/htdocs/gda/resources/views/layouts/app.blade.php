<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('app.name', 'Gestão de Avaliações')}}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/mdb.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/addons/datatables.bootstrap4.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/mdb.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/addons/datatables2.min.js') }}"></script>
    <!-- <script src="{{ asset('js/app.js') }}"></script> !-->

    <style>
        h1 {
            padding: 5px;
            margin: 5px;
        }
        table{
            padding: 10px;
            margin: 10px;
        }
    </style>
</head>

<body>
    <h1>Gestão de Avaliações</h1>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#thenavbar" aria-controls="navbarSupportedContent15" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="thenavbar">
            <ul class="navbar-nav">
                @if (!Auth::user())
                <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/') }}">Início</a>
                </li>
                @elseif (Auth::user()->type == "Aluno")
                <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/') }}">Início</a>
                </li>
                <li class="nav-item {{ Request::is('calendario') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('calendario') }}">Calendário</a>
                </li>
                <li class="nav-item {{ Request::is('inscricao') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('inscricao') }}">Inscrições</a>
                </li>
                <li class="nav-item {{ Request::is('classificacoes') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('classificacoes') }}">Avaliações/Classificações</a>
                </li>
                @elseif (Auth::user()->type == "Docente")
                <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/') }}">Início</a>
                </li>
                <li class="nav-item {{ Request::is('calendario') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('calendario') }}">Calendário</a>
                </li>
                <li class="nav-item {{ Request::is('classificacoes') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('classificacoes') }}">Classificações</a>
                </li>
                @endif

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
                        <a class="dropdown-item">
                            {{ Auth::user()->type }}
                        </a>
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

    <main class="py-4" style="width: 99%;">
        @yield('content')
    </main>
</body>

</html>