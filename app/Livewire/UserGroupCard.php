<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Manage User Groups')]
class UserGroupCard extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, Group> */
    public $groups;
    public ?string $newGroup = null;
    public ?int $editingGroupId = null;
    public string $editingGroupName = '';

    public function mount(): void
    {
        $this->loadGroups();
    }

    public function loadGroups(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Group> $groups */
        $groups = Group::query()->select('id', 'label')->get();

        $this->groups = $groups;
    }

    public function showGroup(int|string $groupId): void
    {
        $this->dispatch('groupSelected', $groupId);
    }

    public function addGroup(): void
    {
        $this->newGroup = is_string($this->newGroup) ? trim($this->newGroup) : '';

        $this->validate([
            'newGroup' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Group::query()->where('label', $value)->exists()) {
                        $this->dispatch('notify', ['message' => 'The group name already exists.', 'type' => 'error']);
                        $fail('The group name already exists.');
                    }
                },
            ],
        ]);

        Group::query()->create(['label' => $this->newGroup]);

        $this->newGroup = '';
        $this->loadGroups();

        $this->dispatch('notify', ['message' => 'Group added successfully.', 'type' => 'success']);
    }

    public function startEditing(int|string $groupId, string $groupName): void
    {
        $this->editingGroupId = (int) $groupId;
        $this->editingGroupName = $groupName;
    }

    public function cancelEditing(): void
    {
        $this->editingGroupId = null;
        $this->editingGroupName = '';
    }

    public function saveGroupName(): void
    {
        $this->validate([
            'editingGroupName' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Group::query()->where('label', $value)->where('id', '!=', $this->editingGroupId)->exists()) {
                        $this->dispatch('notify', ['message' => 'The group name already exists.', 'type' => 'error']);
                        $fail('The group name already exists.');
                    }
                },
            ],
        ], [
            'editingGroupName.required' => 'Enter Group Name',
        ]);

        /** @var Group $group */
        $group = Group::query()->findOrFail($this->editingGroupId);
        $group->update(['label' => $this->editingGroupName]);

        $this->editingGroupId = null;
        $this->editingGroupName = '';
        $this->loadGroups();

        $this->dispatch('notify', ['message' => 'Group name updated successfully.', 'type' => 'success']);
    }

    public function render(): mixed
    {
        return view('livewire.user-group-card');
    }
}
