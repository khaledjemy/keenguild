<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\LegalPage;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_only_public_content_in_both_languages(): void
    {
        Service::create(['slug' => 'visible-service', 'title_ar' => 'خدمة', 'title_en' => 'Service', 'published' => true]);
        Service::create(['slug' => 'draft-service', 'title_ar' => 'مسودة', 'title_en' => 'Draft']);
        Project::create(['slug' => 'concept', 'title_ar' => 'تصور', 'title_en' => 'Concept', 'summary_ar' => 'ملخص التصور', 'summary_en' => 'Concept summary', 'body_ar' => 'تفاصيل التصور', 'body_en' => 'Concept details', 'project_type' => 'concept', 'published' => true]);
        Project::create(['slug' => 'private-client', 'title_ar' => 'عميل', 'title_en' => 'Client', 'project_type' => 'client', 'published' => true]);
        Article::create(['slug' => 'live-article', 'title_ar' => 'منشور', 'title_en' => 'Published', 'summary_ar' => '', 'summary_en' => '', 'body_ar' => 'نص', 'body_en' => 'Text', 'published' => true, 'published_at' => now()->subDay()]);
        Article::create(['slug' => 'future-article', 'title_ar' => 'لاحق', 'title_en' => 'Later', 'summary_ar' => '', 'summary_en' => '', 'body_ar' => 'نص', 'body_en' => 'Text', 'published' => true, 'published_at' => now()->addDay()]);
        LegalPage::create(['type' => 'privacy', 'title_ar' => 'خصوصية', 'title_en' => 'Privacy', 'body_ar' => 'نص', 'body_en' => 'Text', 'published' => true]);

        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('/ar/services/visible-service')->assertSee('/en/services/visible-service')
            ->assertSee('/ar/work/concept')->assertSee('/ar/articles/live-article')->assertSee('/en/legal/privacy')
            ->assertDontSee('/ar/demos/flowboard')->assertDontSee('draft-service')->assertDontSee('private-client')
            ->assertDontSee('future-article')->assertDontSee('/admin/')->assertDontSee('/request-quote');

        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }

    public function test_robots_file_points_to_the_dynamic_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin/')
            ->assertSee('Sitemap: http://localhost/sitemap.xml');
    }

    public function test_incomplete_legal_page_is_not_public_or_indexed(): void
    {
        LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'الخصوصية', 'title_en' => 'Privacy',
            'body_ar' => 'نص عربي', 'body_en' => '  ', 'published' => true,
        ]);

        $this->get('/ar/legal/privacy')->assertNotFound();
        $this->get('/en/legal/privacy')->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/legal/privacy');
        $this->get('/ar/contact')->assertOk()->assertDontSee('/ar/legal/privacy');
        $this->get('/')->assertOk()->assertDontSee('/ar/legal/privacy');
    }

    public function test_disabling_indexing_removes_urls_from_sitemap_and_reenabling_restores_them(): void
    {
        $seo = SeoSetting::create(['allow_indexing' => false]);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /')->assertDontSee('Sitemap:');
        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame([], (array) simplexml_load_string($response->getContent()));
        $response->assertDontSee('<loc>', false);

        $seo->update(['allow_indexing' => true]);
        $this->get('/sitemap.xml')->assertOk()->assertSee('<loc>http://localhost</loc>', false);
    }
}
