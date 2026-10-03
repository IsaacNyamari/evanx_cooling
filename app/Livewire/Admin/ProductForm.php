<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\GeneratesSeoWithAi;
use App\Models\Category;
use App\Models\Product;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use GeneratesSeoWithAi;
    use WithFileUploads;

    public Product $product;

    public string $name = '';

    public string $slug = '';

    public string $sku = '';

    public string $short_description = '';

    public string $meta_title = '';

    public string $meta_description = '';

    public string $description = '';

    public $price = 0;

    public $sale_price = null;

    public string $currency = 'KES';

    public string $image_url = '';

    public $image_file = null;

    public bool $remove_image = false;

    public array $categoryIds = [];

    public bool $in_stock = true;

    public bool $is_active = true;

    public function mount(?Product $product = null): void
    {
        $this->product = $product ?? new Product;

        if ($this->product->exists) {
            $p = $this->product;
            $this->name = $p->name;
            $this->slug = $p->slug;
            $this->sku = (string) $p->sku;
            $this->short_description = (string) $p->short_description;
            $this->meta_title = (string) $p->meta_title;
            $this->meta_description = (string) $p->meta_description;
            $this->description = (string) $p->description;
            $this->price = $p->price;
            $this->sale_price = $p->sale_price;
            $this->currency = $p->currency;
            $this->in_stock = $p->in_stock;
            $this->is_active = $p->is_active;
            $this->categoryIds = $p->categories->pluck('id')->map(fn ($id) => (string) $id)->all();
            $this->image_url = Str::startsWith((string) $p->image, 'http') ? $p->image : '';
        }
    }

    public function updatedName(): void
    {
        if (! $this->product->exists && $this->slug === '') {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedImageFile(): void
    {
        try {
            $this->validateOnly('image_file');
        } catch (ValidationException $e) {
            $this->image_file = null;

            throw $e;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($this->product->id)],
            'sku' => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:120'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'currency' => ['required', 'string', 'size:3'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'categoryIds' => ['array'],
            'categoryIds.*' => ['exists:categories,id'],
        ];
    }

    public function save()
    {
        $this->validate();

        $image = $this->product->image;

        if ($this->image_file) {
            try {
                $stored = ImageUploader::store($this->image_file, 'products');
            } catch (\RuntimeException $e) {
                $this->addError('image_file', $e->getMessage());

                return;
            }

            $this->deleteUpload($image);
            $image = $stored;
        } elseif ($this->image_url !== '') {
            if ($image !== $this->image_url) {
                $this->deleteUpload($image);
            }
            $image = $this->image_url;
        } elseif ($this->remove_image) {
            $this->deleteUpload($image);
            $image = null;
        }

        $this->product->fill([
            'name' => $this->name,
            'slug' => Str::slug($this->slug ?: $this->name),
            'sku' => $this->sku ?: null,
            'short_description' => $this->short_description ?: null,
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
            'description' => $this->description ?: null,
            'price' => $this->price ?: 0,
            'sale_price' => $this->sale_price ?: null,
            'currency' => strtoupper($this->currency),
            'image' => $image,
            'in_stock' => $this->in_stock,
            'is_active' => $this->is_active,
        ])->save();

        $this->product->categories()->sync($this->categoryIds);

        session()->flash('success', 'Product saved.');

        return $this->redirectRoute('admin.products.index', navigate: true);
    }

    private function deleteUpload(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('uploads')->delete($path);
        }
    }

    /** "Generate with AI": writes the Google title and description from what is typed in the form right now. */
    public function generateSeo(): void
    {
        $result = $this->writeWithAi([
            'name' => trim($this->name),
            'categories' => Category::whereIn('id', $this->categoryIds)->pluck('name')->all(),
            'text' => trim(strip_tags($this->short_description.' '.$this->description)),
            'price' => (float) $this->price > 0 ? strtoupper($this->currency).' '.number_format((float) $this->price, 0) : null,
            'sku' => trim($this->sku) ?: null,
        ]);

        if ($result) {
            $this->meta_title = $result['title'];
            $this->meta_description = $result['description'];
            $this->resetErrorBag(['meta_title', 'meta_description']);
        }
    }

    public function render()
    {
        return view('livewire.admin.product-form', [
            'categories' => Category::orderBy('name')->get(),
            'aiEnabled' => $this->aiEnabled(),
        ]);
    }
}
