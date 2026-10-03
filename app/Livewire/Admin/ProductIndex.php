<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\GeneratesSeoWithAi;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use GeneratesSeoWithAi;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $category = '';

    /** @var array<int,string> product id => the title/description just written */
    public array $seoDone = [];

    /** @var array<int,string> product id => why it failed */
    public array $seoErrors = [];

    public function updating($name): void
    {
        if (in_array($name, ['q', 'category'])) {
            $this->resetPage();
        }
    }

    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);
    }

    public function toggleStock(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['in_stock' => ! $product->in_stock]);
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($product->image && ! Str::startsWith($product->image, ['http://', 'https://'])) {
            Storage::disk('uploads')->delete($product->image);
        }

        $product->delete();
        session()->flash('success', 'Product deleted.');
    }

    /** One click on a row: generate the SEO title + description with AI and save them. */
    public function quickSeo(int $id): void
    {
        unset($this->seoDone[$id], $this->seoErrors[$id]);

        $result = $this->generateAndSaveSeo(Product::with('categories')->findOrFail($id));

        $result['ok'] ? $this->seoDone[$id] = $result['title'] : $this->seoErrors[$id] = $result['message'];
    }

    public function render()
    {
        $products = Product::with('categories')
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$this->q.'%')
                ->orWhere('sku', 'like', '%'.$this->q.'%')))
            ->when($this->category !== '', fn ($q) => $q->whereHas('categories', fn ($c) => $c->where('categories.id', $this->category)))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.product-index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'aiEnabled' => $this->aiEnabled(),
        ]);
    }
}
