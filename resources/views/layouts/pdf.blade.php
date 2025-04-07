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
    <!-- vite(['resources/sass/app.scss', 'resources/js/app.js']) -->
    <link href="{{asset('/css/pdf.css')}}" rel="stylesheet">

    <style>
        body {
            font-size: x-small;
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
                /* * {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                } */
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

<body>
    @yield('content')
</body>

</html>