<?php

namespace App\Livewire\Admin\Seo;

use App\Models\SeoPage;
use App\Support\ImageUploader;
use App\Support\Seo;
use App\Support\SeoAnalyzer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Pages extends Component
{
    use WithFileUploads;

    public ?string $editing = null;

    public string $meta_title = '';

    public string $meta_description = '';

    public bool $noindex = false;

    public $image_file = null;

    public bool $remove_image = false;

    public $site_image_file = null;

    public ?string $message = null;

    public function edit(string $key): void
    {
        abort_unless(isset(Seo::pages()[$key]), 404);

        $row = SeoPage::firstWhere('key', $key);

        $this->editing = $key;
        $this->meta_title = (string) $row?->meta_title;
        $this->meta_description = (string) $row?->meta_description;
        $this->noindex = (bool) $row?->noindex;
        $this->image_file = null;
        $this->remove_image = false;
        $this->message = null;
        $this->resetErrorBag();
    }

    public function updatedImageFile(): void
    {
        $this->validateUpload('image_file');
    }

    public function updatedSiteImageFile(): void
    {
        $this->validateUpload('site_image_file');
    }

    private function validateUpload(string $property): void
    {
        try {
            $this->validateOnly($property, [$property => ['nullable', 'image', 'max:4096']]);
        } catch (ValidationException $e) {
            $this->{$property} = null;

            throw $e;
        }
    }

    public function save(): void
    {
        abort_unless($this->editing, 404);

        $this->validate([
            'meta_title' => ['nullable', 'string', 'max:120'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'image_file' => ['nullable', 'image', 'max:4096'],
        ]);

        $row = SeoPage::firstOrNew(['key' => $this->editing]);
        $image = $row->image;

        if ($this->image_file) {
            try {
                $stored = ImageUploader::store($this->image_file, 'seo');
            } catch (\RuntimeException $e) {
                $this->addError('image_file', $e->getMessage());

                return;
            }
            $this->deleteUpload($image);
            $image = $stored;
        } elseif ($this->remove_image) {
            $this->deleteUpload($image);
            $image = null;
        }

        $row->fill([
            'meta_title' => trim($this->meta_title) ?: null,
            'meta_description' => trim($this->meta_description) ?: null,
            'image' => $image,
            'noindex' => $this->noindex,
        ]);

        // Nothing customised any more: fall back to the defaults by removing the row.
        if (! $row->meta_title && ! $row->meta_description && ! $row->image && ! $row->noindex) {
            $row->exists && $row->delete();
        } else {
            $row->save();
        }

        $this->image_file = null;
        $this->remove_image = false;
        $this->message = 'Saved. Search engines pick up the change the next time they visit the page.';
    }

    public function resetToDefault(): void
    {
        abort_unless($this->editing, 404);

        if ($row = SeoPage::firstWhere('key', $this->editing)) {
            $this->deleteUpload($row->image);
            $row->delete();
        }

        $this->edit($this->editing);
        $this->message = 'Back to the default title and description.';
    }

    public function saveSiteImage(): void
    {
        $this->validate(['site_image_file' => ['required', 'image', 'max:4096']], ['site_image_file.required' => 'Choose an image first.']);

        try {
            $stored = ImageUploader::store($this->site_image_file, 'seo');
        } catch (\RuntimeException $e) {
            $this->addError('site_image_file', $e->getMessage());

            return;
        }

        $row = SeoPage::firstOrNew(['key' => Seo::SITE_KEY]);
        $this->deleteUpload($row->image);
        $row->image = $stored;
        $row->save();

        $this->site_image_file = null;
        $this->message = 'Default share image updated.';
    }

    public function removeSiteImage(): void
    {
        if ($row = SeoPage::firstWhere('key', Seo::SITE_KEY)) {
            $this->deleteUpload($row->image);
            $row->delete();
        }

        $this->message = 'Back to the generated share image.';
    }

    private function deleteUpload(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('uploads')->delete($path);
        }
    }

    public function render(Seo $seo)
    {
        $seo->forgetCache();

        $pages = collect(Seo::pages())->map(function ($meta, $key) use ($seo) {
            $data = $seo->forPage($key);

            return $meta + $data + [
                'key' => $key,
                'title_rating' => SeoAnalyzer::title($data['title']),
                'description_rating' => SeoAnalyzer::description($data['description']),
            ];
        })->groupBy('group');

        $preview = null;
        if ($this->editing) {
            $default = Seo::pages()[$this->editing];
            $row = SeoPage::firstWhere('key', $this->editing);
            $title = trim($this->meta_title) ?: $default['title'];
            $description = trim($this->meta_description) ?: str_replace('{phone}', (string) config('site.phone'), $default['description']);

            $image = $this->image_file && $this->image_file->isPreviewable()
                ? $this->image_file->temporaryUrl()
                : (($row?->image && ! $this->remove_image) ? SeoPage::resolveImage($row->image) : $seo->defaultImage());

            $preview = [
                'label' => $default['label'], 'title' => $title, 'description' => $description, 'image' => $image,
                'url' => route($this->editing), 'has_custom_image' => (bool) ($row?->image && ! $this->remove_image),
                'default_title' => $default['title'], 'default_description' => str_replace('{phone}', (string) config('site.phone'), $default['description']),
            ];
        }

        $site = SeoPage::firstWhere('key', Seo::SITE_KEY);

        return view('livewire.admin.seo.pages', [
            'groups' => $pages,
            'preview' => $preview,
            'siteImage' => $seo->defaultImage(),
            'siteImageCustom' => (bool) $site?->image,
        ]);
    }
}
