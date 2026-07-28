<div class="min-h-screen bg-gradient-to-br from-[#F8FAFC] via-[#F1F5F9] to-[#E2E8F0] antialiased selection:bg-blue-600 selection:text-white text-slate-700">
    <main class="scrollcontainer px-4 sm:px-6 md:px-8 pb-20 pt-20 sm:pt-24 md:ml-16 max-w-[1750px] mx-auto transition-all duration-300">

        {{-- Executive Header / Background --}}
        <div class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="absolute -top-16 -left-16 w-72 sm:w-80 h-72 sm:h-80 bg-gradient-to-br from-blue-500/10 to-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-400 overflow-x-auto py-1">
                        <button type="button" onclick="window.history.back()" class="hover:text-blue-600 transition cursor-pointer flex items-center gap-1 shrink-0 bg-transparent border-none p-0 text-slate-400 font-bold">
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <svg class="h-3 w-3 text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        <span class="text-slate-600 font-bold shrink-0">Category Management</span>
                    </div>

                    <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900">
                        Category Management
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-500 font-normal">
                        Create, edit and manage categories.
                    </p>
                </div>
            </div>
        </div>

        {{-- Add New Category Section --}}
        <div class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8">
            <div class="mb-5 sm:mb-6 border-b border-slate-200 pb-4">
                <h2 class="text-[10px] sm:text-[15px] font-bold uppercase tracking-widest text-slate-900">
                    Add New Category
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-normal">
                    Create categories for organizing tasks.
                </p>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <div class="w-full sm:max-w-md">
                    <input wire:model.defer="newCategory" type="text" placeholder="Enter category name..."
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 shadow-inner outline-none transition focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100">

                    @error('newCategory')
                        <p class="mt-1.5 text-xs font-medium text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button wire:click="addCategory" wire:loading.attr="disabled" type="button"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-500/25 transition-all hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl active:scale-95 disabled:opacity-60 cursor-pointer">
                    <span wire:loading.remove wire:target="addCategory">
                        + Add Category
                    </span>
                    <span wire:loading wire:target="addCategory">
                        Adding...
                    </span>
                </button>
            </div>
        </div>

        {{-- Categories List Section --}}
        <div class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white">
            <div class="border-b border-slate-200 px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <h2 class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-400">
                        Categories List
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 font-normal">
                    Manage all categories from here.
                </p>
            </div>

            <div class="p-6 sm:p-8">
                @if ($this->categories->isEmpty())
                    <div class="flex min-h-[280px] flex-col items-center justify-center text-center px-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                        <div class="mb-4 rounded-2xl bg-blue-100/60 p-5 shadow-inner">
                            <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h16.5v15h-16.5z" />
                            </svg>
                        </div>

                        <h3 class="text-sm sm:text-base font-semibold text-slate-800">
                            No Categories Available
                        </h3>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 font-normal">
                            Start by creating your first category.
                        </p>
                    </div>
                @else
                    <div class="grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($this->categories as $category)
                            <div wire:key="{{ $category->id }}" x-data="{ isEditing: false, newCategoryName: @js($category->name) }"
                                class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm transition-all duration-300 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1">

                                <div x-show="isEditing" x-cloak>
                                    <input wire:model="editCategory" x-model="newCategoryName" type="text"
                                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100">

                                    <div class="mt-3.5 grid grid-cols-2 gap-2">
                                        <button type="button"
                                            @click="$wire.updateCategory({{ $category->id }}, newCategoryName).then(() => { isEditing = false; })"
                                            wire:loading.attr="disabled"
                                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-semibold uppercase tracking-wider text-white shadow-md shadow-blue-500/20 transition hover:bg-blue-700 disabled:opacity-60 cursor-pointer">
                                            <span wire:loading.remove>Save</span>
                                            <span wire:loading>Saving...</span>
                                        </button>

                                        <button type="button" @click="isEditing = false"
                                            class="rounded-xl bg-slate-100 px-4 py-2.5 text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-700 border border-slate-200 transition hover:bg-slate-200 cursor-pointer">
                                            Cancel
                                        </button>
                                    </div>
                                </div>

                                <div x-show="!isEditing">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <span
                                                class="mb-2.5 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-blue-600 border border-blue-100 shadow-2xs">
                                                Category
                                            </span>

                                            <h3 class="truncate text-sm sm:text-base font-semibold text-slate-900">
                                                {{ $category->name }}
                                            </h3>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-2">
                                            {{-- Edit Button --}}
                                            <button type="button" @click="isEditing = true"
                                                class="group relative flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center overflow-hidden rounded-2xl
                                                bg-blue-50/80 border border-blue-200 text-blue-600 shadow-sm transition-all duration-300
                                                hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-105 active:scale-95 cursor-pointer" title="Edit Category">

                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor"
                                                    class="relative z-10 h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:rotate-12">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487a2.625 2.625 0 113.712 3.712L7.5 21H3v-4.5L16.862 4.487z" />
                                                </svg>
                                            </button>

                                            @if (!$category->hasTasks())
                                                {{-- Delete Button --}}
                                                <button type="button" wire:click="deleteCategory({{ $category->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="group relative flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center overflow-hidden rounded-2xl
                                                    bg-rose-50/80 border border-rose-200 text-rose-600 shadow-sm transition-all duration-300
                                                    hover:bg-rose-600 hover:text-white hover:border-rose-600 hover:scale-105 active:scale-95 disabled:opacity-60 cursor-pointer" title="Delete Category">

                                                    <svg wire:loading.remove wire:target="deleteCategory({{ $category->id }})"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        class="relative z-10 h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-300 group-hover:rotate-12">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6m5-10v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7h10z" />
                                                    </svg>

                                                    <svg wire:loading wire:target="deleteCategory({{ $category->id }})"
                                                        class="relative z-10 h-4 w-4 sm:h-5 sm:w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
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
        </div>
    </main>
</div>
