<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public int $activeImage = 0;

    public function setImage(int $index): void
    {
        $this->activeImage = $index;
    }

    public function render()
    {
        $gallery = $this->product->galleryUrls();

        $related = Product::active()
            ->where('id', '!=', $this->product->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $this->product->categories->pluck('id')))
            ->limit(4)
            ->get();

        return view('livewire.shop.product-detail', compact('gallery', 'related'));
    }
}
