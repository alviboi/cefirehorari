<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- és necessari crear este element per a que axios puga llegit el token per a fer les peticions --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SFP HORARI</title>
    <!-- CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <script>
        var missatges = {{ $conta }};
    </script>

</head>

<body>

    <div id="app">
        <!--HEADER-->
        <header id="top-head" class="uk-position-fixed">
            <div class="uk-container uk-container-expand uk-background-primary">
                <nav class="uk-navbar uk-light" data-uk-navbar="mode:click; duration: 250">
                    <div class="uk-navbar-left">
                        <div class="uk-navbar-item uk-hidden@m">
                            {{-- <a class="uk-logo" href="#"><img class="custom-logo" src="img/dashboard-logo-white.svg" alt=""></a> --}}
                        </div>
                        <ul class="uk-navbar-nav uk-visible@m">
                            {{-- <li><a class="uk-navbar-dropdown-close" href="#">Accounts</a></li> --}}
                            <li>
                                <a href="#">Ferramentes <span data-uk-icon="icon: triangle-down"></span></a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li class="uk-nav-header">EL TEU COMPTE</li>
                                        {{-- <li><a class="uk-navbar-dropdown-close" href="#" @click="view = 'centresmeus'"><span data-uk-icon="icon: info"></span> Els meus centres</a></li> --}}
                                        @if (Auth::user()->Perfil == 1)
                                            <li><a class="uk-navbar-dropdown-close" href="#"
                                                    @click="view = 'afegirvacances'"><span
                                                        data-uk-icon="icon: calendar"></span> Dies de vacances</a></li>
                                            <li><a class="uk-navbar-dropdown-close" href="#"
                                                    @click="view = 'horariespecial'"><span
                                                        data-uk-icon="icon: happy"></span> Dies d'horari especial</a></li>

                                            <li><a class="uk-navbar-dropdown-close" href="#"
                                                    @click="view = 'controlass'"><span
                                                        data-uk-icon="icon: refresh"></span> Control d'Assessors</a>
                                            </li>
                                            <li><a class="uk-navbar-dropdown-close" href="#"
                                                    @click="view = 'configuracio'"><span
                                                        data-uk-icon="icon: settings"></span> Configuració</a></li>
                                        @endif
                                        <li class="uk-nav-divider"></li>
                                        <li><a class="uk-navbar-dropdown-close" href="#"
                                                @click="showModal = true"><span data-uk-icon="icon: warning"></span>
                                                Escriu Avís</a></li>
                                        <li><a class="uk-navbar-dropdown-close" href="#"
                                                @click="showModalInc = true"><span
                                                    data-uk-icon="icon: lifesaver"></span> Escriu Incidència</a></li>
                                        <li><a class="uk-navbar-dropdown-close" href="#"
                                                @click="showModalBorsa = true"><span data-uk-icon="icon: future"></span>
                                                Borsa hores</a></li>
                                        <li><a class="uk-navbar-dropdown-close" href="#"
                                                @click="showModalCompensaBorsa = true"><span
                                                    data-uk-icon="icon: push"></span> Compensa deute</a>
                                        </li>

                                        {{-- <li><a class="uk-navbar-dropdown-close" href="#" @click="showModalMosc = true"><span data-uk-icon="icon: tripadvisor"></span> Escriu Moscoso</a></li> --}}

                                        <li class="uk-nav-divider"></li>
                                        <li><a class="uk-navbar-dropdown-close" href="#"
                                                @click="view = 'personals'"><span data-uk-icon="icon: image"></span>
                                                Dades Personals</a></li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a class="uk-navbar-dropdown-close" href="#"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span
                                                    data-uk-icon="icon: sign-out"></span> Ix</a></li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a @click="view='horari'">
                                    <div style="font-size:1.25rem">{{ date('d-m-Y') }}</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="uk-navbar-right">
                        <ul class="uk-navbar-nav" style="gap: 5px;">
                            <li>
                                <a href="{{ route('entrada') }}"><span data-uk-icon="icon: home"></span></a>
                            </li>
                            <li><a class="uk-navbar-dropdown-close" href="#" @click="showMissatge = true"
                                    data-uk-icon="icon:mail" title="Envia missatge a company" data-uk-tooltip></a></li>
                            {{-- <li><a class="uk-navbar-dropdown-close" href="#" data-uk-icon="icon: commenting" title="Ajuda" data-uk-tooltip></a></li> --}}
                            <li><a class="uk-navbar-dropdown-close" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    data-uk-icon="icon:  sign-out" title="Eixir" data-uk-tooltip></a></li>
                            <li><a class="uk-navbar-dropdown-close" class="uk-navbar-toggle uk-inline" data-uk-toggle
                                    data-uk-navbar-toggle-icon href="#offcanvas-nav" title="Missatge rebuts"
                                    data-uk-tooltip>
                                    @if ($conta > 0)
                                        <span class="uk-badge uk-position-top-right"
                                            style="background-color: red; margin-top: 5px;"><b>?</b></span>
                                    @endif
                                </a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--/HEADER-->
        <!-- LBARRA DRETA -->
        <aside id="left-col" class="uk-light uk-visible@m">
            <div class="left-logo uk-flex uk-flex-middle">
                <img class="custom-logo" src="img/cefire_la.png" alt="">
            </div>
            <div class="left-content-box  content-box-dark">
                {{-- <img src="img/avatar.svg" alt="" class="uk-border-circle profile-img"> --}}
                <h4 class="uk-text-center uk-margin-remove-vertical text-light">{{ Auth::user()->name }}</h4>

                <div class="uk-position-relative uk-text-center uk-display-block">
                    <a href="#" class="uk-text-small uk-text-muted uk-display-block uk-text-center"
                        data-uk-icon="icon: triangle-down; ratio: 0.7">
                        @if (Auth::user()->Perfil == 1)
                            Admin
                        @elseif (Auth::user()->Perfil == 0)
                            Assessor
                        @elseif (Auth::user()->Perfil == 2)
                            Administratiu
                        @endif

                    </a>
                    <!-- dropdown -->
                    <div class="uk-dropdown user-drop"
                        data-uk-dropdown="mode: click; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 150">
                        <ul class="uk-nav uk-dropdown-nav uk-text-left">
                            <li><a class="uk-navbar-dropdown-close" href="#" @click="showEdita = true"><span
                                        data-uk-icon="icon: refresh"></span> Edita perfil</a></li>
                            @if (Auth::user()->Perfil == 1)
                                <li><a class="uk-navbar-dropdown-close" href="#"
                                        @click="view = 'configuracio'"><span data-uk-icon="icon: settings"></span>
                                        Configuració</a></li>
                            @endif
                            <li class="uk-nav-divider"></li>
                            <li><a class="uk-navbar-dropdown-close" href="#"
                                    @click.prevent="view = 'personals'"><span data-uk-icon="icon: image"></span> Les
                                    teues dades</a></li>
                            <li class="uk-nav-divider"></li>
                            <li><a class="uk-navbar-dropdown-close" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span
                                        data-uk-icon="icon: sign-out"></span> Surt</a></li>
                        </ul>
                    </div>
                    <!-- /user dropdown -->
                </div>
            </div>

            <div class="left-nav-wrap">
                <ul class="uk-nav uk-nav-default uk-nav-parent-icon" data-uk-nav>
                    <li class="uk-nav-header">QUÈ VOLS FER?</li>
                    <li><a class="uk-navbar-dropdown-close" href="#" @click="view='horari'"><span
                                data-uk-icon="icon: user" class="uk-margin-small-right"></span>Horari</a></li>
                    <li><a class="uk-navbar-dropdown-close" href="#" @click="view='principal'"><span
                                data-uk-icon="icon: info" class="uk-margin-small-right"></span>Avisos</a></li>
                    <li><a class="uk-navbar-dropdown-close" href="#" @click="view='horaritots'"><span
                                data-uk-icon="icon: users" class="uk-margin-small-right"></span>Horaris Assessors</a>
                    </li>
                    <li><a class="uk-navbar-dropdown-close" href="#" @click="view='buscaenhoraris'"><span
                                data-uk-icon="icon: search" class="uk-margin-small-right"></span>Busca en horaris</a>
                    </li>
                    @if (Auth::user()->Perfil == 1)
                        <li><a class="uk-navbar-dropdown-close" href="#" @click="view='calendar'"><span
                                    data-uk-icon="icon: calendar" class="uk-margin-small-right"></span>Afegix
                                guàrdies</a></li>
                        <li><a class="uk-navbar-dropdown-close" href="#" @click="view='compensacions'"><span
                                    data-uk-icon="icon: future" class="uk-margin-small-right"></span>Validacions</a>
                        </li>
                    @endif
                    {{-- <li><a class="uk-navbar-dropdown-close" href="#" @click="view='centres'"><span data-uk-icon="icon: thumbnails" class="uk-margin-small-right"></span>Filtra centres</a></li> --}}
                    <li><a class="uk-navbar-dropdown-close" href="#" @click="ajuda_f()"><span
                                data-uk-icon="icon: lifesaver" class="uk-margin-small-right"></span>Ajuda</a></li>
                    <li class="uk-parent">
                        <a href="#"><span data-uk-icon="icon: comments"
                                class="uk-margin-small-right"></span>Reports</a>
                        <ul class="uk-nav-sub">
                            <li><a class="uk-navbar-dropdown-close" href="#"
                                    @click="view='report1'">Personal</a></li>
                            @if (Auth::user()->Perfil == 1)
                                <li><a class="uk-navbar-dropdown-close" href="#"
                                        @click="view='llistatpermisos'">Llistat permisos</a></li>
                                <li><a class="uk-navbar-dropdown-close" href="#"
                                        @click="view='incidencies'">Incidències</a></li>
                                <li><a class="uk-navbar-dropdown-close" href="#"
                                        @click="view='fitxatgessuma'">Suma fitxatges mes</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- /BARRA DRETA -->

        <div id="content" data-uk-height-viewport="expand: true">
            <div class="uk-container uk-container-expand">
                <div class="uk-grid uk-grid-medium" data-uk-grid>

                    <!-- panel -->
                    <div class="uk-width-1-1">
                        <transition name="component-fade" mode="out-in">
                            <component v-bind:is="view"></component>
                        </transition>
                        <div>
                            @include('modals')
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <!-- MISATGES -->
        <div id="offcanvas-nav" data-uk-offcanvas="flip: true; overlay: true">
            <div class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide">
                <h4 class="uk-text-muted">Missatges Rebuts</h4>
                <llegirmsg-component />
            </div>
        </div>
        <!-- /MISSATGES -->
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/home.js') }}" defer></script>

</body>

</html>
