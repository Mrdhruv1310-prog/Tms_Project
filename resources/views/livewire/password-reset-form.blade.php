<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-10 w-auto" src="{{ asset('icons/tms.png') }}" alt="Your Company">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Reset your password</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        @if (session()->has('passwordresetmessage'))
            <div x-init="$dispatch('notify', { message: 'Operation successful!', type: 'success' })"></div>
        @endif

        <form wire:submit="resetPassword" class="space-y-6" x-data="{ show: true, password: '', passwordconfirmation: '' }">
            <!-- New Password -->
            <div class="relative flex flex-col mt-1 mb-2">
                <x-input-label for="password" :value="__('New Password')"
                    class="block text-sm font-medium leading-6 text-gray-900" />
                <div class="mt-2 relative">
                    <input wire:model="password" x-model="password" id="password"
                        class="w-full rounded-md border-gray-300 shadow-sm pr-12 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        :type="show ? 'password' : 'text'" name="password" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <!-- Password Confirmation Field -->
            <div class="relative flex flex-col mt-1 mb-2">
                <x-input-label for="passwordconfirmation" :value="__('Confirm Password')"
                    class="block text-sm font-medium leading-6 text-gray-900" />
                <div class="mt-2 relative">
                    <input wire:model="passwordconfirmation" x-model="passwordconfirmation" id="passwordconfirmation"
                        class="w-full rounded-md border-gray-300 shadow-sm pr-12 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        :type="show ? 'password' : 'text'" name="passwordconfirmation" required
                        autocomplete="new-password" />
                    <!-- Show/Hide Password Button -->
                    <button type="button" x-cloak class="absolute inset-y-0 right-0 flex items-center pr-3"
                        @click="show = !show" :class="{ 'hidden': !show, 'block': show }">
                        <!-- Heroicon name: eye -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <button type="button" x-cloak class="absolute inset-y-0 right-0 flex items-center pr-3"
                        @click="show = !show" :class="{ 'block': !show, 'hidden': show }">
                        <!-- Heroicon name: eye-slash -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('passwordconfirmation')" class="mt-2" />

            </div>

            <div class="flex justify-start mt-3 ml-4 p-1">
                <ul>
                    <li class="flex items-center py-1">
                        <div :class="{
                            'bg-green-200 text-green-700': password == passwordconfirmation && password.length > 0,
                            'bg-red-200 text-red-700': password != passwordconfirmation || password.length == 0
                        }"
                            class="rounded-full p-1 fill-current">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password == passwordconfirmation && password.length > 0"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                                <path x-show="password != passwordconfirmation || password.length == 0"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span
                            :class="{
                                'text-green-700': password == passwordconfirmation && password.length > 0,
                                'text-red-700': password != passwordconfirmation || password.length == 0
                            }"
                            class="font-medium text-sm ml-3"
                            x-text="password == passwordconfirmation && password.length > 0 ? 'Passwords match' : 'Passwords do not match' ">
                        </span>
                    </li>

                    <li class="flex items-center py-1">
                        <div :class="{
                            'bg-green-200 text-green-700': password.length >= 8,
                            'bg-red-200 text-red-700': password.length < 8
                        }"
                            class="rounded-full p-1 fill-current">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="password.length >= 8" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                <path x-show="password.length < 8" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span
                            :class="{
                                'text-green-700': password.length >= 8,
                                'text-red-700': password.length < 8
                            }"
                            class="font-medium text-sm ml-3"
                            x-text="password.length >= 8 ? 'The minimum length is reached' : 'At least 8 characters required' ">
                        </span>
                    </li>

                    <li class="flex items-center py-1">
                        <div :class="{
                            'bg-green-200 text-green-700': /[A-Z]/.test(password),
                            'bg-red-200 text-red-700': !/[A-Z]/.test(password)
                        }"
                            class="rounded-full p-1 fill-current">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="/[A-Z]/.test(password)" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                <path x-show="!/[A-Z]/.test(password)" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span
                            :class="{
                                'text-green-700': /[A-Z]/.test(password),
                                'text-red-700': !/[A-Z]/.test(password)
                            }"
                            class="font-medium text-sm ml-3"
                            x-text="/[A-Z]/.test(password) ? 'Contains an uppercase letter' : 'At least 1 uppercase letter required' ">
                        </span>
                    </li>

                    <li class="flex items-center py-1">
                        <div :class="{
                            'bg-green-200 text-green-700': /[a-z]/.test(password),
                            'bg-red-200 text-red-700': !/[a-z]/.test(password)
                        }"
                            class="rounded-full p-1 fill-current">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="/[a-z]/.test(password)" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                <path x-show="!/[a-z]/.test(password)" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span
                            :class="{
                                'text-green-700': /[a-z]/.test(password),
                                'text-red-700': !/[a-z]/.test(password)
                            }"
                            class="font-medium text-sm ml-3"
                            x-text="/[a-z]/.test(password) ? 'Contains a lowercase letter' : 'At least 1 lowercase letter required' ">
                        </span>
                    </li>

                    <li class="flex items-center py-1">
                        <div :class="{
                            'bg-green-200 text-green-700': /[!@#$%^&*()_+\-=|\\:;,.<>\/?~]/.test(password),
                            'bg-red-200 text-red-700': !/[!@#$%^&*()_+\-=|\\:;,.<>\/?~]/.test(password)
                        }"
                            class="rounded-full p-1 fill-current">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <!-- Path shown if the password contains at least one special symbol -->
                                <path x-show="new RegExp('[!@#$%^&*()_+\\-=|\\\\:;,.<>/\\?~]').test(password)"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"/>
                                <!-- Path shown if the password does not contain any special symbol -->
                                <path x-show="!new RegExp('[!@#$%^&*()_+\\-=|\\\\:;,.<>/\\?~]').test(password)"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <span
                            :class="{
                                'text-green-700': /[!@#$%^&*()_+\-=|\\:;,.<>\/?~]/.test(password),
                                'text-red-700': !/[!@#$%^&*()_+\-=|\\:;,.<>\/?~]/.test(password)
                            }"
                            class="font-medium text-sm ml-3"
                            x-text="/[!@#$%^&*()_+\-=|\\:;,.<>\/?~]/.test(password) ? 'Contains a special symbol' : 'At least 1 special symbol required' ">
                        </span>
                    </li>

                </ul>
            </div>
            <!-- Reset Password Button -->
            <div>
                <x-primary-button
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Reset Password') }}</span>
                    <span wire:loading>{{ __('Processing...') }}
                        <svg class="inline-block animate-spin h-5 w-5 text-gray-500" viewBox="0 0 24 24">
                            <path fill="white"
                                d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity="0.25" />
                            <path fill="white"
                                d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                                transform="rotate(360 12 12)" />
                        </svg>
                    </span>
                </x-primary-button>
            </div>

        </form>
    </div>

</div>
