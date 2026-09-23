<div x-data="{
    show: @entangle('isOpen'),
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    role: '',
    status: '',
    resetForm() {
        this.first_name = '';
        this.last_name = '';
        this.email = '';
        this.phone_number = '';
        this.role = '';
        this.status = '';
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

            <!-- Modal header -->
            <div class="flex justify-between items-center pb-5 mb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-700 dark:text-white tracking-tight">
                        Add New User
                    </h3>
                    <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-0.5">
                        Create and configure a new system profile with custom role access
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

            <!-- Modal body -->
            <form wire:submit.prevent="saveUser" class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">

                    <!-- First Name -->
                    <div>
                        <label for="first_name"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">First
                            Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="first_name" wire:model="first_name" x-model="first_name"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                            @if ($submitted && $errors->has('first_name')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                            @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif"
                            placeholder="Enter First Name">
                        @if ($submitted)
                            @error('first_name')
                                <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Last
                            Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="last_name" wire:model="last_name" x-model="last_name"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                            @if ($submitted && $errors->has('last_name')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                            @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif"
                            placeholder="Enter Last Name">
                        @if ($submitted)
                            @error('last_name')
                                <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Email
                            <span class="text-rose-500">*</span></label>
                        <input type="text" id="email" wire:model="email" x-model="email"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                            @if ($submitted && $errors->has('email')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                            @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif"
                            placeholder="Enter Email Id">
                        @if ($submitted)
                            @error('email')
                                <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Phone
                            Number</label>
                        <input type="text" id="phone_number" wire:model="phone_number" x-model="phone_number"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                            @if ($submitted && $errors->has('phone_number')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                            @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif"
                            placeholder="Enter Phone Number">
                        @if ($submitted)
                            @error('phone_number')
                                <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Role</label>
                        <select id="role" wire:model.defer="role" x-model="role"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 focus:ring-2 transition-all shadow-2xl font-medium
                                @if ($submitted && $errors->has('role')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                                @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif
                            ">
                            <option value="">Select Role</option>
                            <option value="super-admin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        @if ($submitted && $errors->has('role'))
                            <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">
                                {{ $errors->first('role') }}
                            </p>
                        @endif
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Status</label>
                        <select id="status" wire:model.defer="status" x-model="status"
                            class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 focus:ring-2 transition-all shadow-2xs font-medium
                                @if ($submitted && $errors->has('status')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                                @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif
                            ">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @if ($submitted)
                            @error('status')
                                <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Password -->
                    <div class="sm:col-span-2">
                        <label for="password"
                            class="block mb-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" wire:model="password"
                                class="bg-slate-50/70 dark:bg-slate-950/50 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-2xl block w-full p-3.5 pr-10 focus:ring-2 transition-all shadow-2xs font-medium
                                @if ($submitted && $errors->has('password')) border-rose-500 focus:ring-rose-500 focus:border-rose-500
                                @else border-slate-200 dark:border-slate-800 focus:ring-blue-600 focus:border-blue-600 dark:focus:ring-blue-500 @endif"
                                placeholder="Enter Password">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5">
                                <i
                                    class="fa-regular fa-eye toggle-password text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer text-sm transition-colors"></i>
                            </div>
                        </div>
                        @if ($submitted)
                            @error('password')
                                <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex items-center gap-3 pt-3">
                    <button type="submit"
                        class="relative flex-1 justify-center rounded-2xl bg-blue-600 hover:bg-blue-700 px-5 py-3 text-xs sm:text-sm font-black uppercase tracking-wider text-white shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all cursor-pointer flex items-center"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Save User</span>
                        <span wire:loading class="flex items-center justify-center gap-2">
                            Saving User...
                            <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
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
