<div class="min-h-screen bg-gradient-to-br from-[#F8FAFC] via-[#F1F5F9] to-[#E2E8F0] antialiased selection:bg-blue-600 selection:text-white text-slate-700">
    <main class="scrollcontainer px-4 sm:px-6 md:px-8 pb-20 pt-20 sm:pt-24 md:ml-16 max-w-[1750px] mx-auto transition-all duration-300">

        {{-- Session Flash Messages --++ }}
        @if (session()->has('message'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Executive Header --}}
        <div class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-1">
                    <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900">
                        Category Management List
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-normal">
                        Create, edit and manage all system categories.
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
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <div class="w-full sm:max-w-md">
                    <input wire:model.defer="newCategory" type="text" placeholder="Enter category name..."
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100">
                    @error('newCategory')
                        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <button wire:click="addCategory" wire:loading.attr="disabled" type="button"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg cursor-pointer">
                    <span wire:loading.remove wire:target="addCategory">+ Add Category</span>
                    <span wire:loading wire:target="addCategory">Adding...</span>
                </button>
            </div>
        </div>

        {{-- Categories List Section --}}
        <div class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white">
            <div class="border-b border-slate-200 px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent">
                <h2 class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    Categories List (Showing All Data)
                </h2>
            </div>

            <div class="p-6 sm:p-8">
                @if (empty($this->categories) || $this->categories->isEmpty())
                    <div class="flex min-h-[280px] flex-col items-center justify-center text-center px-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800">No Categories Available</h3>
                    </div>
                @else
                    <div class="grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($this->categories as $category)
                            <div wire:key="{{ $category->id }}" x-data="{ isEditing: false, newCategoryName: @js($category->name) }"
                                class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm transition-all duration-300 hover:border-blue-400 hover:shadow-xl">

                                <div x-show="isEditing" x-cloak>
                                    <input wire:model="editCategory" x-model="newCategoryName" type="text"
                                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 outline-none">
                                    <div class="mt-3.5 grid grid-cols-2 gap-2">
                                        <button type="button"
                                            @click="$wire.updateCategory({{ $category->id }}, newCategoryName).then(() => { isEditing = false; })"
                                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white uppercase">
                                            Save
                                        </button>
                                        <button type="button" @click="isEditing = false"
                                            class="rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-semibold text-slate-700 border">
                                            Cancel
                                        </button>
                                    </div>
                                </div>

                                <div x-show="!isEditing">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <span class="mb-2.5 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-600 border border-blue-100">
                                                Category
                                            </span>
                                            <h3 class="truncate text-sm sm:text-base font-semibold text-slate-900">
                                                {{ $category->name }}
                                            </h3>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-2">
                                            <button type="button" @click="isEditing = true"
                                                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 cursor-pointer">
                                                ✏️
                                            </button>

                                            @if (method_exists($category, 'hasTasks') ? !$category->hasTasks() : true)
                                                <button type="button" wire:click="deleteCategory({{ $category->id }})"
                                                    wire:confirm="Are you sure you want to delete this category?"
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 cursor-pointer">
                                                    🗑️
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

