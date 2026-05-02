<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-10 w-auto" src="{{ asset('icons/tms.png') }}" alt="Your Company">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Forgot password</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

        <form wire:submit="forgotPassword" class="space-y-6">
            <!-- email -->
            <div x-data="{ email: '', emailError: false }">
                <div class="relative flex flex-col mt-1 mb-2">
                    <x-input-label for="email" :value="__('Email address')"
                        class="block text-sm font-medium leading-6 text-gray-900" />

                    <div class="mt-2">
                        <x-text-input x-model="email" wire:model="email"
                            x-on:input="emailError = !/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/.test(email)"
                            id="email"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            type="email" name="email" required autofocus autocomplete="off" />
                        <template x-if="emailError">
                            <span class="text-red-600 text-sm mt-2">Please enter a valid email address.</span>
                        </template>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Button -->
            <div>
                <x-primary-button
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Forgot Password') }}</span>
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
    <!-- Back to Login Link -->
    <div class="mt-4 flex flex-row justify-center">
        <a href="{{ route('login') }}" wire:navigate
            class="inline-flex items-center text-gray-600 hover:text-gray-900 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Login
        </a>
    </div>
    @if (session()->has('errormessage'))
        <div x-init="$dispatch('notify', { message: '{{ session('errormessage') }}', type: 'error' })"></div>
    @endif
</div>
