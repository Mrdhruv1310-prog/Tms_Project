<!-- Single Parent Root Element Wrapper -->
<div>
    <!-- Main Messages Feed Scrollable Dock Container -->
    <div
        class="mb-5 max-h-[420px] space-y-4 overflow-y-auto rounded-3xl border border-slate-200/80 dark:border-gray-800 bg-slate-50/70 dark:bg-gray-900/50 backdrop-blur-md p-5 custom-scrollbar shadow-inner">
        @forelse ($messages as $message)
            <!-- Message Row Box Layer -->
            <div
                class="group relative rounded-2xl bg-white dark:bg-gray-900 border border-slate-200/60 dark:border-gray-800 p-4 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-gray-700 transition-all duration-300">

                <!-- Metadata Header Row Control -->
                <div
                    class="mb-3 flex items-center justify-between gap-3 pb-2.5 border-b border-slate-100 dark:border-gray-800/80">
                    <div class="flex items-center gap-3">
                        <!-- Dynamic Minimal Avatar Circle Badge with subtle gradient -->
                        <div
                            class="w-8 h-8 rounded-xl bg-gradient-to-tr from-slate-900 to-slate-800 dark:from-blue-600 dark:to-indigo-600 flex items-center justify-center text-white text-[11px] font-black uppercase tracking-tight shadow-sm shadow-slate-900/20">
                            {{ strtoupper(substr($message->user?->first_name ?? 'U', 0, 1)) }}
                        </div>

                        <!-- Sender Identity Identifier Name -->
                        <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $message->user?->first_name }} {{ $message->user?->last_name }}
                        </span>
                    </div>

                    <!-- Dynamic Time Stamp Log Badge Layout -->
                    <span
                        class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-gray-500 bg-slate-100/80 dark:bg-gray-800 px-2.5 py-1 rounded-lg border border-slate-200/40 dark:border-gray-700">
                        {{ $message->created_at?->format('d M, Y • H:i') }}
                    </span>
                </div>

                <!-- Text Content Narrative Block Area -->
                <p
                    class="whitespace-pre-line text-xs sm:text-sm text-slate-600 dark:text-gray-300 leading-relaxed font-medium pl-1">
                    {{ $message->message }}
                </p>
            </div>
        @empty
            <!-- Premium Aesthetic Minimalistic Empty State UI Feed Layout -->
            <div
                class="rounded-2xl bg-white dark:bg-gray-900 backdrop-blur-md border border-slate-200/80 dark:border-gray-800 p-10 text-center shadow-sm">
                <div class="max-w-xs mx-auto flex flex-col items-center">
                    <!-- Modern Minimal Conversation Icon Container -->
                    <div
                        class="w-12 h-12 bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60 dark:border-blue-900 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-3.5 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">No Feed
                        History</h4>
                    <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1 font-medium">There are no task
                        conversation or remarks logged yet.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Embedded Minimal Custom Scrollbar Track Configuration Stylesheet -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</div>
