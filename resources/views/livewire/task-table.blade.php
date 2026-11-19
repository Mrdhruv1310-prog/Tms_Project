<div x-data="{
    filterOpen: false,
    toggleFilters() {
        this.filterOpen = !this.filterOpen;
        const filterEl = document.querySelector('.fi-ta-filters');
        if (filterEl) {
            filterEl.style.display = this.filterOpen ? 'block' : 'none';
        }
    }
}">
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

        /* --- BULLETPROOF RESPONSIVE FILTER PANEL FIXES --- */
        .fi-ta-filters {
            display: none;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            padding: 1.25rem !important;
            margin-bottom: 1.5rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        }

        /* Responsive Grid layout for filter form elements */
        .fi-ta-filters>div,
        .fi-ta-filters .filament-tables-filters-form {
            display: grid !important;
            grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
            gap: 1.25rem !important;
            width: 100% !important;
            align-items: center !important;
        }

        @media (min-width: 640px) {

            .fi-ta-filters>div,
            .fi-ta-filters .filament-tables-filters-form {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (min-width: 1024px) {

            .fi-ta-filters>div,
            .fi-ta-filters .filament-tables-filters-form {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        /* Ensure individual filter fields take full width inside their grid cell without overlapping */
        .fi-ta-filters .fi-fo-field-wrp,
        .fi-ta-filters .fi-fo-field-wrp-content,
        .fi-ta-filter-field {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            max-width: 100% !important;
            gap: 0.35rem !important;
        }

        .fi-ta-filters select,
        .fi-ta-filters input,
        .fi-ta-filters .fi-input-wrp {
            width: 100% !important;
            max-width: 100% !important;
            border-radius: 0.75rem !important;
            border-color: #cbd5e1 !important;
            font-size: 0.875rem !important;
        }

        /* Fix labels and hints to prevent text overlapping */
        .fi-ta-filters .fi-fo-field-wrp-label {
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            color: #475569 !important;
            margin-bottom: 0.25rem !important;
        }

        @media (max-width: 1024px) {
            .fi-ta-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
                padding: 0.5rem !important;
            }

            .fi-ta-header-toolbar {
                width: 100% !important;
                flex-wrap: wrap !important;
                gap: 0.5rem !important;
                justify-content: space-between !important;
            }

            .fi-ta-search-field {
                width: 100% !important;
                max-width: 100% !important;
            }

            .fi-ta-search-field input {
                width: 100% !important;
                font-size: 14px !important;
            }

            .fi-ta-table {
                font-size: 0.72rem !important;
                min-width: 750px !important;
            }

            .fi-ta-cell {
                padding: 0.35rem !important;
            }

            .fi-ta-header-cell {
                padding: 0.35rem !important;
                font-size: 0.70rem !important;
            }

            .fi-ta-actions {
                gap: 0.25rem !important;
            }

            /* Pagination & Footer Controls */
            .fi-ta-pagination {
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.75rem !important;
                padding: 0.75rem 0.5rem !important;
                text-align: center !important;
            }

            .fi-ta-pagination-wrp {
                width: 100% !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.5rem !important;
            }

            .fi-ta-pagination-records-per-page-select {
                width: 100% !important;
                max-width: 100% !important;
                justify-content: center !important;
            }

            .fi-pagination {
                justify-content: center !important;
                flex-wrap: wrap !important;
                gap: 0.25rem !important;
            }
        }
    </style>

    <div
        class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] antialiased text-gray-800 overflow-x-hidden">
        <div
            class="absolute -top-32 -left-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute -bottom-32 -right-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
        </div>

        <main
            class="scrollcontainer md:ml-16 px-2.5 xs:px-3 sm:px-6 md:px-8 py-3.5 sm:py-6 pt-16 sm:pt-20 md:pt-24 pb-16 max-w-[1750px] 2xl:max-w-[2400px] 4xl:max-w-[3400px] mx-auto transition-all duration-300">
            @php
                $titles = [
                    'my_tasks' => 'My Task List',
                    'assigned_to_others' => 'Assigned Task List',
                    'tasks' => 'All Tasks List',
                ];
                $title = $titles[$this->taskView] ?? 'Task Overview';
            @endphp

            <h3 class="text-base sm:text-2xl font-bold tracking-tight mb-3 sm:mb-4 text-center text-slate-900 truncate">
                {{ $title }}
            </h3>

            <div class="mb-3.5 sm:mb-5 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/90 backdrop-blur-md border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-xs transition-all duration-200 active:scale-95 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>

            <!-- Table Container -->
            <div
                class="relative shadow-lg rounded-2xl bg-white/95 backdrop-blur-xl p-2 sm:p-4 overflow-hidden border border-slate-200/80 ring-1 ring-black/5">
                <section class="pt-1">
                    @if (session()->has('message'))
                        <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                    @endif

                    <div wire:loading.flex
                        wire:target.except="$dispatch,close-modal,callMountedTableAction, mountTableAction,nextPage,gotoPage,previousPage"
                        class="absolute inset-0 bg-white/80 backdrop-blur-xs flex justify-center items-center z-50 rounded-2xl">
                        <svg class="animate-spin h-7 w-7 text-[rgb(7,139,221)]" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="ml-2.5 text-xs sm:text-sm font-semibold text-slate-700">Loading Task...</span>
                    </div>

                    <div class="overflow-x-auto w-full touch-pan-x">
                        {{ $this->table }}
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Custom Click Interceptor for Filter Toggle Button -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bindFilterButton = () => {
                const triggerBtn = document.querySelector(
                    '.fi-ta-filters-trigger, button[title*="Filter"], button[aria-label*="Filter"]');
                const filterEl = document.querySelector('.fi-ta-filters');

                if (triggerBtn && filterEl && !triggerBtn.dataset.boundCustom) {
                    triggerBtn.dataset.boundCustom = 'true';

                    const newBtn = triggerBtn.cloneNode(true);
                    triggerBtn.parentNode.replaceChild(newBtn, triggerBtn);

                    newBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const isHidden = window.getComputedStyle(filterEl).display === 'none';
                        filterEl.style.display = isHidden ? 'block' : 'none';
                    });
                }
            };

            bindFilterButton();
            const observer = new MutationObserver(bindFilterButton);
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
</div>
