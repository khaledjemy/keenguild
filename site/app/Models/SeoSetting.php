<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SeoSetting extends Model
{
    protected $fillable = [
        'home_title_ar', 'home_title_en', 'default_description_ar', 'default_description_en',
        'social_image_path', 'allow_indexing',
    ];

    protected function casts(): array
    {
        return ['allow_indexing' => 'boolean'];
    }

    public static function current(): ?self
    {
        return Schema::hasTable('seo_settings') ? self::query()->first() : null;
    }
}
