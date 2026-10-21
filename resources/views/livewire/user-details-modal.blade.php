<div x-data="{
    show: @entangle('isOpen'),
    resetForm() {
        $wire.resetForm();
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
                        class="text-base xs:text-lg sm:text-xl lg:text-2xl 2xl:text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
                        <span x-text="$wire.user_id ? 'Edit Employee Details' : 'Add New Employee'"></span>
                    </h3>
                    <p
                        class="text-[11px] xs:text-xs sm:text-sm lg:text-base font-medium text-slate-400 dark:text-slate-500 mt-0.5">
                        <span
                            x-text="$wire.user_id ? 'Update employee profile and configuration' : 'Create and configure a new system profile with custom role access'"></span>
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

            @php
                $authUserId = (int) Auth::id();
                $editingUserId = (int) ($user_id ?? 0);

                // Auth user cannot change their own role and status
                $isSelfProfile = $editingUserId > 0 && $editingUserId === $authUserId;

                // Disable Role and Status if Auth user is editing their own profile
                $disableRoleAndStatus = $isSelfProfile;
            @endphp

            <!-- Modal Body Form -->
            <form wire:submit.prevent="saveUser" class="space-y-4 sm:space-y-5 lg:space-y-6">
                <div class="grid gap-3.5 sm:gap-4 sm:grid-cols-2">

                    <!-- First Name -->
                    <div>
                        <label for="first_name"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            First Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="first_name" wire:model.defer="first_name" maxlength="255"
                            onkeypress="return /^[a-zA-Z\s]$/.test(event.key)"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                            @error('first_name') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror"
                            placeholder="Enter First Name">
                        @error('first_name')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Last Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="last_name" wire:model.defer="last_name" maxlength="255"
                            onkeypress="return /^[a-zA-Z\s]$/.test(event.key)"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                        @error('last_name') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror"
                            placeholder="Enter Last Name">
                        @error('last_name')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="email" wire:model.defer="email" maxlength="255"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                        @error('email') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror"
                            placeholder="Enter Email Id">
                        @error('email')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="phone_number" wire:model.defer="phone_number" maxlength="10"
                            onkeypress="return /^[0-9]$/.test(event.key)"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                        @error('phone_number') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror"
                            placeholder="Enter 10 Digit Phone Number">
                        @error('phone_number')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <select id="role" wire:model.defer="role" @if ($disableRoleAndStatus) disabled @endif
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 focus:ring-2 transition-all shadow-2xs font-medium disabled:opacity-60 disabled:cursor-not-allowed
                        @error('role') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror">
                            <option value="" disabled>Select Role</option>
                            <option value="manager">Manager</option>
                            <option value="hr">HR</option>
                            <option value="employee">Employee</option>
                        </select>
                        @error('role')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" wire:model.defer="status"
                            @if ($disableRoleAndStatus) disabled @endif
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 focus:ring-2 transition-all shadow-2xs font-medium disabled:opacity-60 disabled:cursor-not-allowed
                            @error('status') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                            @if ($user_id && !$disableRoleAndStatus)
                                <option value="-1" class="text-rose-600 font-bold">Delete</option>
                            @endif
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="sm:col-span-2">
                        <label for="password"
                            class="block mb-1.5 sm:mb-2 text-[10px] xs:text-xs lg:text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Password <template x-if="!$wire.user_id"><span class="text-rose-500">*</span></template>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" wire:model.defer="password"
                                class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm lg:text-base rounded-xl sm:rounded-2xl block w-full p-2.5 xs:p-3 sm:p-3.5 pr-10 focus:ring-2 transition-all shadow-2xs font-medium
                        @error('password') border-rose-500 focus:ring-rose-500 focus:border-rose-500 bg-rose-50/20 @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @enderror"
                                placeholder="Enter Password">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5">
                                <i
                                    class="fa-regular fa-eye toggle-password text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer text-xs sm:text-sm transition-colors"></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 pt-2 sm:pt-3">
                    <button type="submit"
                        class="relative w-full sm:flex-1 justify-center rounded-xl sm:rounded-2xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 sm:py-3 lg:py-3.5 text-xs sm:text-sm lg:text-base font-bold text-white shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all cursor-pointer flex items-center active:scale-[0.98]"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <span x-text="$wire.user_id ? 'Update Employee' : 'Save Employee'"></span>
                        </span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            <span x-text="$wire.user_id ? 'Updating Employee...' : 'Saving Employee...'"></span>
                            <svg class="animate-spin h-3.5 w-3.5 sm:h-4 sm:w-4 text-white" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>

                    <button @click="resetForm(); show = false;" type="button"
                        class="w-full sm:w-auto text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white border border-rose-200 dark:border-rose-900/60 focus:ring-4 focus:outline-none focus:ring-rose-300 font-bold uppercase tracking-wider rounded-xl sm:rounded-2xl text-xs sm:text-sm lg:text-base px-4 py-2.5 sm:py-3 lg:py-3.5 text-center transition-all cursor-pointer active:scale-[0.98]">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-password')) {
            const toggle = e.target.closest('.toggle-password');
            toggle.classList.toggle('fa-eye');
            toggle.classList.toggle('fa-eye-slash');

            const input = document.querySelector("input[name='password']");
            if (!input) return;

            input.type = input.type === 'password' ? 'text' : 'password';
        }
    });
</script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showDeleteConfirmationPrompt', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this employee?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmDeleteUser');
                    } else {
                        Livewire.dispatch('closeModal');
                    }
                });
            } else {
                if (confirm("Are you sure you want to delete this employee?")) {
                    Livewire.dispatch('confirmDeleteUser');
                } else {
                    Livewire.dispatch('closeModal');
                }
            }
        });
    });
</script>
