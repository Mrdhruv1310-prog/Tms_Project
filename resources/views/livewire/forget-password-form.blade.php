<div
    class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] p-4 sm:p-6 lg:p-8 overflow-hidden">

    <!-- Decorative Glow Elements for Professional Depth -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute -bottom-32 -right-32 w-96 h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
    </div>

    <div
        class="relative w-full max-w-md space-y-7 rounded-3xl bg-white/80 backdrop-blur-xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.08)] sm:p-10 my-auto border border-white/80 ring-1 ring-black/5">

        <!-- Logo & Header -->
        <div class="flex flex-col items-center text-center">

            <div class="flex px-5 py-3 items-center justify-center rounded-2xl">
                <img class="h-11 w-auto object-contain drop-shadow-sm" src="{{ url('icons/tms.png') }}" alt="Your Company">
            </div>

            <h2 class="mt-5 text-2xl font-bold tracking-tight text-gray-900">
                Forgot password
            </h2>

            <p class="mt-1.5 text-sm text-gray-500">
                Enter your email address to reset your password.
            </p>

        </div>

        <form wire:submit.prevent="forgotPassword" class="mt-6 space-y-4" novalidate>

            <!-- Email Address -->
            <div>

                <x-input-label for="email" :value="__('Email address')"
                    class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5" />

                <div class="relative">

                    <input wire:model.defer="email" id="email"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-4 py-3 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 sm:text-sm transition-all duration-200
                            @if ($submitted && $errors->has('email')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                            @else
                                focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="email" name="email" autocomplete="email" placeholder="name@example.com" />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                    @endif

                </div>
            </div>

            <!-- Submit Button (Loader Removed) -->
            <div class="pt-3">

                <x-primary-button
                    class="flex w-full justify-center items-center rounded-xl bg-gradient-to-r from-[rgb(7,139,221)] to-[rgb(7,139,221)] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-[rgb(7,139,221)]/25 hover:opacity-95 active:scale-[0.99] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[rgb(7,139,221)] transition-all duration-200">
                    {{ __('Forgot Password') }}
                </x-primary-button>

            </div>

        </form>

        <!-- Back to Login Link -->
        <div class="text-center text-sm pt-3">
            <a href="{{ route('login') }}" wire:navigate
                class="inline-flex items-center font-medium text-[rgb(7,139,221)] hover:text-[rgb(7,139,221)]/80 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Login
            </a>
        </div>

    </div>

    @if (session()->has('errormessage'))
        <div x-init="$dispatch('notify', { message: '{{ session('errormessage') }}', type: 'error' })"></div>
    @endif
    @if (session()->has('successmessage'))
        <div x-init="$dispatch('notify', { message: '{{ session('successmessage') }}', type: 'success' })"></div>
    @endif
</div>
