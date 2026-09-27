<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\PriceOptions\PriceOptionResource;
use App\Filament\Resources\PricingSettings\PricingSettingResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PricingManagement extends StatsOverviewWidget
{
    protected ?string $heading = 'آلية التسعير والمعاينة';

    protected ?string $description = 'التقدير = (سعر بداية الباقة + الإضافات) × معامل التعقيد × معامل الاستعجال، ثم يُقرب لأقرب 500 ج.م. يظهر نطاق استرشادي حسب هامش النطاق. السعر النهائي يحتاج مراجعة المتطلبات.';

    protected function getStats(): array
    {
        return [
            Stat::make('الباقات وأسعار البداية', 'تعديل السعر والنطاق')->url(PackageResource::getUrl('index')),
            Stat::make('الإضافات', 'ثابتة أو لكل وحدة أو نسبة')->url(PriceOptionResource::getUrl('index')),
            Stat::make('المعاملات', 'التعقيد والاستعجال والهامش')->url(PricingSettingResource::getUrl('index')),
            Stat::make('معاينة قبل النشر', 'الأسعار المسودة بالعربية')->url(route('preview.pricing', ['locale' => 'ar'])),
        ];
    }
}
