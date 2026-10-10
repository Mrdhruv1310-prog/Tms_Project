<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryManager extends Component
{
    public string $newCategory = '';

    /** @var \Illuminate\Database\Eloquent\Collection<int, Category> */
    public $categories;

    public ?int $editCategoryId = null;
    public string $editCategory = '';

    public function mount(): void
    {
        $this->loadCategories();
    }

    /**
     * Check if current user has permission to manage categories.
     */
    public function canManageCategories(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $role = strtolower(trim($authUser->role ?? 'user'));

        return in_array($role, ['admin', 'hr', 'manager'], true);
    }

    /**
     * Authorization check for editing/deleting specific category instance.
     */
    private function authorizeCategoryAccess(Category $category): bool
    {
        if (! $this->canManageCategories()) {
            return false;
        }

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $role = strtolower(trim($authUser->role ?? 'user'));

        if ($role !== 'admin' && (int) $category->created_by !== (int) $authUser->id) {
            return false;
        }

        return true;
    }

    public function loadCategories(): void
    {
        if (! Auth::check()) {
            $this->categories = collect();
            return;
        }

        $this->categories = Category::forCurrentUser()->with('creator')->get();
    }

    private function notifyUser(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', ['message' => $message, 'type' => $type]);
    }

    public function addCategory(): void
    {
        if (! $this->canManageCategories()) {
            $this->notifyUser('You do not have permission to add categories.', 'error');
            return;
        }

        $this->newCategory = trim($this->newCategory);
        $userId = Auth::id();

        $this->validate([
            'newCategory' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->where(function ($query) use ($userId) {
                    return $query->where('created_by', $userId);
                }),
            ],
        ], [
            'newCategory.unique' => 'The category already exists.',
        ]);

        try {
            Category::create([
                'name' => $this->newCategory,
                'created_by' => $userId,
            ]);

            $this->newCategory = '';
            $this->loadCategories();

            $this->notifyUser('Category added successfully.', 'success');
        } catch (QueryException $e) {
            $this->notifyUser('A database error occurred or the category already exists.', 'error');
        }
    }

    public function editCategorySetup(int $id): void
    {
        $category = Category::findOrFail($id);

        if (! $this->authorizeCategoryAccess($category)) {
            $this->notifyUser('You do not have permission to edit this category.', 'error');
            return;
        }

        $this->editCategoryId = $category->id;
        $this->editCategory = $category->name;
    }

    public function updateCategory(): void
    {
        if (! $this->editCategoryId) {
            $this->notifyUser('No category selected for update.', 'error');
            return;
        }

        $category = Category::findOrFail($this->editCategoryId);

        if (! $this->authorizeCategoryAccess($category)) {
            $this->notifyUser('You do not have permission to update this category.', 'error');
            return;
        }

        $this->editCategory = trim($this->editCategory);

        $this->validate([
            'editCategory' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->where(function ($query) use ($category) {
                        return $query->where('created_by', $category->created_by);
                    })
                    ->ignore($this->editCategoryId),
            ],
        ], [
            'editCategory.unique' => 'The category already exists.',
        ]);

        try {
            $category->name = $this->editCategory;
            $category->save();

            $this->editCategoryId = null;
            $this->editCategory = '';
            $this->loadCategories();

            $this->notifyUser('Category updated successfully.', 'success');
        } catch (QueryException $e) {
            $this->notifyUser('Failed to update category. It may already exist.', 'error');
        }
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        if (! $this->authorizeCategoryAccess($category)) {
            $this->notifyUser('You do not have permission to delete this category.', 'error');
            return;
        }

        if ($category->tasks()->exists()) {
            $this->notifyUser('Cannot delete category. There are tasks assigned to it.', 'error');
            return;
        }

        $category->delete();
        $this->loadCategories();

        $this->notifyUser('Category deleted successfully.', 'error');
    }

    public function render(): View
    {
        return view('livewire.category-manager')->layout('components.layouts.app', ['title' => 'Categories | TMS']);
    }
}
