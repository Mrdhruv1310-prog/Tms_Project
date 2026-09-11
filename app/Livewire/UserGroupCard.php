<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;

class UserGroupCard extends Component
{
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
        $this->groups = Group::select('id', 'label')->get();
    }

    public function showGroup($groupId): void
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
                    if (Group::where('label', $value)->exists()) {
                        $this->dispatch('notify', ['message' => 'The group name already exists.', 'type' => 'error']);
                        $fail('The group name already exists.');
                    }
                },
            ],
        ]);

        Group::create(['label' => $this->newGroup]);

        $this->newGroup = '';
        $this->loadGroups();

        $this->dispatch('notify', ['message' => 'Group added successfully.', 'type' => 'success']);
    }

    public function startEditing($groupId, $groupName): void
    {
        $this->editingGroupId = (int) $groupId;
        $this->editingGroupName = (string) $groupName;
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
                    if (Group::where('label', $value)->where('id', '!=', $this->editingGroupId)->exists()) {
                        $this->dispatch('notify', ['message' => 'The group name already exists.', 'type' => 'error']);
                        $fail('The group name already exists.');
                    }
                },
            ],
        ]);

        $group = Group::findOrFail($this->editingGroupId);
        $group->update(['label' => $this->editingGroupName]);

        $this->editingGroupId = null;
        $this->editingGroupName = '';
        $this->loadGroups();

        $this->dispatch('notify', ['message' => 'Group name updated successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.user-group-card')->layout('components.layouts.app', [
            'title' => 'Manage User Groups',
        ]);
    }
}
