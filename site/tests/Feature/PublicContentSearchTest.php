<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\CustomPage;
use App\Models\Project;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_only_public_content_with_source_links(): void
    {
        Article::create([
            'slug' => 'fast-websites', 'title_ar' => 'مواقع سريعة', 'title_en' => 'Fast websites',
            'summary_ar' => 'تحسين سرعة المواقع', 'summary_en' => 'Improving website speed',
            'body_ar' => 'المحتوى المنشور', 'body_en' => 'Published content',
            'published' => true, 'published_at' => now()->subDay(),
        ]);
        Article::create([
            'slug' => 'future-websites', 'title_ar' => 'مواقع مستقبلية', 'title_en' => 'Future websites',
            'summary_ar' => 'ملخص', 'summary_en' => 'Summary',
            'body_ar' => 'غير منشور بعد', 'body_en' => 'Not yet published',
            'published' => true, 'published_at' => now()->addDay(),
        ]);
        CustomPage::create([
            'slug' => 'hidden-speed', 'title_ar' => 'سرعة مخفية', 'title_en' => 'Hidden speed',
            'summary_ar' => '', 'summary_en' => '', 'body_ar' => 'مسودة', 'body_en' => 'Draft', 'published' => false,
        ]);
        Project::create([
            'slug' => 'private-speed', 'title_ar' => 'مشروع سرعة خاص', 'title_en' => 'Private speed project',
            'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'تفاصيل', 'body_en' => 'Details',
            'project_type' => 'client', 'display_permission_confirmed' => false, 'published' => true,
        ]);

        $response = $this->getJson('/api/agent/search?q='.urlencode('مواقع').'&locale=ar')->assertOk();
        $response->assertJsonCount(1, 'items')
            ->assertJsonPath('counts.articles', 1)
            ->assertJsonPath('counts.projects', 0)
            ->assertJsonPath('items.0.title', 'مواقع سريعة')
            ->assertJsonPath('items.0.url', route('article', ['locale' => 'ar', 'slug' => 'fast-websites']));
    }

    public function test_search_rejects_oversized_query(): void
    {
        $this->getJson('/api/agent/search?q='.str_repeat('x', 201))->assertUnprocessable();
    }

    public function test_project_and_price_queries_include_public_records_only(): void
    {
        Project::create([
            'slug' => 'booking-demo', 'title_ar' => 'تطبيق حجوزات', 'title_en' => 'Booking app',
            'summary_ar' => 'واجهة للحجوزات', 'summary_en' => 'Booking interface',
            'body_ar' => 'تفاصيل النموذج', 'body_en' => 'Concept details',
            'project_type' => 'concept', 'published' => true,
        ]);
        $service = Service::create([
            'slug' => 'websites', 'category' => 'web', 'title_ar' => 'تصميم مواقع', 'title_en' => 'Web design',
            'summary_ar' => 'مواقع للشركات', 'summary_en' => 'Company websites',
            'body_ar' => 'تفاصيل الخدمة', 'body_en' => 'Service details', 'published' => true,
        ]);
        Package::create([
            'service_id' => $service->id, 'slug' => 'landing', 'name_ar' => 'صفحة هبوط', 'name_en' => 'Landing page',
            'description_ar' => 'باقة صفحة هبوط', 'description_en' => 'Landing page package',
            'base_price_egp' => 5000, 'pricing_mode' => 'estimate', 'published' => true,
            'included_features' => ['تصميم متجاوب'], 'included_features_en' => ['Responsive design'],
        ]);

        $this->getJson('/api/agent/search?q='.urlencode('المشاريع'))->assertOk()
            ->assertJsonFragment(['title' => 'تطبيق حجوزات']);
        $this->getJson('/api/agent/search?q='.urlencode('الأسعار'))->assertOk()
            ->assertJsonFragment(['title' => 'صفحة هبوط'])
            ->assertSee('5000 EGP');
    }
}
