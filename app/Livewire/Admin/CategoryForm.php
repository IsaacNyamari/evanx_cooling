<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoryForm extends Component
{
    use WithFileUploads;

    public Category $category;

    public string $name = '';

    public string $slug = '';

    public ?int $parent_id = null;

    public string $description = '';

    public string $image_url = '';

    public $image_file = null;

    public bool $remove_image = false;

    public bool $is_active = true;

    public function mount(?Category $category = null): void
    {
        $this->category = $category ?? new Category;

        if ($this->category->exists) {
            $this->name = $this->category->name;
            $this->slug = $this->category->slug;
            $this->parent_id = $this->category->parent_id;
            $this->description = (string) $this->category->description;
            $this->is_active = $this->category->is_active;
            $this->image_url = Str::startsWith((string) $this->category->image, 'http') ? $this->category->image : '';
        }
    }

    public function updatedName(): void
    {
        if (! $this->category->exists && $this->slug === '') {
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
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($this->category->id)],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$this->category->id])],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save()
    {
        $this->validate();

        $image = $this->category->image;

        if ($this->image_file) {
            try {
                $stored = ImageUploader::store($this->image_file, 'categories');
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

        $this->category->fill([
            'name' => $this->name,
            'slug' => Str::slug($this->slug ?: $this->name),
            'parent_id' => $this->parent_id ?: null,
            'description' => $this->description ?: null,
            'image' => $image,
            'is_active' => $this->is_active,
        ])->save();

        session()->flash('success', 'Category saved.');

        return $this->redirectRoute('admin.categories.index', navigate: true);
    }

    private function deleteUpload(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('uploads')->delete($path);
        }
    }

    public function render()
    {
        return view('livewire.admin.category-form', [
            'parents' => Category::where('id', '!=', $this->category->id)->orderBy('name')->get(),
        ]);
    }
}
