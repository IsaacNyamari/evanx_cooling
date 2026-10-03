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
}
