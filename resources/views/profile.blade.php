<x-app-layout>
    <x-slot name="header">
        <h2
            class="font-bold text-base xs:text-lg sm:text-xl lg:text-2xl 2xl:text-3xl text-gray-800 dark:text-gray-200 leading-tight tracking-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4 xs:py-6 sm:py-8 lg:py-12 transform-gpu transition-all duration-150">
        <div
            class="max-w-[2560px] mx-auto px-2.5 xs:px-4 sm:px-6 lg:px-8 2xl:px-12 3xl:px-16 space-y-3.5 xs:space-y-4 sm:space-y-6">

            <!-- Update Profile Information Card -->
            <div
                class="p-3.5 xs:p-5 sm:p-8 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-md sm:shadow-lg rounded-2xl sm:rounded-3xl border border-gray-200/80 dark:border-gray-700/80 transition-all duration-150">
                <div class="w-full max-w-full sm:max-w-xl lg:max-w-2xl xl:max-w-3xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Update Password Card -->
            <div
                class="p-3.5 xs:p-5 sm:p-8 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-md sm:shadow-lg rounded-2xl sm:rounded-3xl border border-gray-200/80 dark:border-gray-700/80 transition-all duration-150">
                <div class="w-full max-w-full sm:max-w-xl lg:max-w-2xl xl:max-w-3xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <!-- Delete User Card -->
            <div
                class="p-3.5 xs:p-5 sm:p-8 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-md sm:shadow-lg rounded-2xl sm:rounded-3xl border border-gray-200/80 dark:border-gray-700/80 transition-all duration-150">
                <div class="w-full max-w-full sm:max-w-xl lg:max-w-2xl xl:max-w-3xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
