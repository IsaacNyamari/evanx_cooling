<?php

namespace App\Livewire\Admin\Seo;

use App\Livewire\Concerns\GeneratesSeoWithAi;
use App\Models\Product;
use App\Support\Ai\AiSeoWriter;
use App\Support\Seo;
use App\Support\SeoAnalyzer;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use GeneratesSeoWithAi;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(except: '')]
    public string $q = '';

    /** '' (all) | custom | issues | noimage */
    #[Url(except: '')]
    public string $filter = '';

    public ?int $editingId = null;

    public string $meta_title = '';

    public string $meta_description = '';

    public ?string $message = null;

    public function updating($name): void
    {
        if (in_array($name, ['q', 'filter'])) {
            $this->resetPage();
        }
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId = $id;
        $this->meta_title = (string) $product->meta_title;
        $this->meta_description = (string) $product->meta_description;
        $this->message = null;
        $this->resetErrorBag();
    }

    public function generateSeo(): void
    {
        $product = Product::with('categories')->findOrFail($this->editingId);

        if ($result = $this->writeWithAi(AiSeoWriter::contextFor($product))) {
            $this->meta_title = $result['title'];
            $this->meta_description = $result['description'];
            $this->resetErrorBag();
        }
    }

    public function useSuggestion(Seo $seo): void
    {
        $product = Product::findOrFail($this->editingId);

        $this->meta_title = $seo->autoProductTitle($product);
        $this->meta_description = $seo->autoProductDescription($product);
    }

    public function save(): void
    {
        $product = Product::findOrFail($this->editingId);

        $this->validate([
            'meta_title' => ['nullable', 'string', 'max:120'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        $product->update([
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
        ]);

        $this->message = 'Saved.';
    }

    public function resetToAuto(): void
    {
        Product::whereKey($this->editingId)->update(['meta_title' => null, 'meta_description' => null]);
        $this->edit($this->editingId);
        $this->message = 'Back to the automatic title and description.';
    }

    public function close(): void
    {
        $this->editingId = null;
    }

    public function render(Seo $seo)
    {
        $query = Product::with('categories')
            ->when($this->q !== '', fn ($q) => $q->where('name', 'like', '%'.$this->q.'%'))
            ->when($this->filter === 'custom', fn ($q) => $q->where(fn ($w) => $w->whereNotNull('meta_title')->orWhereNotNull('meta_description')))
            ->when($this->filter === 'noimage', fn ($q) => $q->where(fn ($w) => $w->whereNull('image')->orWhere('image', '')))
            ->orderBy('name');

        $decorate = function (Product $p) use ($seo) {
            $title = $p->meta_title ?: $seo->autoProductTitle($p);
            $description = $p->meta_description ?: $seo->autoProductDescription($p);

            return [
                'product' => $p, 'title' => $title, 'description' => $description, 'custom' => (bool) ($p->meta_title || $p->meta_description),
                'title_rating' => SeoAnalyzer::title($title), 'description_rating' => SeoAnalyzer::description($description),
            ];
        };

        if ($this->filter === 'issues') {
            // Needs the computed ratings (and duplicates), so filter in memory then page by hand.
            $all = $query->get()->map($decorate);
            $dups = $all->groupBy(fn ($r) => mb_strtolower($r['title']))->filter(fn ($g) => $g->count() > 1)->flatten(1)->pluck('product.id')->all();
            $flagged = $all->filter(fn ($r) => $r['title_rating']['status'] !== 'good' || $r['description_rating']['status'] !== 'good'
                || ! $r['product']->hasImage() || in_array($r['product']->id, $dups, true))->values();
            $page = LengthAwarePaginator::resolveCurrentPage();
            $rows = new LengthAwarePaginator($flagged->forPage($page, 15)->values(), $flagged->count(), 15, $page, ['path' => request()->url()]);
        } else {
            $rows = $query->paginate(15);
            $rows->setCollection($rows->getCollection()->map($decorate));
        }

        $editing = null;
        if ($this->editingId && ($product = Product::find($this->editingId))) {
            $title = trim($this->meta_title) ?: $seo->autoProductTitle($product);
            $description = trim($this->meta_description) ?: $seo->autoProductDescription($product);

            $editing = [
                'product' => $product, 'title' => $title, 'description' => $description,
                'auto_title' => $seo->autoProductTitle($product), 'auto_description' => $seo->autoProductDescription($product),
                'url' => route('shop.show', $product->slug), 'image' => $product->hasImage() ? $product->imageUrl() : $seo->defaultImage(),
            ];
        }

        return view('livewire.admin.seo.products', ['rows' => $rows, 'editing' => $editing, 'aiEnabled' => $this->aiEnabled()]);
    }
}
