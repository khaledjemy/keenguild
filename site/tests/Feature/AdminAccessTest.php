<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleCategories\Pages\CreateArticleCategory;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\ContactChannels\Pages\CreateContactChannel;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\HomepageContents\Pages\EditHomepageContent;
use App\Filament\Resources\HomepageHeroes\HomepageHeroResource;
use App\Filament\Resources\HomepageHeroes\Pages\CreateHomepageHero;
use App\Filament\Resources\HomepageHeroes\Pages\EditHomepageHero;
use App\Filament\Resources\HomepageVideos\HomepageVideoResource;
use App\Filament\Resources\HomepageVideos\Pages\CreateHomepageVideo;
use App\Filament\Resources\HomepageVideos\Pages\EditHomepageVideo;
use App\Filament\Resources\InquirySettings\Pages\CreateInquirySetting;
use App\Filament\Resources\NavigationItems\Pages\CreateNavigationItem;
use App\Filament\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\PageContents\Pages\CreatePageContent;
use App\Filament\Resources\PriceOptions\Pages\CreatePriceOption;
use App\Filament\Resources\PricingSettings\Pages\EditPricingSetting;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\QuoteRequests\Pages\EditQuoteRequest;
use App\Filament\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Resources\SeoSettings\Pages\CreateSeoSetting;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Widgets\CatalogOverview;
use App\Filament\Widgets\HomepageManagement;
use App\Filament\Widgets\PricingManagement;
use App\Models\ArticleCategory;
use App\Models\ContactChannel;
use App\Models\HomepageContent;
use App\Models\HomepageHero;
use App\Models\HomepageVideo;
use App\Models\InquirySetting;
use App\Models\LegalPage;
use App\Models\NavigationItem;
use App\Models\Package;
use App\Models\PageContent;
use App\Models\PricingSetting;
use App\Models\QuoteRequest;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login_and_non_admin_cannot_enter_panel(): void
    {
        $this->get('/admin/services')->assertRedirect('/admin/login');
        $this->get('/admin/legal-pages')->assertRedirect('/admin/login');
        $this->get('/admin/homepage-heroes')->assertRedirect('/admin/login');
        $this->get('/admin/homepage-videos')->assertRedirect('/admin/login');
        $this->get('/admin/article-categories')->assertRedirect('/admin/login');
        $this->get('/admin/homepage-contents')->assertRedirect('/admin/login');
        $this->get('/admin/navigation-items')->assertRedirect('/admin/login');
        $this->get('/admin/seo-settings')->assertRedirect('/admin/login');
        $this->get('/admin/page-contents')->assertRedirect('/admin/login');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/services')->assertForbidden();
        $this->actingAs($user)->get('/admin/legal-pages')->assertForbidden();
        $this->actingAs($user)->get('/admin/homepage-heroes')->assertForbidden();
        $this->actingAs($user)->get('/admin/homepage-contents')->assertForbidden();
        $this->actingAs($user)->get('/admin/navigation-items')->assertForbidden();
        $this->actingAs($user)->get('/admin/seo-settings')->assertForbidden();
        $this->actingAs($user)->get('/admin/page-contents')->assertForbidden();
    }

    public function test_admin_can_open_catalog_resources(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/services')->assertOk();
        $this->actingAs($admin)->get('/admin/packages')->assertOk();
        $this->actingAs($admin)->get('/admin/price-options')->assertOk();
        $this->actingAs($admin)->get('/admin/projects')->assertOk();
        $this->actingAs($admin)->get('/admin/articles')->assertOk();
        $this->actingAs($admin)->get('/admin/faqs')->assertOk();
        $this->actingAs($admin)->get('/admin/contact-channels')->assertOk();
        $this->actingAs($admin)->get('/admin/legal-pages')->assertOk();
        $this->actingAs($admin)->get('/admin/homepage-heroes')->assertOk();
        $this->actingAs($admin)->get('/admin/homepage-contents')->assertOk();
        $this->actingAs($admin)->get('/admin/navigation-items')->assertOk()->assertSee('/admin/navigation-items/create', false);
        $this->actingAs($admin)->get('/admin/seo-settings')->assertOk()->assertSee('/admin/seo-settings/create', false);
        $this->actingAs($admin)->get('/admin/page-contents')->assertOk()->assertSee('/admin/page-contents/create', false);
        $this->actingAs($admin)->get('/admin/homepage-videos')->assertOk()->assertSee('/admin/homepage-videos/create', false);
        $this->actingAs($admin)->get('/admin/inquiry-settings')->assertOk()->assertSee('/admin/inquiry-settings/create', false);
        $this->actingAs($admin)->get('/admin/article-categories')->assertOk()->assertSee('/admin/article-categories/create', false);
        SeoSetting::create(['allow_indexing' => true]);
        HomepageVideo::create(['published' => false]);
        $this->get('/admin/seo-settings')->assertOk()->assertDontSee('/admin/seo-settings/create', false);
        $this->get('/admin/homepage-videos')->assertOk()->assertDontSee('/admin/homepage-videos/create', false);
        $this->actingAs($admin)->get('/admin/pricing-settings')->assertOk();
        $this->actingAs($admin)->get('/admin/quote-requests')->assertOk();
        $this->actingAs($admin)->get('/admin/services/create')->assertOk();
        $this->actingAs($admin)->get('/admin/packages/create')->assertOk();
        $this->actingAs($admin)->get('/admin/price-options/create')->assertOk();
        $this->actingAs($admin)->get('/admin/projects/create')->assertOk();
        $this->actingAs($admin)->get('/admin/articles/create')->assertOk();
        $this->actingAs($admin)->get('/admin/faqs/create')->assertOk();
        $this->actingAs($admin)->get('/admin/contact-channels/create')->assertOk();
    }

    public function test_admin_can_manage_quote_intake_from_dashboard_without_publishing_social_links(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Livewire::actingAs($admin)->test(CreateInquirySetting::class)
            ->fillForm(['intake_requested' => true])->call('create')->assertHasNoFormErrors();

        $this->assertTrue(InquirySetting::query()->firstOrFail()->intake_requested);
        $this->get('/admin/inquiry-settings')->assertOk()
            ->assertDontSee('/admin/inquiry-settings/create', false)
            ->assertSee('/admin/inquiry-settings/1/edit', false);
        $this->get('/ar/request-quote')->assertOk()->assertDontSee('name="email"', false);
        Livewire::actingAs($admin)->test(HomepageManagement::class)->assertSee('بانتظار سياسة الخصوصية');

        LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'سياسة الخصوصية', 'title_en' => 'Privacy policy',
            'body_ar' => 'نص معتمد', 'body_en' => 'Approved text', 'published' => true,
        ]);
        $this->get('/ar/request-quote')->assertOk()->assertSee('name="email"', false);
        Livewire::actingAs($admin)->test(HomepageManagement::class)->assertSee('النموذج نشط');
        $this->get('/admin')->assertOk();
    }

    public function test_admin_lists_expose_edit_actions_for_managed_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $content = HomepageContent::query()->firstOrFail();
        $video = HomepageVideo::create(['published' => false]);
        $seo = SeoSetting::create(['allow_indexing' => true]);
        $item = NavigationItem::create([
            'location' => 'footer', 'target' => 'home',
            'label_ar' => 'الرئيسية', 'label_en' => 'Home',
            'sort_order' => 1, 'published' => true,
        ]);
        $category = ArticleCategory::create(['slug' => 'design', 'name_ar' => 'تصميم', 'name_en' => 'Design']);
        $page = PageContent::create(['page_key' => 'about']);
        $legalPage = LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'الخصوصية', 'title_en' => 'Privacy',
            'body_ar' => 'مسودة', 'body_en' => 'Draft', 'published' => false,
        ]);

        $this->actingAs($admin)->get('/admin/homepage-contents')->assertOk()
            ->assertSee('/admin/homepage-contents/'.$content->id.'/edit', false);
        $this->get('/admin/homepage-videos')->assertOk()
            ->assertSee('/admin/homepage-videos/'.$video->id.'/edit', false);
        $this->get('/admin/seo-settings')->assertOk()
            ->assertSee('/admin/seo-settings/'.$seo->id.'/edit', false);
        $this->get('/admin/navigation-items')->assertOk()
            ->assertSee('/admin/navigation-items/'.$item->id.'/edit', false);
        $this->get('/admin/article-categories')->assertOk()
            ->assertSee('/admin/article-categories/'.$category->id.'/edit', false);
        $this->get('/admin/page-contents')->assertOk()
            ->assertSee('/admin/page-contents/'.$page->id.'/edit', false)
            ->assertSee('English preview');
        $this->get('/admin/legal-pages')->assertOk()
            ->assertSee('/admin/legal-pages/'.$legalPage->id.'/edit', false)
            ->assertSee('/admin/preview/legal/ar/privacy', false)
            ->assertSee('/admin/preview/legal/en/privacy', false);
    }

    public function test_homepage_copy_editor_has_one_populated_record_and_no_create_route(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->assertSame(1, HomepageContent::query()->count());
        $this->assertCount(102, HomepageContent::query()->firstOrFail()->content);
        $list = $this->actingAs($admin)->get('/admin/homepage-contents')->assertOk()->getContent();
        foreach (['lang="ar"', 'إدارة النصوص', '/admin/homepage-contents/1/edit'] as $expected) {
            $this->assertTrue(str_contains($list, $expected), 'Missing from homepage copy list: '.$expected);
        }
        $edit = $this->get('/admin/homepage-contents/1/edit')->assertOk()->getContent();
        foreach (['عرض المحتوى الحالي على الرئيسية', 'هذه النصوص محفوظة في قاعدة البيانات'] as $expected) {
            $this->assertTrue(str_contains($edit, $expected), 'Missing from homepage copy editor: '.$expected);
        }
        $this->get('/admin/homepage-contents/create')->assertNotFound();
        $this->get('/admin/homepage-contents')->assertOk();

        $this->assertSame(1, HomepageContent::query()->count());
        $content = HomepageContent::query()->firstOrFail()->content;
        $this->assertSame('كل ما تحتاجه في مكان واحد.', $content['menu_heading_ar']);
        $this->assertSame('Everything you need in one place.', $content['menu_heading_en']);
        $this->assertSame('من الفكرة إلى أثر حقيقي.', $content['proof_heading_ar']);
        $this->assertSame('شاهد الأفكار تصبح واقعًا.', $content['reel_heading_ar']);
        Livewire::actingAs($admin)->test(EditHomepageContent::class, ['record' => 1])
            ->assertFormSet(['content.proof_heading_ar' => HomepageContent::query()->firstOrFail()->content['proof_heading_ar']]);
    }

    public function test_non_admin_cannot_open_homepage_copy_editor(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin/homepage-contents')->assertForbidden();
        $this->assertSame(1, HomepageContent::query()->count());
    }

    public function test_dashboard_overview_uses_real_published_content_and_links_to_editors(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        Package::create(['service_id' => $service->id, 'slug' => 'landing', 'name_ar' => 'صفحة هبوط', 'name_en' => 'Landing', 'published' => true]);
        ContactChannel::create(['platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/', 'published' => true]);
        LegalPage::create(['type' => 'privacy', 'title_ar' => 'الخصوصية', 'title_en' => 'Privacy', 'body_ar' => 'نص عربي', 'body_en' => 'English text', 'published' => true]);

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->assertContains(CatalogOverview::class, Filament::getPanel('admin')->getWidgets());
        $this->assertContains(HomepageManagement::class, Filament::getPanel('admin')->getWidgets());
        $this->assertContains(PricingManagement::class, Filament::getPanel('admin')->getWidgets());
        Livewire::actingAs($admin)->test(CatalogOverview::class)
            ->assertSee('حالة محتوى الموقع')
            ->assertSee('خدمات منشورة')
            ->assertSee('باقات ظاهرة')
            ->assertSee('قنوات تواصل رسمية')
            ->assertSee('صفحات قانونية مكتملة')
            ->assertSee('1 / 2')
            ->assertSee('/admin/services')
            ->assertSee('/admin/contact-channels');
        Livewire::actingAs($admin)->test(HomepageManagement::class)
            ->assertSee('معاينة الرئيسية')
            ->assertSee('معاينة الأسعار')
            ->assertSee('/admin/preview/pricing/ar')
            ->assertSee('/sitemap.xml');
    }

    public function test_admin_can_save_homepage_section_copy(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(EditHomepageContent::class, ['record' => HomepageContent::query()->firstOrFail()->getKey()])
            ->fillForm([
                'content' => [
                    'proof_heading_ar' => 'عنوان مخصص',
                    'proof_heading_en' => 'Custom headline',
                    'proof_description_ar' => 'وصف مخصص للقسم',
                    'proof_description_en' => 'Custom section description',
                    'services_description_ar' => 'وصف الخدمة',
                    'services_description_en' => 'Service description',
                ],
            ])->call('save')->assertHasNoFormErrors();

        $this->assertSame('عنوان مخصص', HomepageContent::query()->first()->content['proof_heading_ar']);
        $this->get('/')->assertOk()->assertSee('data-home-ar="عنوان مخصص"', false)
            ->assertSee('data-home-ar="وصف مخصص للقسم"', false)
            ->assertSee('data-home-en="Custom section description"', false)
            ->assertSee("proofCopy.getAttribute('data-home-'+locale)", false)
            ->assertSee('data-ar="وصف الخدمة"', false);
    }

    public function test_admin_can_save_arabic_only_homepage_copy_without_changing_english(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(EditHomepageContent::class, ['record' => HomepageContent::query()->firstOrFail()->getKey()])
            ->fillForm(['content' => ['proof_heading_ar' => 'عنوان عربي من الإدارة']])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame('عنوان عربي من الإدارة', HomepageContent::query()->firstOrFail()->content['proof_heading_ar']);
        $this->get('/')->assertOk()->assertSee('عنوان عربي من الإدارة');
        $this->get('/en')->assertOk()
            ->assertSee('From idea')
            ->assertDontSee('عنوان عربي من الإدارة');
    }

    public function test_admin_can_upload_orbit_logo_and_contact_background(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(EditHomepageContent::class, ['record' => HomepageContent::query()->firstOrFail()->getKey()])
            ->fillForm([
                'orbit_logo_path' => UploadedFile::fake()->image('orbit.png'),
                'contact_background_path' => UploadedFile::fake()->image('contact.jpg'),
            ])->call('save')->assertHasNoFormErrors();

        $content = HomepageContent::query()->firstOrFail();
        Storage::disk('public')->assertExists($content->orbit_logo_path);
        Storage::disk('public')->assertExists($content->contact_background_path);
        $this->get('/')->assertOk()
            ->assertSee('/storage/'.$content->orbit_logo_path, false)
            ->assertSee('/storage/'.$content->contact_background_path, false);
    }

    public function test_admin_can_create_page_copy_navigation_and_indexing_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreatePageContent::class)
            ->fillForm(['page_key' => 'about', 'heading_ar' => 'من نحن الآن', 'heading_en' => 'About us now',
                'extra_copy' => ['about_1_title_ar' => 'خطوة جديدة', 'about_1_title_en' => 'A new step']])
            ->call('create')->assertHasNoFormErrors();
        $this->assertSame('من نحن الآن', PageContent::query()->where('page_key', 'about')->first()->heading_ar);
        $this->assertSame('خطوة جديدة', PageContent::query()->where('page_key', 'about')->first()->extra_copy['about_1_title_ar']);

        Livewire::actingAs($admin)->test(CreateNavigationItem::class)
            ->fillForm(['location' => 'footer', 'target' => 'contact', 'label_ar' => 'كلّمنا', 'label_en' => 'Contact us', 'sort_order' => 100, 'published' => true])
            ->call('create')->assertHasNoFormErrors();
        $this->assertTrue(NavigationItem::query()->where('label_ar', 'كلّمنا')->exists());

        Livewire::actingAs($admin)->test(CreateSeoSetting::class)
            ->fillForm(['home_title_ar' => 'عنوان', 'home_title_en' => 'Title', 'allow_indexing' => true])
            ->call('create')->assertHasNoFormErrors();
        $this->assertTrue(SeoSetting::query()->first()->allow_indexing);
    }

    public function test_admin_can_prepare_homepage_hero_as_private_draft(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateHomepageHero::class)
            ->fillForm([
                'kicker_ar' => 'شركة مواقع', 'kicker_en' => 'Web studio',
                'headline_ar' => 'عنوان جديد', 'headline_en' => 'New headline',
                'accent_ar' => 'للمواقع.', 'accent_en' => 'for websites.',
                'description_ar' => 'وصف عربي', 'description_en' => 'English description',
                'published' => false,
            ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('homepage_heroes', ['headline_ar' => 'عنوان جديد', 'published' => false]);
        $this->assertFalse(HomepageHeroResource::canCreate());
        $this->get('/')->assertDontSee('data-home-ar="عنوان جديد"', false);

        $hero = HomepageHero::query()->firstOrFail();
        Livewire::actingAs($admin)->test(EditHomepageHero::class, ['record' => $hero->id])
            ->fillForm(['headline_ar' => 'مواقع تخدم عملك', 'published' => true])
            ->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertOk()->assertSee('data-home-ar="مواقع تخدم عملك"', false);
    }

    public function test_admin_can_change_the_homepage_video_without_editing_the_design_file(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateHomepageVideo::class)
            ->fillForm(['video_url' => 'https://cdn.keenguild.com/reel.mp4', 'published' => false])
            ->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('homepage_videos', ['video_url' => 'https://cdn.keenguild.com/reel.mp4', 'published' => false]);
        $this->assertFalse(HomepageVideoResource::canCreate());
        $this->get('/')->assertSee('data-src="/assets/showreel.mp4"', false);

        $video = HomepageVideo::query()->firstOrFail();
        Livewire::actingAs($admin)->test(EditHomepageVideo::class, ['record' => $video->id])
            ->fillForm(['published' => true])->call('save')->assertHasNoFormErrors();

        $this->get('/')->assertSee('data-src="https://cdn.keenguild.com/reel.mp4"', false);
    }

    public function test_admin_can_upload_an_mp4_for_the_homepage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateHomepageVideo::class)
            ->fillForm([
                'video_path' => UploadedFile::fake()->create('reel.mp4', 1024, 'video/mp4'),
                'published' => true,
            ])->call('create')->assertHasNoFormErrors();

        $video = HomepageVideo::query()->firstOrFail();
        $this->assertNotNull($video->video_path);
        Storage::disk('public')->assertExists($video->video_path);
        $this->get('/')->assertSee('data-src="/storage/'.$video->video_path.'"', false);
    }

    public function test_admin_can_add_an_article_category_before_publishing_articles(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateArticleCategory::class)
            ->fillForm([
                'slug' => 'websites', 'name_ar' => 'المواقع', 'name_en' => 'Websites',
                'description_ar' => 'مقالات عن المواقع', 'description_en' => 'Articles about websites',
                'sort_order' => 0,
            ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('article_categories', ['slug' => 'websites', 'name_ar' => 'المواقع']);
        $this->assertCount(1, ArticleCategory::all());
    }

    public function test_admin_factor_edit_changes_public_estimate_without_changing_package_base_price(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 20000, 'pricing_mode' => 'estimate', 'published' => true]);
        PricingSetting::create(['key' => 'complexity_moderate', 'value' => 1.2]);

        $payload = ['options' => [], 'complexity' => 'moderate', 'urgency' => 'flexible'];
        $this->postJson("/ar/pricing/{$package->id}/estimate", $payload)->assertOk()->assertJsonPath('total', 24000);

        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'complexity_moderate'])
            ->fillForm(['value' => 1.3])->call('save')->assertHasNoFormErrors();

        $this->assertDatabaseHas('pricing_settings', ['key' => 'complexity_moderate', 'value' => 1.3]);
        $this->assertDatabaseHas('packages', ['id' => $package->id, 'base_price_egp' => 20000]);
        $this->postJson("/ar/pricing/{$package->id}/estimate", $payload)->assertOk()->assertJsonPath('total', 26000);

        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'complexity_moderate'])
            ->fillForm(['value' => 3])->call('save')->assertHasFormErrors(['value' => 'max']);
        $this->assertSame('1.300', PricingSetting::find('complexity_moderate')->value);
    }

    public function test_admin_can_adjust_estimate_range_without_changing_starting_price(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'pricing_mode' => 'estimate', 'published' => true]);
        $payload = ['options' => [], 'complexity' => 'standard', 'urgency' => 'flexible'];

        $this->postJson("/ar/pricing/{$package->id}/estimate", $payload)
            ->assertOk()->assertJsonPath('base', 27000)->assertJsonPath('low', 27000)->assertJsonPath('high', 29500);

        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'estimate_range_percent'])
            ->fillForm(['value' => 20])->call('save')->assertHasNoFormErrors();

        $this->postJson("/ar/pricing/{$package->id}/estimate", $payload)
            ->assertOk()->assertJsonPath('base', 27000)->assertJsonPath('total', 27000)
            ->assertJsonPath('low', 27000)->assertJsonPath('high', 32500);
        $this->assertDatabaseHas('packages', ['id' => $package->id, 'base_price_egp' => 27000]);

        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'estimate_range_percent'])
            ->fillForm(['value' => 40])->call('save')->assertHasFormErrors(['value' => 'max']);
    }

    public function test_admin_cannot_invert_pricing_factor_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        foreach (PricingSetting::FACTOR_DEFAULTS as $key => $value) {
            PricingSetting::create(['key' => $key, 'value' => $value]);
        }

        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'complexity_high'])
            ->fillForm(['value' => 1.1])->call('save')->assertHasFormErrors(['value']);
        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'complexity_standard'])
            ->fillForm(['value' => 1.3])->call('save')->assertHasFormErrors(['value']);
        Livewire::actingAs($admin)->test(EditPricingSetting::class, ['record' => 'urgency_flexible'])
            ->fillForm(['value' => 1.2])->call('save')->assertHasFormErrors(['value']);

        $this->assertSame('1.400', PricingSetting::find('complexity_high')->value);
        $this->assertSame('1.000', PricingSetting::find('complexity_standard')->value);
        $this->assertSame('1.000', PricingSetting::find('urgency_flexible')->value);
    }

    public function test_admin_can_add_an_unpublished_contact_channel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateContactChannel::class)
            ->fillForm(['platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/keenguild', 'published' => false, 'sort_order' => 0])
            ->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('contact_channels', ['platform' => 'LinkedIn', 'published' => false]);
        $this->get('/ar/contact')->assertDontSee('linkedin.com/company/keenguild');
    }

    public function test_admin_can_reserve_contact_channels_without_official_details(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (['Email', 'WhatsApp'] as $platform) {
            Livewire::actingAs($admin)->test(CreateContactChannel::class)
                ->fillForm(['platform' => $platform, 'url' => null, 'published' => false, 'sort_order' => 0])
                ->call('create')->assertHasNoFormErrors();

            $this->assertDatabaseHas('contact_channels', ['platform' => $platform, 'url' => null, 'published' => false]);
        }

        $this->get('/ar/contact')->assertOk()->assertDontSee('mailto:')->assertDontSee('wa.me/');
    }

    public function test_admin_cannot_publish_contact_channel_without_official_details(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateContactChannel::class)
            ->fillForm(['platform' => 'Email', 'url' => null, 'published' => true, 'sort_order' => 0])
            ->call('create')->assertHasFormErrors(['url']);

        $this->assertDatabaseMissing('contact_channels', ['platform' => 'Email']);
    }

    public function test_admin_cannot_publish_a_platform_homepage_as_company_contact(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateContactChannel::class)
            ->fillForm([
                'platform' => 'Instagram',
                'url' => 'https://www.instagram.com/',
                'published' => true,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['url']);

        $this->assertDatabaseMissing('contact_channels', ['platform' => 'Instagram']);
    }

    public function test_admin_cannot_publish_a_reserved_example_email_as_official_contact(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateContactChannel::class)
            ->fillForm(['platform' => 'Email', 'url' => 'team@example.test', 'published' => true, 'sort_order' => 0])
            ->call('create')->assertHasFormErrors(['url']);

        $this->assertDatabaseMissing('contact_channels', ['platform' => 'Email']);
    }

    public function test_admin_can_save_article_and_faq_as_private_drafts(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateArticle::class)
            ->fillForm([
                'slug' => 'choosing-a-website', 'title_ar' => 'اختيار الموقع', 'title_en' => 'Choosing a website',
                'summary_ar' => 'ملخص عربي', 'summary_en' => 'English summary',
                'body_ar' => 'محتوى عربي', 'body_en' => 'English body', 'published' => false,
            ])->call('create')->assertHasNoFormErrors();

        Livewire::actingAs($admin)->test(CreateFaq::class)
            ->fillForm([
                'question_ar' => 'ما البداية؟', 'question_en' => 'Where to start?',
                'answer_ar' => 'نراجع الهدف.', 'answer_en' => 'We review the goal.',
                'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', ['slug' => 'choosing-a-website', 'published' => false]);
        $this->assertDatabaseHas('faqs', ['question_en' => 'Where to start?', 'published' => false]);
        $this->get('/ar/articles/choosing-a-website')->assertNotFound();
        $this->get('/ar/faq')->assertDontSee('ما البداية؟');
    }

    public function test_admin_can_save_a_service_as_unpublished_draft(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateService::class)
            ->fillForm([
                'slug' => 'websites',
                'category' => 'web',
                'title_ar' => 'خدمة ألف تجريبية',
                'title_en' => 'Website development',
                'summary_ar' => 'مواقع احترافية',
                'summary_en' => 'Professional websites',
                'featured' => true,
                'published' => false,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('services', ['slug' => 'websites', 'published' => false]);
        $this->get('/ar/services')->assertDontSee('خدمة ألف تجريبية');
    }

    public function test_admin_cannot_publish_a_service_without_bilingual_summary(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateService::class)
            ->fillForm([
                'slug' => 'empty-service', 'category' => 'web',
                'title_ar' => 'خدمة جديدة', 'title_en' => 'New service',
                'summary_ar' => null, 'summary_en' => null,
                'published' => true, 'sort_order' => 0,
            ])->call('create')->assertHasFormErrors([
                'summary_ar' => 'required', 'summary_en' => 'required',
                'body_ar' => 'required', 'body_en' => 'required',
            ]);

        $this->assertDatabaseMissing('services', ['slug' => 'empty-service']);
    }

    public function test_admin_can_save_bilingual_package_without_publishing_price(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => false]);

        Livewire::actingAs($admin)->test(CreatePackage::class)
            ->fillForm([
                'service_id' => $service->id,
                'slug' => 'company',
                'name_ar' => 'موقع شركة',
                'name_en' => 'Company website',
                'base_price_egp' => 27000,
                'pricing_mode' => 'estimate',
                'included_pages' => 5,
                'included_features' => ['خمس صفحات'],
                'included_features_en' => ['Five pages'],
                'excluded_costs' => ['الاستضافة'],
                'excluded_costs_en' => ['Hosting'],
                'published' => false,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('packages', ['slug' => 'company', 'base_price_egp' => 27000, 'published' => false]);
        $this->get('/ar/pricing')->assertDontSee('27,000');
    }

    public function test_admin_cannot_publish_a_priced_package_without_bilingual_scope(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);

        Livewire::actingAs($admin)->test(CreatePackage::class)
            ->fillForm([
                'service_id' => $service->id, 'slug' => 'no-scope',
                'name_ar' => 'باقة جديدة', 'name_en' => 'New package',
                'base_price_egp' => 12000, 'pricing_mode' => 'estimate',
                'included_pages' => 1,
                'description_ar' => null, 'description_en' => null,
                'included_features' => [], 'included_features_en' => [],
                'published' => true, 'sort_order' => 0,
            ])->call('create')->assertHasFormErrors([
                'description_ar' => 'required', 'description_en' => 'required',
                'included_features' => 'required', 'included_features_en' => 'required',
            ]);

        $this->assertDatabaseMissing('packages', ['slug' => 'no-scope']);
    }

    public function test_estimate_package_requires_a_base_price(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);

        Livewire::actingAs($admin)->test(CreatePackage::class)
            ->fillForm([
                'service_id' => $service->id,
                'slug' => 'unpriced',
                'name_ar' => 'بلا سعر',
                'name_en' => 'No price',
                'pricing_mode' => 'estimate',
                'base_price_egp' => null,
                'included_pages' => 0,
                'published' => false,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['base_price_egp' => 'required']);

        $this->assertDatabaseMissing('packages', ['slug' => 'unpriced']);
    }

    public function test_estimate_package_cannot_publish_a_zero_starting_price(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);

        Livewire::actingAs($admin)->test(CreatePackage::class)
            ->fillForm([
                'service_id' => $service->id,
                'slug' => 'zero-price',
                'name_ar' => 'باقة بلا سعر',
                'name_en' => 'Zero-price package',
                'pricing_mode' => 'estimate',
                'base_price_egp' => 0,
                'included_pages' => 1,
                'published' => true,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['base_price_egp' => 'min']);

        $this->assertDatabaseMissing('packages', ['slug' => 'zero-price']);
    }

    public function test_admin_cannot_save_fractional_prices_or_page_counts(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);

        Livewire::actingAs($admin)->test(CreatePackage::class)
            ->fillForm([
                'service_id' => $service->id,
                'slug' => 'fractional',
                'name_ar' => 'باقة تجريبية',
                'name_en' => 'Test package',
                'pricing_mode' => 'estimate',
                'base_price_egp' => '27000.5',
                'included_pages' => '2.5',
                'published' => false,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['base_price_egp' => 'integer', 'included_pages' => 'integer']);

        $this->assertDatabaseMissing('packages', ['slug' => 'fractional']);
    }

    public function test_admin_can_save_a_draft_price_option_and_concept_project(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'pricing_mode' => 'estimate']);

        Livewire::actingAs($admin)->test(CreatePriceOption::class)
            ->fillForm([
                'package_id' => $package->id, 'code' => 'blog',
                'label_ar' => 'مدونة', 'label_en' => 'Blog',
                'calculation_type' => 'fixed', 'amount_egp' => 8000,
                'percent' => 0, 'max_quantity' => 1,
                'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasNoFormErrors();

        Livewire::actingAs($admin)->test(CreateProject::class)
            ->fillForm([
                'slug' => 'flowboard', 'title_ar' => 'فلو بورد', 'title_en' => 'Flowboard',
                'summary_ar' => 'نموذج تجربة', 'summary_en' => 'Concept demo',
                'project_type' => 'concept', 'demo_status' => 'unavailable',
                'featured' => false, 'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('price_options', ['code' => 'blog', 'published' => false]);
        $this->assertDatabaseHas('projects', ['slug' => 'flowboard', 'project_type' => 'concept', 'published' => false]);
        $this->get('/ar/work/flowboard')->assertNotFound();
    }

    public function test_admin_cannot_publish_an_unpriced_estimator_option(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites']);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'pricing_mode' => 'estimate']);

        foreach (['fixed' => 'free_fixed', 'per_unit' => 'free_unit', 'percent' => 'free_percent'] as $type => $code) {
            Livewire::actingAs($admin)->test(CreatePriceOption::class)
                ->fillForm([
                    'package_id' => $package->id, 'code' => $code,
                    'label_ar' => 'إضافة', 'label_en' => 'Extra',
                    'calculation_type' => $type, 'amount_egp' => 0,
                    'percent' => 0, 'max_quantity' => 1,
                    'published' => true, 'sort_order' => 0,
                ])->call('create')->assertHasFormErrors([$type === 'percent' ? 'percent' : 'amount_egp' => 'min']);

            $this->assertDatabaseMissing('price_options', ['code' => $code]);
        }
    }

    public function test_project_upload_rejects_svg_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateProject::class)
            ->fillForm([
                'slug' => 'unsafe-cover', 'title_ar' => 'تجربة', 'title_en' => 'Concept',
                'project_type' => 'concept', 'demo_status' => 'unavailable',
                'cover_path' => UploadedFile::fake()->create('unsafe.svg', 1, 'image/svg+xml'),
                'featured' => false, 'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasFormErrors(['cover_path']);

        $this->assertDatabaseMissing('projects', ['slug' => 'unsafe-cover']);
    }

    public function test_admin_cannot_mark_a_private_demo_url_ready(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateProject::class)
            ->fillForm([
                'slug' => 'local-demo', 'title_ar' => 'تجربة محلية', 'title_en' => 'Local demo',
                'project_type' => 'concept', 'demo_status' => 'ready',
                'demo_url' => 'https://user:password@localhost/preview',
                'featured' => false, 'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasFormErrors(['demo_url']);

        $this->assertDatabaseMissing('projects', ['slug' => 'local-demo']);

        Livewire::actingAs($admin)->test(CreateProject::class)
            ->fillForm([
                'slug' => 'test-demo', 'title_ar' => 'تجربة تجريبية', 'title_en' => 'Test demo',
                'project_type' => 'concept', 'demo_status' => 'ready',
                'demo_url' => 'https://preview.example.test/demo',
                'featured' => false, 'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasFormErrors(['demo_url']);

        $this->assertDatabaseMissing('projects', ['slug' => 'test-demo']);

        Livewire::actingAs($admin)->test(CreateProject::class)
            ->fillForm([
                'slug' => 'public-demo', 'title_ar' => 'تجربة عامة', 'title_en' => 'Public demo',
                'scope_ar' => 'واجهة اختبارية', 'scope_en' => 'Prototype interface',
                'project_type' => 'concept', 'demo_status' => 'ready',
                'demo_url' => 'https://preview.design-studio.co/demo',
                'featured' => false, 'published' => false, 'sort_order' => 0,
            ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', ['slug' => 'public-demo', 'scope_en' => 'Prototype interface']);
    }

    public function test_published_project_needs_bilingual_summary_and_description(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(CreateProject::class)
            ->fillForm([
                'slug' => 'incomplete-project',
                'title_ar' => 'مشروع غير مكتمل',
                'title_en' => 'Incomplete project',
                'project_type' => 'concept',
                'demo_status' => 'unavailable',
                'published' => true,
                'featured' => false,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['summary_ar' => 'required', 'summary_en' => 'required', 'body_ar' => 'required', 'body_en' => 'required']);

        $this->assertDatabaseMissing('projects', ['slug' => 'incomplete-project']);
    }

    public function test_admin_can_update_quote_status_without_altering_customer_message(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $quote = QuoteRequest::create([
            'name' => 'Customer', 'email' => 'customer@example.test',
            'project_brief' => 'A website with a clear service catalog and multilingual pages.',
            'status' => 'new',
        ]);

        Livewire::actingAs($admin)->test(EditQuoteRequest::class, ['record' => $quote->id])
            ->fillForm(['status' => 'reviewing'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('quote_requests', [
            'id' => $quote->id, 'status' => 'reviewing',
            'project_brief' => 'A website with a clear service catalog and multilingual pages.',
        ]);
    }

    public function test_admin_can_delete_a_quote_request_from_the_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $quote = QuoteRequest::create([
            'name' => 'Customer', 'email' => 'customer@example.test',
            'project_brief' => 'A website with a clear service catalog and multilingual pages.',
            'status' => 'closed',
        ]);

        Livewire::actingAs($admin)->test(ListQuoteRequests::class)
            ->callTableAction('delete', $quote);

        $this->assertDatabaseMissing('quote_requests', ['id' => $quote->id]);
    }
}
