<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="cache-control" content="private, max-age=0, no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <link rel="icon" type="image/png" href="{{ asset('icons/nslogo.png') }}">
    <meta name="theme-color" content="#1a56db" />
    <link rel="apple-touch-icon" href="{{ asset('icons/nslogo.png') }}">
    <link rel="manifest" href="{{ asset('/1manifest.json') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>{{ $title ?? 'TMS Dashboard' }}</title>
    
    <!-- Scripts & Styles -->
    <script src="{{ asset('/sw.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @filamentStyles
    <style>
        #nprogress .bar {
            height: 7px !important;
            background: #ff0800 !important;
        }

        #loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: rgba(255, 255, 255, 1);
        }

        #loader-center {
            background-image: url('{{ asset('icons/tms.png') }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: calc(15vw + 100px);
            width: 100%;
            height: 100%;
            position: relative;
            animation: loader 1s alternate infinite ease-in-out;
        }

        @keyframes loader {
            0% { transform: scale(0.8); }
            100% { transform: scale(0.9); }
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="text-gray-900 antialiased bg-gray-50 dark:bg-gray-900">
    @component('components.spinner')
    @endcomponent

    <!-- Main App Wrapper -->
    <div class="min-h-screen flex flex-row sm:justify-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900">
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
                    @livewire('notifications')
        </div>
    </div>

    <!-- ELEKTRO AI ASSISTANT WIDGET -->
    <div x-data="elektroAssistant()" 
         wire:ignore
         id="elektro-chat-root"
         class="fixed bottom-24 right-6 z-50 select-none font-sans"
         @keydown.escape.window="isOpen = false">
        
        <!-- Floating Launcher Button: Automatically HIDES when chat is open -->
        <button x-show="!isOpen"
                @click="toggleChat()" 
                type="button"
                aria-label="Open Elektro Assistant"
                class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-slate-900 via-indigo-950 to-blue-600 text-white shadow-xl shadow-indigo-950/25 transition-all duration-300 hover:scale-105 hover:shadow-2xl active:scale-95 focus:outline-none border border-white/20">
            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
            </span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </button>

        <!-- Chat Window: Anchored above bottom bar with single close button -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             style="display: none;"
             class="w-[360px] sm:w-[420px] flex flex-col h-[580px] max-h-[80vh] rounded-3xl bg-white dark:bg-slate-900 shadow-2xl shadow-indigo-950/30 border border-slate-200/90 dark:border-slate-800 overflow-hidden">
            
            <!-- Modern Header -->
            <div class="px-5 py-3.5 bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white flex items-center justify-between border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 text-white shadow-sm border border-white/20 text-sm">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-sm tracking-wide text-white">Elektro</h4>
 
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button @click="clearMessages()" 
                            type="button"
                            title="Reset Chat" 
                            class="px-2.5 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-white/10 transition">
                        Clear
                    </button>
                    <!-- Single Canonical Close Button -->
                    <button @click="isOpen = false" 
                            type="button"
                            title="Close Window"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-white hover:bg-white/10 text-xl transition">
                        &times;
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div x-ref="chatBody" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/70 dark:bg-slate-950/40 text-xs sm:text-sm">
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="chat-msg-item flex items-start gap-2.5" :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'">
                        <div x-show="msg.sender === 'bot'" 
                             class="w-7 h-7 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex-shrink-0 flex items-center justify-center text-xs shadow-sm mt-0.5">
                            ⚡
                        </div>
                        <div class="max-w-[88%] px-3.5 py-2.5 rounded-2xl leading-relaxed text-xs sm:text-sm transition-all"
                             :class="msg.sender === 'user' 
                                ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-br-none shadow-sm' 
                                : (msg.isError 
                                    ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 rounded-bl-none shadow-sm' 
                                    : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700/80 rounded-bl-none shadow-sm')">
                            <div class="space-y-1" x-html="renderFormatted(msg.text)"></div>
                        </div>
                    </div>
                </template>

                <!-- Loading Indicator -->
                <div x-show="isLoading" class="flex items-center gap-2.5 justify-start">
                    <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex-shrink-0 flex items-center justify-center text-xs shadow-sm animate-pulse">
                        ⚡
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 px-4 py-2 rounded-2xl rounded-bl-none shadow-sm text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span class="inline-flex space-x-1">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                            <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                            <span class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce"></span>
                        </span>
                        <span>Retrieving tasks...</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Prompt Chips -->
            <div class="px-3 py-2 bg-white dark:bg-slate-900 border-t border-slate-200/70 dark:border-slate-800 flex gap-1.5 overflow-x-auto no-scrollbar">
                <button type="button" 
                        @click="sendMessage('Show high priority pending tasks')"
                        :disabled="isLoading"
                        class="text-[11px] whitespace-nowrap bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 px-2.5 py-1 rounded-lg font-medium transition disabled:opacity-50">
                    🚩 High Priority
                </button>
                <button type="button" 
                        @click="sendMessage('Show in-progress tasks')"
                        :disabled="isLoading"
                        class="text-[11px] whitespace-nowrap bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 px-2.5 py-1 rounded-lg font-medium transition disabled:opacity-50">
                    ⚡ In Progress
                </button>
                <button type="button" 
                        @click="sendMessage('What tasks are assigned to me?')"
                        :disabled="isLoading"
                        class="text-[11px] whitespace-nowrap bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 px-2.5 py-1 rounded-lg font-medium transition disabled:opacity-50">
                    📋 My Tasks
                </button>
                <button type="button" 
                        @click="sendMessage('Summarize all pending tasks')"
                        :disabled="isLoading"
                        class="text-[11px] whitespace-nowrap bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded-lg font-medium transition disabled:opacity-50">
                    📊 All Pending
                </button>
            </div>

            <!-- Message Input Area -->
            <form @submit.prevent="submitForm()" class="p-2.5 bg-white dark:bg-slate-900 border-t border-slate-200/70 dark:border-slate-800 flex items-center gap-2">
                <input x-model="userInput" 
                       x-ref="messageInput"
                       type="text" 
                       placeholder="Ask Elektro about tasks, assignees, deadlines..." 
                       :disabled="isLoading"
                       class="flex-1 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs sm:text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 disabled:opacity-50 transition">
                <button type="submit" 
                        :disabled="!userInput.trim() || isLoading"
                        class="w-9 h-9 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-95 disabled:opacity-40 disabled:scale-100 rounded-xl text-white flex items-center justify-center shadow-md shadow-indigo-600/20 transition">
                    <svg class="h-4 w-4 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Livewire & Flowbite Handlers -->
    <script>
        document.addEventListener('livewire:navigated', () => {
            if (typeof initFlowbite === 'function') {
                initFlowbite();
            }
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (performance && performance.navigation.type === 2)) {
                window.location.reload();
            }
        });

        function elektroAssistant() {
            return {
                isOpen: false,
                isLoading: false,
                userInput: '',
                messages: [
                    {
                        sender: 'bot',
                        text: "Hi {{ auth()->user()->first_name ?? 'there' }}! I am Elektro, your TMS Assistant. You can ask me about task status, deadlines, assignees, or priorities.",
                        isError: false
                    }
                ],
                toggleChat() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.$nextTick(() => {
                            this.$refs.messageInput?.focus();
                            this.scrollToBottom();
                        });
                    }
                },
                clearMessages() {
                    this.messages = [
                        {
                            sender: 'bot',
                            text: "Conversation cleared. How may I assist you with your tasks today?",
                            isError: false
                        }
                    ];
                },
                scrollToBottom() {
                    this.$nextTick(() => {
                        const body = this.$refs.chatBody;
                        if (body) {
                            body.scrollTo({
                                top: body.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    });
                },
                scrollToMessageStart() {
                    this.$nextTick(() => {
                        setTimeout(() => {
                            const body = this.$refs.chatBody;
                            if (!body) return;
                            const msgNodes = body.querySelectorAll('.chat-msg-item');
                            const latestMsg = msgNodes[msgNodes.length - 1];
                            if (latestMsg) {
                                const bodyRect = body.getBoundingClientRect();
                                const msgRect = latestMsg.getBoundingClientRect();
                                const targetScroll = body.scrollTop + (msgRect.top - bodyRect.top) - 10;
                                body.scrollTo({
                                    top: Math.max(0, targetScroll),
                                    behavior: 'smooth'
                                });
                            }
                        }, 50);
                    });
                },
                renderFormatted(raw) {
                    if (!raw) return '';
                    let text = raw;

                    // Support multiple patterns emitted by main.py
                    const taskRegex = /(?:^[•*-]|\n[•*-])\s*\**Task #(\d+)[:\s|]+(?:Title:\s*)?["']?([^"'\n*—–|]+)["']?\**\s*[—–|-]\s*(?:Assigned [tT]o:\s*([^|\n]+))?\|\s*Status:\s*[`']?([a-zA-Z0-9_ -]+)[`']?\s*\|\s*Priority:\s*[`']?([a-zA-Z0-9_ -]+)[`']?(?:\s*\|\s*Due:\s*([^\n\r]+))?/gi;

                    text = text.replace(taskRegex, function(match, id, title, assignee, status, priority, due) {
                        const cleanStatus = (status || 'pending').trim().toLowerCase().replace(/\s+/g, '_');
                        const cleanPriority = (priority || 'normal').trim().toLowerCase();
                        const cleanAssignee = (assignee || 'Unassigned').trim();
                        const cleanDue = (due || 'No deadline').trim();

                        // Priority accent & badge
                        let accentBorder = "border-l-blue-500";
                        let priorityBadge = "bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300";
                        if (cleanPriority.includes('high') || cleanPriority.includes('urgent')) {
                            accentBorder = "border-l-rose-500";
                            priorityBadge = "bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 font-semibold";
                        } else if (cleanPriority.includes('medium')) {
                            accentBorder = "border-l-amber-500";
                            priorityBadge = "bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300";
                        } else if (cleanPriority.includes('low')) {
                            accentBorder = "border-l-emerald-500";
                            priorityBadge = "bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300";
                        }

                        // Status badge & indicator dot
                        let statusBadge = "bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700";
                        let statusDot = "bg-slate-400";
                        if (cleanStatus.includes('progress')) {
                            statusBadge = "bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-900";
                            statusDot = "bg-amber-500 animate-pulse";
                        } else if (cleanStatus.includes('complete') || cleanStatus.includes('done')) {
                            statusBadge = "bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-900";
                            statusDot = "bg-emerald-500";
                        } else if (cleanStatus.includes('pending')) {
                            statusBadge = "bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-900";
                            statusDot = "bg-blue-500";
                        }

                        // Compact single-line markup to prevent extraneous <br> tags
                        return `<div class="my-2 p-3 bg-slate-50/90 dark:bg-slate-800/90 rounded-xl border border-slate-200/90 dark:border-slate-700/80 border-l-4 ${accentBorder} shadow-sm"><div class="flex items-center justify-between gap-1 mb-1.5"><span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">#${id}</span><div class="flex items-center gap-1.5"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold border ${statusBadge}"><span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>${cleanStatus.replace(/_/g, ' ')}</span><span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider ${priorityBadge}">${cleanPriority}</span></div></div><h5 class="font-semibold text-slate-900 dark:text-slate-100 text-xs sm:text-[13px] leading-snug mb-2">${title.trim()}</h5><div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200/70 dark:border-slate-700/70 text-[11px] text-slate-500 dark:text-slate-400"><div class="flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300 truncate max-w-[55%]"><span>👤</span><span class="truncate">${cleanAssignee}</span></div><div class="flex items-center gap-1 whitespace-nowrap text-[10px] text-slate-500"><span>📅</span><span>${cleanDue}</span></div></div></div>`.replace(/\n\s*/g, '');
                    });

                    // 1. Backtick code chips
                    text = text.replace(/`([^`]+)`/g, '<span class="inline-block px-1.5 py-0.5 font-mono text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 rounded border border-slate-200 dark:border-slate-700">$1</span>');

                    // 2. Bold tags
                    text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-slate-900 dark:text-white">$1</strong>');

                    // 3. Bullet points for standard prose
                    text = text.replace(/^[•*-]\s+(.+)$/gm, '<div class="flex items-start gap-1.5 my-1"><span class="text-blue-500 font-bold">•</span><span>$1</span></div>');

                    // 4. Line breaks for natural conversational replies
                    text = text.replace(/\n{2,}/g, '<div class="h-2"></div>');
                    text = text.replace(/\n/g, '<br>');

                    return text;
                },
                submitForm() {
                    if (!this.userInput.trim() || this.isLoading) return;
                    const query = this.userInput.trim();
                    this.userInput = '';
                    this.sendMessage(query);
                },
                async sendMessage(promptText) {
                    this.messages.push({ sender: 'user', text: promptText, isError: false });
                    this.isLoading = true;
                    this.scrollToBottom();

                    try {
                        const response = await fetch("/chatbot/ask", {
                            method: "POST",
                            credentials: "same-origin",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ message: promptText })
                        });

                        const rawText = await response.text();
                        let data = {};
                        try {
                            data = JSON.parse(rawText);
                        } catch (e) {
                            console.error("Non-JSON Response received:", rawText);
                        }

                        if (response.ok && data.reply) {
                            this.messages.push({ sender: 'bot', text: data.reply, isError: false });
                        } else if (response.status === 419) {
                            this.messages.push({ 
                                sender: 'bot', 
                                text: "Session expired. Please refresh the page to continue.", 
                                isError: true 
                            });
                        } else {
                            this.messages.push({ 
                                sender: 'bot', 
                                text: data.reply || ("Error " + response.status + ": Check Laravel logs."), 
                                isError: true 
                            });
                        }
                    } catch (err) {
                        this.messages.push({ 
                            sender: 'bot', 
                            text: "Connection failed. Please ensure the backend service is running.", 
                            isError: true 
                        });
                    } finally {
                        this.isLoading = false;
                        this.scrollToMessageStart();
                    }
                }
            };
        }
    </script>

    @filamentScripts
</body>
</html>