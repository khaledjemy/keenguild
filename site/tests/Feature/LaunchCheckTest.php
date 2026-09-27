<?php

namespace Tests\Feature;

use App\Models\ContactChannel;
use App\Models\InquirySetting;
use App\Models\LegalPage;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\ApprovedWebPackagesSeeder;
use Database\Seeders\CatalogDraftSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaunchCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_launch_check_fails_when_required_content_is_missing(): void
    {
        $this->artisan('keenguild:launch-check')
            ->expectsOutputToContain('No site administrator exists')
            ->expectsOutputToContain('No valid, published official contact channel exists and quote intake is unavailable')
            ->expectsOutputToContain('The bilingual privacy page is not complete')
            ->assertFailed();
    }

    public function test_launch_check_passes_after_owner_content_is_published_without_enabling_inquiries(): void
    {
        User::factory()->create(['is_admin' => true]);
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);
        ContactChannel::create(['platform' => 'Email', 'url' => 'team@gmail.com', 'published' => true]);
        foreach (['privacy', 'terms'] as $type) {
            LegalPage::create([
                'type' => $type,
                'title_ar' => $type,
                'title_en' => $type,
                'body_ar' => 'نص معتمد',
                'body_en' => 'Approved text',
                'published' => true,
            ]);
        }

        $this->artisan('keenguild:launch-check')
            ->expectsOutputToContain('Quote intake is disabled')
            ->expectsOutputToContain('Content launch check passed')
            ->assertSuccessful();

        config()->set('keenguild.inquiries_enabled', true);
        $setting = InquirySetting::create(['intake_requested' => false]);
        $this->artisan('keenguild:launch-check')->expectsOutputToContain('Quote intake is disabled')->assertSuccessful();
        $setting->update(['intake_requested' => true]);
        ContactChannel::query()->delete();
        $this->artisan('keenguild:launch-check')
            ->expectsOutputToContain('No official contact channel is published yet; visitors can still use the quote request form.')
            ->assertSuccessful();
        $this->artisan('keenguild:launch-check')->assertSuccessful();

        config()->set('app.env', 'production');
        config()->set('app.url', 'https://www.site-host.co');
        config()->set('app.debug', false);
        $this->artisan('keenguild:launch-check')->assertSuccessful();
    }

    public function test_launch_check_detects_missing_public_upload_link(): void
    {
        $originalPublicPath = public_path();
        app()->usePublicPath(base_path('tests/missing-public-path'));

        try {
            $this->artisan('keenguild:launch-check')
                ->expectsOutputToContain('Public project uploads are not linked')
                ->assertFailed();
        } finally {
            app()->usePublicPath($originalPublicPath);
        }
    }

    public function test_launch_check_rejects_insecure_production_settings(): void
    {
        config()->set('app.env', 'production');
        config()->set('app.url', 'http://localhost');
        config()->set('app.debug', true);

        $this->artisan('keenguild:launch-check')
            ->expectsOutputToContain('Production APP_URL must be the final public HTTPS domain')
            ->expectsOutputToContain('Production APP_DEBUG must be false')
            ->assertFailed();
    }

    public function test_launch_check_rejects_reserved_example_production_domain(): void
    {
        config()->set('app.env', 'production');
        config()->set('app.url', 'https://www.example.com');
        config()->set('app.debug', false);

        $this->artisan('keenguild:launch-check')
            ->expectsOutputToContain('Production APP_URL must be the final public HTTPS domain')
            ->assertFailed();
    }

    public function test_launch_check_catches_incomplete_published_web_scope(): void
    {
        $this->seed(CatalogDraftSeeder::class);
        $this->seed(ApprovedWebPackagesSeeder::class);

        Package::where('slug', 'store')->update(['description_en' => null, 'base_price_egp' => 0]);
        Service::where('slug', 'websites')->update(['body_en' => null]);

        $this->artisan('keenguild:launch-check')
            ->expectsOutputToContain('The approved store package needs a positive starting price')
            ->expectsOutputToContain('The approved store package is missing description_en')
            ->expectsOutputToContain('Published service websites is missing body_en')
            ->assertFailed();
    }
}
