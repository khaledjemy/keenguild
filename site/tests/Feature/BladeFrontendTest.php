<?php

namespace Tests\Feature;

use App\Models\ContactChannel;
use App\Models\Faq;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BladeFrontendTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_keeps_both_languages_and_only_published_featured_services(): void
    {
        Service::create(['slug' => 'draft', 'title_ar' => 'خدمة مخفية', 'title_en' => 'Hidden service', 'published' => false, 'featured_on_home' => true]);
        Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true, 'featured_on_home' => true]);

        $this->get('/')->assertOk()->assertSee('lang="ar"', false)->assertSee('data-home-ar="مواقع"', false)->assertDontSee('خدمة مخفية');
        $this->get('/en')->assertOk()->assertSee('lang="en"', false)->assertSee('data-home-en="Websites"', false)->assertDontSee('Hidden service');
    }

    public function test_english_homepage_preserves_unicode_css_symbols(): void
    {
        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertStringContainsString(".proof-card:after { content:'✦'", $html);
        $this->assertStringNotContainsString("content:'&#10022;'", $html);
    }

    public function test_homepage_only_exposes_published_valid_social_links(): void
    {
        ContactChannel::create(['platform' => 'GitHub', 'url' => 'https://github.com/keenguild', 'published' => true]);
        ContactChannel::create(['platform' => 'Facebook', 'url' => 'https://facebook.com/hidden-account', 'published' => false]);
        ContactChannel::create(['platform' => 'Instagram', 'url' => 'https://evil.example/profile', 'published' => true]);

        $this->get('/')->assertOk()->assertSee('https://github.com/keenguild')->assertDontSee('hidden-account')->assertDontSee('evil.example');
    }

    public function test_catalog_uses_laravel_views_in_both_languages(): void
    {
        $this->get('/ar/about')->assertOk()->assertSee('lang="ar"', false)->assertDontSee('data-page=', false);
        $this->get('/en/services')->assertOk()->assertSee('lang="en"', false)->assertDontSee('data-page=', false);
        $this->get('/ar/pricing')->assertOk()->assertSee('الأسعار المعروضة')->assertDontSee('data-react-estimator', false);
    }

    public function test_faqs_only_expose_published_localized_content(): void
    {
        Faq::create(['question_ar' => 'سؤال مسودة', 'question_en' => 'Draft question', 'answer_ar' => 'سر', 'answer_en' => 'Secret']);
        Faq::create(['question_ar' => 'كيف نبدأ؟', 'question_en' => 'How do we start?', 'answer_ar' => 'نحدد النطاق.', 'answer_en' => 'We define the scope.', 'published' => true]);

        $this->get('/ar/faq')->assertOk()->assertSee('كيف نبدأ؟')->assertDontSee('سؤال مسودة')->assertDontSee('How do we start?');
        $this->get('/en/faq')->assertOk()->assertSee('How do we start?')->assertDontSee('Draft question')->assertDontSee('كيف نبدأ؟');
    }

    public function test_service_pages_remain_limited_to_published_entries(): void
    {
        Service::create(['slug' => 'draft', 'title_ar' => 'مسودة', 'title_en' => 'Draft', 'published' => false]);
        Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);

        $this->get('/ar/services')->assertOk()->assertSee('مواقع')->assertDontSee('مسودة');
        $this->get('/ar/services/draft')->assertNotFound();
        $this->get('/en/services/websites')->assertOk()->assertSee('Websites');
    }
}
