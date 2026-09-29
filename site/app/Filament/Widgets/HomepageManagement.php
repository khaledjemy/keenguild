<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\LegalPages\LegalPageResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Models\Article;
use App\Models\LegalPage;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Services\InquiryAvailability;
use Filament\Widgets\Widget;

class HomepageManagement extends Widget
{
    protected static ?int $sort = -3;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.homepage-management';

    protected function getViewData(): array
    {
        $intake = app(InquiryAvailability::class);
        $legalCount = LegalPage::query()->whereIn('type', ['privacy', 'terms'])->publiclyVisible()
            ->distinct()->count('type');
        $newRequests = QuoteRequest::query()->where('status', 'new')->count();
        $actions = [];

        if ($legalCount < 2) {
            $detail = "صفحات مكتملة: {$legalCount} من 2";
            if ($intake->requested() && (! $intake->enabled('ar') || ! $intake->enabled('en'))) {
                $detail .= ' · استقبال الطلبات ينتظر سياسة خصوصية صالحة';
            }

            $actions[] = ['title' => 'استكمال الخصوصية والشروط', 'detail' => $detail, 'url' => LegalPageResource::getUrl('index')];
        }

        if ($newRequests > 0) {
            $actions[] = ['title' => 'مراجعة طلبات المشاريع الجديدة', 'detail' => "{$newRequests} طلب بانتظار المتابعة", 'url' => QuoteRequestResource::getUrl('index')];
        }

        $draftProjects = Project::query()->where('published', false)->count();
        if ($draftProjects > 0) {
            $actions[] = ['title' => 'مشاريع مسودة', 'detail' => "{$draftProjects} مشروع غير ظاهر للزوار", 'url' => ProjectResource::getUrl('index')];
        }

        $draftArticles = Article::query()->where('published', false)->count();
        if ($draftArticles > 0) {
            $actions[] = ['title' => 'مقالات مسودة', 'detail' => "{$draftArticles} مقال غير منشور", 'url' => ArticleResource::getUrl('index')];
        }

        return [
            'legalCount' => $legalCount,
            'intakeStatus' => $intake->enabled('ar') && $intake->enabled('en') ? 'النموذج نشط' : ($intake->requested() ? 'بانتظار سياسة الخصوصية' : 'النموذج مغلق'),
            'intakeEnabled' => $intake->enabled('ar') && $intake->enabled('en'),
            'actions' => $actions,
            'recentRequests' => QuoteRequest::query()->latest()->limit(4)->get(),
        ];
    }
}
