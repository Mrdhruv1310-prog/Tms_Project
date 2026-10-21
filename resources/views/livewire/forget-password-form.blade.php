<div
    class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] p-2.5 xs:p-4 sm:p-6 lg:p-8 2xl:p-12 overflow-y-auto antialiased transition-all duration-200">

    <!-- Decorative Glow Elements for Professional Depth -->
    <div
        class="absolute -top-32 -left-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute -bottom-32 -right-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
    </div>

    {{-- Responsive Glassmorphism Form Card --}}
    <div
        class="relative w-full max-w-[340px] xs:max-w-[380px] sm:max-w-md lg:max-w-md xl:max-w-lg 2xl:max-w-xl 3xl:max-w-2xl space-y-4 sm:space-y-6 lg:space-y-7 rounded-2xl sm:rounded-3xl bg-white/80 backdrop-blur-xl p-4 xs:p-6 sm:p-8 lg:p-10 2xl:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.08)] my-auto border border-white/80 ring-1 ring-black/5 transition-all duration-150">

        <!-- Logo & Header -->
        <div class="flex flex-col items-center text-center">

            <div class="flex px-3 py-1.5 sm:px-5 sm:py-3 items-center justify-center rounded-xl sm:rounded-2xl">
                <img class="h-8 xs:h-9 sm:h-11 lg:h-12 2xl:h-14 w-auto object-contain drop-shadow-sm"
                    src="{{ url('icons/tms.png') }}" alt="Your Company">
            </div>

            <h2 class="mt-3 sm:mt-5 text-lg xs:text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-gray-900">
                Forgot password
            </h2>

            <p class="mt-1 sm:mt-1.5 text-xs sm:text-sm lg:text-base text-gray-500 font-normal">
                Enter your email address to reset your password.
            </p>

        </div>

        <form wire:submit.prevent="forgotPassword" class="mt-4 sm:mt-6 space-y-3.5 sm:space-y-5" novalidate>

            <!-- Email Address -->
            <div>

                <x-input-label for="email" :value="__('Email address')"
                    class="block text-[10px] sm:text-xs lg:text-sm font-semibold uppercase tracking-wider text-gray-600 mb-1 sm:mb-1.5" />

                <div class="relative">

                    <input wire:model.defer="email" id="email"
                        class="block w-full rounded-lg sm:rounded-xl border border-gray-200/80 bg-gray-50/50 px-3 py-2.5 sm:px-4 sm:py-3 text-xs sm:text-sm lg:text-base text-gray-800 shadow-xs placeholder:text-gray-400 focus:outline-none focus:ring-2 transition-all duration-150
                            @if ($submitted && $errors->has('email')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                            @else
                                focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="email" name="email" autocomplete="email" placeholder="name@example.com" />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('email')" class="mt-1 sm:mt-1.5 text-[10px] sm:text-xs" />
                    @endif

                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-1 sm:pt-3">

                <x-primary-button
                    class="flex w-full justify-center items-center rounded-lg sm:rounded-xl bg-gradient-to-r from-[rgb(7,139,221)] to-[rgb(7,139,221)] px-4 py-2.5 sm:py-3.5 lg:py-4 text-xs sm:text-sm lg:text-base font-semibold text-white shadow-lg shadow-[rgb(7,139,221)]/25 hover:opacity-95 active:scale-[0.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[rgb(7,139,221)] transition-all duration-150 cursor-pointer">
                    {{ __('Forgot Password') }}
                </x-primary-button>

            </div>

        </form>

        <!-- Back to Login Link -->
        <div class="text-center text-xs sm:text-sm lg:text-base pt-1 sm:pt-3">
            <a href="{{ route('login') }}" wire:navigate
                class="inline-flex items-center font-medium text-[rgb(7,139,221)] hover:text-[rgb(7,139,221)]/80 transition-colors active:scale-95">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
