<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="192x192" href="/planner-assets/images/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="72x72" href="/planner-assets/images/icons/icon-72x72.png">
    <link rel="manifest" href="/planner-assets/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#e4b9c0">
    <meta name="theme-color" content="#e4b9c0">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

@yield('content')

<div class="container-fluid">
    <hr>
    <div class="row">
        <div class="col">
            <a href="{{ route('logout') }}" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
             {{ __('Logout') }}
            </a>

        </div>
    </div>
    <br>
    <br>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
 @csrf
</form>

</body>
</html>