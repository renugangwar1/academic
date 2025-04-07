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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <link href="{{asset('css/dataTables/dataTables.dataTables.css')}}" rel="stylesheet">
    <link href="{{asset('css/dataTables/buttons.dataTables.css')}}" rel="stylesheet">

    <!-- Scripts -->
    <link href="{{ asset('/build/assets/app-a7de543a.css') }}" rel="stylesheet">
    <script src="{{asset('/build/assets/app-9bc64f35.js')}}"></script>

    <style>
    .bgcover {
        /* background-color: {{ BgColor() }}; */
        background-image: url("{{ asset('images/bgnew.jpg') }}");
        background-size: cover;
        background-repeat: no-repeat;
    }

    @media print {
        @page {
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

        .pagination {
            display: none;
        }

        .hideblock {
            display: none;
        }

        .hidebg {
            background: none;
            box-shadow: none;
            border: none;
            border-radius: 0px;
        }

        .mainbg {
            width: 100%;
            border: none;
        }

        body {
            margin: 0cm;
            padding: 0cm;
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
            <main class="h-100">
                <nav
                    class="bg-white border-2 p-0 border-bottom border-secondary hideblock navbar navbar-expand-md navbar-light shadow-sm">
                    <div class="container">
                        <a class="navbar-brand" href="{{ url('/') }}">
                            <!-- {{ config('app.name', 'NCHMCT') }} -->
                            <img class="w-50" src="{{asset('images/logo.png')}}" alt="" />
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <!-- Left Side Of Navbar -->
                            <ul class="navbar-nav me-auto">

                            </ul>

                            <!-- Right Side Of Navbar -->
                            <ul class="gap-3 ms-auto navbar-nav">
                                <!-- Authentication Links -->

                                @if(Auth::guard('student')->check() || Auth::guard('institute')->check() ||
                                Auth::check())
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link rounded-1 fw-bold" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-menu-button-wide" viewBox="0 0 16 16">
                                            <path
                                                d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v2A1.5 1.5 0 0 1 14.5 5h-13A1.5 1.5 0 0 1 0 3.5zM1.5 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5z" />
                                            <path
                                                d="M2 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m10.823.323-.396-.396A.25.25 0 0 1 12.604 2h.792a.25.25 0 0 1 .177.427l-.396.396a.25.25 0 0 1-.354 0zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5" />
                                        </svg>
                                        {{ __('Menu')}}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        @if(Auth::user() && Auth::user()->role !== 0)
                                        @if(isset(Auth::user()->menu_access))
                                        @foreach(strtoarray(Auth::user()->menu_access) as $menu)
                                        <a class="dropdown-item"
                                            href="{{ route(config('constants.menuRoute')[$menu]) }}">
                                            {{ config('constants.menuename')[$menu] }}
                                        </a>
                                        @endforeach
                                        @endif
                                        @if(Auth::user()->role === 3)
                                        <a class="dropdown-item" href="{{ route('excel.Import') }}">
                                            {{ __('Upload Data') }}
                                        </a>
                                        <a class="dropdown-item d-none" href="{{ route('admin.itstudentList') }}">
                                            {{ __('IT List') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('compile_marks') }}">
                                            {{ __('Compile Marks') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('generate_result') }}">
                                            {{ __('Generate Result') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('jnuresult') }}">
                                            {{ __('JNU Result') }}
                                        </a>
                                        @endif
                                        @endif
                                        @if(Auth::guard('student')->user())
                                        <a class="dropdown-item" href="{{ route('student.reappearform') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
                                                <path
                                                    d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z" />
                                                <path
                                                    d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1" />
                                            </svg>
                                            {{ __('Reappear Form') }}
                                        </a>
                                        @endif
                                        @if(Auth::guard('institute')->user())
                                        <a class="dropdown-item" href="{{ route('institute.excel.Import') }}">
                                            {{ __('Upload Data') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('institute.compile_marks') }}">
                                            {{ __('Admit Card') }}
                                        </a>
                                        @endif
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link rounded-1 fw-bold" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-menu-button-wide" viewBox="0 0 16 16">
                                            <path
                                                d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v2A1.5 1.5 0 0 1 14.5 5h-13A1.5 1.5 0 0 1 0 3.5zM1.5 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5z" />
                                            <path
                                                d="M2 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m10.823.323-.396-.396A.25.25 0 0 1 12.604 2h.792a.25.25 0 0 1 .177.427l-.396.396a.25.25 0 0 1-.354 0zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5" />
                                        </svg>
                                        {{ __('Report')}}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        @if(Auth::user() && Auth::user()->role !== 0)
                                        @if(isset(Auth::user()->menu_access))
                                        @foreach(strtoarray(Auth::user()->menu_access) as $menu)
                                        <a class="dropdown-item"
                                            href="{{ route(config('constants.menuRoute')[$menu]) }}">
                                            {{ config('constants.menuename')[$menu] }}
                                        </a>
                                        @endforeach
                                        @endif
                                        @if(Auth::user()->role === 3)
                                        <a class="dropdown-item" href="{{ route('excel.viewdata') }}">
                                            {{ __('View Data') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('excel.viewhistory') }}">
                                            {{ __('Student History') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('excel.instituteview') }}">
                                            {{ __('Institute View') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('excel.export') }}">
                                            {{ __('Download Data') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('excel.printerData') }}">
                                            {{ __('Printer Data') }}
                                        </a>
                                        @endif
                                        @endif
                                        @if(Auth::guard('student')->user())
                                        <a class="dropdown-item" href="{{ route('student.reappearform') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
                                                <path
                                                    d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z" />
                                                <path
                                                    d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1" />
                                            </svg>
                                            {{ __('Reappear Form') }}
                                        </a>
                                        @endif
                                        @if(Auth::guard('institute')->user())
                                        <a class="dropdown-item" href="{{ route('institute.excel.Import') }}">
                                            {{ __('Upload Data') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('institute.compile_marks') }}">
                                            {{ __('Admit Card') }}
                                        </a>
                                        @endif
                                    </div>
                                </li>
                                @if(Auth::guard('student')->check() || Auth::guard('institute')->check() ||
                                Auth::check())
                                <!-- master route -->
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link rounded-1 fw-bold" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-sliders2-vertical" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M0 10.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1H3V1.5a.5.5 0 0 0-1 0V10H.5a.5.5 0 0 0-.5.5M2.5 12a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 1 0v-2a.5.5 0 0 0-.5-.5m3-6.5A.5.5 0 0 0 6 6h1.5v8.5a.5.5 0 0 0 1 0V6H10a.5.5 0 0 0 0-1H6a.5.5 0 0 0-.5.5M8 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 1 0v-2A.5.5 0 0 0 8 1m3 9.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1H14V1.5a.5.5 0 0 0-1 0V10h-1.5a.5.5 0 0 0-.5.5m2.5 1.5a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 1 0v-2a.5.5 0 0 0-.5-.5" />
                                        </svg>
                                        {{ __('Master')}}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        @if(Auth::user() && Auth::user()->role === 3)
                                        <a class="dropdown-item" href="{{ route('admin.coursemaster') }}">
                                            {{ __('Course Master') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.subjectmaster') }}">
                                            {{ __('Subject Master') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.institutemaster') }}">
                                            {{ __('Institute Master') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.students') }}">
                                            {{ __('Student Master') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.users') }}">
                                            {{ __('Users Master') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.excelLog') }}">
                                            {{ __('Excel Logs') }}
                                        </a>
                                        <a class="dropdown-item" href="{{Route('admin.setting')}}">
                                            {{__('Setting')}}
                                        </a>
                                        @endif
                                        @if(Auth::guard('institute')->user())
                                        <a class="dropdown-item" href="{{ route('institute.students') }}">
                                            {{ __('Student Master') }}
                                        </a>

                                        <a class="dropdown-item" href="{{route('institute.newenrollment')}}">
                                            {{__('New Enrollment')}}
                                        </a>

                                        <a class="dropdown-item" href="{{ route('institute.excelLog') }}">
                                            {{ __('Excel Logs') }}
                                        </a>
                                        @endif
                                    </div>
                                </li>
                                @endif
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
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

                                        <form id="logout-form"
                                            action="{{ Auth::guard('student')->user() ? route('student.logout') : (Auth::guard('institute')->user() ? route('institute.logout') : route('logout')) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                                @else
                                <li class="nav-item dropdown">
                                    <a class="nav-link rounded-1 fw-bold" href="{{Route('student.login')}}">Student
                                        Login</a>
                                </li>

                                <!-- <li class="nav-item dropdown">
                                        <a class="nav-link rounded-1 fw-bold" href="{{Route('login')}}">NCHMCT Login</a>
                                    </li> -->

                                <li class="nav-item dropdown">
                                    <a class="nav-link rounded-1 fw-bold" href="{{Route('institute.login')}}">Institute
                                        Login</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </nav>
                <div class="container mx-auto z-2 position-fixed bottom-0 end-0 w-auto fw-bold text-capitalize"
                    id="Message">
                    @if (Session::has('success'))
                    <div
                        class="bg-body fade-in form-select-sm font-monospace m-1 p-1 rounded px-2 border border-2 border-success">
                        {{ Session::get('success') }}
                    </div>
                    @endif

                    @if (Session::has('error'))
                    <div
                        class="bg-body fade-in form-select-sm font-monospace m-1 p-1 rounded px-2 border border-2 border-danger">
                        {{ Session::get('error') }}
                    </div>
                    @endif

                    @if($errors->any())
                    @foreach($errors->all() as $key=>$error)
                    <div
                        class="bg-body fade-in form-select-sm font-monospace m-1 p-1 rounded px-2 border border-2 border-danger">
                        {{$error}}
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="py-3 position-relative">
                    @yield('content')
                </div>
            </main>
        </div>
        <div id="alert"></div>
        <footer class="bg-dark text-white text-center pt-1 pb-0">
            NCHMCT {{'@'.date('Y')}}
        </footer>
    </div>
    <script src="{{asset('js/dataTables/dataTables.js')}}"></script>
    <script src="{{asset('js/dataTables/dataTables.buttons.js')}}"></script>
    <script src="{{asset('js/dataTables/buttons.dataTables.js')}}"></script>
    <script src="{{asset('js/dataTables/jszip.min.js')}}"></script>
    <script src="{{asset('js/dataTables/pdfmake.min.js')}}"></script>
    <script src="{{asset('js/dataTables/vfs_fonts.js')}}"></script>
    <script src="{{asset('js/dataTables/buttons.html5.min.js')}}"></script>
</body>

</html>