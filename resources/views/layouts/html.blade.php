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
        body {
            font-size: x-small;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0px;
            border: 1px solid #ddd;
            text-align: center;
        }

        @page {
            size: A4;
            margin: 1cm;
        }

        @media print {
            .pagebreak {
                page-break-after: always;
            }

            .hide {
                display: none;
            }

            .printheight{
                height:100%;
            }

            hr{
                display:none;
            }
            /* Additional print-specific styles can go here */
        }
    </style>
</head>

<body class="h-100">
    <div class="position-fixed z-n1 top-0 bottom-0 start-0 end-0"></div>
        <div class="position-absolute d-flex flex-column justify-content-between top-0 bottom-0 start-0 end-0 h-100">
            <main class="px-5 h-100">
                <div class="printheight">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <script type="text/javascript">
        // document.addEventListener('DOMContentLoaded', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 2000);
        // });
    </script>
</body>
</html>