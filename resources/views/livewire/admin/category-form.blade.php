<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ $category->exists ? 'Edit category' : 'New category' }}</h5>
    </div>
    <form wire:submit="save">
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
                    <label class="form-label">Parent category</label>
                    <select wire:model="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                        <option value="">— None (top level) —</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea wire:model="description" rows="3" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                @if ($image_file && $image_file->isPreviewable())
                    <div class="mb-2"><img src="{{ $image_file->temporaryUrl() }}" alt="" style="max-height: 80px" class="rounded border"></div>
                @elseif ($category->hasImage())
                    <div class="mb-2"><img src="{{ $category->imageUrl() }}" alt="" style="max-height: 80px" class="rounded border">
                        <div class="form-check mt-1"><input class="form-check-input" type="checkbox" wire:model="remove_image" id="remove_image"><label class="form-check-label" for="remove_image">Remove current image</label></div></div>
                @endif
                <input type="file" wire:model="image_file" accept="image/*" class="form-control mb-2 @error('image_file') is-invalid @enderror">
                <div wire:loading wire:target="image_file" class="small text-muted mb-2">Uploading…</div>
                <input type="url" wire:model="image_url" class="form-control @error('image_url') is-invalid @enderror" placeholder="…or paste an image URL">
                @error('image_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('image_url') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                <label class="form-check-label" for="is_active">Visible in the shop</label>
            </div>
        </div>
        <div class="card-footer d-flex gap-2">
            <button class="btn btn-primary" wire:loading.attr="disabled" wire:target="save,image_file">
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>Save
            </button>
            <a href="{{ route('admin.categories.index') }}" wire:navigate class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
