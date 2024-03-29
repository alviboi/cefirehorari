<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- <meta name="user-name" content="{{ Auth::user()->name }}"> --}}
</head>
<body>
    <div class="uk-background-primary uk-light">
        <nav class="uk-navbar-container uk-navbar-transparent">
            <div class="uk-container">
                <div class="uk-navbar" data-uk-navbar>
                    <div class="uk-navbar-left">
                        <a class="uk-navbar-item uk-logo" href="/">{{ config('app.name', 'Laravel') }}</a>
                        @if (Auth::check())
                        <ul class="uk-navbar-nav">
                                <li>
                                    <a href="/home"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></a>
                                </li>
                        </ul>
                        @endif
                    </div>
                    <div class="uk-navbar-right">
                        <ul class="uk-navbar-nav">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li>
                                        <a href="{{ route('login') }}">Entra</a>
                                    </li>
                                @endif
                                @if (\App\Models\control::all()[0]->registra)
                                    <li>
                                        <a href="{{ route('register') }}">Registra't</a>
                                    </li>
                                @endif
                            @else
                                <li>
                                    <a href="#">
                                        {{ Auth::user()->name }}
                                    </a>
                                    <div class="uk-navbar-dropdown">
                                        <ul class="uk-nav uk-navbar-dropdown-nav">
                                            <li>
                                                <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    Surt
                                                </a>

                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <main>
        @yield('content')
    </main>

    <footer class="uk-section uk-section-xsmall uk-section-secondary" style="position:fixed;bottom:0px;width:100%;">
        <div class="uk-container">
            <div class="uk-grid uk-text-center uk-text-left@s uk-flex-middle" data-uk-grid>
                <div class="uk-text-small uk-text-muted uk-width-1-2@s">
                    Aplicació creada per <a href="mailto:ar.vicenteboix@edu.gva.es">Alfredo Rafael Vicente Boix</a>
                </div>

                <div class="uk-text-small uk-text-muted uk-text-right uk-text-right@s uk-width-1-2@s">
                    {{ config('app.name', 'Laravel') }}
            </div>
        </div>
    </footer>

<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/home.js') }}" defer></script>
</body>
</html>
