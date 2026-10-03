<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-tags me-2"></i> Categories ({{ $categories->total() }})</h5>
            <a href="{{ route('admin.categories.create') }}" wire:navigate class="btn btn-light btn-sm"><i class="fa fa-plus me-1"></i> New category</a>
        </div>
        <div class="card-body border-bottom">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="search" wire:model.live.debounce.300ms="q" class="form-control" placeholder="Search categories...">
                </div>
                <div class="col-auto d-flex align-items-center" wire:loading>
                    <span class="spinner-border spinner-border-sm text-primary"></span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">Name</th><th>Slug</th><th>Parent</th><th class="text-center">Products</th><th>Status</th><th class="text-end pe-3">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr wire:key="cat-{{ $category->id }}">
                            <td class="ps-3">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->parent?->name ?? '—' }}</td>
                            <td class="text-center">{{ $category->products_count }}</td>
                            <td>
                                <button wire:click="toggleActive({{ $category->id }})" class="badge border-0 {{ $category->is_active ? 'text-bg-success' : 'text-bg-secondary' }}" title="Click to toggle">
                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="{{ route('admin.categories.edit', $category) }}" wire:navigate class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                                <button wire:click="delete({{ $category->id }})" wire:confirm="Delete this category? Products stay, but lose this category." class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="card-footer">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
