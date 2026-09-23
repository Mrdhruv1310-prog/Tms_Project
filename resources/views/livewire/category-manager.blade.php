<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">
    <main class="scrollcontainer md:ml-16 px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16">

        <div class="mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/80 backdrop-blur-md border border-gray-200/80 text-sm font-semibold text-gray-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-sm transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        {{-- Executive Header with Icon --}}
        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                    </div>
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
        </div>

        {{-- Add New Category Section --}}
        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8">
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
        <div
            class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white">
            <div
                class="border-b border-slate-200 px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent">
                <h2 class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    Categories List
                </h2>
            </div>

            <div class="p-6 sm:p-8">
                @if (empty($this->categories) || $this->categories->isEmpty())
                    <div
                        class="flex min-h-[280px] flex-col items-center justify-center text-center px-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800">No Categories Available</h3>
                    </div>
                @else
                    <div class="grid gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($this->categories as $category)
                            <div wire:key="{{ $category->id }}" x-data="{ isEditing: false, categoryName: @js($category->name) }"
                                class="group relative rounded-3xl border border-slate-200/80 bg-gradient-to-b from-white via-white to-slate-50/50 p-6 shadow-md shadow-slate-100 transition-all duration-300 hover:border-blue-300 hover:shadow-xl hover:-translate-y-1">

                                {{-- Decorative gradient blur highlight effect on card top --}}
                                <div
                                    class="absolute top-0 left-6 right-6 h-[2px] bg-gradient-to-r from-transparent via-blue-500/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>

                                {{-- Edit View --}}
                                <div x-show="isEditing" x-cloak class="space-y-4">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Edit
                                            Name</label>
                                        <input x-model="categoryName" type="text"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 shadow-sm">
                                    </div>
                                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                                        <button type="button"
                                            @click="$wire.updateCategory({{ $category->id }}, categoryName).then(() => { isEditing = false; })"
                                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-xs font-bold text-white uppercase tracking-wider shadow-md shadow-blue-500/20 hover:opacity-95 transition">
                                            Save
                                        </button>
                                        <button type="button"
                                            @click="isEditing = false; categoryName = @js($category->name)"
                                            class="rounded-xl bg-white px-4 py-2 text-xs font-semibold text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                                            Cancel
                                        </button>
                                    </div>
                                </div>

                                {{-- Display View --}}
                                <div x-show="!isEditing" class="flex flex-col justify-between h-full space-y-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="space-y-2.5 min-w-0">
                                            <div
                                                class="inline-flex items-center gap-1.5 rounded-full bg-blue-50/80 px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest text-blue-600 border border-blue-100/60 shadow-xs">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                Category
                                            </div>
                                            <h3 class="truncate text-base font-bold text-slate-900 tracking-tight"
                                                title="{{ $category->name }}">
                                                {{ $category->name }}
                                            </h3>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-1.5">
                                            {{-- Edit Button --}}
                                            <button type="button" @click="isEditing = true"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-600 border border-slate-200/80 shadow-2xs cursor-pointer hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all"
                                                title="Edit Category">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                                    class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                            </button>

                                            @if (method_exists($category, 'hasTasks') ? !$category->hasTasks() : true)
                                                {{-- Delete Button --}}
                                                <button type="button" wire:click="deleteCategory({{ $category->id }})"
                                                    wire:confirm="Are you sure you want to delete this category?"
                                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-600 border border-slate-200/80 shadow-2xs cursor-pointer hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all"
                                                    title="Delete Category">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                                        class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
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
