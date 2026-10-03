<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    use HasImages;

    protected $fillable = ['key', 'meta_title', 'meta_description', 'image', 'noindex'];

    protected $casts = ['noindex' => 'boolean'];
}
