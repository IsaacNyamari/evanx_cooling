<form wire:submit="save">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">{{ $product->exists ? 'Edit product' : 'New product' }}</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model.blur="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated from name">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" wire:model="sku" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Currency</label>
                            <input type="text" wire:model="currency" maxlength="3" class="form-control @error('currency') is-invalid @enderror">
                            @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price <small class="text-muted">(0 = on request)</small></label>
                            <input type="number" step="0.01" min="0" wire:model="price" class="form-control @error('price') is-invalid @enderror">
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sale price</label>
                            <input type="number" step="0.01" min="0" wire:model="sale_price" class="form-control @error('sale_price') is-invalid @enderror">
                            @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short description <small class="text-muted">(HTML allowed)</small></label>
                        <textarea wire:model="short_description" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full description <small class="text-muted">(HTML allowed)</small></label>
                        <textarea wire:model="description" rows="10" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header"><strong>Publishing</strong></div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active"><label class="form-check-label" for="is_active">Visible in the shop</label></div>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" wire:model="in_stock" id="in_stock"><label class="form-check-label" for="in_stock">In stock</label></div>
                </div>
            </div>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header"><strong>Categories</strong></div>
                <div class="card-body" style="max-height: 260px; overflow-y: auto">
                    @foreach ($categories as $cat)
                        <div class="form-check" wire:key="pc-{{ $cat->id }}">
                            <input class="form-check-input" type="checkbox" wire:model="categoryIds" value="{{ $cat->id }}" id="cat{{ $cat->id }}">
                            <label class="form-check-label" for="cat{{ $cat->id }}">{{ $cat->name }}</label>
                        </div>
                    @endforeach
                    @error('categoryIds.*') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header"><strong><i class="fa fa-magnifying-glass me-1"></i> Google / SEO</strong></div>
                <div class="card-body">
                    <p class="small text-muted">Leave empty and a good title and description are written automatically. Fill in only to customise.</p>
                    <label class="form-label small fw-semibold">Page title</label>
                    <input type="text" wire:model.live.debounce.300ms="meta_title" class="form-control form-control-sm @error('meta_title') is-invalid @enderror" placeholder="Automatic">
                    <x-seo.counter :text="$meta_title" kind="title" />
                    <label class="form-label small fw-semibold mt-3">Meta description</label>
                    <textarea wire:model.live.debounce.300ms="meta_description" rows="3" class="form-control form-control-sm @error('meta_description') is-invalid @enderror" placeholder="Automatic"></textarea>
                    <x-seo.counter :text="$meta_description" kind="description" />
                    @if ($product->exists)<a href="{{ route('admin.seo.products', ['q' => $product->name]) }}" class="small d-inline-block mt-2">See the Google preview</a>@endif
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header"><strong>Image</strong></div>
                <div class="card-body">
                    @if ($image_file && $image_file->isPreviewable())
                        <img src="{{ $image_file->temporaryUrl() }}" alt="" class="img-fluid rounded border mb-2">
                    @elseif ($product->hasImage())
                        <img src="{{ $product->imageUrl() }}" alt="" class="img-fluid rounded border mb-2">
                        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" wire:model="remove_image" id="remove_image"><label class="form-check-label" for="remove_image">Remove current image</label></div>
                    @endif
                    <input type="file" wire:model="image_file" accept="image/*" class="form-control mb-2 @error('image_file') is-invalid @enderror">
                    <div wire:loading wire:target="image_file" class="small text-muted mb-2">Uploading…</div>
                    <input type="url" wire:model="image_url" class="form-control @error('image_url') is-invalid @enderror" placeholder="…or paste an image URL">
                    @error('image_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('image_url') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" wire:loading.attr="disabled" wire:target="save,image_file">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>Save product
        </button>
        <a href="{{ route('admin.products.index') }}" wire:navigate class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
