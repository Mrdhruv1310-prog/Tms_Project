<div class="antialiased bg-gray-50 dark:bg-gray-900">
    <main class="p-4 md:ml-16 h-auto pt-20 pb-16">
        <div class="relative flex items-center mb-6">
            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="window.history.back()">
                <svg class="w-5 h-5 transform rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                </svg>
                <span class="sr-only">Go Back</span>
            </button>

            <h3 class="absolute left-1/2 -translate-x-1/2 text-2xl font-semibold text-center">
                Category Management
            </h3>
        </div>
        
        <!-- Input and Add Button -->
        <div class="relative mb-6 flex flex-col items-center sm:flex-row sm:justify-center space-x-4">
            <!-- Input field -->
            <div class="relative mb-4 sm:mb-0">
                <input wire:model="newCategory" type="text" id="floating_outlined"
                    class="block px-2.5 pb-2.5 pt-4 text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="floating_outlined"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">New Category</label>
                @error('newCategory')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Add Category Button -->
            <button wire:click="addCategory" wire:loading.attr="disabled" type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-0 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <span wire:loading.remove wire:target="addCategory">Add Category</span>
                <span wire:loading wire:target="addCategory">Adding...</span>
            </button>
        </div>
        
        <!-- Category List -->
        <div class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 p-4">
            @if ($this->categories->isEmpty())
                <!-- No Categories -->
                <div class="flex items-center justify-center h-96">
                    <h1 class="text-5xl font-extrabold"><mark class="px-2 text-white bg-blue-600 rounded dark:bg-blue-500">No Categories Available</mark></h1>
                </div>
            @else
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach ($this->categories as $category)
                        <div wire:key="{{ $category->id }}" class="inline-flex items-center" x-data="{ isEditing: false, newCategoryName: '{{ $category->name }}' }">
                            <div x-show="isEditing" class="flex flex-col items-center">
                                <!-- Edit Mode -->
                                <input wire:model="editCategory" x-model="newCategoryName" type="text" class="px-2 py-1 border rounded">
                                <!-- Update Button with Loading State -->
                                <div>
                                <button
                                    type="button"
                                    @click="$wire.updateCategory({{ $category->id }}, newCategoryName).then(() => { isEditing = false; })"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 mt-2 me-1 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                    wire:loading.attr="disabled"
                                    wire:target="updateCategory({{ $category->id }})"
                                >
                                    <!-- Loading Spinner -->
                                    <span wire:loading class="inset-0 flex items-center justify-center">Saving...
                                        <svg class="inline-block animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                                opacity="0.25" />
                                            <path fill="currentColor"
                                                d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                                                transform="rotate(360 12 12)" />
                                        </svg>
                                    </span>
                                    
                                    <!-- Check Icon -->
                                    <span wire:loading.remove>Save</span>
                                </button>

                                <button @click="isEditing = false" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2 mt-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Cancel</button>
                            </div>
                            </div>
                            <div x-show="!isEditing">
                                <!-- Display Mode -->
                                <span
                                    class="flex flex-row items-center bg-blue-100 text-blue-800 text-base font-medium me-2 px-5 py-1 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                                    {{ $category->name }}

                                    <!-- Edit Icon -->
                                    <button @click="isEditing = true" class="ml-4 text-blue-800 dark:text-blue-400" title="Edit">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.1213 2.70705C19.9497 1.53548 18.0503 1.53547 16.8787 2.70705L15.1989 4.38685L7.29289 12.2928C7.16473 12.421 7.07382 12.5816 7.02986 12.7574L6.02986 16.7574C5.94466 17.0982 6.04451 17.4587 6.29289 17.707C6.54127 17.9554 6.90176 18.0553 7.24254 17.9701L11.2425 16.9701C11.4184 16.9261 11.5789 16.8352 11.7071 16.707L19.5556 8.85857L21.2929 7.12126C22.4645 5.94969 22.4645 4.05019 21.2929 2.87862L21.1213 2.70705ZM18.2929 4.12126C18.6834 3.73074 19.3166 3.73074 19.7071 4.12126L19.8787 4.29283C20.2692 4.68336 20.2692 5.31653 19.8787 5.70705L18.8622 6.72357L17.3068 5.10738L18.2929 4.12126ZM15.8923 6.52185L17.4477 8.13804L10.4888 15.097L8.37437 15.6256L8.90296 13.5112L15.8923 6.52185ZM4 7.99994C4 7.44766 4.44772 6.99994 5 6.99994H10C10.5523 6.99994 11 6.55223 11 5.99994C11 5.44766 10.5523 4.99994 10 4.99994H5C3.34315 4.99994 2 6.34309 2 7.99994V18.9999C2 20.6568 3.34315 21.9999 5 21.9999H16C17.6569 21.9999 19 20.6568 19 18.9999V13.9999C19 13.4477 18.5523 12.9999 18 12.9999C17.4477 12.9999 17 13.4477 17 13.9999V18.9999C17 19.5522 16.5523 19.9999 16 19.9999H5C4.44772 19.9999 4 19.5522 4 18.9999V7.99994Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                    <!-- Delete Icon -->
                                    @if(!$category->hasTasks())
                                        <button wire:click="deleteCategory({{ $category->id }})" title="Delete"
                                            wire:loading.attr="disabled"
                                            class="ml-2 text-red-800 dark:text-red-400 relative">
                                            <!-- Trash icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" wire:target="deleteCategory({{ $category->id }})" wire:loading.remove class="h-5 w-5" fill="red" viewBox="0 0 56 56">
                                                <path d="M 44.5235 48.6602 L 46.1407 14.3945 L 48.4844 14.3945 C 49.4454 14.3945 50.2187 13.5976 50.2187 12.6367 C 50.2187 11.6758 49.4454 10.8555 48.4844 10.8555 L 38.2422 10.8555 L 38.2422 7.3398 C 38.2422 3.9883 35.9688 1.8086 32.3595 1.8086 L 23.5938 1.8086 C 19.9844 1.8086 17.7344 3.9883 17.7344 7.3398 L 17.7344 10.8555 L 7.5391 10.8555 C 6.6016 10.8555 5.7813 11.6758 5.7813 12.6367 C 5.7813 13.5976 6.6016 14.3945 7.5391 14.3945 L 9.8829 14.3945 L 11.5000 48.6836 C 11.6641 52.0586 13.8907 54.1914 17.2657 54.1914 L 38.7579 54.1914 C 42.1095 54.1914 44.3595 52.0351 44.5235 48.6602 Z M 21.4844 7.5742 C 21.4844 6.2383 22.4688 5.3008 23.8751 5.3008 L 32.1016 5.3008 C 33.5313 5.3008 34.5157 6.2383 34.5157 7.5742 L 34.5157 10.8555 L 21.4844 10.8555 Z M 17.6173 50.6758 C 16.2579 50.6758 15.2500 49.6445 15.1797 48.2852 L 13.5391 14.3945 L 42.3907 14.3945 L 40.8438 48.2852 C 40.7735 49.6680 39.7891 50.6758 38.4063 50.6758 Z M 34.9610 46.5508 C 35.7344 46.5508 36.3204 45.9180 36.3438 45.0273 L 37.0469 20.2773 C 37.0704 19.3867 36.4610 18.7305 35.6641 18.7305 C 34.9376 18.7305 34.3282 19.4102 34.3048 20.2539 L 33.6016 45.0273 C 33.5782 45.8711 34.1641 46.5508 34.9610 46.5508 Z M 21.0626 46.5508 C 21.8595 46.5508 22.4454 45.8711 22.4219 45.0273 L 21.7188 20.2539 C 21.6954 19.4102 21.0626 18.7305 20.3360 18.7305 C 19.5391 18.7305 18.9532 19.3867 18.9766 20.2773 L 19.7032 45.0273 C 19.7266 45.9180 20.2891 46.5508 21.0626 46.5508 Z M 29.4298 45.0273 L 29.4298 20.2539 C 29.4298 19.4102 28.7969 18.7305 28.0235 18.7305 C 27.2500 18.7305 26.5938 19.4102 26.5938 20.2539 L 26.5938 45.0273 C 26.5938 45.8711 27.2500 46.5508 28.0235 46.5508 C 28.7735 46.5508 29.4298 45.8711 29.4298 45.0273 Z"/>
                                            </svg>

                                            <!-- Custom Spinner -->
                                            <div wire:loading wire:target="deleteCategory({{ $category->id }})">
                                                <svg class="inline-block animate-spin h-5 w-5 text-gray-500"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentcolor"
                                                        d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                                        opacity="0.25" />
                                                    <path fill="red"
                                                        d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                                                        transform="rotate(360 12 12)" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif
        </div>
    </main>
</div>
