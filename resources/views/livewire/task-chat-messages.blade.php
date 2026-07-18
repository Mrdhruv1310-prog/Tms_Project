<!-- Single Parent Root Element Wrapper -->
<div>
    <!-- Main Messages Feed Scrollable Dock Container -->
    <div class="mb-5 max-h-96 space-y-3.5 overflow-y-auto rounded-2xl border border-slate-200/60 bg-slate-50/50 backdrop-blur-sm p-4 custom-scrollbar">
        @forelse ($messages as $message)
            <!-- Message Row Box Layer -->
            <div class="rounded-xl bg-white border border-slate-200/50 p-4 shadow-sm transition-all duration-200 hover:border-slate-300/80">

                <!-- Metadata Header Row Control -->
                <div class="mb-2 flex items-center justify-between gap-3 pb-1.5 border-b border-slate-50">
                    <div class="flex items-center gap-2.5">
                        <!-- Dynamic Minimal Avatar Circle Badge -->
                        <div class="w-6 h-6 rounded-full bg-slate-900 flex items-center justify-center text-white text-[10px] font-black uppercase tracking-tight shadow-sm">
                            {{ strtoupper(substr($message->user?->first_name ?? 'U', 0, 1)) }}
                        </div>

                        <!-- Sender Identity Identifier Name -->
                        <span class="text-sm font-extrabold text-slate-800 tracking-tight">
                            {{ $message->user?->first_name }} {{ $message->user?->last_name }}
                        </span>
                    </div>

                    <!-- Dynamic Time Stamp Log Badge Layout -->
                    <span class="text-[11px] font-semibold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/30">
                        {{ $message->created_at?->format('d M, Y • H:i') }}
                    </span>
                </div>

                <!-- Text Content Narrative Block Area -->
                <p class="whitespace-pre-line text-sm text-slate-600 leading-relaxed font-medium pl-0.5">
                    {{ $message->message }}
                </p>
            </div>
        @empty
            <!-- Premium Aesthetic Minimalistic Empty State UI Feed Layout -->
            <div class="rounded-2xl bg-white/95 backdrop-blur-md border border-slate-200/60 p-8 text-center shadow-sm">
                <div class="max-w-xs mx-auto flex flex-col items-center">
                    <!-- Modern Minimal Conversation Icon Container -->
                    <div class="w-10 h-10 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                        </svg>
                    </div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">No Feed History</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">There are no task conversation or remarks logged yet.</p>
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
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</div>
