<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['slug', 'category', 'title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en', 'featured', 'featured_on_home', 'published', 'sort_order'];

    protected function casts(): array
    {
        return ['featured' => 'boolean', 'featured_on_home' => 'boolean', 'published' => 'boolean'];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
