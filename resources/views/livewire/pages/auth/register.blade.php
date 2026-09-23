<div
    class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] p-4 sm:p-6 lg:p-8 overflow-y-auto">

    <!-- Decorative Glow Elements for Professional Depth -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute -bottom-32 -right-32 w-96 h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
    </div>

    <div
        class="relative w-full max-w-md space-y-4 rounded-3xl bg-white/85 backdrop-blur-xl p-6 sm:p-8 shadow-[0_20px_50px_rgba(0,0,0,0.08)] my-auto border border-white/80 ring-1 ring-black/5">

        <!-- Logo & Header -->
        <div class="flex flex-col items-center text-center">
            <div class="flex px-5 py-3 items-center justify-center rounded-2xl">
                <img class="h-11 w-auto object-contain drop-shadow-sm" src="{{ url('icons/tms.png') }}" alt="Your Company">
            </div>

            <h2 class="mt-2 text-xl font-bold tracking-tight text-gray-900">
                Create your account
            </h2>

            <p class="mt-0.5 text-xs text-gray-500">
                Please fill in the details below to get started.
            </p>
        </div>

        <form x-data="{ submitted: false }"
            x-on:submit.prevent="
                submitted = true;
                if ($el.checkValidity()) {
                    $wire.register();
                } else {
                    $el.reportValidity();
                }
            "
            class="mt-2 space-y-3" novalidate>

            <!-- First Name -->
            <div>
                <x-input-label for="first_name" :value="__('First Name')"
                    class="block text-[11px] font-medium tracking-wide text-gray-500 mb-1" />
                <div class="relative">
                    <input wire:model.defer="first_name" id="first_name"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-3.5 py-2.5 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 text-xs sm:text-sm transition-all duration-200
                        @if ($submitted && $errors->has('first_name')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                        @else focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="text" name="first_name" autocomplete="given-name" placeholder="Enter First Name" />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-xs" />
                    @endif
                </div>
            </div>

            <!-- Last Name -->
            <div>
                <x-input-label for="last_name" :value="__('Last Name')"
                    class="block text-[11px] font-medium tracking-wide text-gray-500 mb-1" />
                <div class="relative">
                    <input wire:model.defer="last_name" id="last_name"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-3.5 py-2.5 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 text-xs sm:text-sm transition-all duration-200
                        @if ($submitted && $errors->has('last_name')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                        @else focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="text" name="last_name" autocomplete="family-name" placeholder="Enter Last Name" />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1 text-xs" />
                    @endif
                </div>
            </div>

            <!-- Phone Number -->
            <div>
                <x-input-label for="phone_number" :value="__('Phone Number')"
                    class="block text-[11px] font-medium tracking-wide text-gray-500 mb-1" />
                <div class="relative">
                    <input wire:model.defer="phone_number" id="phone_number"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-3.5 py-2.5 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 text-xs sm:text-sm transition-all duration-200
                        @if ($submitted && $errors->has('phone_number')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                        @else focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="text" name="phone_number" autocomplete="tel" placeholder="Enter Phone Number" />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-1 text-xs" />
                    @endif
                </div>
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email address')"
                    class="block text-[11px] font-medium tracking-wide text-gray-500 mb-1" />
                <div class="relative">
                    <input wire:model.defer="email" id="email"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-3.5 py-2.5 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 text-xs sm:text-sm transition-all duration-200
                        @if ($submitted && $errors->has('email')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                        @else focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="email" name="email" autocomplete="email" placeholder="Enter Email Address" />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    @endif
                </div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <x-input-label for="password" :value="__('Password')"
                        class="block text-[11px] font-medium tracking-wide text-gray-500" />
                </div>
                <div class="flex flex-col" x-data="{ show: false }">
                    <div class="relative">
                        <input wire:model.defer="password" id="password"
                            class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-3.5 py-2.5 pr-10 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 text-xs sm:text-sm transition-all duration-200
                            @if ($submitted && $errors->has('password')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                            @else focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                            :type="show ? 'text' : 'password'" name="password" autocomplete="new-password"
                            placeholder="••••••••" />

                        <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-[rgb(7,139,221)] focus:outline-none transition-colors"
                            @click="show = !show">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    @endif

                    <!-- Error Message with Fade Out -->
                    <div x-data="{ showError: @entangle('error') }" x-init="$watch('showError', value => {
                        if (value) {
                            setTimeout(() => showError = false, 5000);
                        }
                    })">
                        <div x-show="showError" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-1300"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            @error('credentials')
                                <div
                                    class="mt-1.5 rounded-xl bg-red-50 p-2.5 text-xs text-red-700 border border-red-100 shadow-sm">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sign Up Button -->
            <div class="pt-1">
                <x-primary-button
                    class="flex w-full justify-center items-center rounded-xl bg-gradient-to-r from-[rgb(7,139,221)] to-[rgb(7,139,221)] px-4 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-lg shadow-[rgb(7,139,221)]/25 hover:opacity-95 active:scale-[0.99] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[rgb(7,139,221)] transition-all duration-200">
                    {{ __('Sign up') }}
                </x-primary-button>
            </div>

            <!-- Sign In Link -->
            <div class="text-center text-xs pt-1">
                <span class="text-gray-500">
                    Already have an account?
                </span>
                <a href="{{ route('login') }}" wire:navigate
                    class="font-semibold text-[rgb(7,139,221)] hover:underline ml-1">
                    Sign In
                </a>
            </div>

        </form>
    </div>

    @if (session()->has('successmessage'))
        <div x-init="$dispatch('notify', { message: '{{ session('successmessage') }}', type: 'success' })"></div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({
            fail
        }) => {
            fail(({
                status,
                preventDefault
            }) => {
                if (status === 419) {
                    preventDefault();
                    window.location.reload();
                }
            });
        });
    });
</script>
