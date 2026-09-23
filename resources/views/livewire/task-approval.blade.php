<div x-data="{
    show: @entangle('taskUpdateModalOpen'),
    remark: '',
    resetForm() {
        this.remark = '';
    }
}" x-cloak x-show="show" class="fixed inset-0 flex items-center justify-center z-50 p-4 sm:p-6"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- Backdrop Overlay -->
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
        inert></div>

    <!-- Modal Content Box -->
    <div x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto custom-scrollbar">

        <div
            class="relative bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl shadow-slate-950/20 p-6 sm:p-8 overflow-hidden">

            <!-- Top Accent Glow Highlight -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-5 mb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">
                        Task Remark
                    </h3>
                    <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-0.5">
                        Update status and log feedback remarks for assigned team members
                    </p>
                </div>

                <button type="button" @click="resetForm(); show = false;"
                    class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-rose-600 dark:hover:bg-rose-600 rounded-xl transition-all duration-200 cursor-pointer shadow-2xs">
                    <svg inert class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form wire:submit.prevent="updateTaskRemark" class="space-y-5">

                <!-- Select Users Section -->
                <div>
                    <label for="users"
                        class="block mb-2 text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Select
                        Users</label>
                    <div class="relative">
                        <button id="dropdownUsersButton" type="button" data-dropdown-toggle="approvalDropdownUsers"
                            class="flex items-center justify-between w-full p-3 bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-sm font-medium rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 transition-all shadow-2xs">
                            <span class="text-xs sm:text-sm">Select Team Members</span>
                            <svg class="w-3.5 h-3.5 ml-2 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2.5" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown content -->
                        <div id="approvalDropdownUsers"
                            class="hidden z-20 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-800 w-full mt-2 p-2">
                            <ul
                                class="h-48 px-2 py-1 overflow-y-auto text-sm text-slate-700 dark:text-slate-200 space-y-1 custom-scrollbar">
                                @foreach ($users as $user)
                                    <li wire:key="user-{{ $user->id }}"
                                        class="flex items-center py-2.5 px-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer">
                                        <input id="checkbox-user-{{ $user->id }}" type="checkbox"
                                            wire:model="selectedUsers" value="{{ $user->id }}"
                                            class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-900 focus:ring-2 dark:bg-slate-800 dark:border-slate-700 cursor-pointer">

                                        <div
                                            class="ml-3 w-8 h-8 flex items-center justify-center text-white text-[11px] font-black uppercase rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-sm">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>

                                        <label for="checkbox-user-{{ $user->id }}"
                                            class="ml-3 text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 cursor-pointer truncate">
                                            {{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @error('selectedUsers')
                        <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remark Textarea Field -->
                <div>
                    <label for="remark"
                        class="block mb-2 text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Remark</label>
                    <textarea id="remark" wire:model="remark" x-model="remark" required
                        class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 block w-full p-3.5 dark:placeholder-slate-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all shadow-2xs font-medium resize-none"
                        placeholder="Enter detailed task progress remark..." rows="4"></textarea>
                    @error('remark')
                        <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="relative flex-1 justify-center rounded-2xl bg-blue-600 hover:bg-blue-700 px-5 py-3 text-xs sm:text-sm font-black uppercase tracking-wider text-white shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all cursor-pointer flex items-center"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Remark & Status</span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            Updating...
                            <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>

                    <button @click="resetForm(); show = false;" type="button"
                        class="text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white border border-rose-200 dark:border-rose-900/60 focus:ring-4 focus:outline-none focus:ring-rose-300 font-black uppercase tracking-wider rounded-2xl text-xs sm:text-sm px-5 py-3 text-center transition-all cursor-pointer">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
