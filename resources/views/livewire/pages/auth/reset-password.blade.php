<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('livewire.pages.auth.guest1')] class extends Component {
    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $submitted = false;

    /**
     * Reset Password
     */
    public function resetPassword(): void
    {
        $this->submitted = true;

        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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

        Session::flash('successmessage', 'Password changed successfully.');

        $this->redirectRoute('login', navigate: true);
    }
};

?>

<!-- ONLY ONE ROOT ELEMENT -->
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-15 w-auto" src="{{ asset('icons/nslogo.png') }}" alt="Company">

        <h2 class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Reset Your Password
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

        <form wire:submit="resetPassword" class="space-y-6">

            <!-- EMAIL -->
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                    Email
                </label>

                <div class="mt-2">
                    <input
                        wire:model.defer="email"
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        class="block w-full rounded-md shadow-sm py-3 px-4 text-base focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('email'))
                                border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200
                            @endif"
                    />
                </div>

                @if ($submitted)
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                @endif
            </div>

            <!-- PASSWORD -->
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                    New Password
                </label>

                <div class="relative mt-2">
                    <input
                        wire:model.defer="password"
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        autocomplete="new-password"
                        class="block w-full rounded-md shadow-sm py-3 px-4 pr-10 text-base focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('password'))
                                border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200
                            @endif"
                    />

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500"
                    >
                        👁
                    </button>
                </div>

                @if ($submitted)
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                @endif
            </div>

            <!-- CONFIRM PASSWORD -->
            <div x-data="{ showConfirmPassword: false }">
                <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                    Confirm Password
                </label>

                <div class="relative mt-2">
                    <input
                        wire:model.defer="password_confirmation"
                        id="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="block w-full rounded-md shadow-sm py-3 px-4 pr-10 text-base focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('password_confirmation'))
                                border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200
                            @endif"
                    />

                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500"
                    >
                        👁
                    </button>
                </div>

                @if ($submitted)
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                @endif
            </div>

            <!-- BUTTON -->
            <div>
                <x-primary-button class="w-full justify-center" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="resetPassword">
                        Reset Password
                    </span>

                    <span wire:loading wire:target="resetPassword">
                        Processing...
                    </span>
                </x-primary-button>
            </div>

        </form>
    </div>
    
</div>
