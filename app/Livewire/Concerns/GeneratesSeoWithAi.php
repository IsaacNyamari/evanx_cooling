<?php

namespace App\Livewire\Concerns;

use App\Support\Ai\AiSeoException;
use App\Support\Ai\AiSeoWriter;
use Illuminate\Support\Facades\RateLimiter;

/** "Generate with AI" plumbing shared by the product form and the SEO products editor. */
trait GeneratesSeoWithAi
{
    public ?string $aiError = null;

    public ?string $aiMessage = null;

    /**
     * @param  array<string,mixed>  $context  see AiSeoWriter::generate()
     * @return array{title:string,description:string}|null
     */
    protected function writeWithAi(array $context): ?array
    {
        $this->aiError = $this->aiMessage = null;

        // Each click costs money/quota, so cap it per admin (failed attempts count too).
        $key = 'seo-ai:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 12)) {
            $this->aiError = 'That is a lot of requests. Please wait '.RateLimiter::availableIn($key).' seconds.';

            return null;
        }
        RateLimiter::hit($key, 60);

        try {
            $result = app(AiSeoWriter::class)->generate($context);
        } catch (AiSeoException $e) {
            $this->aiError = $e->getMessage();

            return null;
        }

        $this->aiMessage = 'Written by AI. Read it, adjust if you like, then save.';

        return $result;
    }

    /**
     * One click: write the title/description with AI and save them on the product.
     *
     * @return array{ok:bool,title?:string,message?:string}
     */
    protected function generateAndSaveSeo(\App\Models\Product $product): array
    {
        $result = $this->writeWithAi(\App\Support\Ai\AiSeoWriter::contextFor($product->loadMissing('categories')));

        if (! $result) {
            return ['ok' => false, 'message' => $this->aiError];
        }

        $product->update(['meta_title' => $result['title'], 'meta_description' => $result['description']]);

        return ['ok' => true, 'title' => $result['title']."\n".$result['description']];
    }

    protected function aiEnabled(): bool
    {
        return app(AiSeoWriter::class)->enabled();
    }
}
