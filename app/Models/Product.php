<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\HasImages;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasImages;

    protected $fillable = [
        'external_id', 'name', 'slug', 'sku', 'short_description', 'description',
        'price', 'sale_price', 'currency', 'image', 'gallery', 'in_stock', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'gallery' => 'array',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }


    /** Main image plus gallery, every URL guaranteed to resolve (placeholder if nothing usable). */
    public function galleryUrls(): array
    {
        $urls = collect(array_merge([$this->image], $this->gallery ?? []))
            ->filter()
            ->map(fn ($path) => static::resolveImage($path))
            ->reject(fn ($url) => $url === asset('img/placeholder.svg'))
            ->unique()
            ->values()
            ->all();

        return $urls ?: [static::resolveImage(null)];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasPrice(): bool
    {
        return (float) $this->price > 0;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null
            && (float) $this->sale_price > 0
            && (float) $this->sale_price < (float) $this->price;
    }

    public function currentPrice(): float
    {
        return (float) ($this->isOnSale() ? $this->sale_price : $this->price);
    }

    public function formatPrice(float $amount): string
    {
        return $this->currency.' '.number_format($amount, 0);
    }

    /** First paragraph of the short description as plain text (the rest is contact boilerplate). */
    public function summary(int $limit = 280): string
    {
        $html = (string) $this->short_description;

        if (preg_match('#<p\b[^>]*>(.*?)</p>#is', $html, $match)) {
            $html = $match[1];
        }

        $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5)));

        return Str::limit($text, $limit);
    }

    /** The pre-filled order message: product name, short description, price, page link and image link. */
    public function whatsappMessage(): string
    {
        $lines = [
            'Hello '.config('app.name').', I would like to order this product:',
            '',
            '*'.$this->name.'*',
        ];

        if ($summary = $this->summary()) {
            $lines[] = $summary;
        }

        if ($this->hasPrice()) {
            $lines[] = 'Price: '.$this->formatPrice($this->currentPrice());
        }

        if ($this->sku) {
            $lines[] = 'SKU: '.$this->sku;
        }

        $lines[] = '';
        $lines[] = 'Product page: '.route('shop.show', $this->slug);
        $lines[] = 'Image: '.$this->imageUrl();

        return implode("\n", $lines);
    }

    public function whatsappUrl(): string
    {
        return 'https://wa.me/'.config('site.whatsapp').'?text='.rawurlencode($this->whatsappMessage());
    }
}
