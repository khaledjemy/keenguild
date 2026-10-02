<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ContactChannel;
use App\Models\Faq;
use App\Models\LegalPage;
use App\Models\Package;
use App\Models\PricingSetting;
use App\Models\Project;
use App\Models\Service;
use App\Services\PriceEstimator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CatalogController
{
    public function legal(string $locale, string $type): View
    {
        app()->setLocale($locale);

        $page = LegalPage::query()->where('type', $type)->publiclyVisible()->firstOrFail();

        return view('catalog.legal', compact('page', 'locale'));
    }

    public function legalPreview(string $locale, string $type): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);

        $page = LegalPage::query()->where('type', $type)->firstOrFail();

        return response()->view('catalog.legal', ['page' => $page, 'locale' => $locale, 'preview' => true])
            ->header('Cache-Control', 'private, no-store');
    }

    public function contact(string $locale): View
    {
        app()->setLocale($locale);

        $channels = ContactChannel::query()->where('published', true)->orderBy('sort_order')->get()
            ->filter(fn (ContactChannel $channel) => $channel->publicUrl() !== null);

        return view('catalog.contact', compact('channels', 'locale'));
    }

    public function about(string $locale): View
    {
        app()->setLocale($locale);

        $services = Service::query()->where('published', true)->orderByDesc('featured')->orderBy('sort_order')->get();

        return view('catalog.about', compact('services', 'locale'));
    }

    public function articles(string $locale): View
    {
        app()->setLocale($locale);

        $articles = Article::query()->publiclyVisible()->with('category')->orderByDesc('published_at')->paginate(9);
        $categories = ArticleCategory::query()->whereHas('articles', fn ($query) => $query->publiclyVisible())
            ->orderBy('sort_order')->orderBy('name_ar')->get();
        $selectedCategory = null;

        return view('catalog.articles', compact('articles', 'categories', 'selectedCategory', 'locale'));
    }

    public function articleCategory(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $selectedCategory = ArticleCategory::query()->where('slug', $slug)
            ->whereHas('articles', fn ($query) => $query->publiclyVisible())->firstOrFail();
        $articles = Article::query()->publiclyVisible()->with('category')
            ->where('article_category_id', $selectedCategory->id)->orderByDesc('published_at')->paginate(9);
        $categories = ArticleCategory::query()->whereHas('articles', fn ($query) => $query->publiclyVisible())
            ->orderBy('sort_order')->orderBy('name_ar')->get();

        return view('catalog.articles', compact('articles', 'categories', 'selectedCategory', 'locale'));
    }

    public function article(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $article = Article::query()->publiclyVisible()->with('category')->where('slug', $slug)->firstOrFail();

        return view('catalog.article', compact('article', 'locale'));
    }

    public function articlePreview(string $locale, string $slug): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);
        $article = Article::query()->with('category')->where('slug', $slug)->firstOrFail();

        return response()->view('catalog.article', ['article' => $article, 'locale' => $locale, 'preview' => true])
            ->header('Cache-Control', 'private, no-store');
    }

    public function faqs(string $locale): View
    {
        app()->setLocale($locale);

        $faqs = Faq::query()->where('published', true)->orderBy('sort_order')->orderBy('id')->get();

        return view('catalog.faqs', compact('faqs', 'locale'));
    }

    public function services(string $locale): View
    {
        app()->setLocale($locale);

        $services = Service::query()->where('published', true)->orderByDesc('featured')->orderBy('sort_order')->get();

        return view('catalog.services', compact('services', 'locale'));
    }

    public function service(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $service = Service::query()->where('published', true)->where('slug', $slug)
            ->with(['packages' => fn ($query) => $query->where('published', true)->orderBy('sort_order')->orderBy('base_price_egp')->orderBy('id')])
            ->firstOrFail();

        return view('catalog.service', compact('service', 'locale'));
    }

    public function servicePreview(string $locale, string $slug): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);
        $service = Service::query()->with(['packages' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->where('slug', $slug)->firstOrFail();

        return response()->view('catalog.service', ['service' => $service, 'locale' => $locale, 'preview' => true])
            ->header('Cache-Control', 'private, no-store');
    }

    public function pricing(string $locale): View
    {
        app()->setLocale($locale);

        $services = Service::query()
            ->where('published', true)
            ->with([
                'packages' => fn ($query) => $query->where('published', true)->orderBy('sort_order')->orderBy('base_price_egp')->orderBy('id'),
                'packages.options' => fn ($query) => $query->where('published', true)->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')->get();

        return view('catalog.pricing', ['services' => $services, 'locale' => $locale, 'preview' => false]);
    }

    public function pricingPreview(string $locale): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);

        $services = Service::query()
            ->with(['packages' => fn ($query) => $query->orderBy('sort_order')->orderBy('base_price_egp')->orderBy('id'), 'packages.options' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')->get();

        return response()->view('catalog.pricing', ['services' => $services, 'locale' => $locale, 'preview' => true])
            ->header('Cache-Control', 'private, no-store');
    }

    public function estimate(Request $request, string $locale, Package $package, PriceEstimator $estimator): JsonResponse
    {
        abort_unless($package->published && $package->service()->where('published', true)->exists(), 404);

        return $this->calculateEstimate($request, $locale, $package, $estimator, false);
    }

    public function previewEstimate(Request $request, string $locale, Package $package, PriceEstimator $estimator): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return $this->calculateEstimate($request, $locale, $package, $estimator, true)
            ->header('Cache-Control', 'private, no-store');
    }

    private function calculateEstimate(Request $request, string $locale, Package $package, PriceEstimator $estimator, bool $includeDraftOptions): JsonResponse
    {
        app()->setLocale($locale);

        $input = $request->validate([
            'options' => ['sometimes', 'array', 'max:20'],
            'options.*' => ['integer', 'min:1', 'max:20'],
            'complexity' => ['required', 'in:standard,moderate,high'],
            'urgency' => ['required', 'in:flexible,urgent'],
        ]);

        $defaults = PricingSetting::FACTOR_DEFAULTS;
        $complexityKey = 'complexity_'.$input['complexity'];
        $urgencyKey = 'urgency_'.$input['urgency'];
        $complexity = (float) (PricingSetting::find($complexityKey)?->value ?? $defaults[$complexityKey]);
        $urgency = (float) (PricingSetting::find($urgencyKey)?->value ?? $defaults[$urgencyKey]);
        $rangeMargin = (float) (PricingSetting::find('estimate_range_percent')?->value ?? 10) / 100;

        try {
            return response()->json($estimator->estimate(
                $package,
                $input['options'] ?? [],
                $complexity,
                $urgency,
                $locale,
                $includeDraftOptions,
                $rangeMargin,
            ));
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function work(string $locale): View
    {
        app()->setLocale($locale);

        $projects = Project::query()->publiclyVisible()->orderBy('sort_order')->latest()->get();

        return view('catalog.work', [
            'projects' => $projects->where('project_type', '!=', 'external'),
            'externalExamples' => $projects->where('project_type', 'external'),
            'locale' => $locale,
            'concepts' => \App\Support\InteractiveConcepts::published(),
        ]);
    }

    public function conceptDemo(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $concept = \App\Support\InteractiveConcepts::published()[$slug] ?? null;
        abort_unless($concept, 404);

        return view('catalog.concept-demo', ['concept' => $concept, 'slug' => $slug, 'locale' => $locale, 'noindex' => true]);
    }

    public function project(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $project = Project::query()->publiclyVisible()->where('slug', $slug)->firstOrFail();

        return view('catalog.project', ['project' => $project, 'locale' => $locale, 'preview' => false]);
    }

    public function projectPreview(string $locale, string $slug): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);

        $project = Project::query()->where('slug', $slug)->firstOrFail();

        return response()->view('catalog.project', ['project' => $project, 'locale' => $locale, 'preview' => true])
            ->header('Cache-Control', 'private, no-store');
    }

    public function projectTour(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $project = Project::query()->publiclyVisible()->where('slug', $slug)->firstOrFail();
        abort_if($project->tourImages() === [], 404);

        return view('catalog.project-tour', ['project' => $project, 'locale' => $locale, 'preview' => false, 'noindex' => true]);
    }

    public function projectTourPreview(string $locale, string $slug): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);

        $project = Project::query()->where('slug', $slug)->firstOrFail();
        abort_if($project->tourImages() === [], 404);

        return response()->view('catalog.project-tour', ['project' => $project, 'locale' => $locale, 'preview' => true, 'noindex' => true])
            ->header('Cache-Control', 'private, no-store');
    }
}
