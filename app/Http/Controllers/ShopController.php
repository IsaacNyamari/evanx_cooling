<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        return view('shop.index');
    }

    public function show(string $slug)
    {
        $product = Product::active()->with('categories')->where('slug', $slug)->firstOrFail();

        // Lets the layout build this product's title, description, share image and structured data.
        request()->attributes->set('seo.product', $product);

        return view('shop.show', compact('product'));
    }
}
