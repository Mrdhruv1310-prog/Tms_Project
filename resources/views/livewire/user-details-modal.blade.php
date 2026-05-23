<div x-data="{
    show: @entangle('isOpen'),
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    role: '',
    status: '',
    resetForm() {
        this.first_name = '';
        this.last_name = '';
        this.email = '';
        this.phone_number = '';
        this.role = '';
        this.status = '';
    }
}" x-cloak x-show="show" class="fixed inset-0 flex items-center justify-center z-50"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

    <div x-show="show" class="relative p-4 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Add User
                </h3>
                <button type="button" @click="resetForm(); show = false;"
                    class="text-gray-400 bg-gray-200 text-gray-900 hover:bg-red-500 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg inert class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form wire:submit.prevent="saveUser">
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name<span
                                class="text-red-500">*</span></label>
                        <input type="text" id="first_name" wire:model="first_name" x-model="first_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                            @if ($submitted && $errors->has('first_name')) border-red-500 focus:ring-red-500 focus:border-red-500
                            @else
                                border-gray-300 focus:ring-primary-600 focus:border-primary-600 @endif"
                            placeholder="Enter First Name">
                        @if ($submitted)
                            @error('first_name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last
                            Name<span class="text-red-500">*</span></label>
                        <input type="text" id="last_name" wire:model="last_name" x-model="last_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                            @if ($submitted && $errors->has('last_name')) border-red-500 focus:ring-red-500 focus:border-red-500
                            @else
                                border-gray-300 focus:ring-primary-600 focus:border-primary-600 @endif"
                            placeholder="Enter Last Name">
                        @if ($submitted)
                            @error('last_name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>

                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email<span
                                class="text-red-500">*</span></label>
                        {{-- <input type="email" id="email" wire:model="email" x-model="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Enter User Email Id"> --}}
                        <input type="text" id="email" wire:model="email" x-model="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                            @if ($submitted && $errors->has('email')) border-red-500 focus:ring-red-500 focus:border-red-500
                            @else
                                border-gray-300 focus:ring-primary-600 focus:border-primary-600 @endif"
                            placeholder="Enter Email Id">
                        @if ($submitted)
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="phone_number"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                        <input type="text" id="phone_number" wire:model="phone_number" x-model="phone_number"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                            @if ($submitted && $errors->has('phone_number')) border-red-500 focus:ring-red-500 focus:border-red-500
                            @else
                                border-gray-300 focus:ring-primary-600 focus:border-primary-600 @endif"
                            placeholder="Enter Phone Number">
                        @if ($submitted)
                            @error('phone_number')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="role"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role</label>
                        <select id="role" wire:model.defer="role" x-model="role"
                            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5
                                dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500

                                @if ($submitted && $errors->has('role')) border-red-500 focus:ring-red-500 focus:border-red-500
                                @else
                                    border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500 @endif
                            ">
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        @if ($submitted && $errors->has('role'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                {{ $errors->first('role') }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <label for="status"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="status" wire:model.defer="status" x-model="status"
                            class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5
                                dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500
                                @if ($submitted && $errors->has('status')) border-red-500 focus:ring-red-500 focus:border-red-500
                                @else
                                    border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500 @endif
                            ">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @if ($submitted)
                            @error('status')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Password<span class="text-red-500">*</span>
                        </label>

                        <input type="password" id="password" wire:model="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                        dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Enter Password">
                        @if ($submitted)
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button type="submit"
                        class="relative flex w-full justify-center rounded-md bg-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Save User</span>
                        <span wire:loading class="block inset-0 flex items-center justify-center">Saving User...
                            <svg class="inline-block animate-spin h-5 w-5 text-gray-500" viewBox="0 0 24 24">
                                <path fill="white"
                                    d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                    opacity="0.25" />
                                <path fill="white"
                                    d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                                    transform="rotate(360 12 12)" />
                            </svg>
                        </span>
                    </button>
                    <button @click="resetForm(); show = false;" type="button"
                        class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                        Cancel
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    $(document).on('click', '.toggle-password', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("input[name='password']"); // Updated selector to target the password input
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
