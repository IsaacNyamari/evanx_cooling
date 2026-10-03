<div>
    <div class="container-fluid bg-light py-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" wire:navigate>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop') }}" wire:navigate>Shop</a></li>
                    @if ($cat = $product->categories->first())
                        <li class="breadcrumb-item"><a href="{{ route('shop', ['category' => $cat->slug]) }}" wire:navigate>{{ $cat->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6">
                @if ($gallery)
                    <img src="{{ $gallery[min($activeImage, count($gallery) - 1)] }}" alt="{{ $product->name }}" class="img-fluid rounded border w-100 bg-light" style="max-height: 480px; object-fit: contain">
                    @if (count($gallery) > 1)
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            @foreach ($gallery as $i => $img)
                                <img wire:key="g-{{ $i }}" wire:click="setImage({{ $i }})" src="{{ $img }}" alt="" width="80" height="80"
                                    class="rounded border {{ $i === $activeImage ? 'border-primary border-2' : '' }}" style="object-fit: cover; cursor: pointer">
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
            <div class="col-md-6">
                <h1 class="h2 mb-3">{{ $product->name }}</h1>
                @if ($product->hasPrice())
                    <p class="h4 text-primary">
                        {{ $product->formatPrice($product->currentPrice()) }}
                        @if ($product->isOnSale())<del class="text-muted fs-6 ms-2">{{ $product->formatPrice((float) $product->price) }}</del>@endif
                    </p>
                @else
                    <p class="h5 text-muted">Price on request</p>
                @endif
                <p>
                    <span class="badge {{ $product->in_stock ? 'text-bg-success' : 'text-bg-danger' }}">{{ $product->in_stock ? 'In stock' : 'Out of stock' }}</span>
                    @if ($product->sku)<span class="text-muted small ms-2">SKU: {{ $product->sku }}</span>@endif
                </p>
                <div class="mb-4">{!! $product->short_description !!}</div>

                <div class="d-flex gap-2 flex-wrap mb-4">
                    <a href="{{ $product->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp me-2"></i>Order via WhatsApp</a>
                    <a href="tel:{{ config('site.phone') }}" class="btn btn-outline-primary"><i class="fa fa-phone me-2"></i>Call {{ config('site.phone') }}</a>
                </div>

                @if ($product->categories->isNotEmpty())
                    <p class="small text-muted mb-0">Categories:
                        @foreach ($product->categories as $c)
                            <a href="{{ route('shop', ['category' => $c->slug]) }}" wire:navigate>{{ $c->name }}</a>{{ $loop->last ? '' : ',' }}
                        @endforeach
                    </p>
                @endif
            </div>
        </div>

        @if ($product->description)
            <div class="mt-5 pt-4 border-top">
                <h3 class="mb-3">Product details</h3>
                {!! $product->description !!}
            </div>
        @endif

        @if ($related->isNotEmpty())
            <div class="mt-5 pt-4 border-top">
                <h3 class="mb-4">Related products</h3>
                <div class="row g-4">
                    @foreach ($related as $item)
                        <div class="col-6 col-md-3" wire:key="r-{{ $item->id }}">
                            <a href="{{ route('shop.show', $item->slug) }}" wire:navigate class="card h-100 text-decoration-none text-dark border">
                                <div class="ratio ratio-4x3 bg-light">@if ($item->imageUrl())<img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" loading="lazy" style="object-fit: contain">@endif</div>
                                <div class="card-body"><h6 class="mb-0">{{ $item->name }}</h6></div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
