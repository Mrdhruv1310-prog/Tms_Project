<div>
    <!-- Floating Chatbot Button -->
    <button wire:click="toggleChat" type="button"
        style="position: fixed !important; bottom: 24px !important; right: 95px !important; z-index: 99999 !important;"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-[rgb(7,139,221)] text-white shadow-2xl hover:bg-sky-600 transition-all duration-300 transform active:scale-95 cursor-pointer">
        @if ($isOpen)
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        @else
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        @endif
    </button>

    <!-- Chat Drawer Window -->
    @if ($isOpen)
        <div style="position: fixed !important; bottom: 90px !important; right: 95px !important; z-index: 99999 !important;"
            class="w-80 sm:w-96 rounded-2xl border border-slate-200 bg-white shadow-2xl overflow-hidden flex flex-col h-[520px]">

            @php
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                $firstName = $user->first_name ?? ($user->name ?? 'T');
                $lastName = $user->last_name ?? '';
                $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
            @endphp

            <!-- Header -->
            <div
                class="bg-[rgb(7,139,221)] px-4 py-3.5 text-white flex items-center justify-between shadow-xs shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs">
                        {{ $initials ?: 'TB' }}
                    </div>
                    <div>
                        <h3 class="text-sm font-bold leading-tight">Personal Task Chatbot</h3>
                        <p class="text-[10px] text-sky-100 font-medium">Instant Accurate Assistant</p>
                    </div>
                </div>
                <button wire:click="toggleChat" type="button" class="text-white/80 hover:text-white cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50">
                @foreach ($messages as $msg)
                    @if ($msg['role'] === 'user')
                        <div class="flex justify-end">
                            <div
                                class="bg-[rgb(7,139,221)] text-white text-xs px-3.5 py-2 rounded-2xl rounded-tr-none max-w-[80%] shadow-2xs leading-relaxed">
                                {!! $msg['content'] !!}
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start">
                            <div
                                class="bg-white text-slate-800 text-xs px-3.5 py-2.5 rounded-2xl rounded-tl-none max-w-[85%] border border-slate-200 shadow-2xs leading-relaxed">
                                {!! $msg['content'] !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Quick Action Options Buttons -->
            <div class="px-3 py-2 bg-slate-100 border-t border-slate-200 flex flex-wrap gap-1.5 shrink-0">
                <button type="button" wire:click="selectOption('Pending Task')"
                    class="px-2.5 py-1 text-[11px] font-semibold bg-white text-slate-700 hover:bg-[rgb(7,139,221)] hover:text-white rounded-lg border border-slate-200 shadow-2xs transition-all cursor-pointer">
                    Pending Task
                </button>
                <button type="button" wire:click="selectOption('In Progress Task')"
                    class="px-2.5 py-1 text-[11px] font-semibold bg-white text-slate-700 hover:bg-[rgb(7,139,221)] hover:text-white rounded-lg border border-slate-200 shadow-2xs transition-all cursor-pointer">
                    In Progress Task
                </button>
                <button type="button" wire:click="selectOption('Complete Task')"
                    class="px-2.5 py-1 text-[11px] font-semibold bg-white text-slate-700 hover:bg-[rgb(7,139,221)] hover:text-white rounded-lg border border-slate-200 shadow-2xs transition-all cursor-pointer">
                    Complete Task
                </button>
                <button type="button" wire:click="selectOption('Total Task')"
                    class="px-2.5 py-1 text-[11px] font-semibold bg-white text-slate-700 hover:bg-[rgb(7,139,221)] hover:text-white rounded-lg border border-slate-200 shadow-2xs transition-all cursor-pointer">
                    Total Task
                </button>
            </div>

            <!-- Input Form -->
            <form wire:submit.prevent="sendMessage"
                class="p-3 bg-white border-t border-slate-100 flex items-center gap-2 shrink-0">
                <input type="text" wire:model="userMessage" placeholder="Ask e.g. task link or pending..."
                    class="flex-1 text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-[rgb(7,139,221)]" />
                <button type="submit"
                    class="bg-[rgb(7,139,221)] text-white p-2 rounded-xl hover:bg-sky-600 transition-colors shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    @endif
</div>
