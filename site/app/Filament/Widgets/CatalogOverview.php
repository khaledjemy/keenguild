<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactChannels\ContactChannelResource;
use App\Filament\Resources\LegalPages\LegalPageResource;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\PriceOptions\PriceOptionResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\ContactChannel;
use App\Models\LegalPage;
use App\Models\Package;
use App\Models\PriceOption;
use App\Models\Project;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'حالة محتوى الموقع';

    protected ?string $description = 'الأرقام تخص الموقع العام، والمسودات تبقى مخفية حتى تعتمدها.';

    protected function getStats(): array
    {
        $publishedPackages = Package::query()->where('published', true)
            ->whereHas('service', fn ($query) => $query->where('published', true))->count();
        $visibleProjects = Project::query()->publiclyVisible()->count();
        $validContacts = ContactChannel::query()->where('published', true)->get()
            ->filter(fn (ContactChannel $channel): bool => $channel->publicUrl() !== null)->count();
        $completeLegalPages = LegalPage::query()->whereIn('type', ['privacy', 'terms'])
            ->publiclyVisible()->count();

        return [
            Stat::make('خدمات منشورة', Service::query()->where('published', true)->count())
                ->description('تحرير الخدمات ونطاقها')->url(ServiceResource::getUrl('index')),
            Stat::make('باقات ظاهرة', $publishedPackages)
                ->description('أسعار البداية المعتمدة فقط')->url(PackageResource::getUrl('index')),
            Stat::make('خيارات تسعير مسودة', PriceOption::query()->where('published', false)->count())
                ->description('راجع كل إضافة قبل نشرها')->url(PriceOptionResource::getUrl('index')),
            Stat::make('مشاريع ظاهرة', $visibleProjects)
                ->description('بعد النشر وإذن العرض إن لزم')->url(ProjectResource::getUrl('index')),
            Stat::make('قنوات تواصل رسمية', $validContacts)
                ->description('روابط مؤكدة وصالحة فقط')->url(ContactChannelResource::getUrl('index')),
            Stat::make('صفحات قانونية مكتملة', $completeLegalPages.' / 2')
                ->description('الخصوصية والشروط بالعربية والإنجليزية')->url(LegalPageResource::getUrl('index')),
        ];
    }
}
