<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = ['article_category_id', 'slug', 'title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en', 'cover_path', 'published', 'published_at'];

    public function coverUrl(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        return str_starts_with($this->cover_path, 'assets/articles/')
            ? asset($this->cover_path)
            : asset('storage/'.$this->cover_path);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('published', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    protected function casts(): array
    {
        return ['published' => 'boolean', 'published_at' => 'datetime'];
    }
}
