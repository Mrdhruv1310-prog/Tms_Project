<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">
    <main class="scrollcontainer md:ml-16 px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pt-16 sm:pt-20 pb-12 sm:pb-16 max-w-full">

        <!-- Back to Dashboard Link -->
        <div class="mb-4 sm:mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-white/90 backdrop-blur-md border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-sm transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        {{-- Executive Header --}}
        <div
            class="mb-5 sm:mb-8 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300/80 bg-white/95 backdrop-blur-xl shadow-lg shadow-slate-200/50 p-4 sm:p-6 lg:p-8 relative">
            <div class="flex items-center gap-3 sm:gap-4 relative z-10">
                <div
                    class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                </div>
                <div class="space-y-0.5 sm:space-y-1 min-w-0">
                    <h1 class="text-base sm:text-xl font-bold tracking-tight text-slate-900 truncate">
                        Category Management List
                    </h1>
                    <p class="text-[11px] sm:text-sm text-slate-500 font-normal truncate">
                        Create, edit and manage categories.
                    </p>
                </div>
            </div>
        </div>

        {{-- Add New Category Section --}}
        @if ($this->canManageCategories())
            <div
                class="mb-5 sm:mb-8 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300/80 bg-white/95 backdrop-blur-xl shadow-lg shadow-slate-200/50 p-4 sm:p-6 lg:p-8">
                <div class="mb-3 sm:mb-5 border-b border-slate-200 pb-3">
                    <h2 class="text-xs sm:text-[14px] font-bold uppercase tracking-wider text-slate-900">
                        Add New Category
                    </h2>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="w-full sm:max-w-md">
                        <input wire:model.defer="newCategory" type="text" placeholder="Enter category name..."
                            class="w-full rounded-xl sm:rounded-2xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 sm:px-4 sm:py-3 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('newCategory')
                            <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button wire:click="addCategory" wire:loading.attr="disabled" type="button"
                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 sm:px-6 sm:py-3 text-xs font-bold uppercase tracking-wider text-white shadow-md cursor-pointer hover:opacity-95 transition-all active:scale-98">
                        <span wire:loading.remove wire:target="addCategory">+ Add Category</span>
                        <span wire:loading wire:target="addCategory">Adding...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Categories List Section --}}
        <div
            class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300/80 bg-white/95 backdrop-blur-xl shadow-lg shadow-slate-200/50">
            <div
                class="border-b border-slate-200 px-4 sm:px-8 py-3.5 sm:py-5 bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent">
                <h2 class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-500">
                    Categories List
                </h2>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">
                @if (empty($this->categories) || $this->categories->isEmpty())
                    {{-- Empty State --}}
                    <div
                        class="flex min-h-[220px] sm:min-h-[280px] flex-col items-center justify-center text-center p-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50">
                        <div
                            class="mb-2.5 flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800">No Categories Available</h3>
                        <p class="mt-1 text-[11px] sm:text-xs text-slate-500 max-w-xs">
                            {{ $this->canManageCategories() ? 'Start by adding a new category above.' : 'You do not have any assigned categories to display.' }}
                        </p>
                    </div>
                @else
                    {{-- Responsive Grid: 1 column on 360px screen --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-5">
                        @foreach ($this->categories as $category)
                            <div wire:key="{{ $category->id }}"
                                class="group relative rounded-2xl border border-slate-200/90 bg-gradient-to-b from-white via-white to-slate-50/50 p-4 sm:p-5 shadow-xs transition-all duration-200 hover:border-blue-300 hover:shadow-md">

                                @if ($editCategoryId === $category->id)
                                    {{-- Edit View --}}
                                    <div class="space-y-3">
                                        <div class="space-y-1">
                                            <label
                                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Edit
                                                Name</label>
                                            <input wire:model="editCategory" type="text"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-medium text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 shadow-2xs">
                                            @error('editCategory')
                                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-1">
                                            <button type="button" wire:click="updateCategory"
                                                class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-2 text-xs font-bold text-white uppercase tracking-wider shadow-xs hover:opacity-95 transition">
                                                Save
                                            </button>
                                            <button type="button" wire:click="$set('editCategoryId', null)"
                                                class="rounded-xl bg-white px-3 py-2 text-xs font-semibold text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    {{-- Display View --}}
                                    <div class="flex flex-col justify-between h-full gap-3">
                                        <div class="flex items-center justify-between gap-2.5">
                                            <div class="space-y-1.5 min-w-0 flex-1">
                                                <div
                                                    class="inline-flex items-center gap-1 rounded-full bg-blue-50/80 px-2.5 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-600 border border-blue-100 shadow-2xs">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    Category
                                                </div>
                                                <h3 class="truncate text-sm sm:text-base font-bold text-slate-900 tracking-tight"
                                                    title="{{ $category->name }}">
                                                    {{ $category->name }}
                                                </h3>
                                            </div>

                                            {{-- Authorization Check for Edit & Delete --}}
                                            @if (auth()->user()->role === 'admin' || $category->created_by === auth()->id())
                                                <div class="flex shrink-0 items-center gap-1">
                                                    {{-- Edit Button --}}
                                                    <button type="button"
                                                        wire:click="editCategorySetup({{ $category->id }})"
                                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-600 border border-slate-200/80 cursor-pointer hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all"
                                                        title="Edit Category">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                                            class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                        </svg>
                                                    </button>

                                                    @if (method_exists($category, 'hasTasks') ? !$category->hasTasks() : true)
                                                        {{-- Delete Button --}}
                                                        <button type="button"
                                                            wire:click="deleteCategory({{ $category->id }})"
                                                            wire:confirm="Are you sure you want to delete this category?"
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-600 border border-slate-200/80 cursor-pointer hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all"
                                                            title="Delete Category">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.8"
                                                                stroke="currentColor" class="w-3.5 h-3.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
