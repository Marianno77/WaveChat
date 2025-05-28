<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>WaveChat</title>
</head>

<body>

    <div class="container">
        <div class="header">
            @include('partials.menu')
        </div>
        <div class="content">
            @yield('content')
        </div>
    </div>

</body>

</html>