<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script>
        window.Laravel = {!!json_encode(['csrfToken' => csrf_token()]) !!};
        window.baseUrl = "{!!url("/")!!}";
        window.authUser= {!! json_encode(Auth::user()) !!}
    </script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>

    <link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet" />
    <style type="text/css">
        #map {
            width: 655px;
            height: 350px;
        }
        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255,255,255,0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 5px;
            }
        .info h4 {
            margin: 0 0 5px;
            color: #777;
        }
        .legend {
            text-align: left;
            line-height: 18px;
            color: #555;
            }
            .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
            }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
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
                            <li class="nav-item"><a class="nav-link" href="javascript:;" @click="goTo('web', null)">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:;" @click="goTo('user', null)" v-if="userData.role == 'ADMIN'">User</a></li>
                            <li class="nav-item dropdown"  v-if="userData.role == 'ADMIN'">
                                <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Data Master <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown1">
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('kabupaten', null)">Kabupaten</a>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('kecamatan', null)">Kecamatan</a>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('kelurahan', null)">Kelurahan</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('config-help', null)">Help</a>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('class', null)">Class</a>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('kategori', null)">Kategori</a>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('kriteria', null)">Kriteria</a>
                                </div>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="javascript:;" @click="goTo('kriteria', null)"  v-if="userData.role != 'ADMIN'">Kriteria</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:;" @click="goTo('aturan', null)">Basis Aturan</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:;" @click="goTo('klasifikasi', null)" v-if="userData.role != 'ADMIN'">Klasifikasi</a></li>
                            <li class="nav-item dropdown" v-if="userData.role != 'ADMIN'">
                                <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Maps <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown1">
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('maps', null)">Maps</a>
                                    <a class="dropdown-item" href="javascript:;" @click="goTo('maps-cluster', null)">Cluster</a>
                                </div>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="javascript:;" @click="goTo('help', null)" v-if="userData.role != 'ADMIN'">Bantuan</a></li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
</body>
</html>
