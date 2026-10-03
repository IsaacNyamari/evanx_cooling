<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: 'name_asc')]
    public string $sort = 'name_asc';

    public function updating($name): void
    {
        if (in_array($name, ['q', 'category', 'sort'])) {
            $this->resetPage();
        }
    }

    public function selectCategory(string $slug = ''): void
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('q', 'category', 'sort');
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::active()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();

        $current = $this->category !== ''
            ? Category::active()->with('children')->where('slug', $this->category)->first()
            : null;

        $term = '%'.$this->q.'%';

        $products = Product::active()
            ->with('categories')
            ->when($current, fn ($q) => $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $current->descendantIds())))
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', $term)
                ->orWhere('short_description', 'like', $term)
                ->orWhere('sku', 'like', $term)))
            ->orderBy('name', $this->sort === 'name_desc' ? 'desc' : 'asc')
            ->paginate(12);

        return view('livewire.shop.product-list', compact('categories', 'current', 'products'));
    }
}
