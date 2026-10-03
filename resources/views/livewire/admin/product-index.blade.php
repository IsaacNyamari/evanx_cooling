<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-box me-2"></i> Products ({{ $products->total() }})</h5>
            <a href="{{ route('admin.products.create') }}" wire:navigate class="btn btn-light btn-sm"><i class="fa fa-plus me-1"></i> New product</a>
        </div>
        <div class="card-body border-bottom">
            <div class="row g-2">
                <div class="col-md-5"><input type="search" wire:model.live.debounce.300ms="q" class="form-control" placeholder="Search name or SKU..."></div>
                <div class="col-md-4">
                    <select wire:model.live="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center" wire:loading><span class="spinner-border spinner-border-sm text-primary"></span></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3" style="width:70px">Image</th><th>Name</th><th>Categories</th><th>Price</th><th>Stock</th><th>Status</th><th>SEO</th><th class="text-end pe-3">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="prod-{{ $product->id }}">
                            <td class="ps-3">@if ($product->imageUrl())<img src="{{ $product->imageUrl() }}" alt="" width="50" height="50" style="object-fit:cover" class="rounded" loading="lazy">@endif</td>
                            <td>{{ $product->name }}@if ($product->sku)<br><small class="text-muted">SKU: {{ $product->sku }}</small>@endif</td>
                            <td>@foreach ($product->categories as $c)<span class="badge text-bg-light border">{{ $c->name }}</span> @endforeach</td>
                            <td>{{ $product->hasPrice() ? $product->formatPrice($product->currentPrice()) : 'On request' }}</td>
                            <td><button wire:click="toggleStock({{ $product->id }})" class="badge border-0 {{ $product->in_stock ? 'text-bg-success' : 'text-bg-danger' }}" title="Click to toggle">{{ $product->in_stock ? 'In stock' : 'Out' }}</button></td>
                            <td><button wire:click="toggleActive({{ $product->id }})" class="badge border-0 {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}" title="Click to toggle">{{ $product->is_active ? 'Active' : 'Hidden' }}</button></td>
                            <td><x-seo.quick-button :product="$product" :enabled="$aiEnabled" :done="$seoDone[$product->id] ?? null" :error="$seoErrors[$product->id] ?? null" /></td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="{{ route('shop.show', $product->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('admin.products.edit', $product) }}" wire:navigate class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                                <button wire:click="delete({{ $product->id }})" wire:confirm="Delete this product?" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
</div>
