<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LegalPage extends Model
{
    protected $fillable = ['type', 'title_ar', 'title_en', 'body_ar', 'body_en', 'published'];

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('published', true)
            ->whereNotNull('title_ar')->whereRaw("TRIM(title_ar) <> ''")
            ->whereNotNull('title_en')->whereRaw("TRIM(title_en) <> ''")
            ->whereNotNull('body_ar')->whereRaw("TRIM(body_ar) <> ''")
            ->whereNotNull('body_en')->whereRaw("TRIM(body_en) <> ''");
    }

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
