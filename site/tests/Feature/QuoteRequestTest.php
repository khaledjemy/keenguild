<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use App\Models\InquirySetting;
use App\Models\HomepageContent;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    private function enableInquiries(): void
    {
        config()->set('keenguild.inquiries_enabled', true);
        config()->set('keenguild.privacy_url', 'https://example.test/privacy');
    }

    public function test_form_is_safe_by_default_and_does_not_accept_customer_data(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('data-home-ar="عاين نموذج التواصل ↗"', false)
            ->assertSee('لا تُرسل الطلبات من هذا النموذج حاليًا.')
            ->assertDontSee('href="/ar/request-quote" data-home-route="request-quote"', false);
        $this->get('/ar/request-quote')->assertOk()->assertSee('غير مُفعّل بعد')->assertDontSee('name="email"', false)
            ->assertSee('content="noindex,nofollow"', false)->assertDontSee('rel="canonical"', false);
        $this->post('/ar/request-quote', [
            'name' => 'Test customer', 'email' => 'customer@example.test',
            'project_brief' => str_repeat('Project details ', 4), 'privacy_consent' => '1',
        ])->assertNotFound();
        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_quote_request_requires_consent_and_published_package(): void
    {
        $this->enableInquiries();
        $this->get('/')->assertOk()
            ->assertSee('href="/ar/request-quote" data-home-route="request-quote"', false)
            ->assertSee('data-home-en="Start a project ↗"', false)
            ->assertDontSee('data-home-ar="عاين نموذج التواصل ↗"', false);
        $this->get('/en/request-quote')->assertOk()->assertSee('Send quote request')->assertSee('https://example.test/privacy')
            ->assertSee('rel="canonical"', false)->assertDontSee('content="noindex,nofollow"', false);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'published' => false]);
        Service::create(['slug' => 'integrations', 'title_ar' => 'تكاملات', 'title_en' => 'Integrations', 'published' => true]);
        $this->get('/ar/services/integrations')->assertOk()->assertSee('احكِ لنا عن مشروعك')->assertSee('/ar/request-quote');

        $payload = [
            'package_id' => $package->id, 'name' => 'Test customer', 'email' => 'CUSTOMER@EXAMPLE.TEST',
            'project_brief' => str_repeat('Project details ', 4), 'privacy_consent' => '1',
        ];
        $this->post('/ar/request-quote', $payload)->assertSessionHasErrors('package_id');
        $this->assertDatabaseCount('quote_requests', 0);

        $package->update(['published' => true]);
        $this->post('/ar/request-quote', array_diff_key($payload, ['privacy_consent' => true]))->assertSessionHasErrors('privacy_consent');
        $this->assertDatabaseCount('quote_requests', 0);

        $this->post('/ar/request-quote', $payload)->assertRedirect('/ar/request-quote');
        $this->assertDatabaseHas('quote_requests', [
            'package_id' => $package->id, 'email' => 'customer@example.test', 'status' => 'new',
        ]);
    }

    public function test_language_switch_keeps_the_selected_quote_package(): void
    {
        $this->enableInquiries();
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        $package = Package::create([
            'service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company',
            'published' => true,
        ]);

        $this->get("/ar/request-quote?package={$package->id}")
            ->assertOk()
            ->assertSee('href="http://localhost/en/request-quote?package='.$package->id.'"', false)
            ->assertSee('<option value="'.$package->id.'" selected>', false);

        $this->get("/en/request-quote?package={$package->id}")
            ->assertOk()
            ->assertSee('href="http://localhost/ar/request-quote?package='.$package->id.'"', false)
            ->assertSee('<option value="'.$package->id.'" selected>', false);
    }

    public function test_spam_honeypot_blocks_submission(): void
    {
        $this->enableInquiries();
        $this->post('/en/request-quote', [
            'name' => 'Test customer', 'email' => 'customer@example.test',
            'project_brief' => str_repeat('Project details ', 4), 'privacy_consent' => '1',
            'company_website' => 'spam.example',
        ])->assertSessionHasErrors('company_website');
        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_http_privacy_url_does_not_enable_collection(): void
    {
        config()->set('keenguild.inquiries_enabled', true);
        config()->set('keenguild.privacy_url', 'http://example.test/privacy');

        $this->get('/en/request-quote')->assertOk()->assertSee('not enabled yet');
        $this->post('/en/request-quote', [])->assertNotFound();
    }

    public function test_published_local_privacy_policy_can_enable_inquiries_without_social_links(): void
    {
        config()->set('keenguild.inquiries_enabled', true);
        $policy = LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'سياسة الخصوصية', 'title_en' => 'Privacy policy',
            'body_ar' => 'نص سياسة مُراجع', 'body_en' => 'Reviewed policy text', 'published' => true,
        ]);

        $this->get('/ar/request-quote')->assertOk()->assertSee('name="email"', false)->assertSee('/ar/legal/privacy');
        $this->get('/en/request-quote')->assertOk()->assertSee('/en/legal/privacy');

        $policy->update(['published' => false]);
        $this->get('/ar/request-quote')->assertOk()->assertSee('غير مُفعّل بعد');
        $this->post('/ar/request-quote', [])->assertNotFound();

        $policy->update(['published' => true]);
        $this->get('/ar/request-quote')->assertOk()->assertSee('name="email"', false);
    }

    public function test_admin_intake_setting_controls_quote_form_without_bypassing_privacy(): void
    {
        config()->set('keenguild.inquiries_enabled', true);
        $setting = InquirySetting::create(['intake_requested' => false]);
        $this->get('/ar/request-quote')->assertOk()->assertDontSee('name="email"', false);

        $setting->update(['intake_requested' => true]);
        $this->get('/ar/request-quote')->assertOk()->assertDontSee('name="email"', false);

        LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'سياسة الخصوصية', 'title_en' => 'Privacy policy',
            'body_ar' => 'نص معتمد', 'body_en' => 'Approved text', 'published' => true,
        ]);
        $this->get('/ar/request-quote')->assertOk()->assertSee('name="email"', false);
        $this->get('/en/request-quote')->assertOk()->assertSee('name="email"', false);

        $setting->update(['intake_requested' => false]);
        $this->get('/ar/request-quote')->assertOk()->assertDontSee('name="email"', false);
    }

    public function test_managed_contact_button_copy_applies_to_live_quote_link(): void
    {
        $this->enableInquiries();
        $this->setHomepageContent(['content' => [
            'contact_button_ar' => 'حدثنا عن مشروعك',
            'contact_button_en' => 'Tell us about your project',
        ]]);

        $this->get('/')->assertOk()
            ->assertSee('data-home-ar="حدثنا عن مشروعك"', false)
            ->assertSee('data-home-en="Tell us about your project"', false)
            ->assertSee('href="/ar/request-quote"', false);
        $this->get('/en')->assertOk()->assertSee('Tell us about your project');
    }
}
