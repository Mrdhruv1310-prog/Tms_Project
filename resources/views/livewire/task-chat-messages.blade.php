<!-- Single Parent Root Element Wrapper -->
<div class="w-full transform-gpu transition-all duration-150 antialiased">
    <!-- Main Messages Feed Scrollable Dock Container -->
    <div
        class="mb-3 sm:mb-4 max-h-[260px] xs:max-h-[300px] sm:max-h-[360px] lg:max-h-[420px] space-y-2.5 sm:space-y-3.5 overflow-y-auto rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-gray-800 bg-slate-50/70 dark:bg-gray-900/50 backdrop-blur-md p-2.5 xs:p-3 sm:p-4 custom-scrollbar shadow-inner">
        @forelse ($messages as $message)
            <!-- Message Row Box Layer -->
            <div
                class="group relative rounded-lg sm:rounded-xl bg-white dark:bg-gray-900 border border-slate-200/60 dark:border-gray-800 p-2.5 xs:p-3 sm:p-3.5 shadow-2xs hover:shadow-sm hover:border-slate-300 dark:hover:border-gray-700 transition-all duration-150">

                <!-- Metadata Header Row Control -->
                <div
                    class="mb-2 flex flex-wrap items-center justify-between gap-1.5 pb-1.5 sm:pb-2 border-b border-slate-100 dark:border-gray-800/80">
                    <div class="flex items-center gap-2 min-w-0">
                        <!-- Dynamic Minimal Avatar Circle Badge -->
                        <div
                            class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-gradient-to-tr from-slate-900 to-slate-800 dark:from-blue-600 dark:to-indigo-600 flex items-center justify-center text-white text-[10px] font-black uppercase tracking-tight shadow-xs shrink-0">
                            {{ strtoupper(substr($message->user?->first_name ?? 'U', 0, 1)) }}
                        </div>

                        <!-- Sender Identity Identifier Name -->
                        <span
                            class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white tracking-tight truncate">
                            {{ $message->user?->first_name }} {{ $message->user?->last_name }}
                        </span>
                    </div>

                    <!-- Dynamic Time Stamp Log Badge Layout -->
                    <span
                        class="text-[9px] sm:text-[10px] font-semibold text-slate-400 dark:text-gray-500 bg-slate-100/80 dark:bg-gray-800 px-2 py-0.5 rounded-md border border-slate-200/40 dark:border-gray-700 shrink-0">
                        {{ $message->created_at?->format('d M, Y • H:i') }}
                    </span>
                </div>

                <!-- Text Content Narrative Block Area -->
                <p
                    class="whitespace-pre-line text-xs sm:text-sm text-slate-600 dark:text-gray-300 leading-relaxed font-medium pl-0.5">
                    {{ $message->message }}
                </p>
            </div>
        @empty
            <!-- Premium Aesthetic Minimalistic Empty State UI Feed Layout -->
            <div
                class="rounded-xl bg-white dark:bg-gray-900 backdrop-blur-md border border-slate-200/80 dark:border-gray-800 p-5 sm:p-8 text-center shadow-2xs">
                <div class="max-w-xs mx-auto flex flex-col items-center">
                    <!-- Modern Minimal Conversation Icon Container -->
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60 dark:border-blue-900 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-2.5 shadow-2xs">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        No Feed History</h4>
                    <p class="text-[10px] sm:text-xs text-slate-400 dark:text-gray-500 mt-1 font-medium">
                        There are no task conversation or remarks logged yet.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Embedded Minimal Custom Scrollbar Track Configuration Stylesheet -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</div>
