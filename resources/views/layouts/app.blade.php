<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config("app.name", "DigiMaster") }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/digitacao.css') }}" rel="stylesheet" />
</head>

<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config("app.name", "DigiMasters") }}
            </a>
            <notification></notification>

            <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto"></ul>

                {{-- Links das unidades --}}
                @if($unidade = isset($unidade) ? $unidade : 1)@endif
                <ul class="navbar-nav ">
                    <li class="nav-item">
                        <a class="text-gray" href="#">Units:</a>
                    </li>
                    <li class="nav-item">
                        <a class="link @if ($unidade == 1 ) atual @endif " href="{{ url("/1/1") }}">1</a>
                    </li>
                    <li class="nav-item">
                        <a class="link  @if ($unidade == 2 ) atual @endif " href="{{ url("/2/1") }}">2</a>
                    </li>
                    <li class="nav-item">
                        <a class="link  @if ($unidade == 3 ) atual @endif " href="{{ url("/3/1") }}">3</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __("Login") }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __("Register") }}</a>
                            </li>
                        @endif @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                                <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __("Logout") }}
                                </a>
                                @if(auth()->user() && auth()->user()->is_admin)
                                    <a class="dropdown-item" href="{{ route('settings.index') }}" >
                                        {{ __("Settings") }}
                                    </a>
                                @endif

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <div class="container border pt-1 ">
        <main class="py-0">
            @yield('content')
        </main>
    </div>
</div>
@yield('script')
</body>

</html>
