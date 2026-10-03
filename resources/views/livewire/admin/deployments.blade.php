<div @if ($active) wire:poll.2s @endif>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fa fa-rocket me-2"></i> Deploy latest changes</h5></div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Pulls the newest code from GitHub (<code>{{ $branch ?? 'no branch' }}</code>) and updates this site.
                        Currently on <code>{{ $commit ? substr($commit, 0, 7) : 'unknown' }}</code>.
                    </p>

                    <div class="mb-3">
                        <div class="form-check"><input class="form-check-input" type="checkbox" wire:model.live="dependencies" id="o1"><label class="form-check-label" for="o1">Install PHP dependencies <small class="text-muted">(composer)</small></label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" wire:model.live="migrate" id="o2"><label class="form-check-label" for="o2">Run database migrations</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" wire:model.live="seed" id="o3"><label class="form-check-label" for="o3">Run seeders <small class="text-muted">(adds missing catalogue items only; your edits are kept)</small></label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" wire:model.live="caches" id="o4"><label class="form-check-label" for="o4">Clear and rebuild caches</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" wire:model.live="sitemap" id="o5"><label class="form-check-label" for="o5">Regenerate the sitemap</label></div>
                    </div>

                    <details class="mb-3 small">
                        <summary class="text-muted">Commands that will run</summary>
                        <ol class="mt-2 mb-0 ps-3">@foreach ($steps as [$label, $command])<li><code>{{ $label }}</code></li>@endforeach</ol>
                    </details>

                    <form wire:submit="deploy">
                        <label class="form-label">Confirm with your password</label>
                        <div class="input-group">
                            <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" @disabled($active)>
                            <button class="btn btn-primary" @disabled($active) wire:loading.attr="disabled" wire:target="deploy"
                                wire:confirm="Deploy the latest changes to the live site now?">
                                <span wire:loading wire:target="deploy" class="spinner-border spinner-border-sm me-1"></span>
                                {{ $active ? 'Deploying…' : 'Deploy now' }}
                            </button>
                        </div>
                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </form>

                    @if ($notice)<div class="alert alert-info mt-3 mb-0 py-2 small">{{ $notice }}</div>@endif
                </div>
            </div>

            @if (! $canLaunch)
                <div class="alert alert-warning small">
                    <strong>One-time setup needed.</strong> This server blocks the website from starting background processes,
                    so deployments are queued and picked up by a scheduled task. In cPanel &rarr; <em>Cron Jobs</em> add (every minute):
                    <pre class="bg-light border rounded p-2 mt-2 mb-0 small user-select-all" style="white-space: pre-wrap">{{ $cronLine }}</pre>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header"><strong>History</strong></div>
                <div class="list-group list-group-flush">
                    @forelse ($history as $item)
                        <button type="button" wire:click="select({{ $item->id }})" wire:key="d-{{ $item->id }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selected?->id === $item->id ? 'active' : '' }}">
                            <span>
                                #{{ $item->id }} &middot; {{ $item->created_at->format('M d, H:i') }}
                                <small class="d-block {{ $selected?->id === $item->id ? '' : 'text-muted' }}">
                                    {{ $item->user?->name ?? 'System' }}
                                    @if ($item->to_commit && $item->from_commit && $item->to_commit !== $item->from_commit)
                                        &middot; {{ substr($item->from_commit, 0, 7) }} &rarr; {{ substr($item->to_commit, 0, 7) }}
                                    @endif
                                </small>
                            </span>
                            <span class="badge {{ match ($item->status) { 'success' => 'text-bg-success', 'failed' => 'text-bg-danger', 'running' => 'text-bg-warning', default => 'text-bg-secondary' } }}">{{ ucfirst($item->status) }}</span>
                        </button>
                    @empty
                        <div class="list-group-item text-muted">No deployments yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $selected ? 'Log for deployment #'.$selected->id : 'Log' }}</strong>
                    @if ($selected)
                        <span class="small text-muted">
                            @if ($selected->duration() !== null){{ $selected->duration() }}s &middot; @endif
                            <span class="badge {{ match ($selected->status) { 'success' => 'text-bg-success', 'failed' => 'text-bg-danger', 'running' => 'text-bg-warning', default => 'text-bg-secondary' } }}">{{ ucfirst($selected->status) }}</span>
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <pre class="m-0 p-3 bg-dark text-light small" style="min-height: 420px; max-height: 70vh; overflow: auto; white-space: pre-wrap; word-break: break-word">{{ $selected?->output ?: 'Nothing to show yet. Run a deployment to see its log here.' }}@if ($selected?->status === 'pending')

Waiting to start…@endif</pre>
                </div>
            </div>
        </div>
    </div>
</div>
