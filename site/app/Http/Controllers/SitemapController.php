<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\CustomPage;
use App\Models\LegalPage;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController
{
    public function index(): Response
    {
        if (SeoSetting::current()?->allow_indexing === false) {
            return response()->view('sitemap', ['urls' => []])
                ->header('Content-Type', 'application/xml; charset=UTF-8')
                ->header('Cache-Control', 'no-store');
        }

        $urls = [
            ['url' => route('home'), 'updated' => null],
            ['url' => route('home.en'), 'updated' => null],
        ];
        $pages = ['about', 'services', 'pricing', 'work', 'articles', 'faqs', 'contact'];

        foreach (['ar', 'en'] as $locale) {
            foreach ($pages as $page) {
                $urls[] = ['url' => route($page, ['locale' => $locale]), 'updated' => null];
            }

            foreach (Service::query()->where('published', true)->get() as $service) {
                $urls[] = ['url' => route('service', ['locale' => $locale, 'slug' => $service->slug]), 'updated' => $service->updated_at?->toDateString()];
            }

            foreach (Project::query()->publiclyVisible()->get() as $project) {
                $urls[] = ['url' => route('project', ['locale' => $locale, 'slug' => $project->slug]), 'updated' => $project->updated_at?->toDateString()];
            }

            foreach (ArticleCategory::query()->whereHas('articles', fn ($query) => $query->publiclyVisible())->get() as $category) {
                $urls[] = ['url' => route('articles.category', ['locale' => $locale, 'slug' => $category->slug]), 'updated' => $category->updated_at?->toDateString()];
            }

            foreach (Article::query()->publiclyVisible()->get() as $article) {
                $urls[] = ['url' => route('article', ['locale' => $locale, 'slug' => $article->slug]), 'updated' => $article->updated_at?->toDateString()];
            }

            foreach (LegalPage::query()->publiclyVisible()->whereIn('type', ['privacy', 'terms'])->get() as $page) {
                $urls[] = ['url' => route('legal', ['locale' => $locale, 'type' => $page->type]), 'updated' => $page->updated_at?->toDateString()];
            }

            foreach (CustomPage::query()->publiclyVisible()->get() as $page) {
                $urls[] = ['url' => route('custom-page', ['locale' => $locale, 'slug' => $page->slug]), 'updated' => $page->updated_at?->toDateString()];
            }

        }

        return response()->view('sitemap', compact('urls'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        if (SeoSetting::current()?->allow_indexing === false) {
            return response("User-agent: *\nDisallow: /admin/\nDisallow: /agent-api-bridge.js\n")
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        return response("User-agent: *\nDisallow: /admin/\nDisallow: /agent-api-bridge.js\nSitemap: ".route('sitemap')."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
