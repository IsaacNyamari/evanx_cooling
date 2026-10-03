<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6"><input type="search" wire:model.live.debounce.300ms="q" class="form-control form-control-sm" placeholder="Search products..."></div>
                    <div class="col-md-4">
                        <select wire:model.live="filter" class="form-select form-select-sm">
                            <option value="">All products</option>
                            <option value="issues">Needs improvement</option>
                            <option value="custom">Custom SEO only</option>
                            <option value="noimage">Missing photo</option>
                        </select>
                    </div>
                    <div class="col-auto" wire:loading><span class="spinner-border spinner-border-sm text-primary"></span></div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th class="ps-3">Product</th><th>Title on Google</th><th class="text-center">Title</th><th class="text-center">Desc.</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($rows as $row)
                            @php($p = $row['product'])
                            <tr wire:key="sp-{{ $p->id }}" class="{{ $editing && $editing['product']->id === $p->id ? 'table-active' : '' }}">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $p->imageUrl() }}" alt="" width="38" height="38" class="rounded" style="object-fit: cover" loading="lazy">
                                        <div><div class="fw-semibold small">{{ $p->name }}</div>
                                            <span class="badge {{ $row['custom'] ? 'text-bg-primary' : 'text-bg-light border' }}">{{ $row['custom'] ? 'Custom' : 'Auto' }}</span></div>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($row['title'], 52) }}</td>
                                @foreach ([$row['title_rating'], $row['description_rating']] as $rating)
                                    <td class="text-center"><span class="badge text-bg-{{ ['good' => 'success', 'warn' => 'warning', 'bad' => 'danger'][$rating['status']] }}" title="{{ $rating['message'] }}">{{ $rating['length'] }}</span></td>
                                @endforeach
                                <td class="text-end pe-3"><button wire:click="edit({{ $p->id }})" class="btn btn-sm btn-outline-primary">Edit</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No products match.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())<div class="card-footer">{{ $rows->links() }}</div>@endif
        </div>
        <p class="small text-muted mt-2">Every product gets a good title and description automatically. Only change them for products you want to push on Google.</p>
    </div>

    <div class="col-lg-5">
        @if ($message)<div class="alert alert-success py-2">{{ $message }}</div>@endif

        @if ($editing)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <strong class="text-truncate">{{ $editing['product']->name }}</strong>
                    <button wire:click="close" class="btn-close btn-close-white" aria-label="Close"></button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Page title</label>
                        <input type="text" wire:model.live.debounce.300ms="meta_title" class="form-control @error('meta_title') is-invalid @enderror" placeholder="{{ $editing['auto_title'] }}">
                        <x-seo.counter :text="$editing['title']" kind="title" />
                        @error('meta_title') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meta description</label>
                        <textarea wire:model.live.debounce.300ms="meta_description" rows="3" class="form-control @error('meta_description') is-invalid @enderror" placeholder="{{ $editing['auto_description'] }}"></textarea>
                        <x-seo.counter :text="$editing['description']" kind="description" />
                        @error('meta_description') <div class="text-danger small">{{ $message }}</div> @enderror
                        <div class="form-text">Empty = the automatic text shown in grey. Tip: mention what it is, where you sell it (Nairobi, Kenya) and a reason to buy.</div>
                    </div>

                    <div class="mb-3"><div class="small fw-semibold text-muted mb-1">Google result preview</div><x-seo.serp :title="$editing['title']" :description="$editing['description']" :url="$editing['url']" /></div>
                    <div class="mb-4"><div class="small fw-semibold text-muted mb-1">WhatsApp / Facebook preview <span class="fw-normal">(uses the product photo)</span></div><x-seo.social :title="$editing['title']" :description="$editing['description']" :image="$editing['image']" :url="$editing['url']" /></div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="btn btn-primary"><span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>Save</button>
                        <button wire:click="useSuggestion" class="btn btn-outline-primary" title="Fill the boxes with the automatic text so you can tweak it">Use suggestion</button>
                        <button wire:click="resetToAuto" wire:confirm="Remove your custom text for this product?" class="btn btn-outline-secondary">Reset to auto</button>
                        <a href="{{ $editing['url'] }}" target="_blank" rel="noopener" class="btn btn-outline-secondary ms-auto" title="View product page"><i class="fa fa-external-link-alt"></i></a>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5"><i class="fa fa-arrow-left me-1"></i> Choose a product to edit how it appears on Google and when shared.</div></div>
        @endif
    </div>
</div>
