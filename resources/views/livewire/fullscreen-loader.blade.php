<!-- resources/views/livewire/fullscreen-loader.blade.php -->
<div wire:init="load">
    <div class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-900 bg-opacity-50 z-50" wire:loading>
        <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A8.004 8.004 0 0112 4.472v3.45a4 4 0 10-4 0v0zM12 20a8 8 0 008-8h-4c0 3.922-3.138 7.107-7 7.45v3.45z"></path>
        </svg>
    </div>
</div>
