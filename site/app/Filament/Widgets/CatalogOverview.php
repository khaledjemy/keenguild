<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Article;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected ?string $heading = 'مؤشرات الموقع';

    protected ?string $description = 'أرقام مباشرة من قاعدة البيانات؛ المحتوى غير المنشور لا يدخل ضمن الظاهر للزوار.';

    protected function getStats(): array
    {
        return [
            Stat::make('طلبات جديدة', QuoteRequest::query()->where('status', 'new')->count())
                ->description('تحتاج متابعة')->url(QuoteRequestResource::getUrl('index')),
            Stat::make('خدمات منشورة', Service::query()->where('published', true)->count())
                ->description('ظاهرة للزوار')->url(ServiceResource::getUrl('index')),
            Stat::make('مشاريع ظاهرة', Project::query()->publiclyVisible()->count())
                ->description('مستوفية شروط العرض')->url(ProjectResource::getUrl('index')),
            Stat::make('مقالات منشورة', Article::query()->publiclyVisible()->count())
                ->description('منشورة وتاريخها ساري')->url(ArticleResource::getUrl('index')),
        ];
    }
}
