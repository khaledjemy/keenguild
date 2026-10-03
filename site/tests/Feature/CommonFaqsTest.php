<?php

namespace Tests\Feature;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommonFaqsTest extends TestCase
{
    use RefreshDatabase;

    public function test_common_questions_are_published_in_both_languages(): void
    {
        $this->assertSame(12, Faq::query()->where('published', true)->count());

        $this->get('/ar/faq')->assertOk()
            ->assertSee('كيف أبدأ مشروعًا معكم؟')
            ->assertSee('هل تضمنون ظهور الموقع في نتائج البحث؟')
            ->assertDontSee('How do I start a project with you?');
        $this->get('/en/faq')->assertOk()
            ->assertSee('How do I start a project with you?')
            ->assertSee('No specific search ranking can be guaranteed.')
            ->assertDontSee('كيف أبدأ مشروعًا معكم؟');
    }

    public function test_rerunning_content_migration_preserves_admin_edits_and_other_questions(): void
    {
        $faq = Faq::query()->where('question_en', 'How do I start a project with you?')->firstOrFail();
        $faq->update(['answer_en' => 'Owner-approved answer', 'sort_order' => 1]);
        Faq::create([
            'question_ar' => 'سؤال خاص', 'question_en' => 'Custom question',
            'answer_ar' => 'إجابة خاصة', 'answer_en' => 'Custom answer',
            'published' => true,
        ]);

        $migration = require database_path('migrations/2026_10_03_000001_add_common_faqs.php');
        $migration->up();
        $migration->down();

        $this->assertSame(13, Faq::query()->count());
        $this->assertSame('Owner-approved answer', $faq->fresh()->answer_en);
        $this->assertSame(1, $faq->fresh()->sort_order);
        $this->assertTrue(Faq::query()->where('question_en', 'Custom question')->exists());
    }
}
