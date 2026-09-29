<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\ArticleCategories\ArticleCategoryResource;
use App\Filament\Resources\CustomPages\CustomPageResource;
use App\Filament\Resources\HomepageContents\HomepageContentResource;
use App\Filament\Resources\HomepageHeroes\HomepageHeroResource;
use App\Filament\Resources\HomepageVideos\HomepageVideoResource;
use App\Filament\Resources\LegalPages\LegalPageResource;
use App\Filament\Resources\PageContents\PageContentResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\CustomPage;
use App\Models\HomepageContent;
use App\Models\HomepageHero;
use App\Models\HomepageVideo;
use App\Models\LegalPage;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Service;
use App\Services\InquiryAvailability;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class PagesHub extends Page
{
    protected static ?string $navigationLabel = 'الصفحات';

    protected static ?int $navigationSort = 1;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $slug = 'pages-hub';

    protected string $view = 'filament.pages.pages-hub';

    public function getTitle(): string
    {
        return 'الصفحات';
    }

    protected function getViewData(): array
    {
        $homeContent = HomepageContent::query()->first();
        $homeHero = HomepageHero::query()->first();
        $homeVideo = HomepageVideo::query()->first();
        $content = PageContent::query()->get()->keyBy('page_key');
        $legal = LegalPage::query()->whereIn('type', ['privacy', 'terms'])->get()->keyBy('type');
        $visibleLegalTypes = LegalPage::query()->whereIn('type', ['privacy', 'terms'])->publiclyVisible()->pluck('type')->all();
        $visibleCustomIds = CustomPage::query()->publiclyVisible()->pluck('id')->all();

        $standardPages = [];
        foreach (PageContent::PAGES as $key => $label) {
            $record = $content->get($key);
            $standardPages[] = [
                'label' => $label,
                'status' => $record ? 'نصوص مخصصة' : 'يعرض المحتوى الافتراضي',
                'editLabel' => $record ? 'تعديل العنوان والمقدمة' : 'تخصيص العنوان والمقدمة',
                'edit' => $record
                    ? PageContentResource::getUrl('edit', ['record' => $record])
                    : PageContentResource::getUrl('create', ['page_key' => $key]),
                'previewAr' => route($key, ['locale' => 'ar']),
                'previewEn' => route($key, ['locale' => 'en']),
            ];
        }

        $intake = app(InquiryAvailability::class);
        $standardPages[] = [
            'label' => 'طلب عرض سعر',
            'status' => $intake->enabled('ar') && $intake->enabled('en') ? 'النموذج متاح' : 'النموذج مغلق',
            'editLabel' => 'إعداد استقبال الطلبات',
            'edit' => SiteSettings::getUrl(),
            'previewAr' => route('quote.create', ['locale' => 'ar']),
            'previewEn' => route('quote.create', ['locale' => 'en']),
        ];

        foreach (['privacy' => 'سياسة الخصوصية', 'terms' => 'الشروط والأحكام'] as $type => $label) {
            $record = $legal->get($type);
            $standardPages[] = [
                'label' => $label,
                'status' => in_array($type, $visibleLegalTypes, true) ? 'منشورة ومكتملة' : ($record ? ($record->published ? 'غير مكتملة' : 'مسودة') : 'لم تُنشأ'),
                'editLabel' => $record ? 'تعديل الصفحة' : 'إنشاء الصفحة',
                'edit' => $record
                    ? LegalPageResource::getUrl('edit', ['record' => $record])
                    : LegalPageResource::getUrl('create', ['type' => $type]),
                'previewAr' => $record ? route('preview.legal', ['locale' => 'ar', 'type' => $type]) : null,
                'previewEn' => $record ? route('preview.legal', ['locale' => 'en', 'type' => $type]) : null,
            ];
        }

        $detailPages = collect()
            ->concat(Service::query()->orderBy('sort_order')->get()->map(fn (Service $item): array => [
                'kind' => 'خدمة', 'title' => $item->title_ar, 'status' => $item->published ? 'ظاهرة' : 'مسودة',
                'edit' => ServiceResource::getUrl('edit', ['record' => $item]),
                'previewAr' => route('preview.service', ['locale' => 'ar', 'slug' => $item->slug]),
                'previewEn' => route('preview.service', ['locale' => 'en', 'slug' => $item->slug]),
            ]))
            ->concat(Project::query()->orderBy('sort_order')->get()->flatMap(function (Project $item): array {
                $pages = [[
                    'kind' => 'مشروع', 'title' => $item->title_ar, 'status' => $item->isPubliclyVisible() ? 'ظاهرة' : 'مسودة أو غير مكتملة',
                    'edit' => ProjectResource::getUrl('edit', ['record' => $item]),
                    'previewAr' => route('preview.project', ['locale' => 'ar', 'slug' => $item->slug]),
                    'previewEn' => route('preview.project', ['locale' => 'en', 'slug' => $item->slug]),
                ]];
                if ($item->tourImages() !== []) {
                    $pages[] = [
                        'kind' => 'جولة مشروع', 'title' => $item->title_ar, 'status' => $item->isPubliclyVisible() ? 'متاحة' : 'معاينة فقط',
                        'edit' => ProjectResource::getUrl('edit', ['record' => $item]),
                        'previewAr' => route('preview.project.tour', ['locale' => 'ar', 'slug' => $item->slug]),
                        'previewEn' => route('preview.project.tour', ['locale' => 'en', 'slug' => $item->slug]),
                    ];
                }

                return $pages;
            }))
            ->concat(Article::query()->latest()->get()->map(fn (Article $item): array => [
                'kind' => 'مقال', 'title' => $item->title_ar, 'status' => $item->published && $item->published_at?->isPast() ? 'ظاهرة' : 'مسودة أو مجدولة',
                'edit' => ArticleResource::getUrl('edit', ['record' => $item]),
                'previewAr' => route('preview.article', ['locale' => 'ar', 'slug' => $item->slug]),
                'previewEn' => route('preview.article', ['locale' => 'en', 'slug' => $item->slug]),
            ]))
            ->concat(ArticleCategory::query()->orderBy('sort_order')->get()->map(fn (ArticleCategory $item): array => [
                'kind' => 'تصنيف مقالات', 'title' => $item->name_ar,
                'status' => $item->articles()->publiclyVisible()->exists() ? 'ظاهرة' : 'لا توجد مقالات منشورة',
                'edit' => ArticleCategoryResource::getUrl('edit', ['record' => $item]),
                'previewAr' => $item->articles()->publiclyVisible()->exists() ? route('articles.category', ['locale' => 'ar', 'slug' => $item->slug]) : null,
                'previewEn' => $item->articles()->publiclyVisible()->exists() ? route('articles.category', ['locale' => 'en', 'slug' => $item->slug]) : null,
            ]));

        foreach (['flowboard' => 'Flowboard', 'storefront' => 'Storefront', 'pulse' => 'Pulse'] as $slug => $title) {
            $detailPages->push([
                'kind' => 'تجربة ثابتة', 'title' => $title, 'status' => 'للعرض فقط؛ تعديلها يحتاج تطوير',
                'edit' => null,
                'previewAr' => route('concept.demo', ['locale' => 'ar', 'slug' => $slug]),
                'previewEn' => route('concept.demo', ['locale' => 'en', 'slug' => $slug]),
            ]);
        }

        return [
            'homeParts' => [
                ['label' => 'نصوص وأقسام الرئيسية', 'status' => 'موجودة', 'edit' => $homeContent
                    ? HomepageContentResource::getUrl('edit', ['record' => $homeContent]) : HomepageContentResource::getUrl('index')],
                ['label' => 'مقدمة الرئيسية', 'status' => $homeHero ? ($homeHero->published ? 'مخصصة ومنشورة' : 'مسودة') : 'التصميم الحالي',
                    'edit' => $homeHero ? HomepageHeroResource::getUrl('edit', ['record' => $homeHero]) : HomepageHeroResource::getUrl('create')],
                ['label' => 'فيديو الرئيسية', 'status' => $homeVideo ? ($homeVideo->published ? 'مخصص ومنشور' : 'مسودة') : 'الفيديو الحالي',
                    'edit' => $homeVideo ? HomepageVideoResource::getUrl('edit', ['record' => $homeVideo]) : HomepageVideoResource::getUrl('create')],
            ],
            'standardPages' => $standardPages,
            'customPages' => CustomPage::query()->latest()->get(),
            'visibleCustomIds' => $visibleCustomIds,
            'createPageUrl' => CustomPageResource::getUrl('create'),
            'detailPages' => $detailPages,
        ];
    }
}
