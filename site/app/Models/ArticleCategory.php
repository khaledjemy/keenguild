<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    protected $fillable = ['slug', 'name_ar', 'name_en', 'description_ar', 'description_en', 'sort_order'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
