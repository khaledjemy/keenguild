<?php

namespace App\Support;

use App\Models\PageContent;

class InteractiveConcepts
{
    private const DEFAULTS = [
        'flowboard' => ['name_ar' => 'Flowboard — لوحة فريق', 'name_en' => 'Flowboard — Team board', 'copy_ar' => 'نظّم مهام الفريق وجرّب تغيير حالة كل بطاقة.', 'copy_en' => 'Organize team tasks and try changing each card’s state.', 'type' => 'board'],
        'storefront' => ['name_ar' => 'Storefront — متجر تجريبي', 'name_en' => 'Storefront — Demo store', 'copy_ar' => 'استكشف عرض المنتجات وأضف عنصرًا للسلة التجريبية.', 'copy_en' => 'Explore products and add an item to the demo bag.', 'type' => 'store'],
        'pulse' => ['name_ar' => 'Pulse — لوحة مؤشرات', 'name_en' => 'Pulse — Analytics board', 'copy_ar' => 'بدّل الفترة وشاهد مؤشرات توضيحية تتغير أمامك.', 'copy_en' => 'Change the period and explore sample metrics.', 'type' => 'analytics'],
    ];

    public static function published(): array
    {
        $overrides = PageContent::forPage('work')?->extra_copy ?? [];
        $concepts = [];

        foreach (self::DEFAULTS as $slug => $default) {
            if (($overrides['concept_'.$slug.'_visible'] ?? '1') === '0') {
                continue;
            }

            foreach (['name_ar', 'name_en', 'copy_ar', 'copy_en'] as $field) {
                $value = $overrides['concept_'.$slug.'_'.$field] ?? null;
                if (is_string($value) && trim($value) !== '') {
                    $default[$field] = trim($value);
                }
            }

            $concepts[$slug] = $default;
        }

        return $concepts;
    }
}
