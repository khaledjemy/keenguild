<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ContactChannel;
use App\Models\Faq;
use App\Models\HomepageContent;
use App\Models\HomepageHero;
use App\Models\HomepageVideo;
use App\Models\LegalPage;
use App\Models\NavigationItem;
use App\Models\Package;
use App\Models\PageContent;
use App\Models\PriceOption;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_homepage_hero_is_bilingual_and_keeps_admin_copy(): void
    {
        HomepageHero::create([
            'kicker_ar' => 'عنوان مخصص', 'kicker_en' => 'Custom label',
            'headline_ar' => 'فكرة مخصصة', 'headline_en' => 'Custom idea',
            'accent_ar' => 'تجربة مخصصة', 'accent_en' => 'Custom experience',
            'description_ar' => 'وصف مخصص', 'description_en' => 'Custom description',
            'published' => true,
        ]);

        $this->get('/')->assertOk()
            ->assertSee('class="neo-hero"', false)
            ->assertSee('data-hero-slot="headline" data-home-ar="فكرة مخصصة" data-home-en="Custom idea"', false)
            ->assertSee('data-hero-slot="description" data-home-ar="وصف مخصص" data-home-en="Custom description"', false)
            ->assertDontSee('hero-orb device', false);

        $this->get('/en')->assertOk()
            ->assertSee('Custom idea', false)
            ->assertSee('Custom experience', false)
            ->assertSee('role="tablist"', false);
    }

    public function test_draft_homepage_hero_has_private_bilingual_admin_preview(): void
    {
        $hero = HomepageHero::create([
            'kicker_ar' => 'مسودة خاصة', 'kicker_en' => 'Private draft',
            'headline_ar' => 'عنوان مسودة', 'headline_en' => 'Draft headline',
            'accent_ar' => 'تجربة مسودة', 'accent_en' => 'Draft experience',
            'description_ar' => 'وصف مسودة', 'description_en' => 'Draft description',
            'published' => false,
        ]);

        $arabicUrl = route('preview.home.hero', ['locale' => 'ar', 'hero' => $hero], false);
        $englishUrl = route('preview.home.hero', ['locale' => 'en', 'hero' => $hero], false);
        $this->get($arabicUrl)->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get($arabicUrl)->assertForbidden();
        $this->get('/')->assertOk()->assertDontSee('مسودة خاصة');

        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get('/admin/homepage-heroes')->assertOk()
            ->assertSee('معاينة عربية')->assertSee('English preview');
        $arabic = $this->get($arabicUrl)->assertOk()->assertSee('مسودة خاصة')
            ->assertSee('name="robots" content="noindex,nofollow"', false)
            ->assertDontSee('rel="canonical"', false);
        $this->assertStringContainsString('no-store', $arabic->headers->get('Cache-Control'));
        $english = $this->get($englishUrl)->assertOk()->assertSee('Draft headline')
            ->assertSee('Private draft')->assertSee('name="robots" content="noindex,nofollow"', false);
        $this->assertStringContainsString('no-store', $english->headers->get('Cache-Control'));
        $this->get('/en')->assertOk()->assertDontSee('Draft headline');
    }

    public function test_english_homepage_is_server_rendered_and_indexable_separately(): void
    {
        $this->setHomepageContent(['content' => [
            'demos_heading_ar' => 'تجارب مخصصة', 'demos_heading_en' => 'Custom experiences',
        ]]);

        $this->get('/')->assertOk()
            ->assertSee('rel="alternate" hreflang="en" href="http://localhost/en"', false)
            ->assertSee("location.href='http://localhost/en'+location.hash", false);
        $this->get('/en')->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee('rel="canonical" href="http://localhost/en"', false)
            ->assertSee('rel="alternate" hreflang="ar" href="http://localhost"', false)
            ->assertSee('>Custom experiences</span>', false)
            ->assertSee('href="/en" data-catalog-ar=', false)
            ->assertSee('data-catalog-en="Home"', false)
            ->assertSee('Big ideas,')->assertSee('Quieter execution.')
            ->assertSee('content="KeenGuild builds thoughtful websites and digital products around your business goals."', false)
            ->assertDontSee('<?xml encoding=', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('<loc>http://localhost/en</loc>', false);

        SeoSetting::create(['allow_indexing' => false]);
        $this->get('/en')->assertOk()->assertSee('name="robots" content="noindex,nofollow"', false);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('<loc>', false);
    }

    public function test_catalog_brand_returns_to_home_in_the_current_language(): void
    {
        $this->get('/ar/pricing')->assertOk()->assertSee('class="brand" href="http://localhost"', false);
        $this->get('/en/pricing')->assertOk()->assertSee('class="brand" href="http://localhost/en"', false);
        $this->get('/en/articles')->assertOk()->assertSee('class="brand" href="http://localhost/en"', false);
    }

    public function test_public_page_internal_links_resolve_in_both_languages(): void
    {
        $pages = ['/', '/en'];
        foreach (['ar', 'en'] as $locale) {
            foreach (['services', 'pricing', 'work', 'articles', 'contact', 'about', 'faq', 'demos/flowboard', 'demos/storefront', 'demos/pulse'] as $page) {
                $pages[] = "/{$locale}/{$page}";
            }
        }
        $checked = [];
        foreach ($pages as $page) {
            $html = $this->get($page)->assertOk()->getContent();
            preg_match_all('/href="([^"]+)"/', $html, $matches);
            $paths = [];

            foreach ($matches[1] as $rawHref) {
                $href = html_entity_decode($rawHref, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $host = parse_url($href, PHP_URL_HOST);
                if ($host !== null && $host !== 'localhost') {
                    continue;
                }
                if ($host === null && !str_starts_with($href, '/')) {
                    continue;
                }
                $path = parse_url($href, PHP_URL_PATH) ?: '/';
                if (str_starts_with($path, '//') || preg_match('~^/(assets|storage|build)/~', $path)) {
                    continue;
                }
                $paths[$path] = true;
            }

            $this->assertGreaterThan(3, count($paths), "No public links found on {$page}");
            foreach (array_keys($paths) as $path) {
                if (!isset($checked[$path])) {
                    $this->get($path)->assertSuccessful();
                    $checked[$path] = true;
                }
            }
        }
    }

    public function test_english_homepage_preserves_javascript_templates_used_by_contact_preview(): void
    {
        $this->get('/en')->assertOk()
            ->assertSee("</option></select></label><label class=\"preview-field full\">", false)
            ->assertSee("</button></form><div class=\"mt-5 grid sm:grid-cols-2 gap-3\">", false)
            ->assertSee('previewReturnFocus=document.activeElement', false)
            ->assertSee('if(previewReturnFocus?.isConnected)previewReturnFocus.focus()', false)
            ->assertSee("if(e.key!=='Tab')return", false)
            ->assertSee("i.textContent=open?'×':'＋'", false)
            ->assertDontSee('data-keenguild-preserve', false);
    }

    public function test_mobile_homepage_can_scroll_through_sections_taller_than_the_viewport(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('html { scroll-snap-type:none !important; }', false)
            ->assertSee('main > section { height:auto !important; min-height:100dvh !important;', false)
            ->assertSee('#proof .proof-grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; grid-auto-rows:auto !important; height:auto !important;', false)
            ->assertSee('#proof .proof-grid { grid-template-columns:minmax(0,1fr) !important; }', false)
            ->assertSee('#demos article { min-height:164px !important; height:auto !important;', false)
            ->assertSee('#demos article p { display:block; overflow:visible;', false)
            ->assertSee('#demos article { grid-template-columns:minmax(0,1fr) !important; }', false)
            ->assertSee('#work article { min-height:390px !important; height:auto !important;', false);
        $this->get('/en')->assertOk()
            ->assertSee('html { scroll-snap-type:none !important; }', false);
    }

    public function test_homepage_content_is_escaped_and_keeps_unedited_design_text(): void
    {
        $this->setHomepageContent(['content' => [
            'proof_heading_ar' => '<script>alert(1)</script>',
            'proof_heading_en' => 'Clear direction',
            'demo_1_title_ar' => 'لوحة جديدة',
            'demo_1_title_en' => 'New board',
        ]]);

        $this->get('/')->assertOk()
            ->assertSee('data-home-ar="&lt;script&gt;alert(1)&lt;/script&gt;"', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('data-home-ar="لوحة جديدة"', false)
            ->assertSee('aria-live="polite">نحذف الضوضاء من الطريق حتى يظهر جوهر المنتج، ويشعر العميل بقيمته من أول لحظة.</p>', false)
            ->assertSee('تجربة سريعة.', false);
    }

    public function test_every_homepage_copy_field_from_admin_reaches_the_homepage(): void
    {
        config()->set('keenguild.inquiries_enabled', true);
        config()->set('keenguild.privacy_url', 'https://example.test/privacy');

        $fields = [
            'nav_studio', 'nav_availability', 'nav_cta', 'orbit_kicker',
            'menu_heading', 'menu_description', 'menu_cta',
            'proof_kicker', 'proof_heading', 'proof_description',
            'services_kicker', 'services_heading', 'services_description',
            'demos_kicker', 'demos_heading', 'demos_description',
            'work_kicker', 'work_heading', 'journal_kicker', 'journal_heading',
            'reel_kicker', 'reel_heading', 'reel_description', 'reel_button',
            'contact_kicker', 'contact_heading', 'contact_description', 'contact_button',
            'footer_tagline',
        ];
        for ($i = 1; $i <= 4; $i++) {
            array_push($fields, "menu_{$i}_title", "menu_{$i}_description", "proof_{$i}_title", "proof_{$i}_description");
        }
        for ($i = 1; $i <= 3; $i++) {
            array_push($fields, "demo_{$i}_title", "demo_{$i}_description");
        }
        $content = [];
        foreach ($fields as $field) {
            $content[$field.'_ar'] = 'KG_AR_'.$field;
            $content[$field.'_en'] = 'KG_EN_'.$field;
        }
        $this->setHomepageContent(['content' => $content]);

        $html = $this->get('/')->assertOk()->getContent();
        $missing = array_values(array_filter($fields, fn (string $field): bool => ! str_contains($html, 'KG_AR_'.$field)
            || ! str_contains($html, 'KG_EN_'.$field)));
        $this->assertSame([], $missing, 'Homepage admin fields missing from rendered HTML');
    }

    public function test_homepage_copy_can_be_edited_in_one_language_without_changing_the_other(): void
    {
        $this->setHomepageContent(['content' => [
            'proof_heading_ar' => 'عنوان عربي مستقل',
            'reel_button_en' => 'Watch our reel',
        ]]);

        $this->get('/')->assertOk()
            ->assertSee('عنوان عربي مستقل')
            ->assertDontSee('Watch our reel');
        $this->get('/en')->assertOk()
            ->assertSee('From idea')
            ->assertSee('Watch our reel')
            ->assertDontSee('عنوان عربي مستقل');
    }

    public function test_managed_demo_and_reel_copy_is_present_with_language_switch_guards(): void
    {
        $this->setHomepageContent(['content' => [
            'demos_heading_ar' => 'تجارب من الإدارة', 'demos_heading_en' => 'Managed demos',
            'demos_description_ar' => 'وصف مخصص', 'demos_description_en' => 'Managed description',
            'reel_heading_ar' => 'فيديو مخصص', 'reel_heading_en' => 'Managed reel',
            'reel_button_ar' => 'شغّل الفيديو', 'reel_button_en' => 'Play video',
        ]]);

        $response = $this->get('/')->assertOk()
            ->assertSee('data-home-en="Managed demos"', false)
            ->assertSee('data-home-en="Managed description"', false)
            ->assertSee('data-home-en="Managed reel"', false)
            ->assertSee('data-home-en="Play video"', false);
        $this->assertStringContainsString("!heading.querySelector('[data-home-ar]')", $response->getContent());
        $this->assertStringContainsString("!button.querySelector('[data-home-ar]')", $response->getContent());
    }

    public function test_homepage_menu_cards_can_change_copy_and_safe_targets(): void
    {
        $this->setHomepageContent(['content' => [
            'menu_heading_ar' => 'خريطة جديدة', 'menu_heading_en' => 'New map',
            'menu_1_title_ar' => 'خدماتنا', 'menu_1_title_en' => 'Our services',
            'menu_1_target' => '#pricing',
            'menu_2_target' => 'javascript:alert(1)',
            'menu_cta_ar' => 'كلّمنا', 'menu_cta_en' => 'Talk to us',
        ]]);

        $this->get('/')->assertOk()
            ->assertSee('data-home-ar="خريطة جديدة"', false)
            ->assertSee('data-home-en="Our services"', false)
            ->assertSee('data-home-en="Talk to us"', false)
            ->assertSee('href="/ar/pricing" onclick="closeMenu()" class="menu-card', false)
            ->assertDontSee('href="javascript:alert(1)"', false);
        $this->get('/en')->assertOk()
            ->assertSee('href="/en/pricing" onclick="closeMenu()" class="menu-card', false)
            ->assertDontSee('href="javascript:alert(1)"', false);
    }

    public function test_homepage_short_labels_and_contact_action_follow_managed_copy(): void
    {
        $this->setHomepageContent(['content' => [
            'nav_studio_ar' => 'فريق تصميم', 'nav_studio_en' => 'Design team',
            'nav_cta_ar' => 'ابدأ الآن', 'nav_cta_en' => 'Start now',
            'orbit_kicker_ar' => 'فكرة جديدة', 'orbit_kicker_en' => 'New idea',
            'reel_button_ar' => 'شغّل العرض', 'reel_button_en' => 'Play reel',
        ]]);

        $this->get('/')->assertOk()->assertSee('data-home-ar="فريق تصميم"', false)
            ->assertSee('data-home-en="Start now"', false)
            ->assertSee('<span class="arabic-copy">فكرة جديدة</span>', false)
            ->assertSee('data-home-en="Play reel"', false);
    }

    public function test_unapproved_social_links_do_not_appear_in_footer_or_floating_network(): void
    {
        ContactChannel::create(['platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/keenguild', 'published' => true]);
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('~<div class="social-floater-links">.*?</div>~s', $html, $floating);
        preg_match('~<nav class="social-links".*?</nav>~s', $html, $footer);
        $this->assertStringContainsString('aria-label="LinkedIn"', $floating[0] ?? '');
        $this->assertStringNotContainsString('aria-label="WhatsApp"', $floating[0] ?? '');
        $this->assertStringNotContainsString('aria-label="WhatsApp"', $footer[0] ?? '');
        $this->assertStringContainsString('points.flatMap', $html);
    }

    public function test_uploaded_brand_paths_are_used_on_home_and_catalog(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('branding/logo.png', 'image');
        Storage::disk('public')->put('branding/favicon.png', 'image');
        $this->setHomepageContent([
            'logo_path' => 'branding/logo.png',
            'favicon_path' => 'branding/favicon.png',
            'content' => ['footer_tagline_ar' => 'رسالة جديدة', 'footer_tagline_en' => 'A new message'],
        ]);

        $this->get('/')->assertOk()->assertSee('src="http://localhost/storage/branding/logo.png"', false)
            ->assertSee('href="http://localhost/storage/branding/favicon.png"', false);
        $this->get('/en/pricing')->assertOk()->assertSee('src="http://localhost/storage/branding/logo.png"', false)
            ->assertSee('A new message');
    }

    public function test_missing_or_unsafe_brand_files_fall_back_to_the_default_mark(): void
    {
        Storage::fake('public');
        $branding = $this->setHomepageContent([
            'logo_path' => 'branding/deleted.png',
            'favicon_path' => 'branding/deleted.png',
        ]);

        $this->get('/')->assertOk()->assertSee('src="/assets/brand/favicon-new2.png"', false)
            ->assertDontSee('/storage/branding/deleted.png', false);
        $this->get('/en/pricing')->assertOk()
            ->assertSee('src="http://localhost/assets/brand/favicon-new2.png"', false)
            ->assertSee('href="http://localhost/assets/brand/favicon-new2.png"', false);

        Storage::disk('public')->put('branding/logo.png', 'image');
        $branding->update(['logo_path' => '../branding/logo.png']);
        $this->get('/')->assertOk()->assertDontSee('/storage/../branding/logo.png', false);
    }

    public function test_homepage_orbit_logo_and_contact_background_are_managed_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('branding/orbit.png', 'image');
        Storage::disk('public')->put('homepage/contact.webp', 'image');
        $content = $this->setHomepageContent([
            'orbit_logo_path' => 'branding/orbit.png',
            'contact_background_path' => 'homepage/contact.webp',
        ]);

        $this->get('/')->assertOk()
            ->assertSee('class="orbit-logo" src="http://localhost/storage/branding/orbit.png"', false)
            ->assertSee('id="managed-contact-background"', false)
            ->assertSee('http://localhost/storage/homepage/contact.webp', false);
        $this->get('/en')->assertOk()->assertSee('http://localhost/storage/homepage/contact.webp', false);

        $content->update(['orbit_logo_path' => '../bad.svg', 'contact_background_path' => 'homepage/missing.webp']);
        $this->get('/')->assertOk()->assertSee('class="orbit-logo" src="/assets/brand/kg-transparent.png"', false)
            ->assertDontSee('id="managed-contact-background"', false);
    }

    public function test_navigation_edits_update_home_and_catalog_without_exposing_unpublished_items(): void
    {
        $item = NavigationItem::query()->where('location', 'header')->where('target', 'pricing')->firstOrFail();
        $item->update(['label_ar' => 'باقاتنا', 'label_en' => 'Our packages']);
        NavigationItem::query()->where('location', 'footer')->where('target', 'articles')->update(['published' => false]);

        $this->get('/')->assertOk()->assertSee('data-catalog-ar="باقاتنا"', false);
        $response = $this->get('/ar/pricing')->assertOk()->assertSee('باقاتنا');
        $this->assertMatchesRegularExpression('~<details class="mobile-nav">.*?<a[^>]*href="/ar/pricing"[^>]*>باقاتنا</a>.*?</details>~s', $response->getContent());
        preg_match('~<footer\b.*?</footer>~s', $response->getContent(), $footer);
        $this->assertStringNotContainsString('href="/ar/articles"', $footer[0] ?? '');
        $englishPricing = $this->get('/en/pricing')->assertOk()->assertSee('Our packages');
        $this->assertMatchesRegularExpression('~<details class="mobile-nav">.*?<a[^>]*href="/en/pricing"[^>]*>Our packages</a>.*?</details>~s', $englishPricing->getContent());
        $this->get('/en')->assertOk()->assertSee('href="/en/pricing"', false)
            ->assertSee('data-catalog-en="Our packages"', false);

        NavigationItem::create([
            'location' => 'footer', 'target' => 'home',
            'label_ar' => 'العودة للرئيسية', 'label_en' => 'Back to home',
            'sort_order' => 999, 'published' => true,
        ]);
        $englishHome = $this->get('/en')->assertOk()->getContent();
        preg_match('~<a[^>]*data-catalog-en="Back to home"[^>]*>.*?</a>~s', $englishHome, $homeLink);
        $this->assertStringContainsString('href="/en"', $homeLink[0] ?? '');
    }

    public function test_section_navigation_returns_to_the_matching_homepage_language(): void
    {
        $item = NavigationItem::create([
            'location' => 'footer', 'target' => 'section:pricing',
            'label_ar' => 'أسعار الرئيسية', 'label_en' => 'Home pricing',
            'sort_order' => 999, 'published' => true,
        ]);

        $this->assertSame('/ar/pricing', $item->publicUrl('ar'));
        $this->assertSame('/en/pricing', $item->publicUrl('en'));
        $this->assertSame('/en/pricing', $item->publicUrl('en', true));
        $this->get('/en/services')->assertOk()->assertSee('href="/en/pricing"', false);
    }

    public function test_seo_settings_control_home_metadata_and_public_indexing(): void
    {
        $seo = SeoSetting::create([
            'home_title_ar' => 'عنوان الموقع', 'home_title_en' => 'Site title',
            'default_description_ar' => 'وصف الموقع', 'default_description_en' => 'Site description',
            'allow_indexing' => false,
        ]);

        $this->get('/')->assertOk()->assertSee('<title>عنوان الموقع</title>', false)
            ->assertSee('content="وصف الموقع"', false)->assertSee('name="robots" content="noindex,nofollow"', false);
        $this->get('/en')->assertOk()->assertSee('<title>Site title</title>', false)
            ->assertSee('content="Site description"', false);
        $this->get('/en/pricing')->assertOk()->assertSee('content="Site description"', false)
            ->assertSee('name="robots" content="noindex,nofollow"', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');

        $seo->update(['allow_indexing' => true]);
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap:');
    }

    public function test_home_seo_copy_can_be_changed_in_one_language_without_hiding_the_other(): void
    {
        $seo = SeoSetting::create([
            'home_title_ar' => 'عنوان عربي جديد',
            'default_description_ar' => 'وصف عربي جديد',
            'allow_indexing' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('<title>عنوان عربي جديد</title>', false)
            ->assertSee('content="وصف عربي جديد"', false);
        $this->get('/en')->assertOk()->assertSee('<title>KeenGuild — We build products that grow with you</title>', false)
            ->assertSee('content="KeenGuild builds thoughtful websites and digital products around your business goals."', false);

        $seo->update([
            'home_title_ar' => null, 'default_description_ar' => null,
            'home_title_en' => 'Fresh English title',
            'default_description_en' => 'Fresh English description',
        ]);
        $this->get('/')->assertOk()->assertSee('<title>KeenGuild — نبني المنتجات التي تكبر معك</title>', false)
            ->assertSee('content="KeenGuild — تصميم مواقع وحلول برمجية', false);
        $this->get('/en')->assertOk()->assertSee('<title>Fresh English title</title>', false)
            ->assertSee('content="Fresh English description"', false);
    }

    public function test_internal_page_content_can_be_managed_without_changing_article_categories(): void
    {
        PageContent::create([
            'page_key' => 'articles',
            'heading_ar' => 'مقالات مفيدة', 'heading_en' => 'Useful articles',
            'intro_ar' => 'مقدمة خاصة', 'intro_en' => 'Custom intro',
            'meta_description_ar' => 'وصف البحث', 'meta_description_en' => 'Search description',
        ]);

        $this->get('/ar/articles')->assertOk()->assertSee('<title>مقالات مفيدة — KeenGuild</title>', false)
            ->assertSee('<h1>مقالات مفيدة</h1>', false)->assertSee('content="وصف البحث"', false);
        $this->get('/en/articles')->assertOk()->assertSee('Useful articles')->assertSee('Custom intro');
    }

    public function test_about_and_pricing_steps_are_editable_without_changing_required_price_notice(): void
    {
        PageContent::create(['page_key' => 'about', 'extra_copy' => [
            'about_1_title_ar' => 'نسمعك أولًا', 'about_1_title_en' => 'Listen first',
        ]]);
        PageContent::create(['page_key' => 'pricing', 'extra_copy' => [
            'pricing_2_description_ar' => 'إضافات حسب الحاجة', 'pricing_2_description_en' => 'Extras as needed',
        ]]);

        $this->get('/ar/about')->assertOk()->assertSee('نسمعك أولًا');
        $this->get('/en/about')->assertOk()->assertSee('Listen first');
        $this->get('/ar/pricing')->assertOk()->assertSee('إضافات حسب الحاجة')
            ->assertSee('الأسعار المعروضة، إن وُجدت، تقديرية بالجنيه المصري');
    }

    public function test_each_project_can_have_a_safe_image_walkthrough_separate_from_live_demo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/cover.jpg', 'cover');
        Storage::disk('public')->put('projects/detail.png', 'detail');
        $project = Project::create([
            'slug' => 'walkthrough', 'title_ar' => 'جولة', 'title_en' => 'Walkthrough',
            'summary_ar' => 'ملخص', 'summary_en' => 'Summary',
            'body_ar' => 'تفاصيل', 'body_en' => 'Details',
            'project_type' => 'concept', 'published' => true,
            'cover_path' => 'projects/cover.jpg',
            'gallery_paths' => ['projects/detail.png', '../unsafe.svg'],
        ]);

        $this->get('/ar/work/walkthrough')->assertOk()->assertSee('جولة التصميم');
        $this->get('/ar/work/walkthrough/tour')->assertOk()
            ->assertSee('name="robots" content="noindex,nofollow"', false)
            ->assertSee('storage/projects/cover.jpg')->assertSee('storage/projects/detail.png')
            ->assertDontSee('unsafe.svg')->assertSee('وليست ديمو وظيفيًا');
        $this->get('/en/work/walkthrough/tour')->assertOk()->assertSee('Design walkthrough');

        $project->update(['published' => false]);
        $this->get('/ar/work/walkthrough/tour')->assertNotFound();
        $admin = User::factory()->create(['is_admin' => true]);
        $preview = $this->actingAs($admin)->get('/admin/preview/work/ar/walkthrough/tour')->assertOk();
        $this->assertStringContainsString('no-store', $preview->headers->get('Cache-Control'));
    }

    public function test_draft_services_and_articles_have_private_bilingual_previews_only(): void
    {
        Service::create(['slug' => 'future-service', 'title_ar' => 'خدمة قادمة', 'title_en' => 'Future service', 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'تفاصيل', 'body_en' => 'Details']);
        Article::create(['slug' => 'future-article', 'title_ar' => 'مقال قادم', 'title_en' => 'Future article', 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'نص', 'body_en' => 'Body']);

        $this->get('/ar/services/future-service')->assertNotFound();
        $this->get('/en/articles/future-article')->assertNotFound();
        $this->get('/admin/preview/services/ar/future-service')->assertRedirect('/admin/login');
        $this->get('/admin/preview/articles/en/future-article')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/preview/services/ar/future-service')->assertForbidden();
        $this->get('/admin/preview/articles/en/future-article')->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $service = $this->actingAs($admin)->get('/admin/preview/services/ar/future-service')->assertOk()
            ->assertSee('خدمة قادمة')->assertSee('name="robots" content="noindex,nofollow"', false)
            ->assertDontSee('rel="canonical"', false);
        $this->assertStringContainsString('no-store', $service->headers->get('Cache-Control'));
        $this->get('/admin/preview/services/en/future-service')->assertOk()->assertSee('Future service');
        $article = $this->get('/admin/preview/articles/en/future-article')->assertOk()
            ->assertSee('Future article')->assertSee('Private admin preview')
            ->assertSee('name="robots" content="noindex,nofollow"', false);
        $this->assertStringContainsString('no-store', $article->headers->get('Cache-Control'));
        $this->get('/admin/preview/articles/ar/future-article')->assertOk()->assertSee('مقال قادم');
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('future-service')->assertDontSee('future-article');
    }

    public function test_home_demo_cards_can_be_curated_from_published_projects_with_live_demos(): void
    {
        $this->setHomepageContent(['content' => [
            'demo_1_title_ar' => 'عنوان القالب', 'demo_1_title_en' => 'Template title',
            'demo_1_description_ar' => 'وصف القالب', 'demo_1_description_en' => 'Template description',
        ]]);
        $project = Project::create([
            'slug' => 'client-demo', 'title_ar' => 'مشروع تجريبي', 'title_en' => 'Client demo',
            'summary_ar' => 'تجربة منشورة', 'summary_en' => 'Published experience',
            'body_ar' => 'تفاصيل', 'body_en' => 'Details', 'project_type' => 'concept',
            'published' => true, 'featured_in_demos' => true,
            'demo_status' => 'ready', 'demo_url' => 'https://demo.keenguild.com/',
        ]);

        $this->get('/')->assertOk()->assertSee('data-home-demo-project="client-demo"', false)
            ->assertSee('https://demo.keenguild.com/')->assertSee('data-home-en="Client demo"', false)
            ->assertDontSee('onclick="openDemo(\'Flowboard\')"', false);
        $home = $this->get('/')->assertOk()->getContent();
        preg_match('~<article[^>]*data-home-demo-project="client-demo".*?</article>~s', $home, $card);
        $this->assertStringContainsString('مشروع تجريبي', $card[0] ?? '');
        $this->assertStringNotContainsString('عنوان القالب', $card[0] ?? '');
        $this->assertStringNotContainsString('وصف القالب', $card[0] ?? '');
        $englishHome = $this->get('/en')->assertOk()->getContent();
        preg_match('~<article[^>]*data-home-demo-project="client-demo".*?</article>~s', $englishHome, $englishCard);
        $this->assertStringContainsString('Client demo', $englishCard[0] ?? '');
        $this->assertStringNotContainsString('Template title', $englishCard[0] ?? '');

        $project->update(['demo_status' => 'unavailable']);
        $this->get('/')->assertOk()->assertDontSee('data-home-demo-project="client-demo"', false)
            ->assertSee('href="/ar/demos/flowboard"', false)
            ->assertDontSee('onclick="openDemo(\'Flowboard\')"', false);
    }

    public function test_default_home_demo_cards_open_interactive_concepts_in_each_language(): void
    {
        foreach (['ar' => '/', 'en' => '/en'] as $locale => $homeUrl) {
            $home = $this->get($homeUrl)->assertOk();
            foreach (['flowboard', 'storefront', 'pulse'] as $slug) {
                $home->assertSee('href="/'.$locale.'/demos/'.$slug.'"', false);
                $this->get('/'.$locale.'/demos/'.$slug)->assertOk()
                    ->assertSee('data-demo=', false);
            }
        }
    }

    public function test_home_showreel_waits_until_the_visitor_nears_its_section(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('preload="none"', false)
            ->assertSee('data-src="/assets/showreel.mp4"', false)
            ->assertDontSee('<source src="/assets/showreel.mp4"', false)
            ->assertSee("rootMargin:'800px 0px'", false);
    }

    public function test_home_showreel_supports_browser_byte_range_requests(): void
    {
        $response = $this->withHeader('Range', 'bytes=0-1023')->get('/assets/showreel.mp4');

        $response->assertStatus(206)
            ->assertHeader('Content-Type', 'video/mp4')
            ->assertHeader('Accept-Ranges', 'bytes');
        $this->assertStringStartsWith('bytes 0-1023/', (string) $response->headers->get('Content-Range'));
        $this->assertSame(1024, strlen($response->streamedContent()));
    }

    public function test_home_showreel_changes_only_after_an_admin_publishes_a_valid_video(): void
    {
        $video = HomepageVideo::create(['video_url' => 'https://cdn.keenguild.com/reel.mp4']);
        $this->get('/')->assertOk()->assertSee('data-src="/assets/showreel.mp4"', false);

        $video->update(['published' => true]);
        $this->get('/')->assertOk()->assertSee('data-src="https://cdn.keenguild.com/reel.mp4"', false)
            ->assertDontSee('data-src="/assets/showreel.mp4"', false);

        $video->update(['video_url' => 'javascript:alert(1)']);
        $this->get('/')->assertOk()->assertSee('data-src="/assets/showreel.mp4"', false)
            ->assertDontSee('data-src="javascript:alert(1)"', false);
    }

    public function test_home_showreel_can_use_an_uploaded_public_mp4(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('homepage-videos/reel.mp4', 'test video');
        HomepageVideo::create(['video_path' => 'homepage-videos/reel.mp4', 'published' => true]);

        $this->get('/')->assertOk()->assertSee('data-src="/storage/homepage-videos/reel.mp4"', false);
    }

    public function test_laravel_home_can_point_to_a_separate_agent_without_rewriting_the_design_file(): void
    {
        config()->set('keenguild.agent_api_url', 'https://agent.example.test/');
        config()->set('keenguild.agent_site_key', 'site-key-1234567890');

        $this->get('/')->assertOk()
            ->assertSee('<meta name="keenguild-agent-api" content="https://agent.example.test"', false)
            ->assertSee('<meta name="keenguild-agent-site-key" content="site-key-1234567890"', false);

        config()->set('keenguild.agent_api_url', 'http://insecure.example.test');
        config()->set('keenguild.agent_site_key', 'short');
        $this->get('/')->assertOk()
            ->assertSee('<meta name="keenguild-agent-api" content=""', false)
            ->assertSee('<meta name="keenguild-agent-site-key" content=""', false);
    }

    public function test_published_detail_pages_have_localized_metadata_and_admin_previews_stay_private(): void
    {
        Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'summary_ar' => 'مواقع سريعة وآمنة', 'summary_en' => 'Fast and secure websites', 'published' => true]);
        Article::create(['slug' => 'planning', 'title_ar' => 'التخطيط', 'title_en' => 'Planning', 'summary_ar' => 'خطوات <العمل>', 'summary_en' => 'Steps to build', 'body_ar' => 'نص', 'body_en' => 'Body', 'published' => true, 'published_at' => now()->subDay()]);
        Project::create(['slug' => 'sample', 'title_ar' => 'مشروع', 'title_en' => 'Project', 'summary_ar' => 'ملخص العمل', 'summary_en' => 'Project summary', 'body_ar' => 'تفاصيل العمل', 'body_en' => 'Project details', 'published' => true]);
        Project::create(['slug' => 'draft', 'title_ar' => 'مسودة', 'title_en' => 'Draft', 'summary_ar' => 'ملخص مسودة', 'summary_en' => 'Draft summary', 'published' => false]);

        $this->get('/ar/services/websites')->assertOk()->assertSee('content="مواقع سريعة وآمنة"', false)
            ->assertSee('rel="canonical" href="http://localhost/ar/services/websites"', false)
            ->assertSee('hreflang="en" href="http://localhost/en/services/websites"', false);
        $this->get('/en/services/websites')->assertOk()->assertSee('content="Fast and secure websites"', false);
        $this->get('/ar/articles/planning')->assertOk()->assertSee('content="خطوات &lt;العمل&gt;"', false)
            ->assertSee('hreflang="en" href="http://localhost/en/articles/planning"', false);
        $this->get('/en/work/sample')->assertOk()->assertSee('content="Project summary"', false)
            ->assertSee('hreflang="ar" href="http://localhost/ar/work/sample"', false);

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/preview/work/ar/draft')->assertOk()->assertSee('noindex,nofollow')
            ->assertDontSee('rel="canonical"', false)->assertDontSee('rel="alternate"', false)
            ->assertSee('/admin/preview/work/en/draft');
    }

    public function test_legal_pages_are_bilingual_escaped_and_hidden_until_reviewed(): void
    {
        LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'سياسة الخصوصية', 'title_en' => 'Privacy policy',
            'body_ar' => 'بيانات تجريبية <script>alert(1)</script>', 'body_en' => 'Draft privacy text',
        ]);
        $this->get('/ar/legal/privacy')->assertNotFound();
        $this->get('/ar/services')->assertOk()->assertDontSee('/ar/legal/privacy');

        LegalPage::where('type', 'privacy')->update(['published' => true]);
        $this->get('/ar/legal/privacy')->assertOk()->assertSee('سياسة الخصوصية')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('/en/legal/privacy');
        $this->get('/en/legal/privacy')->assertOk()->assertSee('Draft privacy text')->assertDontSee('بيانات تجريبية');
        $this->get('/ar/services')->assertOk()->assertSee('/ar/legal/privacy');
        $this->get('/')->assertOk()->assertSee('/ar/legal/privacy')->assertDontSee('/ar/legal/terms');
        $this->get('/ar/legal/terms')->assertNotFound();
        $this->get('/ar/legal/unknown')->assertNotFound();
    }

    public function test_admin_can_preview_unpublished_legal_pages_without_exposing_them_publicly(): void
    {
        LegalPage::create([
            'type' => 'privacy', 'title_ar' => 'سياسة الخصوصية', 'title_en' => 'Privacy policy',
            'body_ar' => 'نص مسودة <script>alert(1)</script>', 'body_en' => 'Draft privacy text',
            'published' => false,
        ]);

        $this->get('/admin/preview/legal/ar/privacy')->assertRedirect('/admin/login');
        $this->actingAs(\App\Models\User::factory()->create())
            ->get('/admin/preview/legal/ar/privacy')->assertForbidden();
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/preview/legal/ar/privacy')->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('معاينة خاصة للمشرف')->assertSee('نص مسودة')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertSee('name="robots" content="noindex,nofollow"', false)
            ->assertDontSee('rel="canonical"', false)
            ->assertSee('/admin/preview/legal/en/privacy', false);
        $this->get('/admin/preview/legal/en/privacy')->assertOk()->assertSee('Draft privacy text');
        $this->get('/ar/legal/privacy')->assertNotFound();
        $this->get('/admin/preview/legal/ar/terms')->assertNotFound();
    }

    public function test_existing_homepage_concepts_have_separate_clearly_labeled_demo_pages(): void
    {
        $this->get('/ar/work')->assertOk()->assertSee('Flowboard')->assertSee('Storefront')->assertSee('Pulse')->assertSee('تجارب واجهة افتراضية');
        $this->get('/ar/demos/flowboard')->assertOk()->assertSee('معاينة تصميمية ببيانات افتراضية')->assertSee('خريطة المنتج')
            ->assertSee('name="robots" content="noindex,nofollow"', false);
        $this->get('/en/demos/storefront')->assertOk()->assertSee('Fictional product')->assertSee('/ar/demos/storefront');
        $this->get('/ar/demos/pulse')->assertOk()->assertSee('زيارات افتراضية');
        $this->get('/ar/demos/unknown')->assertNotFound();
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_contact_channels_require_confirmed_valid_account_links(): void
    {
        ContactChannel::create(['platform' => 'LinkedIn', 'url' => 'https://www.linkedin.com/company/keenguild', 'published' => false]);
        ContactChannel::create(['platform' => 'Instagram', 'url' => 'https://www.instagram.com/', 'published' => true]);
        ContactChannel::create(['platform' => 'GitHub', 'url' => 'https://example.com/keenguild', 'published' => true]);
        ContactChannel::create(['platform' => 'Email', 'url' => 'contact@gmail.com', 'published' => true]);

        $this->get('/ar/contact')->assertOk()->assertSee('mailto:contact@gmail.com')->assertDontSee('linkedin.com/company/keenguild')->assertDontSee('https://example.com/keenguild');
        $this->get('/')->assertOk()
            ->assertSee('data-official-url="mailto:contact@gmail.com"', false)
            ->assertSee('href="mailto:contact@gmail.com"', false)
            ->assertDontSee('data-official-url="https://example.com/keenguild"', false)
            ->assertDontSee('href="https://www.instagram.com/"', false)
            ->assertDontSee('mailto:hello@keenguild.com', false);

        ContactChannel::where('platform', 'LinkedIn')->update(['published' => true]);
        $this->get('/ar/contact')->assertSee('https://www.linkedin.com/company/keenguild');
        $this->get('/')->assertSee('data-official-url="https://www.linkedin.com/company/keenguild"', false)
            ->assertSee('href="https://www.linkedin.com/company/keenguild"', false);
    }

    public function test_about_page_only_lists_published_services(): void
    {
        Service::create(['slug' => 'websites', 'title_ar' => 'تطوير المواقع', 'title_en' => 'Website development', 'published' => true]);
        Service::create(['slug' => 'hidden', 'title_ar' => 'خدمة مخفية', 'title_en' => 'Hidden service', 'published' => false]);

        $this->get('/ar/about')->assertOk()->assertSee('تطوير المواقع')->assertDontSee('خدمة مخفية');
        $this->get('/en/about')->assertOk()->assertSee('Website development')->assertDontSee('Hidden service');
    }

    public function test_articles_are_bilingual_and_only_available_after_publication(): void
    {
        Article::create(['slug' => 'draft', 'title_ar' => 'مسودة', 'title_en' => 'Draft', 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'نص مسودة', 'body_en' => 'Draft body']);
        Article::create(['slug' => 'future', 'title_ar' => 'لاحقًا', 'title_en' => 'Later', 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'نص لاحق', 'body_en' => 'Future body', 'published' => true, 'published_at' => now()->addDay()]);
        Article::create(['slug' => 'live', 'title_ar' => 'مقال منشور', 'title_en' => 'Published article', 'summary_ar' => 'ملخص منشور', 'summary_en' => 'Published summary', 'body_ar' => 'محتوى عربي', 'body_en' => 'English body', 'published' => true, 'published_at' => now()->subDay()]);

        $this->get('/ar/articles')->assertOk()->assertSee('مقال منشور')->assertDontSee('مسودة')->assertDontSee('لاحقًا');
        $this->get('/en/articles')->assertOk()->assertSee('Published article')->assertDontSee('مقال منشور');
        $this->get('/ar/articles/draft')->assertNotFound();
        $this->get('/ar/articles/future')->assertNotFound();
        $this->get('/en/articles/live')->assertOk()->assertSee('English body')->assertDontSee('محتوى عربي');
    }

    public function test_faq_page_only_shows_published_content_in_the_selected_language(): void
    {
        Faq::create(['question_ar' => 'سؤال مخفي', 'question_en' => 'Hidden question', 'answer_ar' => 'إجابة مخفية', 'answer_en' => 'Hidden answer']);
        Faq::create(['question_ar' => 'كيف نبدأ؟', 'question_en' => 'How do we start?', 'answer_ar' => 'نحدد النطاق أولًا.', 'answer_en' => 'We define the scope first.', 'published' => true]);

        $this->get('/ar/faq')->assertOk()->assertSee('كيف نبدأ؟')->assertSee('نحدد النطاق أولًا.')->assertDontSee('سؤال مخفي')->assertDontSee('How do we start?');
        $this->get('/en/faq')->assertOk()->assertSee('How do we start?')->assertSee('We define the scope first.')->assertDontSee('Hidden question')->assertDontSee('كيف نبدأ؟');
    }

    public function test_existing_homepage_is_available_with_new_catalog_links(): void
    {
        $this->get('/')->assertOk()->assertSee('KeenGuild')->assertSee('/ar/about')->assertSee('/ar/pricing')->assertSee('/ar/services')->assertSee('/ar/work')->assertSee('/ar/articles')->assertSee('/ar/faq')
            ->assertSee('rel="canonical" href="http://localhost"', false)
            ->assertSee('href="/assets/legacy.css"', false)->assertDontSee('cdn.tailwindcss.com')
            ->assertSee('aria-label="صفحات KeenGuild"', false)
            ->assertSee('href="/ar/pricing" data-catalog-ar="الأسعار" data-catalog-en="Pricing"', false)
            ->assertSee('تصورات تفاعلية.')->assertSee('Interactive concepts.')->assertDontSee('منتجات حقيقية.')->assertDontSee('Real products.')
            ->assertSee('بيانات تجريبية')->assertDontSee('مباشر الآن')
            ->assertSee('data-home-en="Working with ambitious teams"', false)
            ->assertDontSee('<span>القاهرة · نعمل مع فرق طموحة</span>', false)
            ->assertDontSee('© 2026 KeenGuild · القاهرة', false)
            ->assertSee('© '.date('Y').' KeenGuild')
            ->assertSee('تُرسل الرسائل إلى خدمة المساعد المستقلة ويُحفظ سجل المحادثة.')
            ->assertDontSee('لا يتم إرسال الرسائل أو حفظها')
            ->assertSee('aria-label="التبديل إلى الإنجليزية" class="inline-flex', false)
            ->assertDontSee('class="hidden sm:inline-flex bg-white text-ink', false);
        $this->get('/agent-api-bridge.js')->assertOk()
            ->assertSee('Messages are sent to the standalone assistant service and the conversation is saved.');
        $this->get('/assets/legacy.css')->assertOk()->assertHeader('Content-Type', 'text/css; charset=UTF-8');
        $legacyCss = file_get_contents(base_path('../dist/assets/legacy.css'));
        $this->assertStringContainsString('.bg-ink', $legacyCss);
        $this->assertStringContainsString('.md\\:col-span-7', $legacyCss);
        $this->assertStringContainsString('.md\\:col-span-12', $legacyCss);
        $this->get('/assets/journal/ux-question.png')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/assets/brand/unknown.php')->assertNotFound();
    }

    public function test_home_service_cards_link_only_to_published_laravel_services(): void
    {
        Service::create(['slug' => 'websites', 'title_ar' => 'تطوير المواقع', 'title_en' => 'Website development', 'summary_ar' => 'خدمة مواقع مخصصة', 'summary_en' => 'Custom website service', 'published' => true, 'featured_on_home' => true]);
        $store = Service::create(['slug' => 'ecommerce', 'title_ar' => 'متاجر إلكترونية', 'title_en' => 'Online stores', 'published' => false, 'featured_on_home' => true]);

        $response = $this->get('/')->assertOk()
            ->assertSee('data-home-service-slug="websites"', false)
            ->assertSee('href="/ar/services/websites"', false)
            ->assertDontSee('data-home-service-slug="ecommerce"', false)
            ->assertDontSee('href="/ar/services/ecommerce"', false)
            ->assertSee('خدمة مواقع مخصصة')
            ->assertDontSee('<h3 class="text-2xl font-black">متاجر إلكترونية</h3>', false);
        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, 'data-home-service-slug="websites"'));

        $store->update(['published' => true]);
        $this->get('/')->assertSee('href="/ar/services/ecommerce"', false);

        $store->update(['featured_on_home' => false]);
        $this->get('/')->assertDontSee('data-home-service-slug="ecommerce"', false);
    }

    public function test_homepage_journal_switches_from_labeled_previews_to_only_published_articles(): void
    {
        $this->get('/')->assertOk()->assertSee('لا توجد مقالات منشورة بعد')->assertSee('data-home-route="articles"', false)
            ->assertDontSee('onclick="openArticlePreview(0)"', false)
            ->assertDontSee('<h3 class="text-2xl font-black leading-tight mt-7">هل تحتاج تطبيقًا مخصصًا أم منتج SaaS؟</h3>', false);

        Article::create(['slug' => 'draft', 'title_ar' => 'مقال مسودة', 'title_en' => 'Draft article', 'summary_ar' => 'مسودة', 'summary_en' => 'Draft', 'body_ar' => 'نص', 'body_en' => 'Body']);
        Article::create(['slug' => 'future', 'title_ar' => 'مقال لاحق', 'title_en' => 'Future article', 'summary_ar' => 'لاحق', 'summary_en' => 'Future', 'body_ar' => 'نص', 'body_en' => 'Body', 'published' => true, 'published_at' => now()->addDay()]);
        Article::create(['slug' => 'live', 'title_ar' => 'مقال منشور <script>', 'title_en' => 'Published article', 'summary_ar' => 'ملخص حقيقي', 'summary_en' => 'Real summary', 'body_ar' => 'نص', 'body_en' => 'Body', 'published' => true, 'published_at' => now()->subDay()]);

        $this->get('/')->assertOk()->assertSee('href="http://localhost/ar/articles/live"', false)
            ->assertSee('data-home-en="Published article"', false)->assertSee('ملخص حقيقي')
            ->assertSee('مقال منشور &lt;script&gt;', false)->assertDontSee('مقال منشور <script>', false)
            ->assertDontSee('/ar/articles/draft')->assertDontSee('/ar/articles/future')
            ->assertDontSee('onclick="openArticlePreview(0)"', false)->assertSee('id="reel"', false);
    }

    public function test_article_categories_have_crawlable_bilingual_pages_and_only_show_published_articles(): void
    {
        $category = ArticleCategory::create(['slug' => 'websites', 'name_ar' => 'المواقع', 'name_en' => 'Websites', 'description_ar' => 'مقالات عن المواقع', 'description_en' => 'Articles about websites']);
        $this->get('/ar/articles/category/websites')->assertNotFound();

        Article::create(['article_category_id' => $category->id, 'slug' => 'planning', 'title_ar' => 'تخطيط الموقع', 'title_en' => 'Website planning', 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'نص', 'body_en' => 'Body', 'published' => true, 'published_at' => now()->subDay()]);
        Article::create(['article_category_id' => $category->id, 'slug' => 'draft', 'title_ar' => 'مسودة', 'title_en' => 'Draft', 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'نص', 'body_en' => 'Body']);

        $this->get('/ar/articles')->assertOk()->assertSee('/ar/articles/category/websites')->assertSee('/ar/articles/planning');
        $this->get('/ar/articles/category/websites')->assertOk()->assertSee('تخطيط الموقع')->assertDontSee('/ar/articles/draft')
            ->assertSee('rel="canonical" href="http://localhost/ar/articles/category/websites"', false)
            ->assertSee('hreflang="en" href="http://localhost/en/articles/category/websites"', false);
        $this->get('/en/articles/category/websites')->assertOk()->assertSee('Website planning');
        $this->get('/ar/articles/planning')->assertOk()->assertSee('/ar/articles/category/websites');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/ar/articles/category/websites')->assertSee('/en/articles/category/websites');
        $this->get('/ar/articles/category/missing')->assertNotFound();
    }

    public function test_article_pagination_keeps_its_own_canonical_and_language_links(): void
    {
        $category = ArticleCategory::create(['slug' => 'websites', 'name_ar' => 'المواقع', 'name_en' => 'Websites']);
        for ($index = 1; $index <= 10; $index++) {
            Article::create(['article_category_id' => $category->id, 'slug' => 'article-'.$index, 'title_ar' => 'مقال '.$index, 'title_en' => 'Article '.$index, 'summary_ar' => 'ملخص', 'summary_en' => 'Summary', 'body_ar' => 'نص', 'body_en' => 'Body', 'published' => true, 'published_at' => now()->subDay()]);
        }

        $this->get('/ar/articles?page=2')->assertOk()
            ->assertSee('rel="canonical" href="http://localhost/ar/articles?page=2"', false)
            ->assertSee('hreflang="en" href="http://localhost/en/articles?page=2"', false);
        $this->get('/ar/articles/category/websites?page=2')->assertOk()
            ->assertSee('rel="canonical" href="http://localhost/ar/articles/category/websites?page=2"', false)
            ->assertSee('hreflang="en" href="http://localhost/en/articles/category/websites?page=2"', false);
    }

    public function test_homepage_links_to_pricing_page_without_rendering_price_section(): void
    {
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'landing', 'name_ar' => 'صفحة هبوط', 'name_en' => 'Landing page', 'description_ar' => 'صفحة واحدة', 'description_en' => 'One page', 'base_price_egp' => 12000, 'pricing_mode' => 'estimate', 'published' => true, 'featured_on_home' => true]);

        $this->get('/')->assertOk()->assertDontSee('id="pricing"', false)->assertDontSee('12,000')
            ->assertSee('href="/ar/pricing"', false);
        $this->get('/en')->assertOk()->assertDontSee('id="pricing"', false)->assertSee('href="/en/pricing"', false);
        $this->get('/ar/pricing')->assertOk()->assertSee('12,000');
        $package->update(['base_price_egp' => 13500]);
        $this->get('/')->assertOk()->assertDontSee('13,500');
        $this->get('/ar/pricing')->assertOk()->assertSee('13,500')->assertDontSee('12,000');
    }

    public function test_legacy_home_featured_flag_no_longer_adds_pricing_section(): void
    {
        $service = Service::create(['slug' => 'websites', 'title_ar' => 'مواقع', 'title_en' => 'Websites', 'published' => true]);
        $first = Package::create(['service_id' => $service->id, 'slug' => 'custom-site', 'name_ar' => 'موقع مخصص', 'name_en' => 'Custom site', 'description_ar' => 'تفاصيل', 'description_en' => 'Details', 'base_price_egp' => 38000, 'pricing_mode' => 'estimate', 'published' => true, 'featured_on_home' => false]);
        $this->get('/')->assertOk()->assertDontSee('38,000');

        $first->update(['featured_on_home' => true]);
        $this->get('/')->assertOk()->assertDontSee('id="pricing"', false)->assertDontSee('38,000');
        $this->get('/ar/pricing')->assertOk()->assertSee('38,000');
    }

    public function test_homepage_hero_uses_only_approved_bilingual_copy_and_escapes_editor_text(): void
    {
        $hero = HomepageHero::create([
            'kicker_ar' => 'نص جديد', 'kicker_en' => 'New kicker',
            'headline_ar' => 'نبني المواقع', 'headline_en' => 'We build websites',
            'accent_ar' => 'بوضوح.', 'accent_en' => 'with clarity.',
            'description_ar' => 'وصف <script>تجريبي', 'description_en' => 'A focused web studio.',
            'published' => false,
        ]);

        $this->get('/')->assertOk()->assertDontSee('data-home-ar="نبني المواقع"', false);

        $hero->update(['published' => true]);
        $this->get('/')->assertOk()->assertSee('data-home-ar="نبني المواقع"', false)
            ->assertSee('data-home-en="We build websites"', false)
            ->assertSee('data-home-en="A focused web studio."', false)
            ->assertSee('وصف &lt;script&gt;تجريبي', false)
            ->assertDontSee('وصف <script>تجريبي', false);
    }

    public function test_homepage_work_shows_only_public_projects_and_ready_https_demos(): void
    {
        $this->get('/')->assertOk()->assertSee('نماذج واجهات للتصور');

        Project::create(['slug' => 'private-client', 'title_ar' => 'عميل خاص', 'title_en' => 'Private client', 'project_type' => 'client', 'published' => true, 'display_permission_confirmed' => false]);
        Project::create(['slug' => 'draft-project', 'title_ar' => 'مشروع مسودة', 'title_en' => 'Draft project', 'published' => false]);
        Project::create(['slug' => 'approved-client', 'title_ar' => 'عميل منشور', 'title_en' => 'Approved client', 'summary_ar' => 'وصف مشروع حقيقي', 'summary_en' => 'Real project summary', 'body_ar' => 'تفاصيل التنفيذ', 'body_en' => 'Implementation details', 'project_type' => 'client', 'published' => true, 'display_permission_confirmed' => true, 'featured' => true, 'demo_status' => 'ready', 'demo_url' => 'https://demo.design-studio.co']);
        Project::create(['slug' => 'concept', 'title_ar' => 'تصور منشور', 'title_en' => 'Published concept', 'summary_ar' => 'تصور فقط', 'summary_en' => 'Concept only', 'body_ar' => 'فكرة الواجهة', 'body_en' => 'Interface concept', 'published' => true, 'demo_status' => 'ready', 'demo_url' => 'http://insecure.example.test']);

        $this->get('/')->assertOk()->assertSee('href="http://localhost/ar/work/approved-client"', false)
            ->assertSee('href="http://localhost/ar/work/concept"', false)
            ->assertSee('data-home-en="Approved client"', false)
            ->assertSee('https://demo.design-studio.co')->assertDontSee('http://insecure.example.test')
            ->assertDontSee('/ar/work/private-client')->assertDontSee('/ar/work/draft-project')
            ->assertDontSee('onclick="openWorkPreview(\'academy\')"', false)
            ->assertSee('id="journal"', false);
    }

    public function test_only_published_services_have_public_detail_pages(): void
    {
        Service::create(['slug' => 'hidden', 'title_ar' => 'خدمة مخفية', 'title_en' => 'Hidden service', 'published' => false]);
        Service::create(['slug' => 'websites', 'title_ar' => 'تطوير المواقع', 'title_en' => 'Website development', 'published' => true]);

        $this->get('/ar/services')->assertOk()->assertSee('تطوير المواقع')->assertDontSee('خدمة مخفية');
        $this->get('/ar/services/hidden')->assertNotFound();
        $this->get('/en/services/websites')->assertOk()->assertSee('Website development');
    }

    public function test_draft_packages_are_not_public(): void
    {
        $service = Service::create(['slug' => 'web', 'title_ar' => 'المواقع', 'title_en' => 'Websites', 'published' => false]);
        Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'published' => false]);

        $this->get('/ar/pricing')->assertOk()->assertDontSee('27,000');
    }

    public function test_admin_can_preview_draft_prices_and_options_without_publishing_them(): void
    {
        $service = Service::create(['slug' => 'web', 'title_ar' => 'المواقع', 'title_en' => 'Websites', 'published' => false]);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'pricing_mode' => 'estimate', 'published' => false]);
        PriceOption::create(['package_id' => $package->id, 'code' => 'blog', 'label_ar' => 'مدونة', 'label_en' => 'Blog', 'calculation_type' => 'fixed', 'amount_egp' => 8000, 'published' => false]);
        $payload = ['options' => ['blog' => 1], 'complexity' => 'standard', 'urgency' => 'flexible'];

        $this->get('/ar/pricing')->assertOk()->assertDontSee('27,000');
        $this->postJson("/ar/pricing/{$package->id}/estimate", $payload)->assertNotFound();
        $this->get('/admin/preview/pricing/ar')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create())->get('/admin/preview/pricing/ar')->assertForbidden();
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin/preview/pricing/ar')
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')->assertSee('27,000')->assertSee('معاينة للمشرف فقط')->assertSee('noindex,nofollow');
        $this->postJson("/admin/preview/pricing/ar/{$package->id}/estimate", $payload)
            ->assertOk()->assertJsonPath('total', 35000);
    }

    public function test_published_package_appears_on_pricing_page(): void
    {
        $service = Service::create(['slug' => 'web', 'title_ar' => 'المواقع', 'title_en' => 'Websites', 'published' => true]);
        Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'published' => true, 'included_features' => ['خمس صفحات'], 'included_features_en' => ['Five pages']]);

        $this->get('/ar/pricing')->assertOk()->assertSee('يبدأ من')->assertSee('27,000')->assertSee('ج.م')->assertSee('المواقع')->assertSee('خمس صفحات')->assertDontSee('Five pages');
        $this->get('/en/pricing')->assertOk()->assertSee('Starting at')->assertSee('27,000')->assertSee('EGP')->assertSee('Websites')->assertSee('Five pages')->assertDontSee('خمس صفحات');
    }

    public function test_only_published_projects_are_accessible(): void
    {
        Project::create(['slug' => 'demo', 'title_ar' => 'مشروع تجريبي', 'title_en' => 'Demo project', 'published' => false]);
        $this->get('/ar/work')->assertOk()->assertDontSee('مشروع تجريبي');
        $this->get('/ar/work/demo')->assertNotFound();
        $this->get('/admin/preview/work/ar/demo')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/preview/work/ar/demo')->assertForbidden();
        $this->actingAs(User::factory()->create(['is_admin' => true]))->get('/admin/preview/work/ar/demo')
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')->assertSee('مشروع تجريبي')->assertSee('معاينة للمشرف فقط')->assertSee('noindex,nofollow');
    }

    public function test_incomplete_bilingual_project_copy_stays_private_even_when_published(): void
    {
        $project = Project::create([
            'slug' => 'unfinished', 'title_ar' => 'مشروع غير مكتمل', 'title_en' => 'Unfinished project',
            'summary_ar' => 'ملخص عربي', 'summary_en' => 'English summary',
            'body_ar' => 'تفاصيل عربية', 'body_en' => '   ',
            'project_type' => 'concept', 'published' => true,
        ]);

        $this->assertFalse($project->isPubliclyVisible());
        $this->get('/ar/work')->assertOk()->assertDontSee('مشروع غير مكتمل');
        $this->get('/en/work/unfinished')->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/ar/work/unfinished');
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin/preview/work/ar/unfinished')->assertOk()->assertSee('مشروع غير مكتمل');

        $project->update(['body_en' => 'Complete English details']);
        $this->assertTrue($project->fresh()->isPubliclyVisible());
        $this->get('/en/work/unfinished')->assertOk()->assertSee('Complete English details');
    }

    public function test_demo_link_requires_published_project_ready_status_and_https_url(): void
    {
        $project = Project::create(['slug' => 'showcase', 'title_ar' => 'عرض', 'title_en' => 'Showcase', 'summary_ar' => 'ملخص العرض', 'summary_en' => 'Showcase summary', 'body_ar' => 'تفاصيل العرض', 'body_en' => 'Showcase details', 'scope_ar' => 'تصميم واجهة <script>', 'scope_en' => 'Interface design and build', 'demo_url' => 'https://demo.design-studio.co', 'demo_status' => 'unavailable', 'published' => true]);
        $this->get('/ar/work/showcase')->assertOk()->assertSee('نموذج تصميمي / تجربة')->assertDontSee('افتح الديمو');
        $this->get('/ar/work/showcase')->assertSee('نطاق العمل')->assertSee('تصميم واجهة &lt;script&gt;', false)->assertDontSee('تصميم واجهة <script>', false);
        $this->get('/en/work/showcase')->assertSee('Scope of work')->assertSee('Interface design and build')->assertDontSee('تصميم واجهة');

        $project->update(['demo_status' => 'ready', 'demo_url' => 'http://demo.design-studio.co']);
        $this->get('/ar/work/showcase')->assertOk()->assertDontSee('افتح الديمو');

        $project->update(['demo_url' => 'https://demo.design-studio.co']);
        $this->get('/ar/work/showcase')->assertOk()->assertSee('افتح الديمو')->assertSee('https://demo.design-studio.co');

        foreach (['https://user:password@demo.design-studio.co', 'https://localhost/demo', 'https://preview.local/demo', 'https://preview.local./demo', 'https://preview.internal/demo', 'https://preview.example.test/demo', 'https://preview.example.invalid/demo', 'https://demo.example.com', 'https://127.0.0.1/demo'] as $unsafeUrl) {
            $project->update(['demo_url' => $unsafeUrl]);
            $this->get('/ar/work/showcase')->assertOk()->assertDontSee('افتح الديمو')->assertDontSee($unsafeUrl);
        }
    }

    public function test_work_card_shows_a_direct_demo_link_only_when_the_public_url_is_ready(): void
    {
        $project = Project::create([
            'slug' => 'interface-study', 'title_ar' => 'تجربة واجهة', 'title_en' => 'Interface study',
            'summary_ar' => 'تصور تجريبي', 'summary_en' => 'A design study',
            'body_ar' => 'تفاصيل التجربة', 'body_en' => 'Study details',
            'project_type' => 'concept', 'published' => true,
            'demo_status' => 'ready', 'demo_url' => 'https://demo.design-studio.co/study',
        ]);

        $this->get('/ar/work')->assertOk()->assertSee('جرّب الديمو')
            ->assertSee('href="https://demo.design-studio.co/study" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('favicon-new2.png');
        $this->get('/en/work')->assertOk()->assertSee('Try the demo');

        $project->update(['demo_url' => 'https://localhost/private']);
        $this->get('/ar/work')->assertOk()->assertDontSee('جرّب الديمو')->assertDontSee('https://localhost/private');
    }

    public function test_client_work_requires_explicit_display_permission_even_after_publication(): void
    {
        $project = Project::create([
            'slug' => 'client-example', 'title_ar' => 'عمل عميل خاص', 'title_en' => 'Private client work',
            'summary_ar' => 'ملخص العمل', 'summary_en' => 'Work summary',
            'body_ar' => 'تفاصيل العمل', 'body_en' => 'Work details',
            'project_type' => 'client', 'published' => true,
        ]);

        $this->get('/ar/work')->assertOk()->assertDontSee('عمل عميل خاص');
        $this->get('/ar/work/client-example')->assertNotFound();

        $project->update(['display_permission_confirmed' => true]);
        $this->get('/ar/work')->assertOk()->assertSee('عمل عميل خاص');
        $this->get('/ar/work/client-example')->assertOk()->assertSee('مشروع عميل');

        $project->update(['display_permission_confirmed' => false]);
        $this->get('/ar/work/client-example')->assertNotFound();
    }

    public function test_published_package_returns_server_calculated_estimate(): void
    {
        $service = Service::create(['slug' => 'web', 'title_ar' => 'المواقع', 'title_en' => 'Websites', 'published' => true]);
        $package = Package::create(['service_id' => $service->id, 'slug' => 'company', 'name_ar' => 'شركة', 'name_en' => 'Company', 'base_price_egp' => 27000, 'published' => true]);
        PriceOption::create(['package_id' => $package->id, 'code' => 'page', 'label_ar' => 'صفحة', 'label_en' => 'Page', 'calculation_type' => 'per_unit', 'amount_egp' => 2000, 'max_quantity' => 10, 'published' => true]);

        $this->postJson("/ar/pricing/{$package->id}/estimate", ['options' => ['page' => 2], 'complexity' => 'moderate', 'urgency' => 'flexible'])
            ->assertOk()->assertJsonPath('total', 37000);
        $this->postJson("/ar/pricing/{$package->id}/estimate", ['options' => ['unknown' => 1], 'complexity' => 'standard', 'urgency' => 'flexible'])
            ->assertStatus(422);
    }
}
