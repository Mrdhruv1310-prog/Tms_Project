<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (
            !Auth::guard('web')->validate([
                'email' => Auth::user()->email,
                'password' => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div
    class="relative w-full max-w-[320px] xs:max-w-xs sm:max-w-sm md:max-w-md lg:max-w-md space-y-4 sm:space-y-6 rounded-2xl sm:rounded-3xl bg-white/90 backdrop-blur-md p-5 xs:p-6 sm:p-8 md:p-10 shadow-[0_15px_40px_rgba(0,0,0,0.06)] my-auto border border-white/80 ring-1 ring-black/5 mx-auto">

    <!-- Decorative Glow Elements -->
    <div
        class="absolute -top-32 -left-32 w-72 h-72 sm:w-80 sm:h-80 bg-[rgb(114,204,74)]/15 rounded-full blur-2xl pointer-events-none transform-gpu">
    </div>
    <div
        class="absolute -bottom-32 -right-32 w-72 h-72 sm:w-80 sm:h-80 bg-[rgb(7,139,221)]/15 rounded-full blur-2xl pointer-events-none transform-gpu">
    </div>

    <!-- Header / Notice -->
    <div
        class="relative z-10 text-xs sm:text-sm text-gray-600 dark:text-gray-400 leading-relaxed text-center sm:text-left">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <!-- Form -->
    <form wire:submit="confirmPassword" class="space-y-4 relative z-10" novalidate>
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')"
                class="block text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1" />

            <div class="flex flex-col" x-data="{ show: false }">
                <div class="relative">
                    <input wire:model="password" id="password"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-3.5 sm:px-4 py-2 sm:py-2.5 pr-11 text-gray-800 text-xs sm:text-sm shadow-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-2 transition-all duration-150 focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15"
                        :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                        placeholder="••••••••" />

                    <button type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[rgb(7,139,221)] focus:outline-hidden transition-colors duration-150 cursor-pointer min-h-[44px] min-w-[44px] justify-center"
                        @click="show = !show" aria-label="Toggle password visibility">
                        <svg x-cloak x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-cloak x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                class="flex w-full sm:w-auto justify-center items-center rounded-xl bg-[rgb(7,139,221)] px-5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-md shadow-[rgb(7,139,221)]/20 hover:opacity-95 active:scale-[0.99] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[rgb(7,139,221)] transition-all duration-150 transform-gpu min-h-[44px] cursor-pointer">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</div>
