<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\ContactChannels\ContactChannelResource;
use App\Filament\Resources\HomepageContents\HomepageContentResource;
use App\Filament\Resources\HomepageHeroes\HomepageHeroResource;
use App\Filament\Resources\HomepageVideos\HomepageVideoResource;
use App\Filament\Resources\InquirySettings\InquirySettingResource;
use App\Filament\Resources\NavigationItems\NavigationItemResource;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\PageContents\PageContentResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\SeoSettings\SeoSettingResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Services\InquiryAvailability;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HomepageManagement extends StatsOverviewWidget
{
    protected ?string $heading = 'إدارة الصفحة الرئيسية';

    protected ?string $description = 'اضغط على الجزء المطلوب. النصوص والتصميم المرئي منفصلان عن بيانات الخدمات والمقالات والأسعار.';

    protected function getStats(): array
    {
        $intake = app(InquiryAvailability::class);
        $intakeStatus = $intake->enabled('ar') && $intake->enabled('en')
            ? 'النموذج نشط'
            : ($intake->requested() ? 'بانتظار سياسة الخصوصية' : 'مغلق');

        return [
            Stat::make('المقدمة', 'العنوان والوصف')->url(HomepageHeroResource::getUrl('index')),
            Stat::make('نصوص الأقسام', 'لماذا نحن · التجارب · التواصل')->url(HomepageContentResource::getUrl('index')),
            Stat::make('الخدمات', 'البطاقات وتفاصيل الخدمات')->url(ServiceResource::getUrl('index')),
            Stat::make('الأسعار', 'باقات البداية')->url(PackageResource::getUrl('index')),
            Stat::make('المشاريع', 'الأعمال المعروضة')->url(ProjectResource::getUrl('index')),
            Stat::make('المقالات', 'آخر المقالات المنشورة')->url(ArticleResource::getUrl('index')),
            Stat::make('الفيديو', 'ملف أو رابط العرض')->url(HomepageVideoResource::getUrl('index')),
            Stat::make('التواصل', 'الروابط الرسمية')->url(ContactChannelResource::getUrl('index')),
            Stat::make('طلبات المشاريع', $intakeStatus)
                ->description('إدارة تفعيل النموذج')->url(InquirySettingResource::getUrl('index')),
            Stat::make('المنيو والفوتر', 'الروابط والترتيب والظهور')->url(NavigationItemResource::getUrl('index')),
            Stat::make('الفهرسة وSEO', 'عناوين ووصف وفهرسة')->url(SeoSettingResource::getUrl('index')),
            Stat::make('محتوى الصفحات', 'مقدمات الصفحات الداخلية')->url(PageContentResource::getUrl('index')),
            Stat::make('معاينة الرئيسية', 'افتح الموقع كما يراه الزائر')->url(route('home')),
            Stat::make('معاينة الأسعار', 'المسودات والحاسبة قبل النشر')->url(route('preview.pricing', ['locale' => 'ar'])),
            Stat::make('خريطة الموقع', 'تحقق من الروابط القابلة للفهرسة')->url(route('sitemap')),
        ];
    }
}
