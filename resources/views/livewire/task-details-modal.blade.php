<div>
<div x-data="{
    show: @entangle('isOpen'),
    title: '',
    description: '',
    category_id: '',
    priority: '',
    labels: '',
    due_date: @entangle('due_date'),
    recurrence_end_date: '',
    selectedDays: [],
    showReminderModal: false,
    errorMessage: '',
    reminderUnit: '',
    isReminderEnabled: false,
    enableRepeatTask: false,
    recurrence: '',
    selectedUsers: @entangle('selectedUsers'),
    allUsers: @js($users),
    selectedUserNames: [],
    isEditMode: @js($isEditMode),
    label_id_alpine: @entangle('label_id'),
    groupUserMap: @js($groupUserMap),
    userManuallyChanged: false,
    updateUserSelection(event, id) {
        this.userManuallyChanged = true; // Mark as manual change
        if (event.target.checked) {
            if (!this.selectedUsers.includes(id)) {
                this.selectedUsers.push(id);
            }
        } else {
            this.selectedUsers = this.selectedUsers.filter(uid => uid !== id);
        }
    },

    syncSelectedUserNames() {
        this.selectedUserNames = this.allUsers
            .filter(user => this.selectedUsers.includes(user.id))
            .map(user => user.first_name);
    },

    syncGroupUsersOnEdit() {
        if (this.isEditMode && this.label_id_alpine && this.groupUserMap[this.label_id_alpine]) {
            // Only update if selectedUsers is empty or doesn't match
            const groupUserIds = this.groupUserMap[this.label_id_alpine];
            if (this.selectedUsers.length === 0) {
                this.selectedUsers = [...groupUserIds];
                this.syncSelectedUserNames();
            }
        }
    },

    init() {
        this.syncSelectedUserNames();

        // Watch selectedUsers to reset group if users don't match
        this.$watch('selectedUsers', (newSelected) => {
            this.syncSelectedUserNames();
            console.log('Selected Users:', newSelected);
            console.log('Group ID:', this.label_id_alpine);

            if (!this.label_id_alpine || !this.userManuallyChanged) return;
            const groupUserIds = this.groupUserMap[this.label_id_alpine] || [];
            const sortedSelected = [...newSelected].sort();
            const sortedGroup = [...groupUserIds].sort();

            const isSame =
                sortedSelected.length === sortedGroup.length &&
                sortedSelected.every((val, i) => val === sortedGroup[i]);

            if (!isSame) {
                this.label_id_alpine = '';
            }
        });

        // Watch label_id_alpine
        this.$watch('label_id_alpine', (newGroupId) => {
            if (newGroupId && this.groupUserMap[newGroupId]) {
                const groupUserIds = this.groupUserMap[newGroupId];
                this.selectedUsers = [...groupUserIds];
                this.syncSelectedUserNames();
            }
        });

        // Initialize edit mode
        if (this.isEditMode) {
            this.syncGroupUsersOnEdit();
        }

        this.$watch('show', value => {
            if (value) {
                document.body.classList.add('overflow-hidden');
                this.initDatepicker();
            } else {
                document.body.classList.remove('overflow-hidden');
                this.destroyDatepicker();
                this.resetForm();
            }
        });

        if (this.show) {
            this.$watch('due_date', value => {
                this.isReminderEnabled = value ? true : false;
            });
        }
    },

    initDatepicker() {
        window.initializeDateTimepicker('#duedatecalendar', '.scrollcontainer', this.due_date);
        window.initializeDatepicker('#repeatenddatecalendar', '.scrollcontainer', this.recurrence_end_date);
    },

    destroyDatepicker() {
        window.destroyDatepicker();
    },

    resetForm() {
        this.title = '';
        this.description = '';
        this.category_id = '';
        this.priority = '';
        this.labels = '';
        this.recurrence = '';
        this.due_date = '';
        this.recurrence_end_date = '';
        this.selectedDays = [];
        this.isReminderEnabled = false;
        this.selectedUserNames = [];
        this.userManuallyChanged = false;
    },
}" x-cloak x-show="show" class="fixed inset-0 flex items-center justify-center z-50"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

    <div x-show="show" class="relative p-4 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5 modal-scrollbar"
            style="max-height: 90vh; overflow-x: hidden;">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Assign New Task
                </h3>
                <button type="button" @click=" show = false;"
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
            {{-- <form  @submit.prevent="submitForm"> --}}
            {{-- <form wire:submit.prevent="saveTask"> --}}
            <form wire:submit.prevent="{{ $taskId ? 'updateTask' : 'saveTask' }}" novalidate>
                <div x-data="{
                    due_date: '',
                    reminderTime: '',
                    errorMessage: '',
                    reminderUnit: '',

                    updateReminderTime(event) {
                        const updatedField = event.target;
                        const fieldId = updatedField.id;

                        // Update respective fields based on the field's ID
                        if (fieldId === 'reminder_time') {
                            this.reminderTime = Number(updatedField.value); // Convert to number
                        } else if (fieldId === 'reminder_unit') {
                            this.reminderUnit = updatedField.value;
                        } else if (fieldId === 'duedatecalendar') {
                            this.due_ate = updatedField.value;
                        }

                        // Check if all required fields are filled
                        if (!this.due_date || !this.reminderTime || !this.reminderUnit) {
                            (!this.due_date) ? this.errorMessage = 'Please select due date first': this.errorMessage = '';
                            return;
                        }

                        // Parse the due date
                        const [day, month, yearWithTime] = this.due_date.split('/');
                        const [year, time] = yearWithTime.split(' ');
                        const [hours, minutes] = time.split(':');
                        const dueDateObj = new Date(year, month - 1, day, hours, minutes);

                        // Check if due date is valid
                        if (isNaN(dueDateObj.getTime())) {
                            this.errorMessage = 'Invalid due date';
                            return;
                        }

                        // Format the due date as 'YYYY-MM-DD HH:mm:00'
                        const formattedDueDate = `${dueDateObj.getFullYear()}-${String(dueDateObj.getMonth() + 1).padStart(2, '0')}-${String(dueDateObj.getDate()).padStart(2, '0')} ${String(dueDateObj.getHours()).padStart(2, '0')}:${String(dueDateObj.getMinutes()).padStart(2, '0')}:00`;

                        // Calculate reminder duration in milliseconds
                        const unitMultipliers = {
                            minutes: 60 * 1000,
                            hours: 60 * 60 * 1000,
                            days: 24 * 60 * 60 * 1000,
                        };
                        const reminderInMilliseconds = this.reminderTime * unitMultipliers[this.reminderUnit];

                        const currentDate = Date.now();
                        const dueDateTime = dueDateObj.getTime(); // Use the parsed dueDateObj directly

                        // Calculate valid reminder time
                        const validReminderTime = dueDateTime - currentDate;

                        // Validate reminder time
                        if (reminderInMilliseconds <= validReminderTime) {
                            this.errorMessage = '';
                        } else {
                            this.reminderTime = '';
                            this.reminderUnit = '';
                            this.errorMessage = 'Reminder time is NOT valid.';
                        }
                    }
                }" class="flex flex-col space-y-4 mb-4">
                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Task
                            Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" wire:model="title" x-model="title" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('title') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                            placeholder="Enter Task Title" />
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description <span class="text-red-500">*</span></label>
                        <textarea id="description" wire:model="description" x-model="description" rows="4" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('description') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                            placeholder="Enter Task Description"></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex space-x-4">
                        <div class="w-1/2">
                            <label for="category_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category <span class="text-red-500">*</span></label>
                            <select id="category_id" wire:model="category_id" x-model="category_id" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('category_id') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                <option value="">Select Category</option>
                                @if ($categories && is_iterable($categories))
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Users Dropdown with Checkboxes -->
                        <div class="w-1/2">
                            <label for="users"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Users <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <button id="dropdownUsersButton" data-dropdown-toggle="dropdownUsers"
                                    class="flex flex-row justify-between items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('selectedUsers') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                                    type="button">
                                    <span
                                        x-text="selectedUserNames.length ? selectedUserNames.join(', ') : 'Select Users'"></span>
                                    <svg class="w-2.5 h-2.5 ms-3" inert xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>

                                <!-- Dropdown content -->
                                <div id="dropdownUsers"
                                    class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
                                    <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200">
                                        {{-- {{$users}} --}}
                                        @foreach ($users as $user)
                                            <li wire:key="{{ $user->id }}"
                                                class="flex items-center py-2 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input id="checkbox-user-{{ $user->id }}" type="checkbox"
                                                    wire:model="selectedUsers" value="{{ $user->id }}"
                                                    @change="updateUserSelection($event, {{ $user->id }})"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <div class="w-7 h-7 ml-3 flex items-center justify-center text-white rounded-full me-0.5"
                                                    style="background-color: #1d4ed8;">
                                                    {{ Str::upper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                </div>
                                                <label for="checkbox-user-{{ $user->id }}"
                                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @error('selectedUsers')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <div class="w-1/2">
                            <label for="priority"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priority <span
                                    class="text-red-500">*</span></label>
                            <select id="priority" wire:model="priority" x-model="priority" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('priority') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            @error('priority')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="w-1/2">
                            <label for="label_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Group<small
                                    class="text-red-500">(If selected, the user will be auto-selected)</small></label>
                            <select id="label_id" wire:model="label_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('label_id') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                <option value="">Select Group</option>
                                @if ($labels && is_iterable($labels))
                                    @foreach ($labels as $label)
                                        <option value="{{ $label->id }}">{{ $label->label }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('label_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-data="{ enableRepeatTask: @entangle('enableRepeatTask'), recurrence: @entangle('recurrence'), recurrence_end_date: @entangle('recurrence_end_date') }">
                        <!-- Label with Checkbox and Dropdown in one row -->
                        <div
                            class="flex items-center flex-wrap space-x-4 bg-gray-100 border-solid border-transparent rounded-md p-2 mt-2">
                            <!-- Label and Checkbox -->
                            <label for="enableRepeatTask"
                                class="flex items-center text-sm font-medium text-gray-900 dark:text-white">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" inert
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 576 512">
                                    <path
                                        d="M272 416c17.7 0 32-14.3 32-32s-14.3-32-32-32l-112 0c-17.7 0-32-14.3-32-32l0-128 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 128c0 53 43 96 96 96l112 0zM304 96c-17.7 0-32 14.3-32 32s14.3 32 32 32l112 0c17.7 0 32 14.3 32 32l0 128-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-128c0-53-43-96-96-96L304 96z" />
                                </svg>
                                <span class="ml-2">Repeat</span>
                                <input type="checkbox" id="enableRepeatTask" x-model="enableRepeatTask"
                                    wire:model="enableRepeatTask"
                                    @change="if(!enableRepeatTask) { recurrence = 'none'; recurrence_end_date = null }"
                                    class="h-5 w-5 ml-2 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-500 dark:focus:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            </label>

                            <!-- Dropdown for Recurrence (Visible only if checkbox is checked) -->
                            <div x-show="enableRepeatTask" class="transition-opacity duration-300 ease-in-out">
                                <label for="recurrence" class="sr-only">Frequency</label>
                                <select id="recurrence" wire:model="recurrence" x-model="recurrence" required
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('recurrence') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                    <option value="none">Frequency</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            <div x-show="enableRepeatTask" class="transition-opacity duration-300 ease-in-out">
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" inert
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input id="repeatenddatecalendar" name="recurrence_end_date"
                                        wire:model="recurrence_end_date" x-model="recurrence_end_date" type="text"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('recurrence_end_date') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                                        placeholder="Recurrence End Date" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Conditionally Render Days for Weekly Recurrence -->
                        <div x-show="enableRepeatTask && recurrence === 'weekly'" x-effect="console.log(selectedDays)"
                            class="bg-gray-100 border-solid border-transparent rounded-md p-2 mt-2 ">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Weekly on these
                                days</label>
                            <div class="grid grid-rows-* grid-flow-col gap-4">
                                <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']"
                                    :key="day">
                                    <div class="flex flex-col items-center">
                                        <input type="checkbox" :value="day" wire:model="selectedDays"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded  focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <label class="text-sm font-medium text-gray-900 dark:text-gray-300"
                                            x-text="day"></label>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <!-- Error message if recurrence is invalid -->
                        @error('recurrence')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ isReminderEnabled: @entangle('isReminderEnabled') }">
                        <label for="duedate"
                            class="flex flex-row items-center block mb-2 text-sm font-medium text-gray-900 dark:text-white">Due
                            Date & Time <span class="text-red-500">*</span>
                            <button data-tooltip-target="duedatepin" data-tooltip-trigger="click"
                                class="tooltip-button ml-1 text-gray-400 hover:text-gray-900 dark:text-gray-500 dark:hover:text-white">
                                <svg class="h-5 w-5" inert xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="duedatepin" role="tooltip"
                                class="tooltip tooltip-content invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                The default time will be the start of the day at midnight.
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" inert
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </div>
                            <input id="duedatecalendar" name="due_date" wire:model="due_date" x-model="due_date"
                                x-on:change="updateReminderTime($event)" type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('due_date') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                                placeholder="Select date & time" readonly>
                        </div>

                        @error('due_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="inline-flex bg-gray-100 border-solid border-transparent rounded-3xl p-3">
                            <!-- Reminder Icon Button -->
                            <button @click="showReminderModal = true" type="button"
                                class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                                    <path
                                        d="M13 12v-5h-1.999v6.999h5.999v-1.999h-4zm-12.197-4.285c-1.261-1.944-1.035-4.569.675-6.266 1.7-1.687 4.305-1.896 6.235-.645-3.171 1.219-5.692 3.741-6.91 6.911zm18.428 11.18c1.715-1.794 2.771-4.219 2.771-6.896 0-5.522-4.477-10-10.002-10-5.522 0-10 4.477-10 10 0 2.678 1.059 5.104 2.772 6.898l-1.736 4.506c-.159.394.288.759.643.522l3.581-3.122c1.412.761 3.026 1.195 4.742 1.195 1.717 0 3.334-.434 4.744-1.195l3.582 3.122c.355.237.803-.128.643-.522l-1.74-4.508zm-7.23 1.103c-4.412 0-8.001-3.588-8.001-8s3.589-8 8.001-8c4.412 0 8.002 3.588 8.002 8s-3.59 8-8.002 8zm10.553-18.52c-1.697-1.71-4.324-1.937-6.268-.675 3.17 1.218 5.693 3.739 6.912 6.91 1.25-1.931 1.041-4.535-.644-6.235z" />
                                </svg>
                            </button>
                        </span>
                    </div>

                    <!-- Reminder Modal -->
                    <div x-show="showReminderModal" class="fixed inset-0 flex items-center justify-center z-50"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800 p-6">
                            <!-- Modal header -->
                            <div
                                class="flex justify-between items-center pb-4 mb-4 rounded-t border-b dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Task Reminder Before Due Date
                                </h3>
                                <button type="button" @click="showReminderModal = false"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg inert class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Modal body for reminder details -->
                            <div x-data="{
                                reminderTime: @entangle('reminderTime').defer,
                                reminderUnit: @entangle('reminderUnit').defer,
                                dueDate: @entangle('dueDate').defer,
                                isReminderEnabled: false,
                                checkReminder() {
                                    // Enable reminder field if due date exists or reminder is being edited
                                    this.isReminderEnabled = this.dueDate !== null && this.dueDate !== '' || this.reminderTime !== null;
                                }
                            }" x-init="checkReminder" class="flex flex-row space-x-4">

                                <input type="number" id="reminder_time" name="reminder_time"
                                    x-bind:disabled="!isReminderEnabled" wire:model="reminderTime"
                                    x-model="reminderTime"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Enter time" x-on:input="updateReminderTime($event)">

                                <select id="reminder_unit" wire:model="reminderUnit"
                                    x-bind:disabled="!isReminderEnabled" x-model="reminderUnit"
                                    x-on:change="updateReminderTime($event)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option value="">Select Option</option>
                                    <option value="minutes">Minutes</option>
                                    <option value="hours">Hours</option>
                                    <option value="days">Days</option>
                                </select>
                            </div>

                            <p><span x-text="errorMessage" style="color: red;"></span></p>

                            <!-- Modal footer -->
                            <div class="flex justify-center pt-4">
                                <button type="button" @click="showReminderModal = false"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg ml-2">Save Reminder
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="submit"
                            class="relative flex w-full justify-center rounded-md bg-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Save Task</span>
                            <span wire:loading class="block inset-0 flex items-center justify-center">Saving Task...
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
                        <button type="button" @click="resetForm(); show = false;"
                            class="relative flex w-full justify-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-primary-800">
                            Cancel
                        </button>
                    </div>

            </form>

        </div>
    </div>
</div>

<style>
    .modal-scrollbar::-webkit-scrollbar-track {
        background: transparent;
        /* Background of the scrollbar track */
        margin: 5px;
    }

    .modal-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(rgb(28 93 239), rgb(59, 130, 246), rgb(87 146 255));
        border-radius: 4px;
        /* Rounded corners */
    }

    .modal-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(rgb(26 86 219), rgb(59, 130, 246), rgb(95 149 250));
    }
</style>

<script>
    function submitForm() {

        // Perform form submission logic here
        console.log('Form submitted:', {
            selectedDays: this.selectedUsers
        });
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            init() {
                this.$watch('show', (value) => {
                    if (value) {
                        document.body.classList.add(
                            'overflow-hidden'); // Disable body scroll
                    } else {
                        document.body.classList.remove(
                            'overflow-hidden'); // Enable body scroll
                    }
                });
            }
        }));
    });
</script>

</div>
