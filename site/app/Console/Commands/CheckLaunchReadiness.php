<?php

namespace App\Console\Commands;

use App\Models\ContactChannel;
use App\Models\LegalPage;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use App\Services\InquiryAvailability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckLaunchReadiness extends Command
{
    protected $signature = 'keenguild:launch-check';

    protected $description = 'Check KeenGuild content and intake gates before a public launch (read-only)';

    public function handle(InquiryAvailability $availability): int
    {
        $problems = [];
        $notes = [];

        foreach (['users', 'services', 'packages', 'contact_channels', 'legal_pages'] as $table) {
            if (! Schema::hasTable($table)) {
                $problems[] = "Missing database table: {$table}. Run the migrations.";
            }
        }

        if ($problems !== []) {
            foreach ($problems as $problem) {
                $this->error($problem);
            }

            return self::FAILURE;
        }

        if (config('app.env') === 'production') {
            $url = config('app.url');
            $parts = is_string($url) ? parse_url($url) : false;
            $host = is_array($parts) ? strtolower($parts['host'] ?? '') : '';
            $reservedHost = in_array($host, ['example.com', 'example.net', 'example.org'], true);
            foreach (['.example.com', '.example.net', '.example.org', '.test', '.example', '.invalid', '.localhost'] as $suffix) {
                $reservedHost = $reservedHost || str_ends_with($host, $suffix);
            }
            if (! is_array($parts) || ($parts['scheme'] ?? null) !== 'https'
                || filter_var($url, FILTER_VALIDATE_URL) === false
                || filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false
                || ! str_contains($host, '.') || filter_var($host, FILTER_VALIDATE_IP) !== false
                || $host === 'localhost' || $reservedHost) {
                $problems[] = 'Production APP_URL must be the final public HTTPS domain, not localhost, a test/example domain, or an IP address.';
            }
            if ((bool) config('app.debug')) {
                $problems[] = 'Production APP_DEBUG must be false.';
            }
        }

        if (! User::query()->where('is_admin', true)->exists()) {
            $problems[] = 'No site administrator exists.';
        }

        $officialChannels = ContactChannel::query()->where('published', true)->get()
            ->filter(fn (ContactChannel $channel): bool => $channel->publicUrl() !== null);
        if ($officialChannels->isEmpty()) {
            if ($availability->requested() && $availability->enabled('ar') && $availability->enabled('en')) {
                $notes[] = 'No official contact channel is published yet; visitors can still use the quote request form.';
            } else {
                $problems[] = 'No valid, published official contact channel exists and quote intake is unavailable.';
            }
        }

        foreach (['privacy', 'terms'] as $type) {
            $page = LegalPage::query()->where('type', $type)->publiclyVisible()->first();
            if (! $page) {
                $problems[] = "The bilingual {$type} page is not complete and published.";
            } elseif (str_contains($page->body_ar, 'مسودة للمراجعة')
                || str_contains($page->body_en, 'Draft for review')
                || str_contains($page->body_ar, 'يجب إضافة')
                || str_contains($page->body_en, 'Add an official contact')) {
                $problems[] = "The bilingual {$type} page still contains draft instructions; review it before launch.";
            }
        }

        foreach (Service::query()->where('published', true)->get() as $service) {
            foreach (['title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en'] as $field) {
                if (blank($service->{$field})) {
                    $problems[] = "Published service {$service->slug} is missing {$field}.";
                }
            }
        }

        foreach (['landing' => 'websites', 'company' => 'websites', 'store' => 'ecommerce'] as $slug => $serviceSlug) {
            $package = Package::query()->where('slug', $slug)->where('published', true)
                ->whereHas('service', fn ($query) => $query->where('slug', $serviceSlug)->where('published', true))
                ->first();
            if (! $package) {
                $problems[] = "The approved {$slug} package is not publicly visible; confirm whether that is intentional.";

                continue;
            }

            if ($package->pricing_mode !== 'estimate' || ! is_int($package->base_price_egp) || $package->base_price_egp <= 0) {
                $problems[] = "The approved {$slug} package needs a positive starting price.";
            }
            foreach (['description_ar', 'description_en'] as $field) {
                if (blank($package->{$field})) {
                    $problems[] = "The approved {$slug} package is missing {$field}.";
                }
            }
            foreach (['included_features', 'included_features_en', 'excluded_costs', 'excluded_costs_en'] as $field) {
                if (! is_array($package->{$field}) || $package->{$field} === []) {
                    $problems[] = "The approved {$slug} package is missing {$field}.";
                }
            }
        }

        if ($availability->requested()) {
            if (! $availability->enabled('ar') || ! $availability->enabled('en')) {
                $problems[] = 'Quote intake is enabled but its privacy gate is incomplete.';
            }
        } else {
            $notes[] = 'Quote intake is disabled; customer requests cannot be submitted yet.';
        }

        foreach (['../dist/index.html', '../dist/assets/legacy.css', '../dist/agent-api-bridge.js'] as $asset) {
            if (! is_file(base_path($asset))) {
                $problems[] = "Required homepage asset is missing: {$asset}.";
            }
        }

        $homepagePath = base_path('../dist/index.html');
        if (is_file($homepagePath)) {
            preg_match_all('~/assets/([A-Za-z0-9_./%-]+)~', file_get_contents($homepagePath), $matches);
            foreach (array_unique($matches[1]) as $assetPath) {
                if (! is_file(public_path('assets/'.$assetPath)) && ! is_file(base_path('../dist/assets/'.$assetPath))) {
                    $problems[] = "Referenced homepage asset is missing: /assets/{$assetPath}.";
                }
            }
        }

        $publicStorage = realpath(public_path('storage'));
        $uploadStorage = realpath(storage_path('app/public'));
        if (! $publicStorage || ! $uploadStorage || $publicStorage !== $uploadStorage) {
            $problems[] = 'Public project uploads are not linked. Run php artisan storage:link on the target server.';
        }

        foreach ($notes as $note) {
            $this->warn($note);
        }
        foreach ($problems as $problem) {
            $this->error($problem);
        }

        if ($problems !== []) {
            $this->error('Content launch check failed. No data was changed.');

            return self::FAILURE;
        }

        $this->info('Content launch check passed. Still verify HTTPS, backups, server security, and the final site visually before deployment.');

        return self::SUCCESS;
    }
}
