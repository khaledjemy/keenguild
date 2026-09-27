<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\LegacyHomeController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (LegacyHomeController $legacy, \App\Services\InquiryAvailability $availability, \App\Services\HomeLocalization $localizer) => $legacy->index($availability, $localizer))->name('home');
Route::get('/en', fn (LegacyHomeController $legacy, \App\Services\InquiryAvailability $availability, \App\Services\HomeLocalization $localizer) => $legacy->english($availability, $localizer))->name('home.en');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/agent-api-bridge.js', [LegacyHomeController::class, 'agentBridge']);
Route::get('/assets/{path}', [LegacyHomeController::class, 'asset'])->where('path', '.*');

Route::prefix('admin/preview')->middleware('auth')->group(function () {
    Route::get('/home-hero/{locale}/{hero}', [LegacyHomeController::class, 'previewHero'])->where(['locale' => 'ar|en'])->name('preview.home.hero');
    Route::get('/pricing/{locale}', [CatalogController::class, 'pricingPreview'])->where(['locale' => 'ar|en'])->name('preview.pricing');
    Route::post('/pricing/{locale}/{package}/estimate', [CatalogController::class, 'previewEstimate'])->where(['locale' => 'ar|en'])->middleware('throttle:20,1')->name('preview.pricing.estimate');
    Route::get('/work/{locale}/{slug}', [CatalogController::class, 'projectPreview'])->where(['locale' => 'ar|en'])->name('preview.project');
    Route::get('/work/{locale}/{slug}/tour', [CatalogController::class, 'projectTourPreview'])->where(['locale' => 'ar|en'])->name('preview.project.tour');
    Route::get('/services/{locale}/{slug}', [CatalogController::class, 'servicePreview'])->where(['locale' => 'ar|en'])->name('preview.service');
    Route::get('/articles/{locale}/{slug}', [CatalogController::class, 'articlePreview'])->where(['locale' => 'ar|en'])->name('preview.article');
    Route::get('/legal/{locale}/{type}', [CatalogController::class, 'legalPreview'])->where(['locale' => 'ar|en', 'type' => 'privacy|terms'])->name('preview.legal');
});

Route::prefix('{locale}')->where(['locale' => 'ar|en'])->group(function () {
    Route::get('/legal/{type}', [CatalogController::class, 'legal'])->where(['type' => 'privacy|terms'])->name('legal');
    Route::get('/contact', [CatalogController::class, 'contact'])->name('contact');
    Route::get('/about', [CatalogController::class, 'about'])->name('about');
    Route::get('/articles', [CatalogController::class, 'articles'])->name('articles');
    Route::get('/articles/category/{slug}', [CatalogController::class, 'articleCategory'])->name('articles.category');
    Route::get('/articles/{slug}', [CatalogController::class, 'article'])->name('article');
    Route::get('/faq', [CatalogController::class, 'faqs'])->name('faqs');
    Route::get('/services', [CatalogController::class, 'services'])->name('services');
    Route::get('/services/{slug}', [CatalogController::class, 'service'])->name('service');
    Route::get('/request-quote', [QuoteRequestController::class, 'create'])->name('quote.create');
    Route::post('/request-quote', [QuoteRequestController::class, 'store'])->middleware('throttle:5,1')->name('quote.store');
    Route::get('/pricing', [CatalogController::class, 'pricing'])->name('pricing');
    Route::post('/pricing/{package}/estimate', [CatalogController::class, 'estimate'])->middleware('throttle:20,1')->name('pricing.estimate');
    Route::get('/work', [CatalogController::class, 'work'])->name('work');
    Route::get('/demos/{slug}', [CatalogController::class, 'conceptDemo'])->name('concept.demo');
    Route::get('/work/{slug}', [CatalogController::class, 'project'])->name('project');
    Route::get('/work/{slug}/tour', [CatalogController::class, 'projectTour'])->name('project.tour');
});
