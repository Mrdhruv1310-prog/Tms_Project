<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('icons/nslogo.png') }}">
    <meta name="theme-color" content="#1a56db" />
    <link rel="apple-touch-icon" href="{{ asset('icons/nslogo.png') }}">
    <link rel="manifest" href="{{ asset('/1manifest.json') }}">
    <title>TMS Portal | NS GROUP</title>
    <script src="{{ asset('/sw.js') }}"></script>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <style>
        /* Lightweight custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="text-gray-900 antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <x-notifications />

            {{ $slot }}
        </div>
    </div>

    <script>
        // Efficient and smooth Flowbite initialization
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initFlowbite === 'function') {
                initFlowbite();
            }
        });
        document.addEventListener('livewire:navigated', () => {
            if (typeof initFlowbite === 'function') {
                initFlowbite();
            }
        });
    </script>
</body>

</html>
