<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(except: '')]
    public string $q = '';

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        if ($category->image && ! Str::startsWith($category->image, ['http://', 'https://'])) {
            Storage::disk('uploads')->delete($category->image);
        }

        $category->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function render()
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->when($this->q !== '', fn ($q) => $q->where('name', 'like', '%'.$this->q.'%'))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.category-index', compact('categories'));
    }
}
