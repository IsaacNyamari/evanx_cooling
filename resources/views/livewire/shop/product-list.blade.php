<div>
    <div class="container-fluid bg-light py-5">
        <div class="container">
            <h1 class="display-6 mb-2">{{ $current ? $current->name : 'Shop' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" wire:navigate>Home</a></li>
                    <li class="breadcrumb-item"><a href="#" wire:click.prevent="selectCategory('')">Shop</a></li>
                    @if ($current)<li class="breadcrumb-item active">{{ $current->name }}</li>@endif
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="input-group mb-4">
                    <input type="search" wire:model.live.debounce.300ms="q" class="form-control" placeholder="Search products...">
                    <span class="input-group-text bg-primary text-white"><i class="fa fa-search"></i></span>
                </div>

                <h5 class="mb-3">Categories</h5>
                <div class="list-group">
                    <button wire:click="selectCategory('')" class="list-group-item list-group-item-action {{ $current ? '' : 'active' }}">All products</button>
                    @foreach ($categories as $cat)
                        <button wire:key="c-{{ $cat->id }}" wire:click="selectCategory('{{ $cat->slug }}')"
                            class="list-group-item list-group-item-action {{ $current?->id === $cat->id ? 'active' : '' }}">{{ $cat->name }}</button>
                        @foreach ($cat->children as $child)
                            <button wire:key="c-{{ $child->id }}" wire:click="selectCategory('{{ $child->slug }}')"
                                class="list-group-item list-group-item-action ps-5 small {{ $current?->id === $child->id ? 'active' : '' }}">{{ $child->name }}</button>
                        @endforeach
                    @endforeach
                </div>
            </aside>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <span class="text-muted">
                        {{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}@if ($q !== '') matching “{{ $q }}”@endif
                        @if ($q !== '' || $category !== '') · <a href="#" wire:click.prevent="clearFilters">Clear filters</a>@endif
                        <span wire:loading class="spinner-border spinner-border-sm text-primary ms-2"></span>
                    </span>
                    <select wire:model.live="sort" class="form-select form-select-sm w-auto" aria-label="Sort">
                        <option value="name_asc">Name A–Z</option>
                        <option value="name_desc">Name Z–A</option>
                    </select>
                </div>

                <div class="row g-4" wire:loading.class="opacity-50">
                    @forelse ($products as $product)
                        <div class="col-sm-6 col-xl-4" wire:key="p-{{ $product->id }}">
                            <div class="card h-100 border shadow-sm">
                                <a href="{{ route('shop.show', $product->slug) }}" wire:navigate class="ratio ratio-4x3 bg-light">
                                    @if ($product->imageUrl())
                                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy" class="card-img-top" style="object-fit: contain">
                                    @endif
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <small class="text-muted">{{ $product->categories->first()?->name }}</small>
                                    <h6 class="card-title mt-1"><a href="{{ route('shop.show', $product->slug) }}" wire:navigate class="text-dark text-decoration-none">{{ $product->name }}</a></h6>
                                    <div class="mt-auto pt-2">
                                        @if ($product->hasPrice())
                                            <div class="mb-2">
                                                <strong class="text-primary">{{ $product->formatPrice($product->currentPrice()) }}</strong>
                                                @if ($product->isOnSale())<del class="text-muted small ms-1">{{ $product->formatPrice((float) $product->price) }}</del>@endif
                                            </div>
                                        @else
                                            <div class="mb-2 text-muted small">Price on request</div>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ $product->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-sm btn-success flex-grow-1"><i class="fab fa-whatsapp me-1"></i>Order</a>
                                            <a href="{{ route('shop.show', $product->slug) }}" wire:navigate class="btn btn-sm btn-outline-primary flex-grow-1">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">No products found.</div>
                    @endforelse
                </div>

                <div class="mt-5 d-flex justify-content-center">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</div>
