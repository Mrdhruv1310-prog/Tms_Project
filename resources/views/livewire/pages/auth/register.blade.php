<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-15 w-auto" src="{{ asset('icons/nslogo.png') }}" alt="Your Company">

        <h2 class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Create your account
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form x-data="{ submitted: false }"
            x-on:submit.prevent="
        submitted = true;
        if ($el.checkValidity()) {
            $wire.register();
        } else {
            $el.reportValidity();
        }
    "
            class="space-y-6">
            <div>
                <label>First Name</label>
                {{-- <input wire:model="first_name" type="text" maxlength="255"
                    class="block w-full rounded-md border px-3 py-2"> --}}
                <input wire:model.defer="first_name" id="first_name"
                    class="w-full rounded-md shadow-sm focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('first_name')) border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200 @endif"
                    :type="show ? 'first_name' : 'text'" name="first_name" autocomplete="first_name" />
                @if ($submitted)
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                @endif
            </div>

            <div>
                <label>Last Name</label>
                <input wire:model.defer="last_name" id="last_name"
                    class="w-full rounded-md shadow-sm focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('last_name')) border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200 @endif"
                    :type="show ? 'phone_number' : 'text'" name="phone_number" autocomplete="phone_number" />
                @if ($submitted)
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                @endif
            </div>

            <div>
                <label>Phone Number</label>
                <input wire:model.defer="phone_number" id="phone_number"
                    class="w-full rounded-md shadow-sm focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('phone_number')) border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200 @endif"
                    :type="show ? 'phone_number' : 'text'" name="phone_number" autocomplete="phone_number" />
                @if ($submitted)
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                @endif
            </div>

            <div>
                <label>Email</label>
                <input wire:model.defer="email" id="email"
                    class="w-full rounded-md shadow-sm focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('email')) border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200 @endif"
                    :type="show ? 'email' : 'text'" name="email" autocomplete="email" />
                @if ($submitted)
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                @endif
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')"
                        class="block text-sm font-medium leading-6 text-gray-900" />
                </div>
                <div class="flex flex-col mt-1 mb-2">
                    <div class="relative flex-1 col-span-4" x-data="{ show: true }">
                        <input wire:model.defer="password" id="password"
                            class="w-full rounded-md shadow-sm focus:ring focus:ring-opacity-50
                            @if ($submitted && $errors->has('password')) border-red-500 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 focus:border-indigo-300 focus:ring-indigo-200 @endif"
                            :type="show ? 'password' : 'text'" name="password" autocomplete="current-password" />

                        <button type="button" class="flex flex-row absolute inset-y-0 right-0 items-center pr-3"
                            @click="show = !show" :class="{ 'hidden': !show, 'block': show }">
                            <!-- Heroicon name: eye -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <button type="button" class="flex flex-row absolute inset-y-0 right-0 items-center pr-3"
                            @click="show = !show" :class="{ 'block': !show, 'hidden': show }">
                            <!-- Heroicon name: eye-slash -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    @if ($submitted)
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    @endif

                    <!-- Error Message with Fade Out -->
                    <div x-data="{ showError: @entangle('error') }" x-init="$watch('showError', value => {
                        if (value) {
                            setTimeout(() => showError = false, 5000);
                        }
                    })">
                        <div x-show="showError" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-1300" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            @error('credentials')
                                <span
                                    class="text-sm text-red-600 dark:text-red-400 space-y-1 mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sign Up Button -->
            <div>
                <x-primary-button
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Sign up') }}</span>
                    <span wire:loading>{{ __('Signing up...') }}
                        <svg class="inline-block animate-spin h-5 w-5 text-gray-500" viewBox="0 0 24 24">
                            <path fill="white"
                                d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity="0.25" />
                            <path fill="white" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,
                                transform="rotate(360 12 12)" />
                        </svg>
                    </span>

                </x-primary-button>
            </div>
            <div class="text-sm text-center">
                <span class="font-medium text-gray-900">Don't have an account?</span>
                <a href="{{ route('login') }}" wire:navigate
                    class="font-semibold text-indigo-600 hover:text-indigo-500">Sign In</a>

        </form>
    </div>

       @if (session()->has('successmessage'))
                                <div x-init="$dispatch('notify', { message: '{{ session('successmessage') }}', type: 'success' })"></div>
                                @endif
            </div>
