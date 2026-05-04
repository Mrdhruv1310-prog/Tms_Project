<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cache-control" content="private, max-age=0, no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <link rel="icon" type="image/png" href="{{ asset('icons/nslogo.png') }}">
    <meta name="theme-color" content="#1a56db"/>
    <link rel="apple-touch-icon" href="{{ asset('icons/nslogo.png') }}">
    <link rel="manifest" href="{{ asset('/1manifest.json') }}">
    <title>{{ 'TMS Portal | NS GROUP' }}</title>
    <script src="{{ asset('/sw.js') }}"></script>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <style>
            /* CSS to customize scrollbar */
            ::-webkit-scrollbar {
            width: 12px;
            /* width of the scrollbar */
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            /* color of the track */
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            /* color of the thumb */
            border-radius: 6px;
            /* roundness of the thumb */
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
            /* color of the thumb on hover */
        }
    </style>
</head>

<body class="text-gray-900 antialiased">
    <!-- Loader HTML -->
    {{-- <div id="loader">
            <div id="loader-center"></div>
        </div> --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <x-notifications />

            {{ $slot }}
        </div>
    </div>

    <script data-navigate-once>
        document.addEventListener('DOMContentLoaded', function() {
            initFlowbite();
            console.log('Flowbite initialized on DOMContentLoaded');
        });
        document.addEventListener('livewire:navigated', () => {
            // let user = @json(auth()->user());
            // if (window.location.pathname === '/login') {
            //     // If the user is authenticated (user is not null)
            //     if (user) {
            //         // Redirect the user to a different page (e.g., home page)
            //         window.location.href = '/dashboard';
            //         return;
            //     }
            // }
            // console.log(user);
            initFlowbite();
            console.log('Flowbite initialized on main page');
        });
        // $(document).ready(function() {
        //     $('#loader').fadeOut('slow');
        // });
    </script>

</body>

</html>
