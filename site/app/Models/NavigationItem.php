<?php

namespace App\Models;

use App\Services\InquiryAvailability;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    public const TARGETS = [
        'home' => 'الرئيسية', 'about' => 'من نحن', 'services' => 'الخدمات',
        'pricing' => 'الأسعار', 'work' => 'الأعمال', 'articles' => 'المقالات',
        'faqs' => 'الأسئلة الشائعة', 'contact' => 'تواصل', 'quote.create' => 'طلب عرض سعر',
        'section:top' => 'قسم بداية الرئيسية', 'section:proof' => 'قسم لماذا نحن',
        'section:services' => 'قسم الخدمات', 'section:pricing' => 'صفحة الأسعار',
        'section:demos' => 'قسم التجارب', 'section:work' => 'قسم الأعمال',
        'section:journal' => 'قسم المقالات', 'section:reel' => 'قسم الفيديو',
        'section:contact' => 'قسم التواصل',
    ];

    protected $fillable = ['location', 'target', 'label_ar', 'label_en', 'sort_order', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function publicUrl(string $locale = 'ar', bool $onHome = false): ?string
    {
        if (! array_key_exists($this->target, self::TARGETS)) {
            return null;
        }
        if ($this->target === 'quote.create' && ! app(InquiryAvailability::class)->enabled($locale)) {
            return null;
        }
        if ($this->target === 'section:pricing') {
            return route('pricing', ['locale' => $locale], false);
        }
        if (str_starts_with($this->target, 'section:')) {
            $anchor = substr($this->target, 8);

            return ($onHome ? '' : ($locale === 'en' ? route('home.en', [], false) : '/')).'#'.$anchor;
        }

        return $this->target === 'home' ? ($locale === 'en' ? route('home.en', [], false) : '/') : route($this->target, ['locale' => $locale], false);
    }
}
