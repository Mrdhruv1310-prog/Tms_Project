<div>
    <style>
        /* Filament Table horizontal scrollbar customization */
        .fi-ta-content {
            scrollbar-width: thin !important;
            scrollbar-color: rgba(7, 139, 221, 0.4) transparent !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-x pan-y !important;
        }

        .fi-ta-content::-webkit-scrollbar {
            height: 4px !important;
        }

        .fi-ta-content::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 10px !important;
        }

        .fi-ta-content::-webkit-scrollbar-thumb {
            background: #078bdd !important;
            border-radius: 10px !important;
        }

        .fi-ta-content::-webkit-scrollbar-thumb:hover {
            background: #0056b3 !important;
        }

        @media (max-width: 640px) {
            .fi-ta-table {
                font-size: 0.72rem !important;
                min-width: 750px !important;
            }

            .fi-ta-cell {
                padding: 0.35rem !important;
            }

            .fi-ta-header-cell {
                padding: 0.35rem !important;
                font-size: 0.7rem !important;
            }

            .fi-ta-actions {
                gap: 0.25rem !important;
            }
        }
    </style>

    <div
        class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)]">
        <div
            class="absolute -top-32 -left-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute -bottom-32 -right-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
        </div>

        <main
            class="scrollcontainer md:ml-16 px-2 sm:px-6 lg:px-8 py-3 sm:py-6 pt-14 sm:pt-20 pb-12 sm:pb-16 max-w-full">
            @php
                $titles = [
                    'my_tasks' => 'My Task List',
                    'assigned_to_others' => 'Assigned Task List',
                    'tasks' => 'All Tasks List',
                ];
                $title = $titles[$this->taskView] ?? 'Task Overview';
            @endphp

            <h3 class="text-lg sm:text-2xl font-semibold mb-2 sm:mb-3 text-center text-slate-800 tracking-tight">
                {{ $title }}
            </h3>

            <div class="mb-3 sm:mb-4 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-white/90 backdrop-blur-md border border-slate-200 text-[11px] sm:text-sm font-semibold text-slate-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-xs transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>

            <!-- Table Container -->
            <div class="relative shadow-md rounded-xl bg-white p-1.5 sm:p-3 overflow-hidden border border-slate-200/80">
                <section class="pt-1">
                    @if (session()->has('message'))
                        <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                    @endif

                    <div wire:loading.flex
                        wire:target.except="$dispatch,close-modal,callMountedTableAction, mountTableAction,nextPage,gotoPage,previousPage"
                        class="absolute inset-0 bg-white/80 backdrop-blur-xs flex justify-center items-center z-50 rounded-xl">
                        <svg class="animate-spin h-6 w-6 sm:h-7 sm:w-7 text-[rgb(7,139,221)]"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="ml-2 text-xs sm:text-sm font-medium text-slate-600">Loading Task...</span>
                    </div>

                    <div class="overflow-x-auto w-full touch-pan-x">

                        {{ $this->table }}
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>
