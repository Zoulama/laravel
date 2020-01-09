<?php
    use App\Traits\ColorsTrait;
    $version = time();
?>
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title> {{ trans('texts.app_name') }} </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Pas d'indexation -->
    <meta name="robots" content="noindex" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            {{-- <div class="container" id="user-information" style="opacity: 1;">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-success" style="position: absolute;" role="alert">
                            yolo
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="container">
                <div class="navbar-header">

                    <!-- Bouton pour menu responsive -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Icone du Header -->
                    <a class="navbar-brand" style="padding: 5px 15px;" href="{{ route('root') }}"><img height="40" src="{{ asset('icons/sbeeh.png') }}"></a>
                    {{-- <a class="navbar-brand" href="{{ route('root') }}"> {{ trans('texts.app_name') }}  </a> --}}
                </div>
                <!-- Barre de navigation -->
                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Coté gauche de la barre de navigation -->
                    <ul class="nav navbar-nav">
                    @if (Auth::check())
                        <!-- Onglet Mes balances -->
                        <li class="dropdown
                        @if (Request::is('mes-balances*') && ! Request::is('mes-balances/creer') && ! Request::is('mes-balances/etat'))
                            current-dropdown
                        @endif">
                            <a href="{{ route('scales.show') }}" class="dropdown-toggle" data-toggle="dropdown" role="button">
                                <span class="glyphicon glyphicon-th-list"></span> {{ trans('texts.my_scales') }}<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <!-- Affichage -->
                                <li>
                                    <a href="{{ route('scales.show') }}">
                                        <span class="glyphicon glyphicon-list-alt"></span> 
                                        {{ trans('texts.all_my_scales') }}
                                    </a>
                                </li>
                                <!-- Ajout -->
                                <li>
                                    <a href="{{ route('scales.add') }}">
                                        <span class="glyphicon glyphicon-link"></span> 
                                         {{ trans('texts.add_scale_to') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                     <!-- Onglet Couverture réseau -->
                        <li class="dropdown
                            @if (Request::is('couverture-reseau*'))
                                current-dropdown
                            @endif">
                            <a href="{{ route('maps.show') }}"><span class="glyphicon glyphicon-signal"></span> {{ trans('texts.network_coverage') }} </a>
                        </li>

                      <!-- Onglet tutoriel -->
                      <!--
                        <li class="dropdown">
                            <a href="{{ route('informations.show') }}"><span class="glyphicon glyphicon-book"></span> Informations complémentaires</a>
                        </li>
                      -->
                    </ul>

                    <!-- Coté droit de la barre de navigation -->
                    <ul class="nav navbar-nav navbar-right">
                       <!-- Spécifique aux personnes non logguées -->
                        @if (Auth::guest())
                           <!-- Enregistrement -->
                            @if (Route::current()->getName() == 'login')
                                <li class="dropdown">
                                    <a href="{{ route('reference-showVerification') }}">
                                        <span class="glyphicon glyphicon-log-in"></span> 
                                        {{ trans('texts.sign_up') }}
                                     </a>
                                 </li>
                            @else
                            <!-- Connexion -->
                                <li class="dropdown"><a href="{{ route('login') }}"><span class="glyphicon glyphicon-log-in"></span> {{ trans('texts.login') }} </a></li>
                            @endif
                           
                        @else
                            <!-- Onglet Administration  -->
                            @if (Auth::user()->isAdmin())
                                <li class="dropdown
                                    @if (Request::is('mes-balances/creer') || Request::is('mes-balances/etat'))
                                        current-dropdown
                                    @endif">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                                        <span class="glyphicon glyphicon-wrench"></span> {{ trans('texts.admin') }} <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu" role="menu">
                                        <!-- Etat -->
                                        <li>
                                            <a href="{{ route('scales.showStates') }}"><span class="glyphicon glyphicon-tasks"></span> {{ trans('texts.scales') }}</a>
                                        </li>
                                        <!-- Création balance -->
                                        <li>
                                            <a href="{{ route('scales.create') }}"><span class="glyphicon glyphicon-plus"></span> {{ trans('texts.new_scale') }}</a>
                                        </li>
                                        <!-- Affichage des utilisateurs -->
                                        <li>
                                            <a href="{{ route('users.showUsers') }}"><span class="glyphicon glyphicon-user"></span> {{ trans('texts.users') }}</a>
                                        </li>
                                        <!-- Affichage des messages -->
                                        <li>
                                            <a href="{{ route('messages.showMessages') }}"><span class="glyphicon glyphicon-list-alt"></span> {{ trans('texts.posts') }}</a>
                                        </li>
                                    </ul>
                                </li>
                            <!-- Spécifique à l'utilisateur non admin -->
                            @elseif (!Auth::user()->isAdmin())
                                <!-- Contact -->
                                <li class="dropdown
                                    @if (Request::is('nous-contacter*'))
                                        current-dropdown
                                    @endif">
                                    <a href="{{ route('contact.show') }}"><span class="glyphicon glyphicon-envelope "></span> {{ trans('texts.contact') }}</a>
                                </li>
                            @endif
                           
                            <!-- Onglet Mon compte / Partagé entre utilisateurs -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                                    <span class="glyphicon glyphicon-user"></span> {{ trans('texts.my_account') }} <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <!-- Informations personnelles -->
                                    <li>
                                        <a href="{{ route('home.update') }}"><span class="glyphicon glyphicon-pencil"></span> {{ trans('texts.personnal_info') }}</a>
                                    </li>
                                    <!-- Déconnection -->
                                    <li>
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i><span class="glyphicon glyphicon-log-out"></span> {{ trans('texts.logout') }}</i>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
        </nav>

        @if (Session::has('cMessage'))
        <div class="container" id="user-information" style="opacity: 1;">
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-{{ Session::get('cStyle') }}" role="alert">
                        {{ Session::get('cMessage') }}
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Contenu principal -->
        @yield('content')
    </div>

       <!-- Icone du site sur l'onglet -->
       <link rel="shortcut icon" type="image/x-icon" href="{{ asset('icons/sbeeh.png') }}">

<!-- Styles -->
{{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('plugins/bootstrap/3.3.7/css/bootstrap.min.css') }}?v={{ $version }}" rel="stylesheet">
<link href="{{ asset('plugins/bootstrap/3.3.7/css/bootstrap-theme.min.css') }}?v={{ $version }}" rel="stylesheet">
<link href="{{ asset('plugins/bootstrap-select/1.12.4/css/bootstrap-select.min.css') }}?v={{ $version }}" rel="stylesheet">
<link href="{{ asset('plugins/bootstrap-daterangepicker/2.1.25/css/daterangepicker.css') }}?v={{ $version }}" rel="stylesheet">

<style type="text/css">
    .dropdown, .navbar-brand {
        border-top: 3px solid transparent;
    }
    .current-dropdown {
        border-top-color: {{ ColorsTrait::hexa("sbh_light_green") }};
    }
</style>
@yield('style')

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    <script src="{{ asset('js/var.js') }}?v={{ $version }}"></script>
    <script src="{{ asset('plugins/jquery/3.2.1/dist/jquery.min.js') }}?v={{ $version }}"></script>
    <script src="{{ asset('plugins/bootstrap/3.3.7/js/bootstrap.min.js') }}?v={{ $version }}"></script>
    <script src="{{ asset('plugins/moment/2.18.1/moment.min.js') }}?v={{ $version }}"></script>
    <script src="{{ asset('plugins/bootstrap-select/1.12.4/js/bootstrap-select.min.js') }}?v={{ $version }}"></script>
    <script src="{{ asset('plugins/bootstrap-daterangepicker/2.1.25/js/daterangepicker.js') }}?v={{ $version }}"></script>
    @yield('script')
   
   <script type="text/javascript">
    (function() {
        var userInformation = document.getElementById('user-information');
        if (userInformation) {
            window.setTimeout(function() {
                animate(userInformation);
            }, 3000);
        }

        function animate(element) {
            var opacity = element.style.opacity;

            if (opacity <= 0) {
                element.parentNode.removeChild(element);
                return true;
            }

            element.style.opacity = (opacity - 0.01);
            window.setTimeout(function() {
                animate(element);
            }, 10);

            return false;
        }
    })();
    </script>
</body>
</html>
