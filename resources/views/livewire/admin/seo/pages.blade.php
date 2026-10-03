<div class="row g-4">
    <div class="col-lg-6">
        @foreach ($groups as $group => $items)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header"><strong>{{ $group }}</strong></div>
                <div class="list-group list-group-flush">
                    @foreach ($items as $page)
                        <button type="button" wire:click="edit('{{ $page['key'] }}')" wire:key="pg-{{ $page['key'] }}"
                            class="list-group-item list-group-item-action {{ $editing === $page['key'] ? 'active' : '' }}">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">{{ $page['label'] }}
                                    @if ($page['custom'])<span class="badge text-bg-light border ms-1">Custom</span>@endif
                                    @if (str_starts_with($page['robots'], 'noindex'))<span class="badge text-bg-danger ms-1">noindex</span>@endif
                                </span>
                                <span class="d-flex gap-1">
                                    @foreach ([$page['title_rating'], $page['description_rating']] as $rating)
                                        <span class="badge text-bg-{{ ['good' => 'success', 'warn' => 'warning', 'bad' => 'danger'][$rating['status']] }}" title="{{ $rating['message'] }}">{{ $loop->first ? 'T' : 'D' }}</span>
                                    @endforeach
                                </span>
                            </div>
                            <div class="small {{ $editing === $page['key'] ? '' : 'text-muted' }} text-truncate">{{ $page['title'] }}</div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endforeach
        <p class="small text-muted mb-4"><span class="badge text-bg-success">T</span> title and <span class="badge text-bg-success">D</span> description length: green is good, amber could be better, red needs fixing.</p>

        <div class="card shadow-sm border-0">
            <div class="card-header"><strong><i class="fa fa-image me-1"></i> Default share image</strong></div>
            <div class="card-body">
                <p class="small text-muted">Shown when your site is shared on WhatsApp, Facebook, X or LinkedIn, for every page that has no image of its own (products use their own photo). Best size: <strong>1200 &times; 630 px</strong>.</p>
                <img src="{{ $siteImage }}" alt="" class="img-fluid rounded border mb-3" style="max-width: 360px">
                <div class="small mb-2">{{ $siteImageCustom ? 'Using your uploaded image.' : 'Using the generated Evanx Cooling card.' }}</div>
                <div class="input-group mb-1">
                    <input type="file" wire:model="site_image_file" accept="image/*" class="form-control @error('site_image_file') is-invalid @enderror">
                    <button wire:click="saveSiteImage" class="btn btn-primary" wire:loading.attr="disabled" wire:target="site_image_file,saveSiteImage">Upload</button>
                </div>
                @error('site_image_file') <div class="text-danger small">{{ $message }}</div> @enderror
                @if ($siteImageCustom)<button wire:click="removeSiteImage" wire:confirm="Go back to the generated share image?" class="btn btn-sm btn-outline-secondary mt-2">Use the generated image</button>@endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        @if ($message)<div class="alert alert-success py-2">{{ $message }}</div>@endif

        @if ($preview)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white"><strong>Edit: {{ $preview['label'] }}</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Page title</label>
                        <input type="text" wire:model.live.debounce.300ms="meta_title" class="form-control @error('meta_title') is-invalid @enderror" placeholder="{{ $preview['default_title'] }}">
                        <x-seo.counter :text="$preview['title']" kind="title" />
                        @error('meta_title') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Meta description</label>
                        <textarea wire:model.live.debounce.300ms="meta_description" rows="3" class="form-control @error('meta_description') is-invalid @enderror" placeholder="{{ $preview['default_description'] }}"></textarea>
                        <x-seo.counter :text="$preview['description']" kind="description" />
                        @error('meta_description') <div class="text-danger small">{{ $message }}</div> @enderror
                        <div class="form-text">Leave a box empty to use the suggested text shown in grey.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Share image for this page <span class="fw-normal text-muted">(optional)</span></label>
                        <input type="file" wire:model="image_file" accept="image/*" class="form-control @error('image_file') is-invalid @enderror">
                        <div wire:loading wire:target="image_file" class="small text-muted">Uploading…</div>
                        @error('image_file') <div class="text-danger small">{{ $message }}</div> @enderror
                        @if ($preview['has_custom_image'])
                            <div class="form-check mt-1"><input class="form-check-input" type="checkbox" wire:model.live="remove_image" id="rm_img"><label class="form-check-label small" for="rm_img">Remove this page's own image</label></div>
                        @endif
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" wire:model.live="noindex" id="noindex">
                        <label class="form-check-label" for="noindex">Hide this page from Google <span class="text-muted small">(noindex)</span></label>
                    </div>

                    <div class="mb-3"><div class="small fw-semibold text-muted mb-1">Google result preview</div><x-seo.serp :title="$preview['title']" :description="$preview['description']" :url="$preview['url']" /></div>
                    <div class="mb-4"><div class="small fw-semibold text-muted mb-1">WhatsApp / Facebook preview</div><x-seo.social :title="$preview['title']" :description="$preview['description']" :image="$preview['image']" :url="$preview['url']" /></div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save,image_file" class="btn btn-primary"><span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>Save</button>
                        <button wire:click="resetToDefault" wire:confirm="Remove your changes and use the default text?" class="btn btn-outline-secondary">Reset to default</button>
                        <a href="{{ $preview['url'] }}" target="_blank" rel="noopener" class="btn btn-outline-secondary ms-auto"><i class="fa fa-external-link-alt me-1"></i>View page</a>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5"><i class="fa fa-arrow-left me-1"></i> Choose a page on the left to edit its title, description and share image.</div></div>
        @endif
    </div>
</div>
