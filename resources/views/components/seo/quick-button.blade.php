@props(['product', 'enabled' => false, 'done' => null, 'error' => null])
@php
    $has = filled($product->meta_title) && filled($product->meta_description);
    $target = 'quickSeo('.$product->id.')';
@endphp
<div class="d-inline-flex flex-column align-items-start">
    <div class="d-flex align-items-center gap-1">
        <span wire:loading.remove wire:target="{{ $target }}">
            @if ($done)
                <span class="badge text-bg-success" title="{{ $done }}"><i class="fa fa-check me-1"></i>Done</span>
            @elseif ($has)
                <span class="badge text-bg-primary" title="{{ $product->meta_title }}">Custom</span>
            @else
                <span class="badge text-bg-light border" title="Using the automatic title and description">Auto</span>
            @endif
        </span>

        @if ($enabled)
            <button type="button" wire:click="quickSeo({{ $product->id }})" wire:loading.attr="disabled" wire:target="{{ $target }}"
                @if ($has || $done) wire:confirm="Replace the current SEO title and description with new AI-written text?" @endif
                class="btn btn-sm btn-outline-primary text-nowrap" title="Write the Google title and description with AI and save them">
                <span wire:loading.remove wire:target="{{ $target }}"><i class="fa fa-wand-magic-sparkles me-1"></i>{{ ($has || $done) ? 'Redo' : 'AI SEO' }}</span>
                <span wire:loading wire:target="{{ $target }}"><span class="spinner-border spinner-border-sm me-1"></span>Generating…</span>
            </button>
        @else
            <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" disabled title="Add GEMINI_API_KEY to the .env file to enable AI SEO"><i class="fa fa-wand-magic-sparkles me-1"></i>AI SEO</button>
        @endif
    </div>
    @if ($error)<div class="small text-danger mt-1" style="max-width: 230px; white-space: normal">{{ $error }}</div>@endif
</div>
