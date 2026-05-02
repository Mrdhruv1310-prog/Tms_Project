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
    <title>{{ $title ?? 'Page Title' }}</title>
    <!-- Include jQuery from a CDN -->
    <script src="{{ asset('/sw.js') }}"></script>

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @filamentStyles
    <style>
        #nprogress .bar {
            height: 7px !important;
            background: #ff0800 !important;
        }
        /* add background color */
        /* Full page loader styles */
        #loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: rgba(255, 255, 255, 1);
            /* White background */
        }

        /* Centered loader content */
        #loader-center {
            background-image: url('{{ asset('icons/tms.png') }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: calc(15vw + 100px);
            /* Adjust size as needed */
            width: 100%;
            height: 100%;
            position: relative;
            animation: loader 1s alternate infinite ease-in-out;
        }

        @keyframes loader {
            0% {
                transform: scale(0.8);
            }

            100% {
                transform: scale(0.9);
            }
        }

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
            background: linear-gradient(rgb(28 93 239), rgb(59, 130, 246), rgb(87 146 255));
            /* color of the thumb */
            border-radius: 6px;
            /* roundness of the thumb */
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(rgb(26 86 219), rgb(59, 130, 246), rgb(95 149 250));
            /* color of the thumb on hover */
        }
    </style>
</head>

<body class="text-gray-900 antialiased">
    {{-- <div x-data="{ loading: true }" x-init="window.addEventListener('load', () => loading = false)
    Livewire.on('contentLoading', () => loading = true);
    Livewire.on('contentLoaded', () => loading = false);"> --}}
    <!-- Loader HTML -->
    {{-- <div x-show="loading" x-transition:leave="transition ease-in duration-1000" id="loader">
            <div id="loader-center"></div>
        </div> --}}

    <!-- Include the spinner component -->
    @component('components.spinner')
    @endcomponent
    <!-- Main content -->
    <div class="min-h-screen flex flex-row  sm:justify-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900">
        <div class="w-full px-6 py-4 bg-transparent dark:bg-gray-800 overflow-hidden sm:rounded-lg">
            <x-notifications />
            <livewire:navbar>
                <livewire:sidebar>
                    {{ $slot }}
                    @livewire('view-user-groups')
                    @livewire('task-approval')
                    @livewire('export-task-modal')
                    @livewire('task-update-modal')
                    @livewire('task-view-modal')
                    @livewire('user-details-modal')
                    @livewire('task-details-modal')
        </div>
    </div>
    {{-- </div> --}}

    <script>
            // Re-initialize components after Livewire navigation
        document.addEventListener('livewire:navigated', () => {
        initFlowbite();
        console.log('Flowbite initialized after Livewire navigation');
        // let user = @json(auth()->user());
        
        // if (window.location.pathname === '/login') {
        //     if (user) {
        //         // Redirect authenticated users to the dashboard
        //         window.location.href = '/dashboard';
        //         return;
        //     }
        // }
        //call datepicker destroyer function
        // destroyDatepicker();
        });
    </script>
    {{-- <script data-navigate-once>
        document.addEventListener('DOMContentLoaded', function() {

            initFlowbite();

            console.log('Flowbite initialized on DOMContentLoaded');

            const bottomNavAdd$triggerEl = document.getElementById('bottomnavaddmenuBtn');
            const bottomNavAdd$parentEl = document.getElementById('bottomnavaddmenuParent');
            const bottomNavAdd$targetEl = document.getElementById('bottomnavaddmenuContent');

            const bottomNavMoreMenu$triggerEl = document.getElementById('bottomnavmoremenuBtn');
            const bottomNavMoreMenu$parentEl = document.getElementById('bottomnavmoremenuParent');
            const bottomNavMoreMenu$targetEl = document.getElementById('bottomnavmoremenuContent');

            // Create a new Dial object
            const bottomNavAdddial = new Dial(bottomNavAdd$parentEl, bottomNavAdd$triggerEl,
                bottomNavAdd$targetEl);
            const bottomNavMoreMenudial = new Dial(bottomNavMoreMenu$parentEl,
                bottomNavMoreMenu$triggerEl, bottomNavMoreMenu$targetEl);
            // Toggle visibility on button click
            bottomNavAdd$triggerEl.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent click from propagating to the document
                bottomNavAdddial.toggle();
            });

            document.addEventListener('click', (e) => {
                if (!bottomNavAdd$parentEl.contains(e.target) && bottomNavAdddial
                    ._visible) {
                    bottomNavAdddial.toggle();
                }
            });

            // Toggle visibility on button click
            bottomNavMoreMenu$triggerEl.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent click from propagating to the document
                bottomNavMoreMenudial.toggle();
            });

            document.addEventListener('click', (e) => {
                if (!bottomNavMoreMenu$parentEl.contains(e.target) && bottomNavMoreMenudial
                    ._visible) {
                    bottomNavMoreMenudial.toggle();
                }
            });
        });
        document.addEventListener('livewire:navigated', () => {
            let user = @json(auth()->user());
            if (window.location.pathname === '/login') {
                // If the user is authenticated (user is not null)
                if (user) {
                    // Redirect the user to a different page (e.g., home page)
                    window.location.href = '/dashboard';
                    return;
                }
            }
            initFlowbite();
            console.log('Flowbite initialized on main page');
        });

    </script> --}}
        @filamentScripts
</body>

</html>
