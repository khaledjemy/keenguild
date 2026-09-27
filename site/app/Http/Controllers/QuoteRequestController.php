<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\QuoteRequest;
use App\Services\InquiryAvailability;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuoteRequestController
{
    public function create(Request $request, string $locale, InquiryAvailability $availability): View
    {
        app()->setLocale($locale);

        $enabled = $availability->enabled($locale);
        $privacyUrl = $enabled ? $availability->privacyUrl($locale) : null;
        $packages = $enabled ? Package::query()->where('published', true)
            ->whereHas('service', fn ($query) => $query->where('published', true))
            ->orderBy('sort_order')->get() : collect();
        return view('catalog.quote-request', compact('locale', 'enabled', 'privacyUrl', 'packages'));
    }

    public function store(Request $request, string $locale, InquiryAvailability $availability): RedirectResponse
    {
        app()->setLocale($locale);
        abort_unless($availability->enabled($locale), 404);

        $data = $request->validate([
            'package_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:30'],
            'project_brief' => ['required', 'string', 'min:30', 'max:4000'],
            'privacy_consent' => ['accepted'],
            'company_website' => ['nullable', 'size:0'],
        ]);

        if (! empty($data['package_id'])) {
            $packageExists = Package::query()->whereKey($data['package_id'])->where('published', true)
                ->whereHas('service', fn ($query) => $query->where('published', true))->exists();
            if (! $packageExists) {
                return back()->withErrors(['package_id' => $locale === 'ar' ? 'الباقة غير متاحة.' : 'This package is unavailable.'])->withInput();
            }
        }

        QuoteRequest::create([
            'package_id' => $data['package_id'] ?? null,
            'name' => trim($data['name']),
            'email' => Str::lower(trim($data['email'])),
            'phone' => isset($data['phone']) ? trim($data['phone']) : null,
            'project_brief' => trim($data['project_brief']),
            'status' => 'new',
        ]);

        return redirect()->route('quote.create', ['locale' => $locale])->with('quote_success', true);
    }
}
