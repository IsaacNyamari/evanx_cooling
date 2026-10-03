<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fa fa-sitemap me-2"></i> Sitemap</h5></div>
            <div class="card-body">
                @if ($message)<div class="alert alert-success py-2">{{ $message }}</div>@endif
                @if ($error)<div class="alert alert-danger py-2">{{ $error }}</div>@endif

                <label class="form-label fw-semibold">Sitemap link (give this to Google)</label>
                <div class="input-group mb-3" x-data="{ copied: false }">
                    <input type="text" readonly class="form-control" value="{{ $url }}" id="sitemap-url" onclick="this.select()">
                    <button type="button" class="btn btn-outline-primary"
                        @click="navigator.clipboard.writeText(document.getElementById('sitemap-url').value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                        <span x-show="!copied"><i class="fa fa-copy me-1"></i>Copy</span>
                        <span x-show="copied" x-cloak><i class="fa fa-check me-1"></i>Copied</span>
                    </button>
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="fa fa-external-link-alt me-1"></i>Open</a>
                </div>

                <dl class="row mb-4 small">
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if (! $exists)
                            <span class="badge text-bg-warning">Not generated yet</span>
                        @elseif ($stale)
                            <span class="badge text-bg-warning">Out of date</span> products changed since it was generated
                        @else
                            <span class="badge text-bg-success">Up to date</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">Last generated</dt>
                    <dd class="col-sm-8">{{ $generatedAt ? $generatedAt->format('M d, Y H:i').' ('.$generatedAt->diffForHumans().')' : '—' }}</dd>
                    <dt class="col-sm-4">URLs listed</dt>
                    <dd class="col-sm-8">{{ $exists ? $count : '—' }}</dd>
                </dl>

                <div class="d-flex gap-2 flex-wrap">
                    <button wire:click="generate" wire:loading.attr="disabled" class="btn btn-primary">
                        <span wire:loading wire:target="generate" class="spinner-border spinner-border-sm me-1"></span>
                        <i class="fa fa-sync-alt me-1" wire:loading.remove wire:target="generate"></i>{{ $exists ? 'Regenerate sitemap' : 'Generate sitemap' }}
                    </button>
                    <a href="{{ route('admin.sitemap.download') }}" class="btn btn-outline-primary"><i class="fa fa-download me-1"></i>Download sitemap.xml</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header"><strong>What is included</strong></div>
            <div class="card-body small">
                <ul class="mb-0">
                    <li>The home page, About, Services (and the six service pages), Contact and the Shop.</li>
                    <li>Every <strong>visible</strong> product page, with its last-updated date and main image. Hidden products are left out.</li>
                    <li>Admin, login and password pages are never listed, and <code>robots.txt</code> keeps crawlers out of them.</li>
                    <li>Category filters are not listed on purpose: they are the same page as the Shop to Google, and listing them would create duplicates.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header"><strong>Add it to Google (one time)</strong></div>
            <div class="card-body small">
                <ol class="mb-3 ps-3">
                    <li class="mb-2">Click <strong>Generate sitemap</strong> above (or <strong>Regenerate</strong> after adding products).</li>
                    <li class="mb-2">Open <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a> and choose your property for this site. If you don't have one, add it with <em>URL prefix</em> and verify it.</li>
                    <li class="mb-2">In the left menu choose <strong>Sitemaps</strong>.</li>
                    <li class="mb-2">Under <em>Add a new sitemap</em>, type <code>sitemap.xml</code> (Search Console already shows your domain before the box) and press <strong>Submit</strong>.</li>
                    <li>The status should change to <strong>Success</strong>. Google then re-reads it on its own, so you only submit once.</li>
                </ol>
                <p class="text-muted mb-0">Bing and others find it automatically through <a href="{{ url('/robots.txt') }}" target="_blank" rel="noopener">robots.txt</a>.
                    The sitemap is also rebuilt after every deployment, and you can regenerate it here any time.</p>
            </div>
        </div>
    </div>
</div>
