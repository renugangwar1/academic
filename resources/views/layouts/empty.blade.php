<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link href="{{asset('/build/assets/app-a7de543a.css')}}" rel="stylesheet">
    <script src="{{asset('/build/assets/app-9bc64f35.js')}}"></script>
    
    <style>
        .bgcover {
            /* background-color: {{BgColor()}}; */
            background-image: url({{asset('images/bgnew.jpg')}});
            background-size: cover;
            background-repeat: no-repeat;
        }
        
        @media print {
            @page {
                /* size: 1280px 1808px;  */
                /* size: 1480px 1608px; Set the desired paper size */
                /* transform: scale(0.8);  */
                /* transform-origin: top left; */
                size: A4;
                margin: 0cm;
            }

            div {
                break-inside: avoid;
            }
            a[href]:after {
                content: none !important;
            }
            @media print and (color) {
                * {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
            .pagination{
                display: none;
            }
            .hideblock{
                display: none;
            }
            .hidebg{
                background: none;
                box-shadow: none;
                border: none;
                border-radius: 0px;
            }
            .mainbg{
                width: 100%;
                border: none;
            }
            body
            {
                margin: 0mm;
            }
            .print-container {
                background: none;
                /* transform: scale(0.9); */
                /* transform-origin: top left; */
            }
        }
    </style>
</head>
<body class="h-100">
    <div class="bgcover position-fixed z-n1 top-0 bottom-0 start-0 end-0"></div>
    <div class="position-absolute d-flex flex-column justify-content-between top-0 bottom-0 start-0 end-0 h-100">
        <div id="app">
        <nav class="bg-white border-2 p-0 border-bottom border-secondary hideblock navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <!-- {{ config('app.name', 'NCHMCT') }} -->
                    <img class="w-50" src="{{asset('images/logo.png')}}" alt=""/>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="gap-3 ms-auto navbar-nav">
                        <!-- Authentication Links -->
                        @if(Auth::guard('student')->check() || Auth::guard('institute')->check() || Auth::check())
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <span>Welcome </span>
                                    @if(Auth::guard('student')->check())
                                        {{ Auth::guard('student')->user()->name }}
                                    @elseif(Auth::guard('institute')->check())
                                        {{ Auth::guard('institute')->user()->InstituteName }}
                                    @elseif(Auth::check())
                                        {{ Auth::user()->name }}
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link rounded-1 fw-bold" href="{{Route('student.login')}}">Student Login</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link rounded-1 fw-bold" href="{{Route('institute.login')}}">Institute Login</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5">
                        <div class="mx-auto position-relative z-2" id="Message">
                            @if (Session::has('success'))
                                <div class="bg-success text-white p-2 text-center rounded-md my-2 rounded shadow">
                                    {{ Session::get('success') }}
                                </div>
                            @endif

                            @if (Session::has('error'))
                                <div class="bg-danger text-white p-2 text-center rounded-md my-2 rounded shadow">
                                    {{ Session::get('error') }}
                                </div>
                            @endif

                            @if($errors->any())
                                @foreach($errors->all() as $key=>$error)
                                    <div class="bg-danger text-white p-2 text-center rounded-md my-2 rounded shadow">
                                        {{$error}}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
    <footer class="bg-dark text-white text-center pt-1 pb-0">
        NCHMCT {{'@'.date('Y')}}
    </footer>
</div>
</body>
</html>
