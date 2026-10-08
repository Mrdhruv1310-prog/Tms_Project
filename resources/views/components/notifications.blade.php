<div x-data="{
    messages: [],
    remove(mid) {
        $dispatch('close-me', { id: mid })

        let m = this.messages.filter((m) => { return m.id == mid })
        if (m.length) {
            setTimeout(() => { this.messages.splice(this.messages.indexOf(m[0]), 1) }, 4000)
        }
    },
}"
    @notify.window="console.log($event.detail);
                let mid = Date.now();
                let data = $event.detail;

                // Check if the payload is an array
            if (Array.isArray(data)) {
                        data.forEach(item => {
                                    console.log('Item:', item); // Log each item

                    messages.push({id: mid, msg: item.message, type: item.type});
                });
            }
        else {
                console.log('Single item:', data); // Log single item

            // If not, treat it as a single object
            messages.push({id: mid, msg: data.message, type: data.type});
        }
            setTimeout(() => { remove(mid) }, 4000);
        "
    class="fixed inset-0 flex flex-col items-end justify-start px-4 py-6 pointer-events-none sm:p-6 sm:justify-start space-y-4"
    style="z-index: 9999999999 !important;">
    <template x-for="(message, messageIndex) in messages" :key="messageIndex" hidden>
        <div x-data="{ id: message.id, show: false }" x-init="$nextTick(() => { show = true })" x-show="show"
            @close-me.window="if ($event.detail.id == id) {show=false}"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="{
                'bg-green-50': message.type === 'success',
                'bg-red-50': message.type === 'error',
            }"
            class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto">
            <div class="rounded-lg shadow-lg overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Dynamically change the icon based on the message type -->
                            <template x-if="message.type === 'success'">
                                <svg class="h-6 w-6" viewBox="0 0 50 50" xml:space="preserve">
                                    <circle style="fill:#4ade80;" cx="25" cy="25" r="25" />
                                    <polyline
                                        style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;"
                                        points="
                                        38,15 22,33 12,25 " />
                                </svg>
                            </template>
                            <template x-if="message.type === 'error'">
                                <svg class="h-6 w-6" viewBox="0 0 50 50" xml:space="preserve">
                                    <circle style="fill:#f87171;" cx="25" cy="25" r="25" />
                                    <polyline
                                        style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-miterlimit:10;"
                                        points="16,34 25,25 34,16
                                        " />
                                    <polyline
                                        style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-miterlimit:10;"
                                        points="16,16 25,25 34,34
                                        " />
                                </svg>
                            </template>
                        </div>
                        <template x-if="message.type === 'success'">
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p x-text="message.msg" class="text-sm leading-5 font-medium text-green-700"></p>
                            </div>
                        </template>
                        <template x-if="message.type === 'error'">
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p x-text="message.msg" class="text-sm leading-5 font-medium text-red-700"></p>
                            </div>
                        </template>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="remove(message.id)"
                                class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @persist('navigate-spinner')
                <div id="loader" x-data="{ navigating: false }" x-init="document.addEventListener('livewire:navigating', () => navigating = true);
                document.addEventListener('livewire:navigated', () => navigating = false);" x-show="navigating"
                    x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    style="z-index: 9999999; display: none;"
                    class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center pointer-events-none">

                    <div
                        class="flex flex-col items-center gap-3 p-4 rounded-2xl bg-white/80 dark:bg-gray-800/80 shadow-2xl border border-white/20">
                        <div id="loader-center"
                            class="w-12 h-12 border-4 border-blue-600/20 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>
                </div>
            @endpersist
        </div>
    </template>
</div>
