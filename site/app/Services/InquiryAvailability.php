<?php

namespace App\Services;

use App\Models\LegalPage;
use App\Models\InquirySetting;

class InquiryAvailability
{
    public function enabled(string $locale): bool
    {
        return $this->requested()
            && $this->privacyUrl($locale) !== null;
    }

    public function requested(): bool
    {
        $setting = InquirySetting::current();

        return $setting ? $setting->intake_requested : (bool) config('keenguild.inquiries_enabled');
    }

    public function privacyUrl(string $locale): ?string
    {
        $page = LegalPage::query()->where('type', 'privacy')->publiclyVisible()->first();
        if ($page) {
            return route('legal', ['locale' => $locale, 'type' => 'privacy']);
        }

        $external = config('keenguild.privacy_url');

        return is_string($external)
            && str_starts_with($external, 'https://')
            && filter_var($external, FILTER_VALIDATE_URL) !== false
            ? $external : null;
    }
}
