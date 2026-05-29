<div class="min-h-screen bg-gray-50 dark:bg-gray-950">
    <main class="px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16 md:ml-16">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.history.back()"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-800">
                    <svg class="h-5 w-5 rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </button>

                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                        Category Management
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        Create, edit and manage categories.
                    </p>
                </div>
            </div>
        </div>

        <div
            class="mb-6 rounded-2xl bg-white p-4 sm:p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="mb-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                    Add New Category
                </h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Create categories for organizing tasks.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                <div class="w-full sm:max-w-md">
                    <input wire:model.defer="newCategory" type="text" placeholder="Enter category name..."
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:ring-blue-900/40">

                    @error('newCategory')
                        <p class="mt-1 text-xs font-medium text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button wire:click="addCategory" wire:loading.attr="disabled" type="button"
                    class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-60">
                    <span wire:loading.remove wire:target="addCategory">
                        + Add Category
                    </span>
                    <span wire:loading wire:target="addCategory">
                        Adding...
                    </span>
                </button>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
            <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                        Categories List
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        Manage all categories from here.
                    </p>
                </div>
            </div>

            @if ($this->categories->isEmpty())
                <div class="flex min-h-[280px] flex-col items-center justify-center text-center px-4">
                    <div class="mb-4 rounded-full bg-blue-100 p-5 dark:bg-blue-900/30">
                        <svg class="h-10 w-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h16.5v15h-16.5z" />
                        </svg>
                    </div>

                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white">
                        No Categories Available
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Start by creating your first category.
                    </p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($this->categories as $category)
                        <div wire:key="{{ $category->id }}" x-data="{ isEditing: false, newCategoryName: @js($category->name) }"
                            class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">

                            <div x-show="isEditing" x-cloak>
                                <input wire:model="editCategory" x-model="newCategoryName" type="text"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <button type="button"
                                        @click="$wire.updateCategory({{ $category->id }}, newCategoryName).then(() => { isEditing = false; })"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60">
                                        <span wire:loading.remove>Save</span>
                                        <span wire:loading>Saving...</span>
                                    </button>

                                    <button type="button" @click="isEditing = false"
                                        class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                        Cancel
                                    </button>
                                </div>
                            </div>

                            <div x-show="!isEditing">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <span
                                            class="mb-2 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                            Category
                                        </span>

                                        <h3 class="truncate text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $category->name }}
                                        </h3>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2">
                                        <button type="button" @click="isEditing = true"
                                            class="group relative flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center overflow-hidden rounded-2xl
    bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md transition-all duration-300
    hover:scale-110 hover:shadow-xl active:scale-95">

                                            <span
                                                class="absolute inset-0 bg-white/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                            </span>

                                            <!-- Edit Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor"
                                                class="relative z-10 h-5 w-5 transition-transform duration-300 group-hover:rotate-12">

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487a2.625 2.625 0 113.712 3.712L7.5 21H3v-4.5L16.862 4.487z" />
                                            </svg>
                                        </button>
                                        @if (!$category->hasTasks())
                                            <button type="button" wire:click="deleteCategory({{ $category->id }})"
                                                wire:loading.attr="disabled"
                                                class="group relative flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center overflow-hidden rounded-2xl
        bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-md transition-all duration-300
        hover:scale-110 hover:shadow-xl active:scale-95 disabled:opacity-60">

                                                <!-- Glow Effect -->
                                                <span
                                                    class="absolute inset-0 bg-white/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                                </span>

                                                <!-- Attractive Delete Icon -->
                                                <svg wire:loading.remove
                                                    wire:target="deleteCategory({{ $category->id }})"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="relative z-10 h-5 w-5 transition-transform duration-300 group-hover:rotate-12">

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6m5-10v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7h10z" />
                                                </svg>

                                                <!-- Loader -->
                                                <svg wire:loading wire:target="deleteCategory({{ $category->id }})"
                                                    class="relative z-10 h-5 w-5 animate-spin" viewBox="0 0 24 24">

                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4">
                                                    </circle>

                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v8H4z">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</div>
