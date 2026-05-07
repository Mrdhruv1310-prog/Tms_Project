<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;
use App\Models\Task;
use App\Models\User;

class UserGroupCard extends Component
{
    public $groups = [];
    public $newGroup;
    public $editingGroupId = null; // Track which group is being edited
    public $editingGroupName = ''; // Store the new group name during editing

    public function mount()
    {
        // Show Manager users Groups:
        // $this->groups = Group::all();
        // FIX:
        $this->groups = Group::select('id', 'label')
            ->get()
            ->map(function ($group) {

                return [
                    'id' => $group->id ?? 0,
                    'name' => !empty($group->label) ? (string) $group->label : 'No Group',
                    'percentage' => 0,
                    'pending' => 0,
                    'in_progress' => 0,
                    'completed' => 0,
                    'total' => 0,
                ];
            })
            ->toArray();
    }

    public function showGroup($groupId)
    {
        // Emit an event with the selected group ID
        $this->dispatch('groupSelected', $groupId);
    }

    public function addGroup()
    {
        // Trim whitespace from the input
        $this->newGroup = trim($this->newGroup);
        // Validate input
        $this->validate([
            'newGroup' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Group::where('label', $value)->exists()) {
                        $this->notify('The gruop name already exists.', 'error');
                        $fail('');
                    }
                },
            ],
        ]);

        // Create new category
        Group::create(['label' => $this->newGroup]);

        // Clear input
        $this->newGroup = '';

        // Refresh category list
        $this->groups = Group::all();

        // Optionally dispatch a notification or event
        $this->notify('Group added successfully.', 'success');
    }

    public function startEditing($groupId, $groupName)
    {
        $this->editingGroupId = $groupId;
        $this->editingGroupName = $groupName;
    }

    public function cancelEditing()
    {
        $this->editingGroupId = null;
        $this->editingGroupName = '';
    }

    public function saveGroupName()
    {
        $this->validate([
            'editingGroupName' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check for duplicate names excluding the current group being edited
                    if (Group::where('label', $value)->where('id', '!=', $this->editingGroupId)->exists()) {
                        $this->notify('The gruop name already exists.', 'error');
                        $fail('');
                    }
                },
            ],
        ]);

        $group = Group::find($this->editingGroupId);
        $group->update(['label' => $this->editingGroupName]);

        $this->editingGroupId = null;
        $this->editingGroupName = '';
        $this->groups = Group::all();

        $this->notify('Group name updated successfully.', 'success');
    }

    public function render()
    {
        return view('livewire.user-group-card')->layout('components.layouts.app', ['title' => 'Manage User Groups']);
    }
}
