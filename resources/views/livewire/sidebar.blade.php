<div>
    <div x-data="{ hoveredTab: null, activeTab: null, hoverTimeout: null, contentVisible: false, isFadingOut: false, isTransitioning: false, isHoveringSidebar: false }"
        class="hidden md:flex fixed top-0 left-0 z-20 flex flex-shrink-0 transition-all h-screen antialiased text-gray-900 dark:bg-dark dark:text-light">

        <div class="fixed inset-y-0 z-10 w-16 bg-white"></div>

        <!-- Left mini bar -->
        <nav aria-label="Options"
            class="z-20 flex-col items-center flex-shrink-0 hidden w-16 py-4 bg-white border-r-2 border-indigo-100 shadow-md sm:flex rounded-tr-3xl rounded-br-3xl">
            <div class="flex flex-col items-center flex-1 p-2 space-y-4 mt-14">
                <!-- Dashboard Button -->
                <button @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'dashboardTab'; contentVisible = true"
                    @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 250)"
                    @click="hoveredTab === 'dashboardTab' ? (contentVisible = !contentVisible) : (hoveredTab = 'dashboardTab', contentVisible = true)"
                    class="p-2 transition-colors group rounded-lg shadow-md hover:bg-blue-600 hover:text-white "
                    :class="{
                        'bg-blue-600 text-white outline-none ring ring-blue-600 ring-offset-white ring-offset-2': $wire
                            .activeMenu === 'dashboard',
                        'text-gray-500 bg-white hover:bg-blue-600 hover:text-white': $wire.activeMenu !== 'dashboard'
                    }"
                    title="Dashboard">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg x-bind:class="{ 'text-blue-800': activeTab === 'dashboardTab' }"
                        class="w-6 h-6 transition duration-75 group-hover:text-white"
                        :class="{
                            'text-white': $wire.activeMenu === 'dashboard',
                            'text-gray-500 dark:text-gray-400 group-hover:text-white': $wire.activeMenu !== 'dashboard'
                        }"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                </button>

                <!-- Tasks Button -->
                <button @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'tasksTab'; contentVisible = true"
                    @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 250)"
                    @click="hoveredTab === 'tasksTab' ? (contentVisible = !contentVisible) : (hoveredTab = 'tasksTab', contentVisible = true)"
                    class="p-2 transition-colors group rounded-lg shadow-md hover:bg-blue-600 hover:text-white"
                    :class="{
                        'bg-blue-600 text-white outline-none ring ring-blue-600 ring-offset-white ring-offset-2': [
                            'tasks', 'my_tasks', 'assigned_to_others'
                        ].includes($wire.activeMenu),
                        'text-gray-500 bg-white hover:bg-blue-600 hover:text-white':
                            !['tasks', 'my_tasks', 'assigned_to_others'].includes($wire.activeMenu)
                    }"
                    title="Task Management">

                    <span class="sr-only">Tasks</span>
                    <svg class="w-6 h-6 transition duration-75 group-hover:text-white"
                        :class="{
                            'text-white': ['tasks', 'my_tasks', 'assigned_to_others'].includes($wire.activeMenu),
                            'text-gray-500 dark:text-gray-400 group-hover:text-white':
                                !['tasks', 'my_tasks', 'assigned_to_others'].includes($wire.activeMenu)
                        }"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd"></path>
                    </svg>

                </button>

                {{-- BUTTON FOR categories --}}
                @auth
                    @if (auth()->user()->role === 'admin')
                        <button
                            @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'categoriesTab'; contentVisible = true"
                            @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 250)"
                            @click="hoveredTab === 'categoriesTab' ? (contentVisible = !contentVisible) : (hoveredTab = 'categoriesTab', contentVisible = true)"
                            class="p-2 transition-colors group rounded-lg shadow-md hover:bg-blue-600 hover:text-white"
                            :class="{
                                'bg-blue-600 text-white outline-none ring ring-blue-600 ring-offset-white ring-offset-2': $wire
                                    .activeMenu === 'categories',
                                'text-gray-500 bg-white hover:bg-blue-600 hover:text-white': $wire
                                    .activeMenu !== 'categories'
                            }"
                            title="Categories">
                            <span class="sr-only">Categories</span>
                            <svg class="w-6 h-6 transition duration-75 group-hover:text-white"
                                :class="{
                                    'text-white': $wire
                                        .activeMenu === 'categories',
                                    'text-gray-500 dark:text-gray-400 group-hover:text-white': $wire
                                        .activeMenu !== 'categories'
                                }"
                                fill="currentColor" viewBox="0 0 1025 1024" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M513.009189 73.143171a73.142805 73.142805 0 0 1 30.171407 6.582852l404.479711 192.548434A18.285701 18.285701 0 0 1 951.866018 274.285884a18.285701 18.285701 0 0 1-3.474283 2.011427L543.180596 468.845745a73.142805 73.142805 0 0 1-60.342814 0L78.35807 276.297311A18.285701 18.285701 0 0 1 74.152359 274.285884a18.285701 18.285701 0 0 1 3.474283-2.011427L482.837782 79.726023A73.142805 73.142805 0 0 1 513.009189 73.143171m0-73.142805a143.725612 143.725612 0 0 0-61.622814 13.714276L46.906664 206.080219a73.142805 73.142805 0 0 0 0 136.411331l404.479711 192.365577a145.371325 145.371325 0 0 0 123.245627 0l404.479711-192.365577a73.142805 73.142805 0 0 0 0-136.411331L574.632002 13.714642A143.725612 143.725612 0 0 0 513.009189 0.000366z" />
                                <path
                                    d="M513.009189 773.485528a176.639874 176.639874 0 0 1-79.177087-18.285702L21.306683 550.217116a36.571402 36.571402 0 1 1 32.548548-65.462811l412.525419 204.799854a105.691353 105.691353 0 0 0 93.257077 0l412.525419-204.799854a36.571402 36.571402 0 0 1 32.548548 65.462811l-412.525419 204.799853A176.639874 176.639874 0 0 1 513.009189 773.485528z" />
                                <path
                                    d="M513.009189 1023.999634a179.017015 179.017015 0 0 1-79.177087-18.285701L21.306683 800.731222a36.571402 36.571402 0 1 1 32.548548-65.46281l412.525419 204.799854a105.691353 105.691353 0 0 0 93.257077 0l412.525419-204.799854a36.571402 36.571402 0 0 1 32.548548 65.46281L592.186275 1005.713933A179.017015 179.017015 0 0 1 513.009189 1023.999634z" />
                            </svg>
                        </button>
                    @endif
                @endauth

                @auth
                    @if (auth()->user()->role === 'admin')
                        {{-- button for users --}}
                        <button @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'usersTab'; contentVisible = true"
                            @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 250)"
                            class="p-2 transition-colors group rounded-lg shadow-md hover:bg-blue-600 hover:text-white"
                            @click="hoveredTab === 'usersTab' ? (contentVisible = !contentVisible) : (hoveredTab = 'usersTab', contentVisible = true)"
                            :class="{
                                'bg-blue-600 text-white outline-none ring ring-blue-600 ring-offset-white ring-offset-2': [
                                    'users', 'manageusergroup'
                                ].includes($wire.activeMenu),
                                'text-gray-500 bg-white hover:bg-blue-600 hover:text-white': ![
                                    'users', 'manageusergroup'
                                ].includes($wire.activeMenu)
                            }"
                            title="Users">
                            <span class="sr-only">Users</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                                :class="{
                                    'text-white': ['users', 'manageusergroup'].includes($wire
                                        .activeMenu),
                                    'text-gray-500 dark:text-gray-400 group-hover:text-white': !['users',
                                        'manageusergroup'
                                    ].includes($wire.activeMenu)
                                }">
                                <path fill-rule="evenodd"
                                    d="M10 12a3 3 0 100-6 3 3 0 000 6zM10 2a5 5 0 00-5 5c0 2.5 2 4.5 4.5 4.5S14 9.5 14 7A5 5 0 0010 2zm0 10c-2.5 0-7 1.25-7 3.5V17h14v-1.5c0-2.25-4.5-3.5-7-3.5z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    @endif
                @endauth

                {{-- export report link --}}
                <button @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'exportTab'; contentVisible = true"
                    @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 250)"
                    @click="hoveredTab === 'exportTab' ? (contentVisible = !contentVisible) : (hoveredTab = 'exportTab', contentVisible = true)"
                    class="p-2 transition-colors group rounded-lg shadow-md hover:bg-blue-600 hover:text-white"
                    :class="{
                        'bg-blue-600 text-white outline-none ring ring-blue-600 ring-offset-white ring-offset-2': $wire
                            .activeMenu === 'export',
                        'text-gray-500 bg-white hover:bg-blue-600 hover:text-white': $wire.activeMenu !== 'export'
                    }"
                    title="Export Report">
                    <span class="sr-only">Export Report</span>
                    <svg class="w-6 h-6 transition duration-75 group-hover:text-white"
                        :class="{
                            'text-white': $wire
                                .activeMenu === 'export',
                            'text-gray-500 dark:text-gray-400 group-hover:text-white': $wire
                                .activeMenu !== 'export'
                        }"
                        fill="currentColor" stroke="currentColor" viewBox="0 0 486.4 486.4"
                        xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <g>
                                <g>
                                    <path
                                        d="M404.587,35.439h-89.6V9.5c0-5.246-4.254-9.5-9.5-9.5H180.913c-5.246,0-9.5,4.254-9.5,9.5v25.939h-89.6
                                        c-5.246,0-9.5,4.254-9.5,9.5V476.9c0,5.246,4.254,9.5,9.5,9.5h322.774c5.246,0,9.5-4.254,9.5-9.5V44.939
                                        C414.087,39.692,409.833,35.439,404.587,35.439z M190.413,19h105.574v54.749H190.413V19z M395.087,467.4H91.313V54.439h80.1
                                        V83.25c0,5.246,4.254,9.5,9.5,9.5h124.574c5.246,0,9.5-4.254,9.5-9.5V54.439h80.1V467.4z" />
                                    <path d="M131.618,141.596h146.541c5.246,0,9.5-4.254,9.5-9.5c0-5.246-4.254-9.5-9.5-9.5H131.618c-5.246,0-9.5,4.254-9.5,9.5
                                        C122.118,137.342,126.372,141.596,131.618,141.596z" />
                                    <path d="M357.656,171.443H131.618c-5.246,0-9.5,4.254-9.5,9.5c0,5.246,4.254,9.5,9.5,9.5h226.038c5.246,0,9.5-4.254,9.5-9.5
                                        C367.156,175.697,362.902,171.443,357.656,171.443z" />
                                    <path d="M357.656,220.291H131.618c-5.246,0-9.5,4.254-9.5,9.5c0,5.246,4.254,9.5,9.5,9.5h226.038c5.246,0,9.5-4.254,9.5-9.5
                                        C367.156,224.545,362.902,220.291,357.656,220.291z" />
                                    <path d="M357.656,266.264H131.618c-5.246,0-9.5,4.254-9.5,9.5c0,5.246,4.254,9.5,9.5,9.5h226.038c5.246,0,9.5-4.254,9.5-9.5
                                        C367.156,270.518,362.902,266.264,357.656,266.264z" />
                                    <path d="M326.049,141.596h31.607c5.246,0,9.5-4.254,9.5-9.5c0-5.246-4.254-9.5-9.5-9.5h-31.607c-5.246,0-9.5,4.254-9.5,9.5
                                        C316.549,137.342,320.803,141.596,326.049,141.596z" />
                                    <path
                                        d="M261.631,373.82c0-2.536-1.015-4.968-2.818-6.752l-36.227-35.857c-3.588-3.552-9.331-3.677-13.069-0.281
                                        c-12.546,11.393-19.742,27.592-19.742,44.444c0,16.116,6.335,31.261,17.838,42.647c1.851,1.832,4.267,2.748,6.683,2.748
                                        s4.832-0.915,6.683-2.748l37.835-37.449C260.617,378.787,261.631,376.356,261.631,373.82z M215.053,397.153
                                        c-4.089-6.473-6.277-13.961-6.277-21.779c0-8.494,2.685-16.752,7.546-23.631l22.306,22.077L215.053,397.153z" />
                                    <path d="M341.655,332.729v-0.001l-0.001-0.001c-11.477-11.36-26.728-17.616-42.946-17.616c-15.234,0-29.811,5.618-41.044,15.819
                                        c-1.931,1.753-3.055,4.222-3.112,6.829c-0.056,2.607,0.962,5.122,2.816,6.956l29.405,29.105l-31.013,30.697
                                        c-1.803,1.784-2.817,4.216-2.817,6.752s1.014,4.967,2.817,6.752c11.477,11.36,26.73,17.616,42.949,17.616
                                        c33.517,0,60.786-27.034,60.786-60.263C359.493,359.259,353.158,344.115,341.655,332.729L341.655,332.729z M278.731,339.128
                                        c6.077-3.272,12.922-5.016,19.976-5.016c7.885,0,15.437,2.142,21.973,6.147l-20.403,20.195L278.731,339.128z M298.707,416.637
                                        c-7.887,0-15.438-2.142-21.973-6.147l37.611-37.228l19.87-19.667c4.09,6.474,6.278,13.961,6.278,21.779
                                        C340.493,398.127,321.748,416.637,298.707,416.637z" />
                                    <path d="M247.95,34.2h-32.3c-5.246,0-9.5,4.254-9.5,9.5c0,5.246,4.254,9.5,9.5,9.5h32.3c5.246,0,9.5-4.254,9.5-9.5
                                        C257.45,38.454,253.196,34.2,247.95,34.2z" />
                                </g>
                            </g>
                        </g>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Sidebar content for links -->
        {{-- <div > --}}

        <!-- Links section -->
        <nav x-show="contentVisible" x-transition:enter="transition-transform duration-500"
            x-transition:enter-start="transform -translate-x-full" x-transition:enter-end="transform translate-x-0"
            x-transition:leave="transition-transform duration-250" x-transition:leave-start="transform translate-x-0"
            x-transition:leave-end="transform -translate-x-full" x-show="hoveredTab !== null"
            class="fixed inset-y-0 left-0 z-10 flex-shrink-0 w-64 bg-white border-r-2 border-blue-100 shadow-lg sm:left-16 rounded-tr-3xl rounded-br-3xl sm:w-72 lg:static lg:w-64"
            @mouseenter="isHoveringSidebar = true"
            @mouseleave="setTimeout(() => {isHoveringSidebar = false; if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 500)"
            aria-label="Main" class="flex flex-col h-full">
            <!-- Dashboard Link -->
            <div x-show="hoveredTab === 'dashboardTab'"
                class="group flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                <a href="{{ route('dashboard') }}" wire:navigate
                    @click.prevent="activeTab = 'dashboardTab'; contentVisible = false"
                    class="flex items-center w-full space-x-2 text-white bg-blue-600 rounded-lg p-2 text-base font-medium"
                    :class="{
                        'bg-blue-600': $wire
                            .activeMenu === 'dashboard',
                        'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                            .activeMenu !== 'dashboard'
                    }">
                    <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                        :class="{
                            'bg-white text-blue-600': $wire
                                .activeMenu === 'dashboard',
                            'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                .activeMenu !== 'dashboard'
                        }">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </span>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- my Tasks Link -->
            <div x-show="hoveredTab === 'tasksTab'"
                class="flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                <div class="group">
                    <a href="{{ route('tasks', ['task_view' => 'my_tasks']) }}"
                        @click.prevent="activeTab = 'tasksTab'; contentVisible = false; window.location.href = $el.getAttribute('href')"
                        class="flex items-center w-full space-x-2 text-white rounded-lg p-2 text-base font-medium"
                        :class="{
                            'bg-blue-600': $wire
                                .activeMenu === 'my_tasks',
                            'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                                .activeMenu !== 'my_tasks'
                        }">

                        <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                            :class="{
                                'bg-white text-blue-600': $wire
                                    .activeMenu === 'my_tasks',
                                'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                    .activeMenu !== 'my_tasks'
                            }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="30px"
                                height="30px" viewBox="0 0 47 47" stroke="none">
                                <g>
                                    <g id="Layer_1_22_">
                                        <g>
                                            <path d="M6.12,38.52V5.136h26.962v28.037l5.137-4.243V2.568C38.219,1.15,37.07,0,35.652,0h-32.1C2.134,0,0.985,1.15,0.985,2.568
                                        v38.519c0,1.418,1.149,2.568,2.567,2.568h22.408L22.33,38.52H6.12z" />
                                            <path
                                                d="M45.613,27.609c-0.473-0.446-1.2-0.467-1.698-0.057l-11.778,9.734l-7.849-4.709c-0.521-0.312-1.188-0.219-1.603,0.229
                                        c-0.412,0.444-0.457,1.117-0.106,1.613l8.506,12.037c0.238,0.337,0.625,0.539,1.037,0.543c0.004,0,0.008,0,0.012,0
                                        c0.408,0,0.793-0.193,1.035-0.525l12.6-17.173C46.149,28.78,46.084,28.055,45.613,27.609z" />
                                            <path d="M27.306,8.988H11.897c-1.418,0-2.567,1.15-2.567,2.568s1.149,2.568,2.567,2.568h15.408c1.418,0,2.566-1.15,2.566-2.568
                                        S28.724,8.988,27.306,8.988z" />
                                            <path d="M27.306,16.691H11.897c-1.418,0-2.567,1.15-2.567,2.568s1.149,2.568,2.567,2.568h15.408c1.418,0,2.566-1.149,2.566-2.568
                                        C29.874,17.841,28.724,16.691,27.306,16.691z" />
                                            <path d="M27.306,24.395H11.897c-1.418,0-2.567,1.15-2.567,2.568s1.149,2.568,2.567,2.568h15.408c1.418,0,2.566-1.15,2.566-2.568
                                        C29.874,25.545,28.724,24.395,27.306,24.395z" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span>My Tasks</span>
                    </a>
                </div>

                <div class="group">
                    <a href="{{ route('tasks', ['task_view' => 'assigned_to_others']) }}"
                        @click.prevent="activeTab = 'tasksTab'; contentVisible = false; window.location.href = $el.getAttribute('href')"
                        class="flex items-center w-full space-x-2 text-white  rounded-lg p-2 text-base font-medium"
                        :class="{
                            'bg-blue-600': $wire
                                .activeMenu === 'assigned_to_others',
                            'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                                .activeMenu !== 'assigned_to_others'
                        }">
                        <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                            :class="{
                                'bg-white text-blue-600': $wire
                                    .activeMenu === 'assigned_to_others',
                                'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                    .activeMenu !== 'assigned_to_others'
                            }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="30px"
                                height="30px" viewBox="0 0 297 297" stroke="none">
                                <g>
                                    <g>
                                        <path
                                            d="M276.354,215.56h-25.516l-22.562-22.562c2.91-4.938,4.163-10.739,3.466-16.518c-1.564-12.969-2.813-22.582-3.71-28.573
                                                c-2.718-18.133-16.647-32.894-35.485-37.604l-14.834-3.708c-1.96-1.904-2.289-8.161-1.751-11.493
                                                c2.381-2.294,4.691-4.876,6.752-7.754c8.026-11.21,12.628-25.494,12.628-39.187c0-27.51-19.214-46.724-46.724-46.724
                                                s-46.724,19.214-46.724,46.724c0,13.693,4.603,27.977,12.628,39.187c2.06,2.875,4.371,5.456,6.75,7.75
                                                c0.54,3.33,0.213,9.591-1.749,11.496l-14.835,3.708c-18.837,4.71-32.766,19.471-35.484,37.604
                                                c-0.901,6.013-2.15,15.626-3.711,28.573c-0.69,5.721,0.53,11.465,3.378,16.371l-22.709,22.709H20.646
                                                C9.262,215.559,0,224.82,0,236.206v23.196c0,11.385,9.262,20.646,20.646,20.646h54.23c11.385,0,20.646-9.262,20.646-20.646
                                                v-23.196c0-11.385-9.262-20.646-20.646-20.646h-0.524l10.496-10.496c2.192,0.585,4.47,0.892,6.781,0.892h47.022v25.12h-17.266
                                                c-11.385,0-20.646,9.262-20.646,20.646v23.196c0,11.385,9.262,20.646,20.646,20.646h54.23c11.385,0,20.646-9.262,20.646-20.646
                                                v-23.196c0-11.385-9.262-20.646-20.646-20.646h-17.031v-25.12h47.022c2.245,0,4.457-0.293,6.592-0.846l10.45,10.45h-0.524
                                                c-11.385,0-20.646,9.262-20.646,20.646v23.196c0,11.385,9.262,20.646,20.646,20.646h54.23c11.385,0,20.646-9.262,20.646-20.646
                                                v-23.196C297,224.82,287.738,215.56,276.354,215.56z M74.876,233.498c1.492,0,2.707,1.215,2.707,2.707v23.196
                                                c0,1.492-1.215,2.707-2.707,2.707h-54.23c-1.492,0-2.707-1.215-2.707-2.707v-23.196c0-1.492,1.215-2.707,2.707-2.707H74.876z
                                                M148.618,21.367c12.952,0,26.791,7.038,26.791,26.791c0,19.339-13.521,39.043-26.791,39.043
                                                c-13.27,0-26.791-19.704-26.791-39.043C121.827,28.406,135.666,21.367,148.618,21.367z M161.256,117.952
                                                c-0.661,6.39-6.076,11.389-12.638,11.389c-6.562,0-11.977-5-12.638-11.389c2.684-3.671,4.106-7.842,4.818-11.749
                                                c2.578,0.605,5.192,0.932,7.82,0.932c2.627,0,5.242-0.328,7.82-0.932C157.15,110.109,158.571,114.281,161.256,117.952z
                                                M175.614,249.015c1.493,0,2.707,1.215,2.707,2.707v23.196c0,1.492-1.215,2.707-2.707,2.707h-54.23
                                                c-1.492,0-2.707-1.215-2.707-2.707v-23.196c0-1.492,1.215-2.707,2.707-2.707H175.614z M210.389,183.872
                                                c-0.712,0.803-2.281,2.151-4.784,2.151H91.629c-2.503,0-4.072-1.348-4.785-2.151c-0.712-0.804-1.862-2.522-1.563-5.007
                                                c1.517-12.578,2.775-22.262,3.635-28.004c1.497-9.986,9.778-18.514,20.605-21.222l8.342-2.085
                                                c4.502,12.643,16.586,21.72,30.754,21.72c14.168,0,26.252-9.078,30.754-21.72l8.341,2.085
                                                c10.829,2.708,19.109,11.236,20.606,21.222c0.857,5.721,2.114,15.404,3.634,28.004
                                                C212.251,181.349,211.101,183.068,210.389,183.872z M279.06,259.401c0,1.492-1.215,2.707-2.707,2.707h-54.23
                                                c-1.492,0-2.707-1.215-2.707-2.707v-23.196c0-1.492,1.215-2.707,2.707-2.707h54.23c1.492,0,2.707,1.215,2.707,2.707V259.401z" />
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span>Assigned Task</span>
                    </a>
                </div>

                <div class="group">
                    <a href="{{ route('tasks', ['task_view' => 'tasks']) }}"
                        @click.prevent="activeTab = 'tasksTab'; contentVisible = false; window.location.href = $el.getAttribute('href')"
                        class="flex items-center w-full space-x-2 text-white rounded-lg p-2 text-base font-medium"
                        :class="{
                            'bg-blue-600': $wire
                                .activeMenu === 'tasks',
                            'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                                .activeMenu !== 'tasks'
                        }">

                        <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                            :class="{
                                'bg-white text-blue-600': $wire
                                    .activeMenu === 'tasks',
                                'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                    .activeMenu !== 'tasks'
                            }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="30px"
                                height="30px" viewBox="0 0 308.847 308.847" stroke="none">
                                <g>
                                    <g>
                                        <g>
                                            <path
                                                d="M61.423,0c-22.607,0-41,18.393-41,41s18.393,41,41,41c22.607,0,41-18.393,41-41S84.031,0,61.423,0z M61.423,64
                                        c-12.683,0-23-10.318-23-23s10.317-23,23-23c12.683,0,23,10.318,23,23S74.107,64,61.423,64z" />
                                            <path d="M279.424,48.983h-152c-4.971,0-9,4.029-9,9c0,4.971,4.029,9,9,9h152c4.971,0,9-4.029,9-9
                                        C288.424,53.012,284.395,48.983,279.424,48.983z" />
                                            <path d="M127.423,33.017h152c4.971,0,9-4.029,9-9c0-4.971-4.029-9-9-9h-152c-4.971,0-9,4.029-9,9
                                        C118.424,28.988,122.453,33.017,127.423,33.017z" />
                                            <path d="M61.423,113.423c-22.607,0-41,18.393-41,41c0,22.607,18.393,41,41,41c22.607,0,41-18.393,41-41
                                        S84.031,113.423,61.423,113.423z M61.423,177.423c-12.683,0-23-10.318-23-23s10.317-23,23-23c12.683,0,23,10.318,23,23
                                        S74.107,177.423,61.423,177.423z" />
                                            <path
                                                d="M279.424,162.406h-152c-4.971,0-9,4.029-9,9s4.029,9,9,9h152c4.971,0,9-4.029,9-9S284.395,162.406,279.424,162.406z" />
                                            <path
                                                d="M279.424,128.44h-152c-4.971,0-9,4.029-9,9s4.029,9,9,9h152c4.971,0,9-4.029,9-9S284.395,128.44,279.424,128.44z" />
                                            <path
                                                d="M61.423,226.847c-22.607,0-41,18.393-41,41s18.393,41,41,41c22.607,0,41-18.393,41-41S84.031,226.847,61.423,226.847z
                                            M61.423,290.847c-12.683,0-23-10.318-23-23s10.317-23,23-23c12.683,0,23,10.318,23,23S74.107,290.847,61.423,290.847z" />
                                            <path
                                                d="M279.424,275.83h-152c-4.971,0-9,4.029-9,9s4.029,9,9,9h152c4.971,0,9-4.029,9-9S284.395,275.83,279.424,275.83z" />
                                            <path d="M279.424,241.863h-152c-4.971,0-9,4.029-9,9c0,4.971,4.029,9,9,9h152c4.971,0,9-4.029,9-9
                                        C288.424,245.892,284.395,241.863,279.424,241.863z" />
                                            <circle cx="61.423" cy="41" r="8.122" />
                                            <circle cx="61.423" cy="154.423" r="8.122" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span>All Tasks</span>
                    </a>
                </div>

            </div>

            <!-- Categories Link -->
            <div x-show="hoveredTab === 'categoriesTab'"
                class="group flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                <a href="{{ route('categories') }}" wire:navigate
                    @click.prevent="activeTab = 'categoriesTab'; contentVisible = false"
                    class="flex items-center w-full space-x-2 text-white rounded-lg p-2 text-base font-medium"
                    :class="{
                        'bg-blue-600': $wire
                            .activeMenu === 'categories',
                        'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                            .activeMenu !== 'categories'
                    }">
                    <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                        :class="{
                            'bg-white text-blue-600': $wire
                                .activeMenu === 'categories',
                            'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                .activeMenu !== 'categories'
                        }">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 1025 1024" stroke="none">
                            <path
                                d="M513.009189 73.143171a73.142805 73.142805 0 0 1 30.171407 6.582852l404.479711 192.548434A18.285701 18.285701 0 0 1 951.866018 274.285884a18.285701 18.285701 0 0 1-3.474283 2.011427L543.180596 468.845745a73.142805 73.142805 0 0 1-60.342814 0L78.35807 276.297311A18.285701 18.285701 0 0 1 74.152359 274.285884a18.285701 18.285701 0 0 1 3.474283-2.011427L482.837782 79.726023A73.142805 73.142805 0 0 1 513.009189 73.143171m0-73.142805a143.725612 143.725612 0 0 0-61.622814 13.714276L46.906664 206.080219a73.142805 73.142805 0 0 0 0 136.411331l404.479711 192.365577a145.371325 145.371325 0 0 0 123.245627 0l404.479711-192.365577a73.142805 73.142805 0 0 0 0-136.411331L574.632002 13.714642A143.725612 143.725612 0 0 0 513.009189 0.000366z" />
                            <path
                                d="M513.009189 773.485528a176.639874 176.639874 0 0 1-79.177087-18.285702L21.306683 550.217116a36.571402 36.571402 0 1 1 32.548548-65.462811l412.525419 204.799854a105.691353 105.691353 0 0 0 93.257077 0l412.525419-204.799854a36.571402 36.571402 0 0 1 32.548548 65.462811l-412.525419 204.799853A176.639874 176.639874 0 0 1 513.009189 773.485528z" />
                            <path
                                d="M513.009189 1023.999634a179.017015 179.017015 0 0 1-79.177087-18.285701L21.306683 800.731222a36.571402 36.571402 0 1 1 32.548548-65.46281l412.525419 204.799854a105.691353 105.691353 0 0 0 93.257077 0l412.525419-204.799854a36.571402 36.571402 0 0 1 32.548548 65.46281L592.186275 1005.713933A179.017015 179.017015 0 0 1 513.009189 1023.999634z" />
                        </svg>
                    </span>
                    <span>Categories</span>
                </a>
            </div>

            <!-- Users Link -->

            <div x-show="hoveredTab === 'usersTab'"
                class="flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                <div class="group">
                    <a href="{{ route('users') }}" wire:navigate
                        @click.prevent="activeTab = 'usersTab'; contentVisible = false"
                        class="flex items-center w-full space-x-2 text-white rounded-lg p-2 text-base font-medium"
                        :class="{
                            'bg-blue-600': $wire
                                .activeMenu === 'users',
                            'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                                .activeMenu !== 'users'
                        }">

                        <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                            :class="{
                                'bg-white text-blue-600': $wire
                                    .activeMenu === 'users',
                                'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                    .activeMenu !== 'users'
                            }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="30px"
                                height="30px" viewBox="0 0 24 24" stroke="none">
                                <path
                                    d="M1.5 6.5C1.5 3.46243 3.96243 1 7 1C10.0376 1 12.5 3.46243 12.5 6.5C12.5 9.53757 10.0376 12 7 12C3.96243 12 1.5 9.53757 1.5 6.5Z"
                                    fill="currentColor" />
                                <path
                                    d="M14.4999 6.5C14.4999 8.00034 14.0593 9.39779 13.3005 10.57C14.2774 11.4585 15.5754 12 16.9999 12C20.0375 12 22.4999 9.53757 22.4999 6.5C22.4999 3.46243 20.0375 1 16.9999 1C15.5754 1 14.2774 1.54153 13.3005 2.42996C14.0593 3.60221 14.4999 4.99966 14.4999 6.5Z"
                                    fill="currentColor" />
                                <path
                                    d="M0 18C0 15.7909 1.79086 14 4 14H10C12.2091 14 14 15.7909 14 18V22C14 22.5523 13.5523 23 13 23H1C0.447716 23 0 22.5523 0 22V18Z"
                                    fill="currentColor" />
                                <path
                                    d="M16 18V23H23C23.5522 23 24 22.5523 24 22V18C24 15.7909 22.2091 14 20 14H14.4722C15.4222 15.0615 16 16.4633 16 18Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <span>Manage Users</span>
                    </a>
                </div>

                <div class="group">
                    <a href="{{ route('manageusergroup') }}" wire:navigate
                        @click.prevent="activeTab = 'usersTab'; contentVisible = false"
                        class="flex items-center w-full space-x-2 text-white rounded-lg p-2 text-base font-medium"
                        :class="{
                            'bg-blue-600': $wire
                                .activeMenu === 'manageusergroup',
                            'bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white': $wire
                                .activeMenu !== 'manageusergroup'
                        }">
                        <span aria-hidden="true" class="p-2 rounded-lg shadow-lg"
                            :class="{
                                'bg-white text-blue-600': $wire
                                    .activeMenu === 'manageusergroup',
                                'bg-gray-500 group-hover:bg-white group-hover:text-blue-600': $wire
                                    .activeMenu !== 'manageusergroup'
                            }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="30px"
                                height="30px" viewBox="0 0 512 512" stroke="none">
                                <g>
                                    <g>
                                        <g>
                                            <path d="M330.691,85.346h42.667c5.888,0,10.667-4.779,10.667-10.667s-4.779-10.667-10.667-10.667h-42.667
                                                    c-29.419,0-53.333,23.936-53.333,53.333v138.667h-53.333c-5.888,0-10.667,4.779-10.667,10.667s4.779,10.667,10.667,10.667h53.333
                                                    v138.667c0,29.397,23.915,53.333,53.333,53.333h42.667c5.888,0,10.667-4.779,10.667-10.667s-4.779-10.667-10.667-10.667h-42.667
                                                    c-17.643,0-32-14.357-32-32V277.346h74.667c5.888,0,10.667-4.779,10.667-10.667s-4.779-10.667-10.667-10.667h-74.667V117.346
                                                    C298.691,99.703,313.049,85.346,330.691,85.346z"
                                                fill="currentColor" />
                                            <path
                                                d="M177.646,179.831c-3.349-29.525-26.752-51.819-54.4-51.819H68.782c-27.648,0-51.051,22.272-54.4,51.819L0.238,303.735
                                                    c-1.173,10.368,2.027,20.651,8.768,28.224c5.803,6.528,13.547,10.304,21.909,10.773l11.776,159.403
                                                    c0.427,5.568,5.056,9.877,10.645,9.877h85.333c5.589,0,10.219-4.309,10.645-9.877l11.776-159.403
                                                    c8.384-0.469,16.107-4.267,21.909-10.773c6.741-7.552,9.941-17.856,8.768-28.224L177.646,179.831z"
                                                fill="currentColor" />
                                            <path
                                                d="M96.025,106.679c29.461,0,53.333-23.872,53.333-53.333c0-29.461-23.872-53.333-53.333-53.333
                                                    c-29.461,0-53.333,23.872-53.333,53.333C42.691,82.807,66.563,106.679,96.025,106.679z"
                                                fill="currentColor" />
                                            <path d="M480.025,405.346h-42.667c-17.643,0-32,14.357-32,32v42.667c0,17.643,14.357,32,32,32h42.667c17.643,0,32-14.357,32-32
                                                    v-42.667C512.025,419.703,497.667,405.346,480.025,405.346z"
                                                fill="currentColor" />
                                            <path d="M480.025,213.346h-42.667c-17.643,0-32,14.357-32,32v42.667c0,17.643,14.357,32,32,32h42.667c17.643,0,32-14.357,32-32
                                                    v-42.667C512.025,227.703,497.667,213.346,480.025,213.346z"
                                                fill="currentColor" />
                                            <path d="M480.025,21.346h-42.667c-17.643,0-32,14.357-32,32v42.667c0,17.643,14.357,32,32,32h42.667c17.643,0,32-14.357,32-32
                                                    V53.346C512.025,35.703,497.667,21.346,480.025,21.346z"
                                                fill="currentColor" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span>Manage User Group</span>
                    </a>
                </div>
            </div>

            <div x-show="hoveredTab === 'exportTab'"
                class="group flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                <a href="#" wire:click="$dispatch('openExportModal', { component: 'export-task-modal' })"
                    x-data="{ isLoading: false }" x-on:taskexportmodalopened.window="isLoading = false"
                    x-on:click="isLoading = true" x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                    wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                    class="flex items-center w-full space-x-2 text-white rounded-lg p-2 text-base font-medium bg-gray-400 text-black group-hover:bg-blue-600 group-hover:text-white">

                    <span aria-hidden="true"
                        class="p-2 rounded-lg shadow-lg bg-gray-500 group-hover:bg-white group-hover:text-blue-600">
                        <svg x-show="!isLoading" inert
                            class="w-6 h-6 fill-gray-500 transition duration-75 group-hover:fill-blue-600"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor"
                                d="M12 16a1 1 0 0 1-.707-.293l-5-5a1 1 0 1 1 1.414-1.414L11 12.586V3a1 1 0 1 1 2 0v9.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-5 5A1 1 0 0 1 12 16z" />
                            <path fill="currentColor" d="M5 20a1 1 0 1 1 0-2h14a1 1 0 1 1 0 2H5z" />
                        </svg>
                        <svg x-show="isLoading" inert
                            class="animate-spin w-6 h-6 fill-black-500 transition duration-75"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25"
                                stroke-width="4"></circle>
                            <path d="M4 12a8 8 0 0 1 16 0" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="4" class="opacity-75"></path>
                        </svg>
                    </span>
                    <span>Export Report</span>
                </a>
            </div>

        </nav>
        {{-- </div> --}}
    </div>
    <div x-data="{}" @click.away="contentVisible = false"></div>

    {{-- desktop bottom speed dial --}}
    <div data-dial-init="" class="hidden md:block fixed right-6 bottom-6 group" style="z-index: 99999;">
        <div id="speed-dial-menu-dropdown-square"
            class="flex flex-col justify-end py-1 mb-4 space-y-2 bg-white border border-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 hidden">
            <ul class="text-sm text-gray-500 dark:text-gray-300">
                <li>
                    <a href="#" wire:click="$dispatch('openTaskModal', { component: 'task-details-modal' })"
                        x-data="{ isLoading: false }" x-on:addtaskmodalopened.window="isLoading = false"
                        x-on:click="isLoading = true" x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                        wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                        class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                        <svg x-show="!isLoading" class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 0 1 0-2h4V5a1 1 0 0 1 1-1z" />
                        </svg>

                        <!-- Round loader spinner when loading -->
                        <svg x-show="isLoading" inert
                            class="animate-spin w-6 h-6 me-2 text-gray-500 transition duration-75 dark:text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25"
                                stroke-width="4"></circle>
                            <path d="M4 12a8 8 0 0 1 16 0" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="4" class="opacity-75"></path>
                        </svg>
                        <span class="text-sm font-medium">Add Tasks</span>
                    </a>
                </li>
                @can('view-admin-options')
                    <li>
                        <a href="{{ route('categories') }}" wire:navigate
                            class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                            <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 0 1 0-2h4V5a1 1 0 0 1 1-1z" />
                                <path
                                    d="M17 2H3a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zM3 16V4h14v12H3z" />
                            </svg>
                            <span class="text-sm font-medium">Add Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" wire:click="$dispatch('openModal', { component: 'user-details-modal' })"
                            id="UserDetailsModalButton" x-data="{ isLoading: false }"
                            x-on:addusermodalopened.window="isLoading = false" x-on:click="isLoading = true"
                            x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }" wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                            <svg x-show="!isLoading" inert class="w-5 h-5 me-2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="7" fill="currentColor" r="5" />
                                <path d="M20,19v1a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V19a6,6,0,0,1,6-6h4A6,6,0,0,1,20,19Z"
                                    fill="currentColor" />
                            </svg>

                            <!-- Round loader spinner when loading -->
                            <svg x-show="isLoading" inert
                                class="animate-spin w-6 h-6 me-2 text-gray-500 transition duration-75 dark:text-gray-400"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25"
                                    stroke-width="4"></circle>
                                <path d="M4 12a8 8 0 0 1 16 0" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="4" class="opacity-75"></path>
                            </svg>
                            <span class="text-sm font-medium">Add User</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
        <button type="button" data-dial-toggle="speed-dial-menu-dropdown-square"
            aria-controls="speed-dial-menu-dropdown-square" aria-expanded="false"
            class="flex items-center justify-center ml-auto text-white bg-blue-700 rounded-full w-14 h-14 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800">
            <svg class="w-5 h-5 transition-transform group-hover:rotate-45" inert xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 1v16M1 9h16"></path>
            </svg>
            <span class="sr-only">Open actions menu</span>
        </button>
    </div>

    {{-- Bottom navbar for mobile --}}
    <div
        class="fixed z-30 w-full h-16 max-w-lg -translate-x-1/2 bg-white border border-gray-200 rounded-full bottom-4 left-1/2 dark:bg-gray-700 dark:border-gray-600 md:hidden">
        <div class="grid h-full max-w-lg grid-cols-5 mx-auto">
            <a data-tooltip-target="tooltip-home" type="button" href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex flex-col items-center justify-center px-5 rounded-s-full hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <svg class="w-6 h-6 mb-1 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500"
                    inert xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                </svg>
                <span class="sr-only">Dashboard</span>
            </a>
            <div id="tooltip-home" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Home
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>

            <div data-dial-init id="tooltip-taskList" class="group relative">
                <!-- Menu content that appears above the button -->
                <div id="tooltip-taskListmenuContent"
                    class="absolute bottom-full mb-2 flex flex-col py-1 space-y-2 bg-white border border-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 hidden"
                    style="right: initial;">
                    <ul class="text-sm text-gray-500 dark:text-gray-300">
                        <li>
                            <a href="{{ route('tasks', ['task_view' => 'my_tasks']) }}"
                                class="flex items-center whitespace-nowrap px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                                <svg class="w-3.5 h-3.5 mr-2" fill="currentColor" version="1.1" id="Capa_1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 47 47" xml:space="preserve">
                                    <g>
                                        <g id="Layer_1_22_">
                                            <g>
                                                <path d="M6.12,38.52V5.136h26.962v28.037l5.137-4.243V2.568C38.219,1.15,37.07,0,35.652,0h-32.1C2.134,0,0.985,1.15,0.985,2.568
                                        v38.519c0,1.418,1.149,2.568,2.567,2.568h22.408L22.33,38.52H6.12z" />
                                                <path
                                                    d="M45.613,27.609c-0.473-0.446-1.2-0.467-1.698-0.057l-11.778,9.734l-7.849-4.709c-0.521-0.312-1.188-0.219-1.603,0.229
                                        c-0.412,0.444-0.457,1.117-0.106,1.613l8.506,12.037c0.238,0.337,0.625,0.539,1.037,0.543c0.004,0,0.008,0,0.012,0
                                        c0.408,0,0.793-0.193,1.035-0.525l12.6-17.173C46.149,28.78,46.084,28.055,45.613,27.609z" />
                                                <path d="M27.306,8.988H11.897c-1.418,0-2.567,1.15-2.567,2.568s1.149,2.568,2.567,2.568h15.408c1.418,0,2.566-1.15,2.566-2.568
                                        S28.724,8.988,27.306,8.988z" />
                                                <path d="M27.306,16.691H11.897c-1.418,0-2.567,1.15-2.567,2.568s1.149,2.568,2.567,2.568h15.408c1.418,0,2.566-1.149,2.566-2.568
                                        C29.874,17.841,28.724,16.691,27.306,16.691z" />
                                                <path d="M27.306,24.395H11.897c-1.418,0-2.567,1.15-2.567,2.568s1.149,2.568,2.567,2.568h15.408c1.418,0,2.566-1.15,2.566-2.568
                                        C29.874,25.545,28.724,24.395,27.306,24.395z" />
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="text-sm font-medium">My Tasks</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tasks', ['task_view' => 'assigned_to_others']) }}"
                                class="flex items-center whitespace-nowrap px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">

                                <svg inert class="w-3.5 h-3.5 mr-2 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 297 297" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <g>
                                        <g>
                                            <path
                                                d="M276.354,215.56h-25.516l-22.562-22.562c2.91-4.938,4.163-10.739,3.466-16.518c-1.564-12.969-2.813-22.582-3.71-28.573
                                                c-2.718-18.133-16.647-32.894-35.485-37.604l-14.834-3.708c-1.96-1.904-2.289-8.161-1.751-11.493
                                                c2.381-2.294,4.691-4.876,6.752-7.754c8.026-11.21,12.628-25.494,12.628-39.187c0-27.51-19.214-46.724-46.724-46.724
                                                s-46.724,19.214-46.724,46.724c0,13.693,4.603,27.977,12.628,39.187c2.06,2.875,4.371,5.456,6.75,7.75
                                                c0.54,3.33,0.213,9.591-1.749,11.496l-14.835,3.708c-18.837,4.71-32.766,19.471-35.484,37.604
                                                c-0.901,6.013-2.15,15.626-3.711,28.573c-0.69,5.721,0.53,11.465,3.378,16.371l-22.709,22.709H20.646
                                                C9.262,215.559,0,224.82,0,236.206v23.196c0,11.385,9.262,20.646,20.646,20.646h54.23c11.385,0,20.646-9.262,20.646-20.646
                                                v-23.196c0-11.385-9.262-20.646-20.646-20.646h-0.524l10.496-10.496c2.192,0.585,4.47,0.892,6.781,0.892h47.022v25.12h-17.266
                                                c-11.385,0-20.646,9.262-20.646,20.646v23.196c0,11.385,9.262,20.646,20.646,20.646h54.23c11.385,0,20.646-9.262,20.646-20.646
                                                v-23.196c0-11.385-9.262-20.646-20.646-20.646h-17.031v-25.12h47.022c2.245,0,4.457-0.293,6.592-0.846l10.45,10.45h-0.524
                                                c-11.385,0-20.646,9.262-20.646,20.646v23.196c0,11.385,9.262,20.646,20.646,20.646h54.23c11.385,0,20.646-9.262,20.646-20.646
                                                v-23.196C297,224.82,287.738,215.56,276.354,215.56z M74.876,233.498c1.492,0,2.707,1.215,2.707,2.707v23.196
                                                c0,1.492-1.215,2.707-2.707,2.707h-54.23c-1.492,0-2.707-1.215-2.707-2.707v-23.196c0-1.492,1.215-2.707,2.707-2.707H74.876z
                                                M148.618,21.367c12.952,0,26.791,7.038,26.791,26.791c0,19.339-13.521,39.043-26.791,39.043
                                                c-13.27,0-26.791-19.704-26.791-39.043C121.827,28.406,135.666,21.367,148.618,21.367z M161.256,117.952
                                                c-0.661,6.39-6.076,11.389-12.638,11.389c-6.562,0-11.977-5-12.638-11.389c2.684-3.671,4.106-7.842,4.818-11.749
                                                c2.578,0.605,5.192,0.932,7.82,0.932c2.627,0,5.242-0.328,7.82-0.932C157.15,110.109,158.571,114.281,161.256,117.952z
                                                M175.614,249.015c1.493,0,2.707,1.215,2.707,2.707v23.196c0,1.492-1.215,2.707-2.707,2.707h-54.23
                                                c-1.492,0-2.707-1.215-2.707-2.707v-23.196c0-1.492,1.215-2.707,2.707-2.707H175.614z M210.389,183.872
                                                c-0.712,0.803-2.281,2.151-4.784,2.151H91.629c-2.503,0-4.072-1.348-4.785-2.151c-0.712-0.804-1.862-2.522-1.563-5.007
                                                c1.517-12.578,2.775-22.262,3.635-28.004c1.497-9.986,9.778-18.514,20.605-21.222l8.342-2.085
                                                c4.502,12.643,16.586,21.72,30.754,21.72c14.168,0,26.252-9.078,30.754-21.72l8.341,2.085
                                                c10.829,2.708,19.109,11.236,20.606,21.222c0.857,5.721,2.114,15.404,3.634,28.004
                                                C212.251,181.349,211.101,183.068,210.389,183.872z M279.06,259.401c0,1.492-1.215,2.707-2.707,2.707h-54.23
                                                c-1.492,0-2.707-1.215-2.707-2.707v-23.196c0-1.492,1.215-2.707,2.707-2.707h54.23c1.492,0,2.707,1.215,2.707,2.707V259.401z" />
                                        </g>
                                    </g>
                                </svg>
                                <span class="text-sm font-medium">Assigned Tasks</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tasks', ['task_view' => 'tasks']) }}"
                                class="flex items-center whitespace-nowrap px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                                <svg class="w-3.5 h-3.5 mr-2" fill="currentColor" version="1.1" id="Layer_1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 308.847 308.847" xml:space="preserve">
                                    <g>
                                        <g>
                                            <g>
                                                <path
                                                    d="M61.423,0c-22.607,0-41,18.393-41,41s18.393,41,41,41c22.607,0,41-18.393,41-41S84.031,0,61.423,0z M61.423,64
                                        c-12.683,0-23-10.318-23-23s10.317-23,23-23c12.683,0,23,10.318,23,23S74.107,64,61.423,64z" />
                                                <path d="M279.424,48.983h-152c-4.971,0-9,4.029-9,9c0,4.971,4.029,9,9,9h152c4.971,0,9-4.029,9-9
                                        C288.424,53.012,284.395,48.983,279.424,48.983z" />
                                                <path d="M127.423,33.017h152c4.971,0,9-4.029,9-9c0-4.971-4.029-9-9-9h-152c-4.971,0-9,4.029-9,9
                                        C118.424,28.988,122.453,33.017,127.423,33.017z" />
                                                <path d="M61.423,113.423c-22.607,0-41,18.393-41,41c0,22.607,18.393,41,41,41c22.607,0,41-18.393,41-41
                                        S84.031,113.423,61.423,113.423z M61.423,177.423c-12.683,0-23-10.318-23-23s10.317-23,23-23c12.683,0,23,10.318,23,23
                                        S74.107,177.423,61.423,177.423z" />
                                                <path
                                                    d="M279.424,162.406h-152c-4.971,0-9,4.029-9,9s4.029,9,9,9h152c4.971,0,9-4.029,9-9S284.395,162.406,279.424,162.406z" />
                                                <path
                                                    d="M279.424,128.44h-152c-4.971,0-9,4.029-9,9s4.029,9,9,9h152c4.971,0,9-4.029,9-9S284.395,128.44,279.424,128.44z" />
                                                <path
                                                    d="M61.423,226.847c-22.607,0-41,18.393-41,41s18.393,41,41,41c22.607,0,41-18.393,41-41S84.031,226.847,61.423,226.847z
                                            M61.423,290.847c-12.683,0-23-10.318-23-23s10.317-23,23-23c12.683,0,23,10.318,23,23S74.107,290.847,61.423,290.847z" />
                                                <path
                                                    d="M279.424,275.83h-152c-4.971,0-9,4.029-9,9s4.029,9,9,9h152c4.971,0,9-4.029,9-9S284.395,275.83,279.424,275.83z" />
                                                <path d="M279.424,241.863h-152c-4.971,0-9,4.029-9,9c0,4.971,4.029,9,9,9h152c4.971,0,9-4.029,9-9
                                        C288.424,245.892,284.395,241.863,279.424,241.863z" />
                                                <circle cx="61.423" cy="41" r="8.122" />
                                                <circle cx="61.423" cy="154.423" r="8.122" />
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="text-sm font-medium">All Tasks</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Speed dial toggle button -->
                <button type="button" id="tooltip-taskListBtn" data-dial-toggle="tooltip-taskListmenuContent"
                    aria-controls="tooltip-taskListmenuContent" aria-expanded="false"
                    class="flex items-center justify-center text-black w-14 h-14">
                    <svg class="w-7 h-7 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500"
                        viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7.25 2C5.45507 2 4 3.45508 4 5.25V22.7497C4 24.5446 5.45507 25.9997 7.25 25.9997H16.8177L14.661 23.8429C13.7823 22.9643 13.7823 21.5396 14.661 20.661C15.5396 19.7823 16.9643 19.7823 17.8429 20.661L19.25 22.068L23.659 17.659C23.7653 17.5527 23.8796 17.4592 24 17.3787V5.25C24 3.45507 22.5449 2 20.75 2H7.25ZM10.5 8.75C10.5 9.44036 9.94036 10 9.25 10C8.55964 10 8 9.44036 8 8.75C8 8.05964 8.55964 7.5 9.25 7.5C9.94036 7.5 10.5 8.05964 10.5 8.75ZM9.25 15.2498C8.55964 15.2498 8 14.6902 8 13.9998C8 13.3095 8.55964 12.7498 9.25 12.7498C9.94036 12.7498 10.5 13.3095 10.5 13.9998C10.5 14.6902 9.94036 15.2498 9.25 15.2498ZM9.25 20.5C8.55964 20.5 8 19.9404 8 19.25C8 18.5596 8.55964 18 9.25 18C9.94036 18 10.5 18.5596 10.5 19.25C10.5 19.9404 9.94036 20.5 9.25 20.5ZM12.75 8H19.25C19.6642 8 20 8.33579 20 8.75C20 9.16421 19.6642 9.5 19.25 9.5H12.75C12.3358 9.5 12 9.16421 12 8.75C12 8.33579 12.3358 8 12.75 8ZM12 13.9998C12 13.5856 12.3358 13.2498 12.75 13.2498H19.25C19.6642 13.2498 20 13.5856 20 13.9998C20 14.414 19.6642 14.7498 19.25 14.7498H12.75C12.3358 14.7498 12 14.414 12 13.9998ZM12.75 18.5H19.25C19.6642 18.5 20 18.8358 20 19.25C20 19.6642 19.6642 20 19.25 20H12.75C12.3358 20 12 19.6642 12 19.25C12 18.8358 12.3358 18.5 12.75 18.5Z"
                            fill="#6b7280" />
                        <path
                            d="M19.7803 25.7803L25.7803 19.7803C26.0732 19.4874 26.0732 19.0126 25.7803 18.7197C25.4874 18.4268 25.0126 18.4268 24.7197 18.7197L19.25 24.1893L16.7823 21.7216C16.4894 21.4287 16.0145 21.4287 15.7216 21.7216C15.4287 22.0145 15.4287 22.4894 15.7216 22.7823L18.7197 25.7803C18.8603 25.921 19.0511 26 19.25 26C19.4489 26 19.6397 25.921 19.7803 25.7803Z"
                            fill="#6b7280" />
                    </svg>
                </button>
            </div>

            <div data-dial-init class="group" id="bottomnavaddmenuParent"
                style="display: flex;flex-direction: row;justify-content: center;align-items: center;">
                <div id="bottomnavaddmenuContent"
                    class="absolute flex flex-col justify-end hidden py-1 mb-2 space-y-2 bg-white bottom-full border border-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                    <ul class="text-sm text-gray-500 dark:text-gray-300">
                        <li>
                            <a href="#"
                                wire:click="$dispatch('openTaskModal', { component: 'task-details-modal' })"
                                x-data="{ isLoading: false }" x-on:addtaskmodalopened.window="isLoading = false"
                                x-on:click="isLoading = true"
                                x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                                wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                                class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                                <svg x-show="!isLoading" class="w-3.5 h-3.5 me-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 0 1 0-2h4V5a1 1 0 0 1 1-1z" />
                                </svg>
                                <!-- Round loader spinner when loading -->
                                <svg x-show="isLoading" inert
                                    class="animate-spin w-5 h-5 me-2 text-gray-500 transition duration-75 dark:text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-opacity="0.25" stroke-width="4"></circle>
                                    <path d="M4 12a8 8 0 0 1 16 0" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="4" class="opacity-75"></path>
                                </svg>
                                <span class="text-sm font-medium">Add Tasks</span>
                            </a>
                        </li>
                        @can('view-admin-options')
                            <li>
                                <a href="{{ route('categories') }}" wire:navigate
                                    class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                                    <svg class="w-3.5 h-3.5 me-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 0 1 0-2h4V5a1 1 0 0 1 1-1z" />
                                        <path
                                            d="M17 2H3a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zM3 16V4h14v12H3z" />
                                    </svg>
                                    <span class="text-sm font-medium">Add Category</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" wire:click="$dispatch('openModal', { component: 'user-details-modal' })"
                                    x-data="{ isLoading: false }" x-on:addusermodalopened.window="isLoading = false"
                                    x-on:click="isLoading = true"
                                    x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                                    wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                                    <svg x-show="!isLoading" inert class="w-5 h-5 me-2" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="7" fill="currentColor" r="5" />
                                        <path d="M20,19v1a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V19a6,6,0,0,1,6-6h4A6,6,0,0,1,20,19Z"
                                            fill="currentColor" />
                                    </svg>

                                    <!-- Round loader spinner when loading -->
                                    <svg x-show="isLoading" inert
                                        class="animate-spin w-5 h-5 me-2 text-gray-500 transition duration-75 dark:text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-opacity="0.25" stroke-width="4"></circle>
                                        <path d="M4 12a8 8 0 0 1 16 0" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="4" class="opacity-75"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Add User</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
                <!-- Speed dial toggle button -->
                <button type="button" id="bottomnavaddmenuBtn" data-dial-toggle="bottomnavaddmenuContent"
                    aria-controls="bottomnavaddmenuContent" aria-expanded="false"
                    class="flex items-center justify-center text-white bg-blue-700 rounded-full w-14 h-14 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800">
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-45" inert
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 1v16M1 9h16" />
                    </svg>
                    <span class="sr-only">Open menu</span>
                </button>
            </div>

            {{-- <a href="{{ route('users') }}" wire:navigate data-tooltip-target="tooltip-settings" type="button"
                <svg class="w-8 h-8 mb-1 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500"
                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="8" r="4" fill="currentColor" /> <!-- Head -->
                    <path fill="currentColor" d="M4 20c0-4 4-6 8-6s8 2 8 6v1H4v-1z" /> <!-- Shoulders -->
                </svg>

                <span class=>Users</span>
            </a> --}}
            <a href="#" wire:click="$dispatch('openExportModal', { component: 'export-task-modal' })"
                x-data="{ isLoading: false }" x-on:taskexportmodalopened.window="isLoading = false"
                x-on:click="isLoading = true" x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <svg x-show="!isLoading" inert
                    class="w-8 h-8 mb-1 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor"
                        d="M12 16a1 1 0 0 1-.707-.293l-5-5a1 1 0 0 1 1.414-1.414L11 12.586V3a1 1 0 1 1 2 0v9.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-5 5A1 1 0 0 1 12 16z" />
                    <path fill="currentColor" d="M5 20a1 1 0 1 1 0-2h14a1 1 0 1 1 0 2H5z" />
                </svg>

                <!-- Round loader spinner when loading -->
                <svg x-show="isLoading" inert
                    class="animate-spin w-8 h-8 text-gray-500 transition duration-75 dark:text-gray-400"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25"
                        stroke-width="4"></circle>
                    <path d="M4 12a8 8 0 0 1 16 0" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="4" class="opacity-75"></path>
                </svg>
                <span class="sr-only">Export Report</span>
            </a>

            <div id="tooltip-settings" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                Users
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>

            <!-- "More" Button -->
            <div data-dial-init id="bottomnavmoremenuParent" class="group"
                class="flex flex-row justify-center items-center">
                @can('view-admin-options')
                    <div id="bottomnavmoremenuContent"
                        class="absolute right-1 flex flex-col justify-end hidden py-1 mb-2 space-y-2 bg-white bottom-full border border-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <ul class="text-sm text-gray-500 dark:text-gray-300">

                            <li>
                                <a href="{{ route('categories') }}" wire:navigate
                                    class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                                    <svg class="w-3.5 h-3.5 me-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 0 1 0-2h4V5a1 1 0 0 1 1-1z" />
                                        <path
                                            d="M17 2H3a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zM3 16V4h14v12H3z" />
                                    </svg>
                                    <span class="text-sm font-medium" style="white-space: nowrap;">Categories</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('users') }}" wire:navigate
                                    class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 me-2" fill="currentColor"
                                        viewBox="0 0 24 24" stroke="none">
                                        <path
                                            d="M1.5 6.5C1.5 3.46243 3.96243 1 7 1C10.0376 1 12.5 3.46243 12.5 6.5C12.5 9.53757 10.0376 12 7 12C3.96243 12 1.5 9.53757 1.5 6.5Z"
                                            fill="currentColor" />
                                        <path
                                            d="M14.4999 6.5C14.4999 8.00034 14.0593 9.39779 13.3005 10.57C14.2774 11.4585 15.5754 12 16.9999 12C20.0375 12 22.4999 9.53757 22.4999 6.5C22.4999 3.46243 20.0375 1 16.9999 1C15.5754 1 14.2774 1.54153 13.3005 2.42996C14.0593 3.60221 14.4999 4.99966 14.4999 6.5Z"
                                            fill="currentColor" />
                                        <path
                                            d="M0 18C0 15.7909 1.79086 14 4 14H10C12.2091 14 14 15.7909 14 18V22C14 22.5523 13.5523 23 13 23H1C0.447716 23 0 22.5523 0 22V18Z"
                                            fill="currentColor" />
                                        <path
                                            d="M16 18V23H23C23.5522 23 24 22.5523 24 22V18C24 15.7909 22.2091 14 20 14H14.4722C15.4222 15.0615 16 16.4633 16 18Z"
                                            fill="currentColor" />
                                    </svg>
                                    <span class="text-sm font-medium">Users</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('manageusergroup') }}" wire:navigate
                                    class="flex items-center px-5 py-2 border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white dark:border-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 me-2" fill="currentColor"
                                        viewBox="0 0 512 512" stroke="none">
                                        <g>
                                            <g>
                                                <g>
                                                    <path
                                                        d="M330.691,85.346h42.667c5.888,0,10.667-4.779,10.667-10.667s-4.779-10.667-10.667-10.667h-42.667
                                                                                                        c-29.419,0-53.333,23.936-53.333,53.333v138.667h-53.333c-5.888,0-10.667,4.779-10.667,10.667s4.779,10.667,10.667,10.667h53.333
                                                                                                        v138.667c0,29.397,23.915,53.333,53.333,53.333h42.667c5.888,0,10.667-4.779,10.667-10.667s-4.779-10.667-10.667-10.667h-42.667
                                                                                                        c-17.643,0-32-14.357-32-32V277.346h74.667c5.888,0,10.667-4.779,10.667-10.667s-4.779-10.667-10.667-10.667h-74.667V117.346
                                                                                                        C298.691,99.703,313.049,85.346,330.691,85.346z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M177.646,179.831c-3.349-29.525-26.752-51.819-54.4-51.819H68.782c-27.648,0-51.051,22.272-54.4,51.819L0.238,303.735
                                                                                                        c-1.173,10.368,2.027,20.651,8.768,28.224c5.803,6.528,13.547,10.304,21.909,10.773l11.776,159.403
                                                                                                        c0.427,5.568,5.056,9.877,10.645,9.877h85.333c5.589,0,10.219-4.309,10.645-9.877l11.776-159.403
                                                                                                        c8.384-0.469,16.107-4.267,21.909-10.773c6.741-7.552,9.941-17.856,8.768-28.224L177.646,179.831z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M96.025,106.679c29.461,0,53.333-23.872,53.333-53.333c0-29.461-23.872-53.333-53.333-53.333
                                                                                                        c-29.461,0-53.333,23.872-53.333,53.333C42.691,82.807,66.563,106.679,96.025,106.679z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M480.025,405.346h-42.667c-17.643,0-32,14.357-32,32v42.667c0,17.643,14.357,32,32,32h42.667c17.643,0,32-14.357,32-32
                                                                                                        v-42.667C512.025,419.703,497.667,405.346,480.025,405.346z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M480.025,213.346h-42.667c-17.643,0-32,14.357-32,32v42.667c0,17.643,14.357,32,32,32h42.667c17.643,0,32-14.357,32-32
                                                                                                        v-42.667C512.025,227.703,497.667,213.346,480.025,213.346z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M480.025,21.346h-42.667c-17.643,0-32,14.357-32,32v42.667c0,17.643,14.357,32,32,32h42.667c17.643,0,32-14.357,32-32
                                                                                                        V53.346C512.025,35.703,497.667,21.346,480.025,21.346z"
                                                        fill="currentColor" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <span class="text-sm font-medium">User Groups</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                @endcan
                <!-- Speed dial toggle button -->
                <button type="button" id="bottomnavmoremenuBtn" data-dial-toggle="bottomnavmoremenuContent"
                    aria-controls="bottomnavmoremenuContent" aria-expanded="false"
                    class="flex items-center justify-center text-black w-14 h-14">
                    <svg width="40px" height="40px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M11.0049 8.005C11.0049 8.55728 11.4526 9.005 12.0049 9.005C12.5572 9.005 13.0049 8.55728 13.0049 8.005V7.995C13.0049 7.44271 12.5572 6.995 12.0049 6.995C11.4526 6.995 11.0049 7.44271 11.0049 7.995V8.005ZM12.0049 13.005C11.4526 13.005 11.0049 12.5573 11.0049 12.005V11.995C11.0049 11.4427 11.4526 10.995 12.0049 10.995C12.5572 10.995 13.0049 11.4427 13.0049 11.995V12.005C13.0049 12.5573 12.5572 13.005 12.0049 13.005ZM12.0049 17.005C11.4526 17.005 11.0049 16.5573 11.0049 16.005V15.995C11.0049 15.4427 11.4526 14.995 12.0049 14.995C12.5572 14.995 13.0049 15.4427 13.0049 15.995V16.005C13.0049 16.5573 12.5572 17.005 12.0049 17.005ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12Z"
                            fill="#6b7280" />
                    </svg>

                    <span class="sr-only">More options...</span>
                </button>
            </div>
        </div>
    </div>

</div>
