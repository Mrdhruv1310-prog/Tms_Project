<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategoryManager extends Component
{
    public $newCategory;
    public $categories;
    public $editCategory;
    public function mount()
    {
        $this->categories = Category::all();

    }

    // Add category page
    public function addCategory()
    {
        // Trim whitespace from the input
        $this->newCategory = trim($this->newCategory);
        // Validate input
        $this->validate([
            'newCategory' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Category::where('name', $value)->exists()) {
                        $this->notify('The category already exists.', 'error');
                        $fail('');
                    }
                },
            ],
        ]);

        // Create new category
        Category::create(['name' => $this->newCategory]);

        // Clear input
        $this->newCategory = '';

        // Refresh category list
        $this->categories = Category::all();

        // Optionally dispatch a notification or event
        $this->notify('Category added successfully.', 'success');
    }

    // Update category page
    public function updateCategory($id, $name)
    {
        // Trim whitespace from the input
        $name = trim($name);
        // Validate input
        $this->validate([
            'editCategory' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    if (Category::where('name', $value)->where('id', '!=', $id)->exists()) {
                        $this->notify('The category already exists.', 'error');
                        $fail('');
                    }
                },
            ],
        ]); // Specify the property name for error messages);
        $category = Category::find($id);
        $category->name = $name;
        $category->save();

        $this->categories = Category::all(); // Refresh the list
        $this->notify('Category updated successfully.', 'success');
    }

    // Delete Category with check for assigned tasks
    public function deleteCategory($categoryId)
    {
        // Find the category
        $category = Category::findOrFail($categoryId);

        // Check if the category has any tasks assigned
        if ($category->tasks()->exists()) {
            // If there are tasks, notify the user and do not delete the category
            $this->notify('Cannot delete category. There are tasks assigned to it.', 'error');
            return;
        }

        // If no tasks are assigned, proceed with deletion
        $category->delete();

        // Refresh the category list
        $this->categories = Category::all();

        // Notify success
        $this->notify('Category deleted successfully.', 'success');
    }

    // Open for category page
    public function render()
    {
        return view('livewire.category-manager')->layout('components.layouts.app', ['title' => 'Categories | TMS',]);
    }
}
