<div>
    <div x-data="{
        show: @entangle('isOpen'),
        title: @entangle('title'),
        description: @entangle('description'),
        category_id: @entangle('category_id'),
        priority: @entangle('priority'),
        labels: '',
        due_date: @entangle('due_date'),
        recurrence_end_date: @entangle('recurrence_end_date'),
        selectedDays: @entangle('selectedDays'),
        showReminderModal: false,
        errorMessage: '',
        reminderUnit: @entangle('reminderUnit'),
        isReminderEnabled: @entangle('isReminderEnabled'),
        enableRepeatTask: @entangle('enableRepeatTask'),
        recurrence: @entangle('recurrence'),
        selectedUsers: @entangle('selectedUsers'),
        allUsers: @js($users),
        selectedUserNames: [],
        isEditMode: @entangle('isEditMode'),
        label_id_alpine: @entangle('label_id'),
        groupUserMap: @js($groupUserMap),
        userManuallyChanged: false,
        updateUserSelection(event, id) {
            this.userManuallyChanged = true;
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
                const groupUserIds = this.groupUserMap[this.label_id_alpine];
                if (this.selectedUsers.length === 0) {
                    this.selectedUsers = [...groupUserIds];
                    this.syncSelectedUserNames();
                }
            }
        },

        init() {
            this.syncSelectedUserNames();

            this.$watch('selectedUsers', (newSelected) => {
                this.syncSelectedUserNames();

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

            this.$watch('label_id_alpine', (newGroupId) => {
                if (newGroupId && this.groupUserMap[newGroupId]) {
                    const groupUserIds = this.groupUserMap[newGroupId];
                    this.selectedUsers = [...groupUserIds];
                    this.syncSelectedUserNames();
                }
            });

            if (this.isEditMode) {
                this.syncGroupUsersOnEdit();
            }

            this.$watch('show', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                    this.syncSelectedUserNames();
                    this.$nextTick(() => this.initDatepicker());
                } else {
                    document.body.classList.remove('overflow-hidden');
                    this.destroyDatepicker();
                    this.resetForm();
                }
            });

            this.$watch('due_date', value => {
                this.isReminderEnabled = value ? true : false;
            });

            window.addEventListener('task-edit-form-filled', () => {
                this.syncSelectedUserNames();
                this.$nextTick(() => this.initDatepicker());
            });
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
    }" x-cloak x-show="show"
        class="fixed inset-0 flex items-center justify-center z-50 p-2.5 xs:p-4 sm:p-6 lg:p-8 overflow-y-auto transform-gpu"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" inert></div>

        {{-- Modal Container --}}
        <div x-show="show" x-transition:enter="transition ease-out duration-150 transform-gpu"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-[340px] xs:max-w-[380px] sm:max-w-xl lg:max-w-2xl xl:max-w-3xl 2xl:max-w-4xl 3xl:max-w-5xl my-auto">

            <!-- Modal Content Box -->
            <div
                class="relative p-4 xs:p-5 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200/80 dark:border-gray-500 modal-scrollbar max-h-[90vh] overflow-y-auto">

                <!-- Modal Header -->
                <div
                    class="flex justify-between items-center pb-3.5 sm:pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-base xs:text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $taskId ? 'Edit Task' : 'Assign New Task' }}
                    </h3>
                    <button type="button" @click="show = false;"
                        class="text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white rounded-xl text-xs sm:text-sm p-1.5 ml-auto inline-flex items-center transition-colors cursor-pointer shrink-0">
                        <svg inert class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="{{ $taskId ? 'updateTask' : 'saveTask' }}" novalidate>
                    <div x-data="{
                        due_date: @entangle('due_date'),
                        reminderTime: @entangle('reminderTime').defer,
                        errorMessage: '',
                        reminderUnit: @entangle('reminderUnit').defer,

                        updateReminderTime(event) {
                            const updatedField = event.target;
                            const fieldId = updatedField.id;

                            if (fieldId === 'reminder_time') {
                                this.reminderTime = updatedField.value ? Number(updatedField.value) : '';
                            } else if (fieldId === 'reminder_unit') {
                                this.reminderUnit = updatedField.value;
                            } else if (fieldId === 'duedatecalendar') {
                                this.due_date = updatedField.value;
                            }

                            this.errorMessage = '';

                            if (!this.reminderTime || !this.reminderUnit) {
                                return;
                            }

                            if (!this.due_date) {
                                return;
                            }

                            const [day, month, yearWithTime] = this.due_date.split('/');
                            const [year, time = '00:00'] = yearWithTime.split(' ');
                            const [hours = '00', minutes = '00'] = time.split(':');
                            const dueDateObj = new Date(year, month - 1, day, hours, minutes);

                            if (isNaN(dueDateObj.getTime())) {
                                this.errorMessage = 'Invalid due date';
                                return;
                            }

                            const unitMultipliers = {
                                minutes: 60 * 1000,
                                hours: 60 * 60 * 1000,
                                days: 24 * 60 * 60 * 1000,
                            };

                            const reminderInMilliseconds = this.reminderTime * unitMultipliers[this.reminderUnit];
                            const validReminderTime = dueDateObj.getTime() - Date.now();

                            if (reminderInMilliseconds > validReminderTime) {
                                this.reminderTime = '';
                                this.reminderUnit = '';
                                this.errorMessage = 'Reminder time is NOT valid.';
                            }
                        }
                    }" class="flex flex-col space-y-3.5 sm:space-y-4 mb-4">

                        <!-- Task Title -->
                        <div>
                            <label for="title"
                                class="block mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                Task Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" wire:model="title" x-model="title" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('title') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                                placeholder="Enter Task Title" />
                            @error('title')
                                <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description"
                                class="block mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" wire:model="description" x-model="description" rows="3" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('description') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror"
                                placeholder="Enter Task Description"></textarea>
                            @error('description')
                                <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category and Users Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                            <div>
                                <label for="category_id"
                                    class="block mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select id="category_id" wire:model="category_id" x-model="category_id" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('category_id') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                    <option value="">Select Category</option>
                                    @if ($categories && is_iterable($categories))
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('category_id')
                                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Users Dropdown -->
                            <div x-data="{ openUsersDropdown: false }">
                                <label
                                    class="block mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    Users <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <button type="button" @click="openUsersDropdown = !openUsersDropdown"
                                        @click.outside="openUsersDropdown = false"
                                        class="flex flex-row justify-between items-center bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white cursor-pointer @error('selectedUsers') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                        <span class="truncate"
                                            x-text="selectedUserNames.length ? selectedUserNames.join(', ') : 'Select Users'"></span>
                                        <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-200 shrink-0"
                                            :class="{ 'rotate-180': openUsersDropdown }" fill="none"
                                            viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    </button>

                                    <div x-show="openUsersDropdown" x-transition x-cloak
                                        class="absolute z-30 mt-1 w-full bg-white rounded-xl shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                                        <ul
                                            class="h-40 sm:h-48 px-2.5 py-2 overflow-y-auto text-xs sm:text-sm text-gray-700 dark:text-gray-200 custom-scrollbar space-y-1">
                                            @if (isset($users) && count($users) > 0)
                                                @foreach ($users as $user)
                                                    <li wire:key="user-item-{{ $user->id }}"
                                                        class="flex items-center py-1.5 px-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                                        <input id="checkbox-user-{{ $user->id }}" type="checkbox"
                                                            wire:model="selectedUsers" value="{{ $user->id }}"
                                                            @change="updateUserSelection($event, {{ $user->id }})"
                                                            class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500 cursor-pointer shrink-0">

                                                        <div
                                                            class="w-6 h-6 sm:w-7 sm:h-7 ml-2.5 flex items-center justify-center text-white rounded-full bg-blue-600 text-[10px] sm:text-xs font-bold shrink-0">
                                                            {{ Str::upper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                                                        </div>

                                                        <label for="checkbox-user-{{ $user->id }}"
                                                            class="ml-2 text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer w-full select-none truncate">
                                                            {{ Str::ucfirst($user->first_name ?? '') . ' ' . Str::ucfirst($user->last_name ?? '') }}
                                                        </label>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="py-3 text-center text-xs text-gray-400 italic">
                                                    No active users available
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                @error('selectedUsers')
                                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Priority and Group Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                            <div>
                                <label for="priority"
                                    class="block mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    Priority
                                </label>
                                <select id="priority" wire:model="priority" x-model="priority" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('priority') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                        {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="label_id"
                                    class="block mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    Group <small class="text-red-500 font-normal">(Auto-selects members)</small>
                                </label>
                                <select id="label_id" wire:model="label_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 @error('label_id') border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200 @enderror">
                                    <option value="">Select Group</option>
                                    @if ($labels && is_iterable($labels))
                                        @foreach ($labels as $label)
                                            <option value="{{ $label->id }}">{{ $label->label }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('label_id')
                                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Recurrence Container -->
                        <div x-data="{ enableRepeatTask: @entangle('enableRepeatTask'), recurrence: @entangle('recurrence'), recurrence_end_date: @entangle('recurrence_end_date') }">
                            <div
                                class="flex items-center flex-wrap gap-2.5 sm:gap-3 bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-xl p-2.5 sm:p-3 mt-1">
                                <label for="enableRepeatTask"
                                    class="flex items-center text-xs sm:text-sm font-semibold text-gray-900 dark:text-white cursor-pointer">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0" inert
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 576 512">
                                        <path
                                            d="M272 416c17.7 0 32-14.3 32-32s-14.3-32-32-32l-112 0c-17.7 0-32-14.3-32-32l0-128 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 128c0 53 43 96 96 96l112 0zM304 96c-17.7 0-32 14.3-32 32s14.3 32 32 32l112 0c17.7 0 32 14.3 32 32l0 128-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-128c0-53-43-96-96-96L304 96z" />
                                    </svg>
                                    <span class="ml-2">Repeat</span>
                                    <input type="checkbox" id="enableRepeatTask" x-model="enableRepeatTask"
                                        wire:model="enableRepeatTask"
                                        @change="if(!enableRepeatTask) { recurrence = 'none'; recurrence_end_date = null }"
                                        class="h-4 w-4 ml-2 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-500 dark:focus:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
                                </label>

                                <div x-show="enableRepeatTask" class="transition-opacity duration-200">
                                    <label for="recurrence" class="sr-only">Frequency</label>
                                    <select id="recurrence" wire:model="recurrence" x-model="recurrence" required
                                        class="bg-white border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="none">Frequency</option>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>

                                <div x-show="enableRepeatTask" class="transition-opacity duration-200">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-2.5 pointer-events-none">
                                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" inert
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input id="repeatenddatecalendar" name="recurrence_end_date"
                                            wire:model="recurrence_end_date" x-model="recurrence_end_date"
                                            type="text"
                                            class="bg-white border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-8 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Recurrence End Date" readonly>
                                    </div>
                                </div>
                            </div>

                            <div x-show="enableRepeatTask && recurrence === 'weekly'"
                                class="bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-xl p-2.5 sm:p-3 mt-2">
                                <label
                                    class="block mb-2 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                    Weekly on these days
                                </label>
                                <div class="grid grid-cols-7 gap-1 sm:gap-2">
                                    <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']"
                                        :key="day">
                                        <div class="flex flex-col items-center">
                                            <input type="checkbox" :value="day" wire:model="selectedDays"
                                                class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:bg-gray-600 cursor-pointer">
                                            <label
                                                class="text-[10px] sm:text-xs font-semibold text-gray-900 dark:text-gray-300 mt-0.5"
                                                x-text="day"></label>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            @error('recurrence')
                                <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Due Date & Time -->
                        <div x-data="{ isReminderEnabled: @entangle('isReminderEnabled') }">
                            <label for="duedate"
                                class="flex flex-row items-center mb-1.5 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">
                                Due Date & Time
                                <button data-tooltip-target="duedatepin" data-tooltip-trigger="click" type="button"
                                    class="tooltip-button ml-1 text-gray-400 hover:text-gray-900 dark:text-gray-500 dark:hover:text-white">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5" inert xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="duedatepin" role="tooltip"
                                    class="tooltip tooltip-content invisible absolute z-20 inline-block rounded-xl bg-gray-900 px-3 py-1.5 text-xs font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                                    The default time will be the start of the day at midnight.
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-500 dark:text-gray-400" inert
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input id="duedatecalendar" name="due_date" wire:model="due_date" x-model="due_date"
                                    x-on:change="updateReminderTime($event)" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm lg:text-base rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full ps-9 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('due_date') border-red-500 bg-red-50 @enderror"
                                    placeholder="Select date & time" readonly>
                            </div>
                            @error('due_date')
                                <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Reminder Icon Action Button -->
                        <div>
                            <span
                                class="inline-flex bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-2.5">
                                <button @click="showReminderModal = true" type="button"
                                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M13 12v-5h-1.999v6.999h5.999v-1.999h-4zm-12.197-4.285c-1.261-1.944-1.035-4.569.675-6.266 1.7-1.687 4.305-1.896 6.235-.645-3.171 1.219-5.692 3.741-6.91 6.911zm18.428 11.18c1.715-1.794 2.771-4.219 2.771-6.896 0-5.522-4.477-10-10.002-10-5.522 0-10 4.477-10 10 0 2.678 1.059 5.104 2.772 6.898l-1.736 4.506c-.159.394.288.759.643.522l3.581-3.122c1.412.761 3.026 1.195 4.742 1.195 1.717 0 3.334-.434 4.744-1.195l3.582 3.122c.355.237.803-.128.643-.522l-1.74-4.508zm-7.23 1.103c-4.412 0-8.001-3.588-8.001-8s3.589-8 8.001-8c4.412 0 8.002 3.588 8.002 8s-3.59 8-8.002 8zm10.553-18.52c-1.697-1.71-4.324-1.937-6.268-.675 3.17 1.218 5.693 3.739 6.912 6.91 1.25-1.931 1.041-4.535-.644-6.235z" />
                                    </svg>
                                </button>
                            </span>
                        </div>

                        <!-- Reminder Modal Sub-window -->
                        <div x-show="showReminderModal"
                            class="fixed inset-0 flex items-center justify-center z-50 p-3"
                            aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" inert></div>

                            <div
                                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-4 sm:p-6 w-full max-w-md border border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-center pb-3 mb-3 border-b dark:border-gray-700">
                                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                                        Task Reminder Before Due Date
                                    </h3>
                                    <button type="button" @click="showReminderModal = false"
                                        class="text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white rounded-lg text-xs p-1.5 ml-auto">
                                        <svg inert class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" id="reminder_time" name="reminder_time"
                                        wire:model="reminderTime" x-model="reminderTime"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-xl block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Enter time" x-on:input="updateReminderTime($event)">

                                    <select id="reminder_unit" wire:model="reminderUnit" x-model="reminderUnit"
                                        x-on:change="updateReminderTime($event)"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-xl block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Option</option>
                                        <option value="minutes">Minutes</option>
                                        <option value="hours">Hours</option>
                                        <option value="days">Days</option>
                                    </select>
                                </div>

                                <p class="mt-2 text-xs text-rose-600 font-medium" x-text="errorMessage"></p>

                                <div class="flex justify-end pt-4">
                                    <button type="button" @click="showReminderModal = false"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 text-xs sm:text-sm rounded-xl transition-all cursor-pointer">
                                        Save Reminder
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Submit & Cancel Buttons -->
                        <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 pt-2">
                            <button type="submit"
                                class="relative flex w-full justify-center items-center rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 sm:py-3 text-xs sm:text-sm lg:text-base font-bold text-white shadow-md active:scale-[0.98] transition-all cursor-pointer"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Save Task</span>
                                <span wire:loading class="flex items-center gap-2">
                                    Saving Task...
                                    <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>

                            <button type="button" @click="resetForm(); show = false;"
                                class="relative flex w-full justify-center items-center rounded-xl border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 px-5 py-2.5 sm:py-3 text-xs sm:text-sm lg:text-base font-bold text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 active:scale-[0.98] transition-all cursor-pointer">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .modal-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .modal-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            margin: 5px;
        }

        .modal-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(rgb(28 93 239), rgb(59, 130, 246), rgb(87 146 255));
            border-radius: 4px;
        }

        .modal-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(rgb(26 86 219), rgb(59, 130, 246), rgb(95 149 250));
        }
    </style>

    <script>
        function submitForm() {
            console.log('Form submitted:', {
                selectedDays: this.selectedUsers
            });
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('modal', () => ({
                init() {
                    this.$watch('show', (value) => {
                        if (value) {
                            document.body.classList.add('overflow-hidden');
                        } else {
                            document.body.classList.remove('overflow-hidden');
                        }
                    });
                }
            }));
        });
    </script>
</div>
