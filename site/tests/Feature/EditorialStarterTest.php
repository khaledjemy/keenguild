<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\LegalPage;
use Database\Seeders\EditorialStarterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialStarterTest extends TestCase
{
    use RefreshDatabase;

    public function test_starter_content_is_bilingual_visible_and_safe_to_reseed(): void
    {
        $this->seed(EditorialStarterSeeder::class);
        $this->seed(EditorialStarterSeeder::class);

        $this->assertSame(2, LegalPage::count());
        $this->assertSame(1, ArticleCategory::count());
        $this->assertSame(3, Article::count());
        $this->assertSame('assets/articles/website-brief.png', Article::where('slug', 'website-brief-before-design')->firstOrFail()->cover_path);
        $this->assertSame(0, LegalPage::where('published', true)->count());

        $this->get('/ar/legal/privacy')->assertNotFound();
        $this->get('/ar/articles')->assertOk()->assertSee('كيف تكتب وصفًا واضحًا لمشروع موقعك؟')
            ->assertSee('/assets/articles/website-brief.png', false)
            ->assertSee('/assets/brand/favicon-new2.png', false);
        $this->get('/en/articles')->assertOk()->assertSee('How to write a useful website brief');
        $this->get('/')->assertOk()
            ->assertSee('/assets/articles/website-brief.png', false)
            ->assertDontSee('/storage/assets/articles/', false);
        $this->get('/ar/articles/website-brief-before-design')->assertOk()->assertSee('وصف المشروع الجيد');
        $this->get('/en/articles/landing-page-or-company-website')->assertOk()->assertSee('A landing page usually');

        Article::where('slug', 'website-brief-before-design')->update(['title_ar' => 'عنوان معدل']);
        $this->seed(EditorialStarterSeeder::class);
        $this->assertSame('عنوان معدل', Article::where('slug', 'website-brief-before-design')->firstOrFail()->title_ar);
    }
}
