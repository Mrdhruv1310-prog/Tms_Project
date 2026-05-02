<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Auth' }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="private, max-age=0, no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <link rel="icon" type="image/png" href="{{ asset('icons/nslogo.png') }}">
    <meta name="theme-color" content="#1a56db" />
    <link rel="apple-touch-icon" href="{{ asset('icons/nslogo.png') }}">
    <link rel="manifest" href="{{ asset('/1manifest.json') }}">
    {{-- <title>{{ 'TMS Portal | NS GROUP' }}</title> --}}
    <script src="{{ asset('/sw.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

    {{ $slot }}

    @livewireScripts
</body>

</html>
