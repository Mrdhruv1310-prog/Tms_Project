<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';
    public bool $submitted = false;

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->submitted = true;

        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');
        $this->submitted = false;

        session()->flash('status', __($status));
    }
};

?>

<div
    class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] p-4 sm:p-6 lg:p-8 overflow-hidden">

    <!-- Decorative Glow Elements for Professional Depth -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute -bottom-32 -right-32 w-96 h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
    </div>

    <!-- White Frosted Card -->
    <div
        class="relative w-full max-w-md space-y-7 rounded-3xl bg-white/80 backdrop-blur-xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.08)] sm:p-10 my-auto border border-white/80 ring-1 ring-black/5 overflow-y-auto max-h-[90vh]">

        <!-- Header -->
        <div class="flex flex-col items-center text-center">

            <div class="flex px-5 py-3 items-center justify-center rounded-2xl">
                <img class="h-11 w-auto object-contain drop-shadow-sm" src="{{ url('icons/tms.png') }}" alt="Your Company">
            </div>

            <h2 class="mt-5 text-2xl font-bold tracking-tight text-gray-900">
                Forgot your password?
            </h2>

            <p class="mt-1.5 text-sm text-gray-500">
                No problem. Enter your email address and we will send you a password reset link.
            </p>

        </div>

        <!-- Session Status -->
        <div>
            <x-auth-session-status class="mb-4 text-sm font-medium text-green-600" :status="session('status')" />
        </div>

        <!-- Form -->
        <form wire:submit="sendPasswordResetLink" class="mt-6 space-y-4" novalidate>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')"
                    class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5" />

                <div class="relative">
                    <input wire:model.defer="email" id="email"
                        class="block w-full rounded-xl border border-gray-200/80 bg-gray-50/50 px-4 py-3 text-gray-800 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 sm:text-sm transition-all duration-200
                            @if ($submitted && $errors->has('email')) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-200
                            @else focus:border-[rgb(7,139,221)] focus:bg-white focus:ring-[rgb(7,139,221)]/15 @endif"
                        type="email" name="email" autocomplete="email" placeholder="Enter Email Address"
                        autofocus />

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                    @endif
                </div>
            </div>

            <!-- Reset Button -->
            <div class="pt-3">
                <x-primary-button
                    class="flex w-full justify-center items-center rounded-xl bg-gradient-to-r from-[rgb(7,139,221)] to-[rgb(7,139,221)] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-[rgb(7,139,221)]/25 hover:opacity-95 active:scale-[0.99] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[rgb(7,139,221)] transition-all duration-200">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>

            <!-- Back to Login -->
            <div class="text-center text-sm pt-2">
                <a href="{{ route('login') }}" wire:navigate
                    class="font-semibold text-[rgb(7,139,221)] hover:opacity-80 transition-colors">
                    ← Back to Sign in
                </a>
            </div>

        </form>
    </div>
</div>
