<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Livewire\Volt\Component;

new #[Layout('livewire.pages.auth.guest1')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Reset Password
     */
    public function resetPassword(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $user = User::where('email', $this->email)->first();
        if (!$user) {

            $this->addError('email', 'User not found.');

            return;
        }
        $user->forceFill([

            'password' => Hash::make($this->password),
            'remember_token' => Str::random(60),
        ])->save();

        Session::flash(
            'successmessage',
            'Password changed successfully.'
        );

        $this->redirectRoute('login', navigate: true);
    }
};

?>
<!-- ONLY ONE ROOT ELEMENT -->
<div>

    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">

        <div class="sm:mx-auto sm:w-full sm:max-w-sm">

            <img class="mx-auto h-15 w-auto"
                 src="{{ asset('icons/nslogo.png') }}"
                 alt="Company">

            <h2 class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Reset Your Password
            </h2>

        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

            <form wire:submit="resetPassword"
                  class="space-y-6">

                <!-- EMAIL -->
                <div>

                    <x-input-label
                        for="email"
                        :value="__('Email Address')"
                    />

                    <x-text-input
                        wire:model="email"
                        id="email"
                        type="email"
                        class="block mt-2 w-full rounded-md border-gray-300 shadow-sm py-3 px-4 text-base focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2"
                    />

                </div>

                <!-- PASSWORD -->
                <div x-data="{ show:false }">

                    <x-input-label
                        for="password"
                        :value="__('New Password')"
                    />

                    <div class="relative mt-2">

                        <input
                            wire:model="password"
                            :type="show ? 'text' : 'password'"
                            id="password"
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                        >

                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3">

                            👁

                        </button>

                    </div>

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2"
                    />

                </div>

                <!-- CONFIRM PASSWORD -->
                <div>

                    <x-input-label
                        for="password_confirmation"
                        :value="__('Confirm Password')"
                    />

                    <x-text-input
                        wire:model="password_confirmation"
                        id="password_confirmation"
                        type="password"
                        class="block mt-2 w-full rounded-md border-gray-300 shadow-sm py-3 px-4 text-base focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <x-input-error
                        :messages="$errors->get('password_confirmation')"
                        class="mt-2"
                    />

                </div>

                <!-- BUTTON -->
                <div>

                    <x-primary-button
                        class="w-full justify-center">

                        <span wire:loading.remove>
                            Reset Password
                        </span>

                        <span wire:loading>
                            Processing...
                        </span>

                    </x-primary-button>

                </div>

            </form>

        </div>

    </div>

</div>
