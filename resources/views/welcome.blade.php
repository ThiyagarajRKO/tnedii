<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }



        /*CSS FOR ORGANIZATION CHART*/


        .org-chart-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .org-chart-header h2 {
            display: inline-block;
            position: relative;
            color: #123F6B;
            font-size: 24px;
            padding: 0 20px;
        }

        .org-chart-header h2::before,
        .org-chart-header h2::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 50px;
            height: 2px;
            background: #7CBA0A;
        }

        .org-chart-header h2::before {
            left: -60px;
        }

        .org-chart-header h2::after {
            right: -60px;
        }

        .scroll-container {
            overflow-x: auto;
            padding-bottom: 20px;
        }

        .organization-chart {
            display: inline-block;
        }

        .organization-chart ul {
            padding-top: 20px;
            position: relative;
            transition: all 0.5s;
            display: flex;
        }

        .organization-chart li {
            float: left;
            text-align: center;
            list-style-type: none;
            padding: 20px 5px 0 5px;
            position: relative;
            transition: all 0.5s;
        }

        .organization-chart li::before,
        .organization-chart li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid #ccc;
            width: 50%;
            height: 20px;
            z-index: -1;
        }

        .organization-chart li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #ccc;
        }

        .organization-chart li:only-child::after,
        .organization-chart li:only-child::before {
            display: none;
        }

        .organization-chart li:only-child {
            padding-top: 0;
        }

        .organization-chart li:first-child::before,
        .organization-chart li:last-child::after {
            border: 0 none;
        }

        .organization-chart li:last-child::before {
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
        }

        .organization-chart li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .organization-chart ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            width: 0;
            height: 20px;
            z-index: -1;
        }

        .organization-chart li a {
            border: 2px solid #ccc;
            padding: 15px 10px;
            text-decoration: none;
            color: #212121;
            display: inline-block;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            background-color: #123F6B;
            color: #fff;
            white-space: nowrap;
            transform-origin: center;
        }

        .organization-chart li a:hover {
            border: 2px solid #fff;
            color: #fff;
            background-color: #7CBA0A;
            transform: scale(1.15);
            z-index: 1;
        }

        .organization-chart li a span {
            display: block;
            font-size: 12px;
            color: #999;
        }

        .org-chart-header h2 {
            color: #123F6B;
        }
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
        <div class="top-right links">
            @auth
            <a href="{{ url('/home') }}">Home</a>
            @else
            <a href="{{ route('login') }}">Login</a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}">Register</a>
            @endif
            @endauth
        </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                Laravel
            </div>

            <div class="links">
                <a href="https://laravel.com/docs">Docs</a>
                <a href="https://laracasts.com">Laracasts</a>
                <a href="https://laravel-news.com">News</a>
                <a href="https://blog.laravel.com">Blog</a>
                <a href="https://nova.laravel.com">Nova</a>
                <a href="https://forge.laravel.com">Forge</a>
                <a href="https://vapor.laravel.com">Vapor</a>
                <a href="https://github.com/laravel/laravel">GitHub</a>
            </div>
        </div>
    </div>
</body>

</html>