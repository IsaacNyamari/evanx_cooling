@props(['enabled' => false, 'confirm' => false, 'error' => null, 'message' => null])
<div class="mb-3">
    @if ($enabled)
        <button type="button" wire:click="generateSeo" wire:loading.attr="disabled" wire:target="generateSeo"
            @if ($confirm) wire:confirm="Replace the current title and description with AI-written text?" @endif
            class="btn btn-sm btn-outline-primary">
            <span wire:loading.remove wire:target="generateSeo"><i class="fa fa-wand-magic-sparkles me-1"></i>Generate with AI</span>
            <span wire:loading wire:target="generateSeo"><span class="spinner-border spinner-border-sm me-1"></span>Writing…</span>
        </button>
        <span class="small text-muted ms-1">Uses the name, category and description.</span>
    @else
        <button type="button" class="btn btn-sm btn-outline-secondary" disabled><i class="fa fa-wand-magic-sparkles me-1"></i>Generate with AI</button>
        <div class="small text-muted mt-1">Not set up: add <code>GEMINI_API_KEY</code> to the server's .env file (free key at aistudio.google.com/apikey).</div>
    @endif

    @if ($error)<div class="alert alert-danger py-2 px-3 small mt-2 mb-0">{{ $error }}</div>@endif
    @if ($message)<div class="alert alert-success py-2 px-3 small mt-2 mb-0"><i class="fa fa-circle-check me-1"></i>{{ $message }}</div>@endif
</div>
