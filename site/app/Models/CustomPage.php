<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomPage extends Model
{
    protected $fillable = [
        'slug', 'title_ar', 'title_en', 'summary_ar', 'summary_en',
        'body_ar', 'body_en', 'meta_description_ar', 'meta_description_en', 'published',
    ];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('published', true)
            ->whereRaw("TRIM(title_ar) <> ''")
            ->whereRaw("TRIM(title_en) <> ''")
            ->whereRaw("TRIM(body_ar) <> ''")
            ->whereRaw("TRIM(body_en) <> ''");
    }
}
