<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CategoryManager extends Component
{
    public string $newCategory = '';

    /** @var \Illuminate\Database\Eloquent\Collection<int, Category> */
    public $categories;

    public ?int $editCategoryId = null;
    public string $editCategory = '';

    public function mount(): void
    {
        $this->authorizeAdmin();
        $this->loadCategories();
    }

    /**
     * Centralized authorization check to avoid code repetition.
     */
    private function authorizeAdmin(): void
    {
        if (! Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $role = $authUser->role ?? 'user';

        if (! in_array($role, ['admin', 'super-admin'], true)) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Load all categories.
     */
    public function loadCategories(): void
    {
        $this->categories = Category::all();
    }

    /**
     * Helper to dispatch toast notifications safely.
     */
    private function notifyUser(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', ['message' => $message, 'type' => $type]);
    }

    // Add category function
    public function addCategory(): void
    {
        $this->authorizeAdmin();

        // Trim whitespace
        $this->newCategory = trim($this->newCategory);

        // Validate input with built-in unique rule for concurrency safety
        $this->validate([
            'newCategory' => 'required|string|max:255|unique:categories,name',
        ], [
            'newCategory.unique' => 'The category already exists.',
        ]);

        try {
            Category::create(['name' => $this->newCategory]);

            // Clear input and refresh list
            $this->newCategory = '';
            $this->loadCategories();

            $this->notifyUser('Category added successfully.', 'success');
        } catch (QueryException $e) {
            $this->notifyUser('A database error occurred or the category already exists.', 'error');
        }
    }

    /**
     * Set up category for editing.
     */
    public function editCategorySetup(int $id): void
    {
        $this->authorizeAdmin();
        $category = Category::findOrFail($id);
        $this->editCategoryId = $category->id;
        $this->editCategory = $category->name;
    }

    // Update category function
    public function updateCategory(): void
    {
        $this->authorizeAdmin();

        if (!$this->editCategoryId) {
            $this->notifyUser('No category selected for update.', 'error');
            return;
        }

        // Trim whitespace
        $this->editCategory = trim($this->editCategory);

        // Validate input ignoring current ID
        $this->validate([
            'editCategory' => 'required|string|max:255|unique:categories,name,' . $this->editCategoryId,
        ], [
            'editCategory.unique' => 'The category already exists.',
        ]);

        try {
            $category = Category::findOrFail($this->editCategoryId);
            $category->name = $this->editCategory;
            $category->save();

            // Reset edit state and refresh
            $this->editCategoryId = null;
            $this->editCategory = '';
            $this->loadCategories();

            $this->notifyUser('Category updated successfully.', 'success');
        } catch (QueryException $e) {
            $this->notifyUser('Failed to update category. It may already exist.', 'error');
        }
    }

    // Delete Category with check for assigned tasks
    public function deleteCategory(int $categoryId): void
    {
        $this->authorizeAdmin();

        $category = Category::findOrFail($categoryId);

        // Check if the category has any tasks assigned
        if ($category->tasks()->exists()) {
            $this->notifyUser('Cannot delete category. There are tasks assigned to it.', 'error');
            return;
        }

        $category->delete();
        $this->loadCategories();

        $this->notifyUser('Category deleted successfully.', 'success');
    }

    // Render category page
    public function render(): View
    {
        return view('livewire.category-manager')->layout('components.layouts.app', ['title' => 'Categories | TMS']);
    }
}
