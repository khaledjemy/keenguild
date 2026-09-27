<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PageContent extends Model
{
    public const PAGES = [
        'about' => 'من نحن', 'services' => 'الخدمات', 'pricing' => 'الأسعار',
        'work' => 'الأعمال', 'articles' => 'المقالات', 'faqs' => 'الأسئلة الشائعة',
        'contact' => 'تواصل معنا',
    ];

    protected $fillable = [
        'page_key', 'heading_ar', 'heading_en', 'intro_ar', 'intro_en',
        'meta_description_ar', 'meta_description_en', 'extra_copy',
    ];

    protected function casts(): array
    {
        return ['extra_copy' => 'array'];
    }

    public static function forPage(string $key): ?self
    {
        if (! array_key_exists($key, self::PAGES) || ! Schema::hasTable('page_contents')) {
            return null;
        }

        $cacheKey = 'page_content_'.$key;
        if (request()->attributes->has($cacheKey)) {
            return request()->attributes->get($cacheKey);
        }

        $record = self::query()->where('page_key', $key)->first();
        request()->attributes->set($cacheKey, $record);

        return $record;
    }

    public static function text(string $page, string $field, string $locale, string $fallback): string
    {
        $value = self::forPage($page)?->{$field.'_'.$locale};

        return filled($value) ? $value : $fallback;
    }

    public static function extra(string $page, string $key, string $locale, string $fallback): string
    {
        $value = (self::forPage($page)?->extra_copy ?? [])[$key.'_'.$locale] ?? null;

        return filled($value) ? $value : $fallback;
    }
}
