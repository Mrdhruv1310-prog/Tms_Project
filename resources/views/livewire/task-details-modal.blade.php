<div>
<div x-data="{
    show: @entangle('isOpen').live,
    title: @entangle('title').blur,
    description: @entangle('description').blur,
    category_id: @entangle('category_id').live,
    priority: @entangle('priority').live,
    due_date: @entangle('due_date').live,
    recurrence_end_date: @entangle('recurrence_end_date').live,
    selectedDays: @entangle('selectedDays').live,
    showReminderModal: false,
    openDropdown: false,
    errorMessage: '',
    reminderUnit: @entangle('reminderUnit').live,
    reminderTime: @entangle('reminderTime').live,
    isReminderEnabled: @entangle('isReminderEnabled').live,
    enableRepeatTask: @entangle('enableRepeatTask').live,
    recurrence: @entangle('recurrence').live,
    selectedUsers: @entangle('selectedUsers').live,
    allUsers: @js($users),
    selectedUserNames: [],
    isEditMode: @entangle('isEditMode').live,
    label_id_alpine: @entangle('label_id').live,
    groupUserMap: @js($groupUserMap),
    userManuallyChanged: false,

    dueDatePickerInstance: null,
    repeatDatePickerInstance: null,

    updateUserSelection(event, id) {
        this.userManuallyChanged = true;
        id = Number(id);
        if (event.target.checked) {
            if (!this.selectedUsers.includes(id)) {
                this.selectedUsers.push(id);
            }
        } else {
            this.selectedUsers = this.selectedUsers.filter(uid => Number(uid) !== id);
        }
    },

    syncSelectedUserNames() {
        if (!Array.isArray(this.selectedUsers)) return;
        const selectedIds = this.selectedUsers.map(id => Number(id));
        this.selectedUserNames = this.allUsers
            .filter(user => selectedIds.includes(Number(user.id)))
            .map(user => user.first_name);
    },

    syncGroupUsersOnEdit() {
        if (this.isEditMode && this.label_id_alpine && this.groupUserMap[this.label_id_alpine]) {
            const groupUserIds = this.groupUserMap[this.label_id_alpine];
            if (!this.selectedUsers || this.selectedUsers.length === 0) {
                this.selectedUsers = [...groupUserIds];
            }
        }
    },

    init() {
        this.syncSelectedUserNames();

        this.$watch('selectedUsers', (newSelected) => {
            this.syncSelectedUserNames();
            if (!this.label_id_alpine || !this.userManuallyChanged) return;
            const groupUserIds = (this.groupUserMap[this.label_id_alpine] || []).map(Number);
            const sortedSelected = [...(newSelected || [])].map(Number).sort();
            const sortedGroup = [...groupUserIds].sort();

            const isSame =
                sortedSelected.length === sortedGroup.length &&
                sortedSelected.every((val, i) => val === sortedGroup[i]);

            if (!isSame) {
                this.label_id_alpine = '';
            }
        });

        this.$watch('label_id_alpine', (newGroupId) => {
            if (newGroupId && this.groupUserMap[newGroupId]) {
                const groupUserIds = this.groupUserMap[newGroupId];
                this.selectedUsers = [...groupUserIds];
                this.syncSelectedUserNames();
            }
        });

        this.$watch('show', value => {
            if (value) {
                document.body.classList.remove('overflow-hidden');
                document.body.classList.add('overflow-hidden');
                this.syncSelectedUserNames();
                if (this.isEditMode) {
                    this.syncGroupUsersOnEdit();
                }
            } else {
                document.body.classList.remove('overflow-hidden');
                this.destroyDatepicker();
                this.openDropdown = false;
            }
        });

        this.$watch('due_date', value => {
            if (value) {
                this.isReminderEnabled = true;
            }
            if (this.dueDatePickerInstance && value !== this.dueDatePickerInstance.input.value) {
                this.dueDatePickerInstance.setDate(value, false);
            }
        });

        this.$watch('recurrence_end_date', value => {
            if (this.repeatDatePickerInstance && value !== this.repeatDatePickerInstance.input.value) {
                this.repeatDatePickerInstance.setDate(value, false);
            }
        });

        window.addEventListener('task-edit-form-filled', () => {
            this.syncSelectedUserNames();
        });
    },

    initDuePicker(el) {
        if (!el || typeof flatpickr === 'undefined') return;
        if (this.dueDatePickerInstance) this.dueDatePickerInstance.destroy();
        this.dueDatePickerInstance = flatpickr(el, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            defaultDate: this.due_date || null,
            onChange: (selectedDates, dateStr) => {
                this.due_date = dateStr;
            }
        });
    },

    initRepeatPicker(el) {
        if (!el || typeof flatpickr === 'undefined') return;
        if (this.repeatDatePickerInstance) this.repeatDatePickerInstance.destroy();
        this.repeatDatePickerInstance = flatpickr(el, {
            enableTime: false,
            dateFormat: 'Y-m-d',
            defaultDate: this.recurrence_end_date || null,
            onChange: (selectedDates, dateStr) => {
                this.recurrence_end_date = dateStr;
            }
        });
    },

    destroyDatepicker() {
        if (this.dueDatePickerInstance) this.dueDatePickerInstance.destroy();
        if (this.repeatDatePickerInstance) this.repeatDatePickerInstance.destroy();
    },

    resetForm() {
        this.title = '';
        this.description = '';
        this.category_id = '';
        this.priority = 'low';
        this.label_id_alpine = '';
        this.recurrence = 'none';
        this.due_date = '';
        this.recurrence_end_date = '';
        this.selectedDays = [];
        this.isReminderEnabled = false;
        this.enableRepeatTask = false;
        this.selectedUsers = [];
        this.selectedUserNames = [];
        this.userManuallyChanged = false;
        this.reminderTime = '';
        this.reminderUnit = '';
        this.openDropdown = false;
    }
}" x-cloak x-show="show" class="fixed inset-0 flex items-center justify-center z-50"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

    <div x-show="show" class="relative p-4 w-full max-w-2xl h-full md:h-auto">
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5 modal-scrollbar" style="max-height: 90vh; overflow-x: hidden;">
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="isEditMode ? 'Edit Task' : 'Assign New Task'"></h3>
                <button type="button" @click="$wire.close()" class="text-gray-400 bg-gray-200 hover:bg-red-500 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="{{ $taskId ? 'updateTask' : 'saveTask' }}" novalidate>
                <div class="flex flex-col space-y-4 mb-4">
                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Task Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" wire:model.blur="title" x-model="title" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('title') border-red-500 bg-red-50 @enderror"
                            placeholder="Enter Task Title" />
                        @error('title') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description <span class="text-red-500">*</span></label>
                        <textarea id="description" wire:model.blur="description" x-model="description" rows="4" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 bg-red-50 @enderror"
                            placeholder="Enter Task Description"></textarea>
                        @error('description') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex space-x-4">
                        <div class="w-1/2">
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category <span class="text-red-500">*</span></label>
                            <select id="category_id" wire:model.live="category_id" x-model="category_id" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('category_id') border-red-500 bg-red-50 @enderror">
                                <option value="">Select Category</option>
                                @if ($categories && is_iterable($categories))
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Dropdown Fixed Section -->
                        <div class="w-1/2">
                            <label for="users" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Users <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="openDropdown = false">
                                <button id="dropdownUsersButton" @click="openDropdown = !openDropdown"
                                    class="flex flex-row justify-between items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('selectedUsers') border-red-500 bg-red-50 @enderror"
                                    type="button">
                                    <span class="truncate" x-text="selectedUserNames.length ? selectedUserNames.join(', ') : 'Select Users'"></span>
                                    <svg class="w-2.5 h-2.5 ms-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>

                                <div id="dropdownUsers" x-show="openDropdown" x-transition
                                    class="absolute left-0 top-full mt-1 z-20 bg-white rounded-lg shadow w-full dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    <ul class="h-48 px-3 py-2 overflow-y-auto text-sm text-gray-700 dark:text-gray-200">
                                        @foreach ($users as $user)
                                            <li wire:key="user-item-{{ $user->id }}" class="flex items-center py-2 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input id="checkbox-user-{{ $user->id }}" type="checkbox"
                                                    wire:model.live="selectedUsers" value="{{ $user->id }}"
                                                    :checked="selectedUsers.map(Number).includes({{ $user->id }})"
                                                    @change="updateUserSelection($event, {{ $user->id }})"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <div class="w-7 h-7 ml-3 flex items-center justify-center text-white rounded-full me-0.5" style="background-color: #1d4ed8;">
                                                    {{ Str::upper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                </div>
                                                <label for="checkbox-user-{{ $user->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                    {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @error('selectedUsers') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="w-1/2">
                            <label for="priority" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priority <span class="text-red-500">*</span></label>
                            <select id="priority" wire:model.live="priority" x-model="priority" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('priority') border-red-500 bg-red-50 @enderror">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            @error('priority') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="w-1/2">
                            <label for="label_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Group <small class="text-red-500">(If selected, the user will be auto-selected)</small></label>
                            <select id="label_id" wire:model.live="label_id" x-model="label_id_alpine"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('label_id') border-red-500 bg-red-50 @enderror">
                                <option value="">Select Group</option>
                                @if ($labels && is_iterable($labels))
                                    @foreach ($labels as $label)
                                        <option value="{{ $label->id }}">{{ $label->label }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('label_id') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Repeat Section -->
                    <div>
                        <div class="flex items-center flex-wrap space-x-4 bg-gray-100 border-solid border-transparent rounded-md p-2 mt-2">
                            <label for="enableRepeatTask" class="flex items-center text-sm font-medium text-gray-900 dark:text-white">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 576 512">
                                    <path d="M272 416c17.7 0 32-14.3 32-32s-14.3-32-32-32l-112 0c-17.7 0-32-14.3-32-32l0-128 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 128c0 53 43 96 96 96l112 0zM304 96c-17.7 0-32 14.3-32 32s14.3 32 32 32l112 0c17.7 0 32 14.3 32 32l0 128-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-128c0-53-43-96-96-96L304 96z" />
                                </svg>
                                <span class="ml-2">Repeat</span>
                                <input type="checkbox" id="enableRepeatTask" x-model="enableRepeatTask" wire:model.live="enableRepeatTask"
                                    @change="if(!enableRepeatTask) { recurrence = 'none'; recurrence_end_date = '' }"
                                    class="h-5 w-5 ml-2 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-500 dark:border-gray-600">
                            </label>

                            <div x-show="enableRepeatTask" class="transition-opacity duration-300 ease-in-out">
                                <select id="recurrence" wire:model.live="recurrence" x-model="recurrence"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="none">Frequency</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>

                            <div x-show="enableRepeatTask" class="transition-opacity duration-300 ease-in-out" wire:ignore>
                                <div class="relative cursor-pointer">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0 2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input id="repeatenddatecalendar"
                                           x-init="initRepeatPicker($el)"
                                           name="recurrence_end_date"
                                           wire:model.live="recurrence_end_date"
                                           x-model="recurrence_end_date"
                                           type="text"
                                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer"
                                           placeholder="Recurrence End Date">
                                </div>
                            </div>
                        </div>

                        <div x-show="enableRepeatTask && recurrence === 'weekly'" class="bg-gray-100 border-solid border-transparent rounded-md p-2 mt-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Weekly on these days</label>
                            <div class="grid grid-rows-* grid-flow-col gap-4">
                                <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']" :key="day">
                                    <div class="flex flex-col items-center">
                                        <input type="checkbox" :value="day" wire:model.live="selectedDays"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500">
                                        <label class="text-sm font-medium text-gray-900 dark:text-gray-300" x-text="day"></label>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('recurrence') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="duedatecalendar" class="flex flex-row items-center block mb-2 text-sm font-medium text-gray-900 dark:text-white">Due Date & Time</label>
                        <div class="relative cursor-pointer" wire:ignore>
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0 2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </div>
                            <input id="duedatecalendar"
                                   x-init="initDuePicker($el)"
                                   name="due_date"
                                   wire:model.live="due_date"
                                   x-model="due_date"
                                   type="text"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer"
                                   placeholder="Select date & time">
                        </div>
                        @error('due_date') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Reminder Modal Trigger -->
                    <div>
                        <span class="inline-flex bg-gray-100 border-solid border-transparent rounded-3xl p-3">
                            <button @click="showReminderModal = true" type="button" class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                                    <path d="M13 12v-5h-1.999v6.999h5.999v-1.999h-4zm-12.197-4.285c-1.261-1.944-1.035-4.569.675-6.266 1.7-1.687 4.305-1.896 6.235-.645-3.171 1.219-5.692 3.741-6.91 6.911zm18.428 11.18c1.715-1.794 2.771-4.219 2.771-6.896 0-5.522-4.477-10-10.002-10-5.522 0-10 4.477-10 10 0 2.678 1.059 5.104 2.772 6.898l-1.736 4.506c-.159.394.288.759.643.522l3.581-3.122c1.412.761 3.026 1.195 4.742 1.195 1.717 0 3.334-.434 4.744-1.195l3.582 3.122c.355.237.803-.128.643-.522l-1.74-4.508zm-7.23 1.103c-4.412 0-8.001-3.588-8.001-8s3.589-8 8.002-8c4.412 0 8.002 3.588 8.002 8zm10.553-18.52c-1.697-1.71-4.324-1.937-6.268-.675 3.17 1.218 5.693 3.739 6.912 6.91 1.25-1.931 1.041-4.535-.644-6.235z" />
                                </svg>
                            </button>
                        </span>
                    </div>

                    <!-- Reminder Modal -->
                    <div x-show="showReminderModal" class="fixed inset-0 flex items-center justify-center z-50" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800 p-6 z-10">
                            <div class="flex justify-between items-center pb-4 mb-4 border-b dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task Reminder Before Due Date</h3>
                                <button type="button" @click="showReminderModal = false" class="text-gray-400 hover:bg-gray-200 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                            </div>
                            <div class="flex flex-row space-x-4">
                                <input type="number" id="reminder_time" wire:model.blur="reminderTime" x-model="reminderTime"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter time">

                                <select id="reminder_unit" wire:model.live="reminderUnit" x-model="reminderUnit"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Option</option>
                                    <option value="minutes">Minutes</option>
                                    <option value="hours">Hours</option>
                                    <option value="days">Days</option>
                                </select>
                            </div>
                            <div class="flex justify-center pt-4">
                                <button type="button" @click="showReminderModal = false" class="bg-blue-600 text-white px-4 py-2 rounded-lg ml-2">Save Reminder</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="submit" class="relative flex w-full justify-center rounded-md bg-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-800" wire:loading.attr="disabled">
                            <span wire:loading.remove>Save Task</span>
                            <span wire:loading class="block">Saving Task...</span>
                        </button>
                        <button type="button" @click="$wire.close()" class="relative flex w-full justify-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
