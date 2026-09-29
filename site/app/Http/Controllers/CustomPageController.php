<?php

namespace App\Http\Controllers;

use App\Models\CustomPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class CustomPageController
{
    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $page = CustomPage::query()->publiclyVisible()->where('slug', $slug)->firstOrFail();

        return view('catalog.custom-page', compact('page', 'locale'));
    }

    public function preview(string $locale, string $slug): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app()->setLocale($locale);

        $page = CustomPage::query()->where('slug', $slug)->firstOrFail();

        return response()->view('catalog.custom-page', ['page' => $page, 'locale' => $locale, 'preview' => true])
            ->header('Cache-Control', 'private, no-store');
    }
}
