<div>
    <div x-data="{ hoveredTab: null, activeTab: null, hoverTimeout: null, contentVisible: true, isFadingOut: false, isTransitioning: false, isHoveringSidebar: false }"
        class="hidden md:flex fixed top-0 left-0 z-20 h-screen font-sans text-gray-800 bg-gray-50">

        <div class="fixed inset-y-0 z-10 w-16 bg-white border-r border-gray-200"></div>

        <!-- Left mini bar -->
        <nav class="z-20 flex flex-col items-center w-16 py-6 bg-white border-r border-gray-200 shadow-sm rounded-r-3xl">
            <div class="flex flex-col items-center flex-1 p-2 space-y-6 mt-14">
                <!-- Dashboard Button -->
                <button @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'dashboardTab'; contentVisible = true"
                    @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 300)"
                    class="p-3 rounded-xl transition-all duration-200 hover:bg-indigo-600 hover:text-white"
                    :class="{
                    'bg-indigo-600 text-white shadow-md': activeTab === 'dashboardTab',
                    'text-gray-500 hover:shadow-md': activeTab !== 'dashboardTab'
                    }">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg x-bind:class="{ 'text-blue-800': activeTab === 'dashboardTab' }"
                        class="w-6 h-6 transition duration-75 group-hover:text-white"
                        :class="{
                        'text-white': activeTab === 'dashboardTab',
                        'text-gray-500 dark:text-gray-400 group-hover:text-white': activeTab !== 'dashboardTab'
                    }"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                </button>

                <!-- Tasks Button -->
                <button @mouseenter="clearTimeout(hoverTimeout); hoveredTab = 'tasksTab'; contentVisible = true"
                    @mouseleave="hoverTimeout = setTimeout(() => { if (!isHoveringSidebar) { hoveredTab = null; contentVisible = false } }, 500)"
                    class="p-2 transition-colors group rounded-lg shadow-md hover:bg-green-600 hover:text-white focus:outline-none focus:ring focus:ring-green-600 focus:ring-offset-white focus:ring-offset-2"
                    :class="{
                    'bg-green-600 text-white': activeTab === 'tasksTab',
                    'text-gray-500 bg-white hover:bg-green-600 hover:text-white': activeTab !== 'tasksTab'
                }">
                    <span class="sr-only">Tasks</span>
                    <svg x-bind:class="{ 'text-green-800': activeTab === 'tasksTab' }"
                        class="w-6 h-6 transition duration-75 group-hover:text-white"
                        :class="{
                        'text-white': activeTab === 'tasksTab',
                        'text-gray-500 dark:text-gray-400 group-hover:text-white': activeTab !== 'tasksTab'
                    }"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 3h10a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4a1 1 0 011-1z"></path>
                        <path d="M4 4h1v12H4z"></path>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Sidebar content for links -->
        <div x-transition:enter="transition-opacity duration-500"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" x-show="hoveredTab !== null"
            class="fixed inset-y-0 left-0 z-10 flex-shrink-0 w-64 bg-white border-r-2 border-blue-100 shadow-lg sm:left-16 rounded-tr-3xl rounded-br-3xl sm:w-72 lg:static lg:w-64"
            @mouseenter="isHoveringSidebar = true" @mouseleave="isHoveringSidebar = false">

            <!-- Links section -->
            <nav x-show="contentVisible" aria-label="Main" class="flex flex-col h-full">
                <!-- Dashboard Link -->
                <div x-show="hoveredTab === 'dashboardTab'" @mouseenter="activeTab = 'dashboardTab'; contentVisible = true"
                    @mouseleave=" setTimeout(() => isTransitioning = false, 500)"
                    class="flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        @click.prevent="activeTab = 'dashboardTab'; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 500)"
                        class="flex items-center w-full space-x-2 text-white bg-indigo-600 rounded-lg">
                        <span aria-hidden="true" class="p-2 bg-indigo-700 rounded-lg">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </span>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Tasks Link -->
                <div x-show="hoveredTab === 'tasksTab'" @mouseenter="activeTab = 'tasksTab'; contentVisible = true"
                    @mouseleave="setTimeout(() => isTransitioning = false, 500)"
                    class="flex-1 px-4 space-y-2 overflow-hidden hover:overflow-auto mt-24">
                    <a href="{{ route('tasks') }}" wire:navigate
                        @click.prevent="activeTab = 'tasksTab'; setTimeout(() => window.location.href = '{{ route('tasks') }}', 500)"
                        class="flex items-center w-full space-x-2 text-white bg-green-600 rounded-lg">
                        <span aria-hidden="true" class="p-2 bg-green-700 rounded-lg">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 2l2.293 6.707L18 8l-4 4 1.707 6.293L12 14l-5.707 4.293L6 12l-4-4 3.707-.293L12 2z" />
                            </svg>
                        </span>
                        <span>Tasks</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>































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
                            <a href="{{ route('/* The above code appears to be a comment block in
                                PHP. Comments in PHP are used to provide
                                explanations or documentation within the code and
                                are not executed by the PHP engine. In this case,
                                the comment block starts with /* and ends with
                                */, indicating a multi-line comment. The text
                                "categor" and " */
                                categories') }}" wire:navigate
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
