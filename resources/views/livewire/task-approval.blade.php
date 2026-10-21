<div x-data="{
    show: @entangle('taskUpdateModalOpen'),
    remark: '',
    resetForm() {
        this.remark = '';
    }
}" x-cloak x-show="show"
    class="fixed inset-0 flex items-center justify-center z-50 p-2.5 xs:p-4 sm:p-6 lg:p-8 overflow-y-auto transform-gpu"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- Backdrop Overlay -->
    <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" inert></div>

    <!-- Modal Content Box -->
    <div x-show="show" x-transition:enter="transition ease-out duration-150 transform-gpu"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100 transform-gpu"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-[340px] xs:max-w-[380px] sm:max-w-xl lg:max-w-2xl xl:max-w-2xl 2xl:max-w-3xl 3xl:max-w-4xl max-h-[92vh] overflow-y-auto custom-scrollbar my-auto">

        <div
            class="relative bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl shadow-slate-950/20 p-4 xs:p-6 sm:p-8 lg:p-10 2xl:p-12 overflow-hidden transition-all duration-150">

            <!-- Top Accent Glow Highlight -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

            <!-- Modal Header -->
            <div
                class="flex justify-between items-center pb-3.5 sm:pb-5 mb-4 sm:mb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3
                        class="text-base xs:text-lg sm:text-xl lg:text-2xl 2xl:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        Task Remark
                    </h3>
                    <p
                        class="text-[11px] xs:text-xs sm:text-sm lg:text-base font-medium text-slate-400 dark:text-slate-500 mt-0.5">
                        Update status and log feedback remarks for assigned team members
                    </p>
                </div>

                <button type="button" @click="resetForm(); show = false;"
                    class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 flex items-center justify-center text-slate-400 hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-rose-600 dark:hover:bg-rose-600 rounded-lg sm:rounded-xl transition-all duration-150 cursor-pointer shadow-2xs shrink-0 ml-2">
                    <svg inert class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form wire:submit.prevent="updateTaskRemark" class="space-y-4 sm:space-y-5 lg:space-y-6">

                <!-- Select Users Section -->
                <div>
                    <label for="users"
                        class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Select Users
                    </label>
                    <div class="relative">
                        <button id="dropdownUsersButton" type="button" data-dropdown-toggle="approvalDropdownUsers"
                            class="flex items-center justify-between w-full p-2.5 xs:p-3 lg:p-3.5 bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs sm:text-sm lg:text-base font-medium rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 transition-all shadow-2xs cursor-pointer">
                            <span class="text-xs sm:text-sm lg:text-base truncate">Select Team Members</span>
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 ml-2 text-slate-400 shrink-0"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2.5" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown content -->
                        <div id="approvalDropdownUsers"
                            class="hidden z-20 bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-800 w-full mt-2 p-1.5 sm:p-2">
                            <ul
                                class="h-40 sm:h-48 px-1.5 py-1 overflow-y-auto text-xs sm:text-sm lg:text-base text-slate-700 dark:text-slate-200 space-y-1 custom-scrollbar">
                                @foreach ($users as $user)
                                    <li wire:key="user-{{ $user->id }}"
                                        class="flex items-center py-2 px-2.5 sm:py-2.5 sm:px-3 rounded-lg sm:rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer">
                                        <input id="checkbox-user-{{ $user->id }}" type="checkbox"
                                            wire:model="selectedUsers" value="{{ $user->id }}"
                                            class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600 bg-slate-100 border-slate-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-900 focus:ring-2 dark:bg-slate-800 dark:border-slate-700 cursor-pointer shrink-0">

                                        <div
                                            class="ml-2.5 sm:ml-3 w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-white text-[10px] sm:text-[11px] font-black uppercase rounded-lg sm:rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-xs shrink-0">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>

                                        <label for="checkbox-user-{{ $user->id }}"
                                            class="ml-2.5 sm:ml-3 text-xs sm:text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200 cursor-pointer truncate">
                                            {{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @error('selectedUsers')
                        <p class="mt-1.5 text-xs sm:text-sm font-bold text-rose-600 dark:text-rose-400">{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remark Textarea Field -->
                <div>
                    <label for="remark"
                        class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Remark
                    </label>
                    <textarea id="remark" wire:model="remark" x-model="remark" required
                        class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 xs:p-3 sm:p-3.5 dark:placeholder-slate-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-2xs font-medium resize-none"
                        placeholder="Enter detailed task progress remark..." rows="4"></textarea>
                    @error('remark')
                        <p class="mt-1.5 text-xs sm:text-sm font-bold text-rose-600 dark:text-rose-400">{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Form Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 pt-2">
                    <button type="submit"
                        class="relative w-full sm:flex-1 justify-center rounded-xl sm:rounded-2xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 sm:py-3 lg:py-3.5 text-xs sm:text-sm lg:text-base font-black uppercase tracking-wider text-white shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all cursor-pointer flex items-center active:scale-[0.98]"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Remark & Status</span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            Updating...
                            <svg class="animate-spin h-3.5 w-3.5 sm:h-4 sm:w-4 text-white" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>

                    <button @click="resetForm(); show = false;" type="button"
                        class="w-full sm:w-auto text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white border border-rose-200 dark:border-rose-900/60 focus:ring-4 focus:outline-none focus:ring-rose-300 font-black uppercase tracking-wider rounded-xl sm:rounded-2xl text-xs sm:text-sm lg:text-base px-4 py-2.5 sm:py-3 lg:py-3.5 text-center transition-all cursor-pointer active:scale-[0.98]">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
